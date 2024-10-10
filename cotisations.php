<?php
include 'connPDO.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$idLicence = "";
$nom = "";
$prenom = "";
$montantR = "";
$datePaiement = "";
$moyenPaiement = "";

try {
    $query = $pdo->query("SHOW COLUMNS FROM cotisation LIKE 'moyenPaiement'");
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $type = $row['Type'];
    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $enum_values = explode("','", $matches[1]);
} catch (PDOException $e) {
    echo 'Erreur de requête : ' . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['submit_first_form'])) {
        // Traitement du premier formulaire
        $nom = isset($_POST['nomLicencié']) ? htmlspecialchars($_POST['nomLicencié']) : "";
        $prenom = isset($_POST['prenomLicencié']) ? htmlspecialchars($_POST['prenomLicencié']) : "";

        $stmt = $pdo->prepare("SELECT idLicence FROM licencie WHERE nom = ? AND prenom = ?");
        $stmt->execute([$nom, $prenom]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $idLicence = htmlspecialchars($result['idLicence']);
        } else {
            echo "Aucune licence trouvée pour le nom et le prénom spécifiés.";
        }
    } elseif (isset($_POST['submit_second_form']) && isset($_POST['idLicence'])) {
        // Traitement du deuxième formulaire
        $idLicence = isset($_POST['idLicence']) ? htmlspecialchars($_POST['idLicence']) : "";
        $moyenPaiement = isset($_POST['moyenPaiement']) ? htmlspecialchars($_POST['moyenPaiement']) : "";
        $montantR = isset($_POST['montantR']) ? htmlspecialchars($_POST['montantR']) : "";
        $datePaiement = isset($_POST['datePaiement']) ? htmlspecialchars($_POST['datePaiement']) : "";

        // Afficher les valeurs pour le débogage
        // echo "idLicence: $idLicence<br>";
        // echo "moyenPaiement: $moyenPaiement<br>";
        // echo "montantR: $montantR<br>";
        // echo "datePaiement: $datePaiement<br>";

        // Récupérer le numéro de cotisation
        $stmt3 = $pdo->prepare("SELECT numCotisation FROM cotisation WHERE idLicence = ? AND complet = FALSE");
        $stmt3->execute([$idLicence]);
        $numCotisation = $stmt3->fetchColumn();

        // Afficher la valeur pour le débogage
        // echo "numCotisation: $numCotisation<br>";

        if (!$numCotisation) {
            // Créer une nouvelle cotisation si aucune cotisation incomplète n'est trouvée
            $montant = 200;
            $stmt1 = $pdo->prepare("INSERT INTO cotisation(idLicence, montant, moyenPaiement) VALUES (?, ?, ?)");
            if ($stmt1->execute([$idLicence, $montant, $moyenPaiement])) {
                // echo "Cotisation ajoutée avec succès.";
                $numCotisation = $pdo->lastInsertId();
            } else {
                // echo "Erreur lors de l'ajout de la cotisation.";
                exit;
            }
        }

        // Vérifier si des paiements existent déjà pour ce licencié
        $stmt4 = $pdo->prepare("SELECT MAX(CONVERT(numeroVersement, UNSIGNED INTEGER)) AS maxVersement FROM ajoutePaiement WHERE numCotisation = ?");
        $stmt4->execute([$numCotisation]);
        $maxVersement = $stmt4->fetchColumn();
        

        // Afficher la valeur pour le débogage
        // echo "maxVersement: $maxVersement<br>";

        if ($maxVersement === null) {
            $numeroVersement = 1;
        } else {
            $numeroVersement = $maxVersement + 1;
        }

        // Insérer le paiement dans la base de données avec le numéro de versement approprié
        $stmt = $pdo->prepare("INSERT INTO ajoutePaiement (numCotisation, numeroVersement, montantR, datePaiement) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$numCotisation, $numeroVersement, $montantR, $datePaiement])) {
            // Mettre à jour la cotisation si le montant total est atteint
            $stmt5 = $pdo->prepare("UPDATE cotisation c SET complet = TRUE WHERE c.numCotisation = ? AND (SELECT SUM(ap.montantR) FROM ajoutePaiement ap WHERE ap.numCotisation = c.numCotisation) >= c.montant");
            $stmt5->execute([$numCotisation]);

            // Redirection vers la page protegee.php
            header("Location: pageProtegee.php");
            exit();
        } else {
            echo "Erreur lors de l'ajout du paiement.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Règlement de la cotisation</title>
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
            <form action="" method="POST">
                <!-- Premier formulaire pour la recherche -->
                <label>Nom du licencié</label>
                <input type="text" id="nomLicencié" name="nomLicencié" value="<?php echo htmlspecialchars($nom); ?>" required>
                <label>Prénom du licencié</label>
                <input type="text" id="prenomLicencié" name="prenomLicencié" value="<?php echo htmlspecialchars($prenom); ?>" required>
                <button type="submit" name="submit_first_form" class="custom-btn"><i class="fas fa-search"></i></button>
            </form>

            <?php if (!empty($idLicence)): ?>
            <form action="" method="POST">
                <!-- Deuxième formulaire pour ajouter la cotisation et le paiement -->
                <input type="hidden" name="submit_second_form" value="1">
                <label>Numéro de licence :</label>
                <input type="text" id="idLicence" name="idLicence" value="<?php echo htmlspecialchars($idLicence); ?>" readonly>
                <label for='moyenPaiement'>Moyen de paiement :</label>
                <select name='moyenPaiement' id='moyenPaiement'>
                    <?php
                    foreach ($enum_values as $value) {
                        $selected = ($moyenPaiement == $value) ? "selected" : "";
                        echo "<option value=\"$value\" $selected>$value</option>";
                    }
                    ?>
                </select>
                <label>Montant réglé :</label>
                <input type="number" id="montantR" name="montantR" value="<?php echo htmlspecialchars($montantR); ?>">
                <label for="datePaiement">Date de paiement :</label>
                <input type="date" id="datePaiement" name="datePaiement" value="<?php echo htmlspecialchars($datePaiement); ?>" required>
                <div class="terminer">
                    <button class="custom-btn" type="submit"><i class="fa-solid fa-plus"></i></button>
                    <button class="custom-btn" type="reset"><i class="fa-solid fa-times"></i></button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
