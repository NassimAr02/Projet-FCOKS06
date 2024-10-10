<?php
include "connPDO.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['idLicence'])) {
    $idLicence = htmlspecialchars($_GET['idLicence']);

    // Récupérer les informations du licencié
    $sql = $pdo->prepare("SELECT * FROM licencie WHERE idLicence = :idLicence");
    $sql->execute([':idLicence' => $idLicence]);
    $licencie = $sql->fetch(PDO::FETCH_ASSOC);

    // Récupérer les catégories
    $sqlCat = $pdo->prepare("SELECT * FROM categorie");
    $sqlCat->execute();
    $categories = $sqlCat->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les équipes
    $sqlEquipe = $pdo->prepare("SELECT * FROM equipe");
    $sqlEquipe->execute();
    $equipes = $sqlEquipe->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Redirection si idLicence n'est pas spécifié
    header("Location: rechercheJoueur2.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les informations du licencié</title>
    <link rel="stylesheet" type="text/css" href="styleModif.css">
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
<div class="main">
    <div class="formulaire">
        <div class="form">
            <form id="editForm" method="POST" action="modifierLicencie.php">
                <input type="hidden" name="idLicence" value="<?php echo htmlspecialchars($licencie['idLicence']); ?>">
                <input type="hidden" name="numLicence" value="<?php echo htmlspecialchars($licencie['numLicence']); ?>">
                <input type="hidden" name="nom" value="<?php echo htmlspecialchars($licencie['nom']); ?>">
                <input type="hidden" name="prenom" value="<?php echo htmlspecialchars($licencie['prenom']); ?>">
                <input type="hidden" name="dateNaissance" value="<?php echo htmlspecialchars($licencie['dateNaissance']); ?>">
                <input type="hidden" name="nomEquipe" value="<?php echo htmlspecialchars($licencie['nomEquipe']); ?>">
                <input type="hidden" name="codeCat" value="<?php echo htmlspecialchars($licencie['codeCat']); ?>">
                <input type="hidden" name="fonction" value="<?php echo htmlspecialchars($licencie['fonction']); ?>">
                <input type="hidden" name="numTel" value="<?php echo htmlspecialchars($licencie['numTel']); ?>">
                <input type="hidden" name="mail" value="<?php echo htmlspecialchars($licencie['mail']); ?>">
                <label for="adresse">Adresse:</label>
                <input type="text" name="adresse" value="<?php echo htmlspecialchars($licencie['adresse']); ?>"><br>
                <label for="cp">Code postal:</label>
                <input type="text" name="cp" value="<?php echo htmlspecialchars($licencie['cp']); ?>"><br>
                <label for="ville">Ville:</label>
                <input type="text" name="ville" value="<?php echo htmlspecialchars($licencie['ville']); ?>"><br>
                <label for="saisie">Nom de saisie de licence:</label>
                <input type="text" name="saisie" value="<?php echo htmlspecialchars($licencie['nomSaisieLicence']); ?>"><br>
                <label for="dateSaisie">Date de saisie de licence:</label>
                <input type="date" name="dateSaisie" value="<?php echo htmlspecialchars($licencie['dateSaisieLicence']); ?>"><br>
                <button type="submit" class="custom-btn"><i class='fas fa-save'></i></button>
                <button type="button" class="custom-btn" onclick="goBack()"><i class="fas fa-arrow-left"></i></button>
            </form>
        </div>
    </div>
</div>

<script>
    function saveChanges(form) {
    const formData = new FormData(form);

    fetch('modifierLicencie.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur lors de la sauvegarde des modifications.');
        }
        return response.text();
    })
    .then(data => {
        console.log(data);
        alert('Modifications sauvegardées avec succès');
    })
    .catch(error => console.error('Erreur:', error));
}
function goBack() {
        window.history.back();
    }

</script>

</body>
</html>
