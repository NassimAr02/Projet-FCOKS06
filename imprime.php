<?php
include "connPDO.php";

// Appel à la fonction getEnumValues pour récupérer les valeurs de la colonne 'Fonction'
$enum_values = getEnumValues($pdo, 'licencie', 'fonction');

function getEnumValues($pdo, $table, $field) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM $table LIKE ?");
        $stmt->execute([$field]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Extraire les valeurs enum de la réponse
        preg_match("/^enum\(\'(.*)\'\)$/", $row['Type'], $matches);
        $enum_values = explode("','", $matches[1]);
        
        return $enum_values;
    } catch (PDOException $e) {
        echo 'Erreur de requête : ' . $e->getMessage();
        exit;
    }
}
?>

                

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimer une fiche récapitulative</title>
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
        <form action="pageàimprimer.php" method="POST">
            <label>Nom du licencié</label>
            <input type="text" id="nomLicencié" name="nomLicencié">
            <label>Prénom du licencié</label>
            <input type="text" id="prenomLicencié" name="prenomLicencié">
            <label for="fonction">Fonction :</label>
            <select name="fonction" id="fonction" required>
                <option value="">Sélectionnez une fonction</option>
                <?php foreach ($enum_values as $value): ?>
                    <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                <?php endforeach; ?>
            </select>
            <label>Sélectionnez la saison</label>
            <select name="saison" id="saison">
                <option value="2022-2023">2022-2023</option>
                <option value="2023-2024">2023-2024</option>
                <option value="2024-2025">2024-2025</option>
                <option value="2025-2026">2025-2026</option>
                <option value="2026-2027">2026-2027</option>
                <option value="2027-2028">2027-2028</option>
                <option value="2028-2029">2028-2029</option>
                <option value="2029-2030">2029-2030</option>
            </select>
            <div class="terminer">
                <button type="submit" class="custom-btn"><i class="fas fa-search"></i></button>
                <button class="custom-btn" type="reset"><i class="fa-solid fa-times"></i></button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
