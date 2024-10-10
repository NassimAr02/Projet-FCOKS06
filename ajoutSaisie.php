<?php
include "connPDO.php";
session_start();

$errors = [];
$numLicence = $nomSaisieLicence = $dateSaisieLicence = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $numLicence = isset($_GET['numLicence']) ? htmlspecialchars($_GET['numLicence']) : "";
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['numLicence'])) {
    $numLicence = isset($_POST['numLicence']) ? htmlspecialchars($_POST['numLicence']) : "";
    $nomSaisieLicence = isset($_POST['nomSaisie']) ? htmlspecialchars($_POST['nomSaisie']) : "";
    $dateSaisieLicence = isset($_POST['dateSaisie']) ? htmlspecialchars($_POST['dateSaisie']) : "";

    // Vérification que tous les champs nécessaires sont remplis
    if (!empty($numLicence) && !empty($dateSaisieLicence) && !empty($nomSaisieLicence)) {
        try {
            // Préparation de la requête SQL
            $stmt = $pdo->prepare("UPDATE licencie SET dateSaisieLicence = :dateSaisieLicence, nomSaisieLicence = :nomSaisieLicence WHERE numLicence = :numLicence");
            $stmt->bindParam(':dateSaisieLicence', $dateSaisieLicence);
            $stmt->bindParam(':nomSaisieLicence', $nomSaisieLicence);
            $stmt->bindParam(':numLicence', $numLicence);
            $stmt->execute();

            // Redirection vers la page de succès après l'exécution
            header("Location: pageProtegee.php");
            exit();
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
    <title>Mise à jour d'une saisie de licence</title>
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
        <form action="ajoutSaisie.php" method="POST">
            <input type="hidden" id="numLicence" name="numLicence" value="<?php echo htmlspecialchars($numLicence); ?>" required>

            <label for="nomSaisie">Saisie par :</label>
            <input type="text" id="nomSaisie" name="nomSaisie" value="<?php echo htmlspecialchars($nomSaisieLicence); ?>" required>

            <label for="dateSaisie">Date de saisie :</label>
            <input type="date" id="dateSaisie" name="dateSaisie" value="<?php echo htmlspecialchars($dateSaisieLicence); ?>" required>

            <div class="terminer">
                <button class="custom-btn" type="submit"><i class="fa-solid fa-plus"></i></button>
                <button class="custom-btn" type="reset"><i class="fa-solid fa-times"></i></button>
            </div>
        </form>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
