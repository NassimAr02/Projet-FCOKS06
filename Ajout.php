<?php
include "connPDO.php"; // Assurez-vous que ce fichier inclut la connexion à votre base de données
session_start();

// Initialize variables
$numLicence = $nom = $prenom = $codeCat = $fonction = $nomEquipe = "";

// Function to fetch enum values from the database
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

// Fetch enum values for 'fonction'
$enum_values = getEnumValues($pdo, 'licencie', 'fonction');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Sanitize inputs
    $numLicence = htmlspecialchars($_POST['numLicence']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $codeCat = htmlspecialchars($_POST['codeCat']);
    $fonction = htmlspecialchars($_POST['fonction']);
    $nomEquipe = isset($_POST['nomEquipe']) ? htmlspecialchars($_POST['nomEquipe']) : '';

    // Check if all required fields are filled
    if (!empty($numLicence) && !empty($fonction) && !empty($nom) && !empty($prenom) && !empty($codeCat) && !empty($nomEquipe)) {
        try {
            // Check if the licence number already exists
            $checkLicence = $pdo->prepare("SELECT numLicence FROM licencie WHERE numLicence = :numLicence");
            $checkLicence->bindParam(':numLicence', $numLicence, PDO::PARAM_STR);
            $checkLicence->execute();

            if ($checkLicence->rowCount() > 0) {
                $errors[] = "Numéro de licence déjà existant. Veuillez en choisir un autre.";
            } else {
                // Check if the selected team exists for the given category
                $checkEquipe = $pdo->prepare("SELECT nomEquipe FROM equipe WHERE nomEquipe = :nomEquipe AND codeCat = :codeCat");
                $checkEquipe->bindParam(':nomEquipe', $nomEquipe, PDO::PARAM_STR);
                $checkEquipe->bindParam(':codeCat', $codeCat, PDO::PARAM_STR);
                $checkEquipe->execute();

                if ($checkEquipe->rowCount() == 0) {
                    $errors[] = "L'équipe sélectionnée n'existe pas pour cette catégorie.";
                } else {
                    // Insertion into database
                    $stmt = $pdo->prepare("INSERT INTO licencie (numLicence, fonction, nom, prenom, nomEquipe, codeCat) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$numLicence, $fonction, $nom, $prenom, $nomEquipe, $codeCat]);

                    // Redirect on success
                    header("Location: AjoutR.php?numLicence=" . urlencode($numLicence));
                    exit();
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'exécution de la requête : " . $e->getMessage();
        }
    } 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un licencié</title>
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

<main>
    <div class="formulaire">
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="numLicence">Numéro de licence :</label>
            <input type="text" id="numLicence" name="numLicence" value="<?php echo htmlspecialchars($numLicence); ?>" required>

            <label for="fonction">Fonction :</label>
            <select name="fonction" id="fonction" required>
                <option value="">Sélectionnez une fonction</option>
                <?php foreach ($enum_values as $value): ?>
                    <option value="<?php echo $value; ?>" <?php echo ($fonction == $value) ? "selected" : ""; ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>

            <label for="codeCat">Catégorie :</label>
            <select id="codeCat" name="codeCat" onchange="this.form.submit()" required>
                <option value="">Choisissez une catégorie</option>
                <?php
                $sql = "SELECT DISTINCT codeCat FROM equipe";
                $result = $pdo->query($sql);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($codeCat == $row['codeCat']) ? "selected" : "";
                    echo '<option value="' . $row['codeCat'] . '" ' . $selected . '>' . $row['codeCat'] . '</option>';
                }
                ?>
            </select>

            <?php if (!empty($codeCat)): ?>
                <label for="nomEquipe">Équipe :</label>
                <select id="nomEquipe" name="nomEquipe" required>
                    <option value="">Choisissez une équipe</option>
                    <?php
                    $sqlNom = "SELECT nomEquipe FROM equipe WHERE codeCat = :codeCat";
                    $stmtNom = $pdo->prepare($sqlNom);
                    $stmtNom->bindParam(":codeCat", $codeCat, PDO::PARAM_STR);
                    $stmtNom->execute();
                    $resultNom = $stmtNom->fetchAll(PDO::FETCH_ASSOC);
                    if ($resultNom) {
                        foreach ($resultNom as $row1) {
                            $selected = ($nomEquipe == $row1['nomEquipe']) ? "selected" : "";
                            echo '<option value="' . $row1['nomEquipe'] . '" ' . $selected . '>' . $row1['nomEquipe'] . '</option>';
                        }
                    } 
                    ?>
                </select>
            <?php endif; ?>

            <div class="terminer">
                <button class="custom-btn" type="submit"><i class="fas fa-plus"></i></button>
                <button class="custom-btn" type="reset"><i class="fas fa-times"></i></button>
            </div>
        </form>
    </div>
</main>

</body>
</html>
