<?php
include "connPDO.php";
session_start();

// Initialisation des variables
$numLicence = $dateDeNaissance = $adresse = $cp = $Ville = $numTél = $mail = "";
$errors = [];

// Vérification du numéro de licence via GET
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['numLicence'])) {
    $numLicence = htmlspecialchars($_GET['numLicence']);
    
    // Récupération des informations actuelles du licencié
    $stmt = $pdo->prepare("SELECT * FROM licencie WHERE numLicence = ?");
    $stmt->execute([$numLicence]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Assignation des valeurs actuelles aux variables
        $dateDeNaissance = $row['dateNaissance'];
        $adresse = $row['adresse'];
        $CP = $row['CP'];
        $Ville = $row['Ville'];
        $numTél = $row['numTél'];
        $mail = $row['mail'];
    } else {
        $errors[] = "Licencié non trouvé.";
    }
}

// Traitement du formulaire soumis
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['numLicence'])) {
    // Récupération des données du formulaire
    $numLicence = htmlspecialchars($_POST['numLicence']);
    $dateDeNaissance = htmlspecialchars($_POST['dateDeNaissance']);
    $adresse = htmlspecialchars($_POST['Adresse']);
    $CP = htmlspecialchars($_POST['CP']);
    $Ville = htmlspecialchars($_POST['Ville']);
    $numTél = htmlspecialchars($_POST['numTel']);
    $mail = htmlspecialchars($_POST['mail']);

    // Transformation de la date de naissance au format YYYY-MM-DD si elle n'est pas vide
    if (!empty($dateDeNaissance)) {
        $dateDeNaissance = implode('-', array_reverse(explode('/', $dateDeNaissance)));
    }

    // Validation des champs requis
    if (!empty($numLicence) && !empty($dateDeNaissance) && !empty($adresse) && !empty($CP) && !empty($Ville) && !empty($numTél) && !empty($mail)) {
        try {
            // Préparation de la requête SQL
            $stmt = $pdo->prepare("UPDATE licencie SET dateNaissance = ?, adresse = ?, cp = ?, ville = ?, numTel = ?, mail = ? WHERE numLicence = ?");
            // Exécution de la requête avec les données
            $stmt->execute([$dateDeNaissance, $adresse, $CP, $Ville, $numTél, $mail, $numLicence]);

            // Vérification si la mise à jour a réussi
            if ($stmt->rowCount() > 0) {
                // Redirection après succès
                header("Location: ajoutSaisie.php?numLicence=" . urlencode($numLicence));
                exit();
            } else {
                $errors[] = "Aucune ligne mise à jour. Veuillez vérifier le numéro de licence.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'exécution de la requête : " . $e->getMessage();
        }
    } else {
        $errors[] = "Veuillez remplir tous les champs nécessaires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour d'un licencié</title>
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
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label>Date de naissance :</label>
            <input type="date" id="dateDeNaissance" name="dateDeNaissance" value="<?php echo htmlspecialchars($dateDeNaissance); ?>" required>

            <label>Adresse :</label>
            <input type="text" id="Adresse" name="Adresse" value="<?php echo htmlspecialchars($adresse); ?>" required>

            <label>Code postal :</label>
            <input type="text" id="CP" name="CP" value="<?php echo htmlspecialchars($CP); ?>" required>

            <label>Ville :</label>
            <input type="text" id="Ville" name="Ville" value="<?php echo htmlspecialchars($Ville); ?>" required>

            <label>Numéro de téléphone :</label>
            <input type="text" id="numTel" name="numTel" value="<?php echo htmlspecialchars($numTél); ?>" required>

            <label>Mail de contact :</label>
            <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($mail); ?>" required>

            <input type="hidden" name="numLicence" value="<?php echo htmlspecialchars($numLicence); ?>" required>

            <div class="terminer">
                <button class="custom-btn" type="submit"><i class="fa-solid fa-plus"></i></button>
                <button class="custom-btn" type="reset"><i class="fa-solid fa-times"></i></button>
            </div>
        </form>
    </div>
</main>

</body>
</html>
