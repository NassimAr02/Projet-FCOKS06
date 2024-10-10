<?php
include "connPDO.php";
session_start();

$idLicence = "";
$equipements = [
    ['nom' => 'Parka', 'type' => 'Sorti/Senior2'],
    ['nom' => 'Haut', 'type' => 'Sorti/Senior2'],
    ['nom' => 'Bas', 'type' => 'Sorti/Senior2'],
    ['nom' => 'Polo', 'type' => 'Sorti/Senior2'],
    ['nom' => 'Short', 'type' => 'Sorti/Senior2'],
    ['nom' => 'Basket', 'type' => 'Sorti/Senior2'],
    ['nom' => 'Claquette', 'type' => 'Sorti/Senior2'],
    ['nom' => 'Sac', 'type' => 'Sorti/Senior2'],
];

$saisons = [
    '2022-2023',
    '2023-2024',
    '2024-2025',
    '2025-2026',
    '2026-2027',
    '2027-2028',
    '2028-2029',
    '2029-2030'
];

$tailles = ['164', '176', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
$tailles2 = ['39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50'];
$tailles3 = ['Unique'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $idLicence = isset($_GET['idLicence']) ? htmlspecialchars($_GET['idLicence']) : "";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idLicence = isset($_POST['idLicence']) ? htmlspecialchars($_POST['idLicence']) : "";
    $saisonSelectionnee = isset($_POST['saison']) ? htmlspecialchars($_POST['saison']) : "";
    $allFieldsFilled = true;
    $countEquipements = count($equipements);

    for ($i = 0; $i < $countEquipements; $i++) {
        $nomEquipement = $equipements[$i]['nom'];
        $tailleEquipement = isset($_POST['taille'][$i]) ? htmlspecialchars($_POST['taille'][$i]) : "";
        $distribue = isset($_POST['distribue_entrainement'][$i]) ? htmlspecialchars($_POST['distribue_entrainement'][$i]) : "";
        $dateDistribution = isset($_POST['date'][$i]) ? htmlspecialchars($_POST['date'][$i]) : "";

        if (empty($tailleEquipement) ) {
            $allFieldsFilled = false;
            break;
        }
        if (empty($distribue)){
            $dateDistribution = null;
            $distribue = 0;
        }

        if (($nomEquipement == 'Basket' || $nomEquipement == 'Claquette') && !in_array($tailleEquipement, $tailles2)) {
            $allFieldsFilled = false;
            break;
        } elseif ($nomEquipement == 'Sac' && !in_array($tailleEquipement, $tailles3)) {
            $allFieldsFilled = false;
            break;
        } elseif (!($nomEquipement == 'Basket' || $nomEquipement == 'Claquette' || $nomEquipement == 'Sac') && !in_array($tailleEquipement, $tailles)) {
            $allFieldsFilled = false;
            break;
        }

        try {
            $dateDistribution = !empty($dateDistribution) ? $dateDistribution : null;
            $stmt = $pdo->prepare("INSERT INTO equipement (tailleEquipement, dateDistribution, nomEquipement, distribue, typeEquipement, idLicence, saison) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tailleEquipement, $dateDistribution, $nomEquipement, $distribue, $equipements[$i]['type'], $idLicence, $saisonSelectionnee]);
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion dans la base de données : " . $e->getMessage();
        }
    }

    if ($allFieldsFilled) {
        header("Location: ajoutSenior2Entrainement.php?idLicence=" . urlencode($idLicence));
        exit();
    } else {
        echo "Veuillez remplir tous les champs correctement.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout de l'équipement d'entraînement du licencié</title>
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
    <div class="formulaire1">
        <form action="" method="POST">
            <div class="form">
                <label for="idLicence">Identifiant de licence :</label>
                <input type="text" id="idLicence" name="idLicence" value="<?php echo htmlspecialchars($idLicence); ?>" readonly>
            </div>
            <br>
            <div class="form">
                <label for="typeEquipement">Type d'équipement :</label>
                <input type="text" id="typeEquipement" name="typeEquipement" value="Sorti/Senior2" readonly>
            </div>
            <br>
            <div class="form">
                <label for="saison">Sélectionnez la saison :</label>
                <select name="saison" id="saison" required>
                    <option value="">Choisissez une saison</option>
                    <?php foreach ($saisons as $option): ?>
                        <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>

            <table>
                <thead>
                    <tr>
                        <th>Nom d'équipement</th>
                        <th>Taille</th>
                        <th>Distribué</th>
                        <th>Date de distribution</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipements as $index => $equipement): ?>
                        <tr>
                            <td>
                                <input type="text" name="nomEquipement[]" value="<?php echo htmlspecialchars($equipement['nom']); ?>" readonly>
                            </td>
                            <td>
                                <select name="taille[]" required>
                                    <option value="">Sélectionnez une taille</option>
                                    <?php
                                    $taillesUtilisees = ($equipement['nom'] == 'Basket' || $equipement['nom'] == 'Claquette') ? $tailles2 : (($equipement['nom'] == 'Sac') ? $tailles3 : $tailles);
                                    foreach ($taillesUtilisees as $taille): ?>
                                        <option value="<?php echo $taille; ?>"><?php echo $taille; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <div class="radio-container">
                                    <label><input type="checkbox" name="distribue_entrainement[<?php echo $index; ?>]" value="1"></label>
                                    
                                </div>
                            </td>
                            <td>
                                <input type="date" name="date[]">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="terminer">
                <button class="custom-btn" type="submit"><i class="fa-solid fa-plus"></i></button>
                <button class="custom-btn" type="reset"><i class="fa-solid fa-times"></i></button>
            </div>
        </form>
    </div>
</main>

</body>
</html>
