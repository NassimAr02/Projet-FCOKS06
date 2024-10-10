<?php
include "connPDO.php";

$nom = "";
$prenom = "";
$fonction = "";
$message = "";
$idLicence = "";

// Function to get ENUM values from a specific field in a table
function getEnumValues($pdo, $table, $field) {
    try {
        $query = $pdo->query("SHOW COLUMNS FROM $table LIKE '$field'");
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $type = $row['Type'];
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        return explode("','", $matches[1]);
    } catch (PDOException $e) {
        echo 'Erreur de requête : ' . $e->getMessage();
        exit;
    }
}

$enum_values = getEnumValues($pdo, 'licencie', 'fonction');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nom = htmlspecialchars($_POST['nomLicencié'] ?? "");
    $prenom = htmlspecialchars($_POST['prenomLicencié'] ?? "");
    $fonction = htmlspecialchars($_POST['fonction'] ?? "");

    // Query to retrieve licencie information
    $stmt = $pdo->prepare("SELECT l.idLicence, c.ecoleDeFoot, l.fonction, e.senior1, e.senior2, l.dateSaisieLicence
                            FROM licencie l
                            JOIN categorie c ON l.codeCat = c.codeCat
                            JOIN equipe e ON l.nomEquipe = e.nomEquipe
                            WHERE l.nom = ? AND l.prenom = ? AND l.fonction = ?");
    $stmt->execute([$nom, $prenom, $fonction]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $idLicence = $result['idLicence'];

        if ($result['fonction'] == "Educateur") {
            header("Location: ajoutE3.php?idLicence=" . urlencode($idLicence));
        } else {
            if ($result['senior1']) {
                header("Location: ajoutSenior1Sorti.php?idLicence=" . urlencode($idLicence));
            } else if ($result['senior2']) {
                header("Location: ajoutSenior2Sorti.php?idLicence=" . urlencode($idLicence));
            } else if ($result['ecoleDeFoot']) {
                header("Location: ajoutE2.php?idLicence=" . urlencode($idLicence));
            } else {
               header("Location: choixEquipement.php?idLicence=" . urlencode($idLicence));
            }
        }
        exit();
    } else {
        $message = "Aucune licence trouvée pour le nom et le prénom spécifiés.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche du licencié</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
    <link rel="stylesheet" type="text/css" href="styleHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
<header class="fixed-header">
    <div class="logo">
        <img src="logo.png" alt="Logo FCOSK 06">
    </div>
    <nav class="main-nav">
        <ul>
            <li><a href="pageProtegee.php"><i class="fas fa-home"></i> Accueil</a></li>
            <li><a href="ajoutCSV.php"><i class="fas fa-upload"></i> Importer</a></li>
            <li><a href="Ajout.php"><i class="fas fa-user-plus"></i> Ajouter</a></li>
            <li><a href="rechercheJoueur.php"><i class="fas fa-tshirt"></i> Equipement</a></li>
            <li><a href="cotisations.php"><i class="fas fa-money-check-alt"></i> Cotisations</a></li>
            <li><a href="rechercheJoueur2.php"><i class="fas fa-pencil-alt"></i> Consulter</a></li>
            <li><a href="imprime.php"><i class="fas fa-print"></i> Imprimer</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </nav>
</header>

<div class="formulaireRecherche">
    <div class="form">
        <form action="" method="POST">
            <label>Nom du licencié</label>
            <input type="text" id="nomLicencié" name="nomLicencié" value="<?php echo htmlspecialchars($nom); ?>" required>
            <label>Prénom du licencié</label>
            <input type="text" id="prenomLicencié" name="prenomLicencié" value="<?php echo htmlspecialchars($prenom); ?>" required>
            <label for="fonction">Fonction :</label>
            <select name="fonction" id="fonction">
                <?php foreach ($enum_values as $value): ?>
                    <option value="<?php echo $value; ?>" <?php echo ($fonction == $value) ? "selected" : ""; ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="custom-btn"><i class="fas fa-search"></i></button>
        </form>
        <?php if (!empty($message)) { echo '<p>' . htmlspecialchars($message) . '</p>'; } ?>
        <?php if (!empty($idLicence)) { echo '<p>ID Licence : ' . htmlspecialchars($idLicence) . '</p>'; } ?>
    </div>
</div>
</body>
</html>
