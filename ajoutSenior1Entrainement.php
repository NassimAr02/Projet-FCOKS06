<?php
include "connPDO.php";
session_start();

$idLicence = "";
$equipements = [
    ['nom' => '3 Sweats', 'type' => 'Entrainement/Senior1'],
    ['nom' => '3 Maillots', 'type' => 'Entrainement/Senior1'],
    ['nom' => '3 Shorts', 'type' => 'Entrainement/Senior1'],
    ['nom' => '3 Pantalons', 'type' => 'Entrainement/Senior1'],
    ['nom' => '3 Chaussette Basse', 'type' => 'Entrainement/Senior1'],
    ['nom' => '2 Chaussette Match Blanche', 'type' => 'Entrainement/Senior1'],
    ['nom' => '2 Chaussette Match Noire', 'type' => 'Entrainement/Senior1'],
    ['nom' => 'Tech-Fit Blanc', 'type' => 'Entrainement/Senior1'],
    ['nom' => 'Tech-Fit Noir', 'type' => 'Entrainement/Senior1'],
];

$tailles = ['164', '176', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
$tailles2 = ['39/42', '43/45', '46/48', '49/52'];

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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $idLicence = isset($_GET['idLicence']) ? htmlspecialchars($_GET['idLicence']) : "";
    
}

// Traitement du formulaire soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idLicence = isset($_POST['idLicence']) ? htmlspecialchars($_POST['idLicence']) : "";
    $saison = isset($_POST['saison']) ? htmlspecialchars($_POST['saison']) : "";

    // Vérifier que tous les champs requis sont remplis
    $allFieldsFilled = true;
    $countEquipements = count($equipements);

    for ($i = 0; $i < $countEquipements; $i++) {
        $nomEquipement = $equipements[$i]['nom'];
        $typeEquipement = $equipements[$i]['type'];

        $taillesUtilisees = ($nomEquipement == '3 Chaussette Basse' || $nomEquipement == '2 Chaussette Match Blanche' || $nomEquipement == '2 Chaussette Match Noire') ? $tailles2 : $tailles;

        $tailleEquipement = isset($_POST['taille'][$i]) ? htmlspecialchars($_POST['taille'][$i]) : "";
        $distribue = isset($_POST['distribue_entrainement'][$i]) ? htmlspecialchars($_POST['distribue_entrainement'][$i]) : "";
        $dateDistribution = isset($_POST['date'][$i]) ? htmlspecialchars($_POST['date'][$i]) : "";

        // Vérifier si au moins un des champs requis est vide
        if (empty($tailleEquipement)) {
            $allFieldsFilled = false;
            break;
        }
        if (empty($distribue)){
            $dateDistribution = null;
            $distribue = 0;
        }

        // Vérification de l'utilisation des tailles
        if ($nomEquipement == '3 Chaussette Basse' || $nomEquipement == '2 Chaussette Match Blanche' || $nomEquipement == '2 Chaussette Match Noire') {
            if (!in_array($tailleEquipement, $tailles2)) {
                $allFieldsFilled = false;
                break;
            }
        } else {
            if (!in_array($tailleEquipement, $tailles)) {
                $allFieldsFilled = false;
                break;
            }
        }

        // Définir la valeur par défaut pour $coupe
        $coupe = 0;
        if ($nomEquipement == '2 Chaussette Match Blanche' || $nomEquipement == '2 Chaussette Match Noire') {
            $coupe = isset($_POST['coupe'][$i]) && $_POST['coupe'][$i] == "1" ? 1 : 0;
        }

        // Insertion dans la base de données (exemple)
        try {
            // Utilisation d'une valeur par défaut pour dateDistribution si elle est vide
            $dateDistribution = !empty($dateDistribution) ? $dateDistribution : null;

            $stmt = $pdo->prepare("INSERT INTO equipement (tailleEquipement, dateDistribution, nomEquipement, distribue, typeEquipement, idLicence, coupe, saison) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tailleEquipement, $dateDistribution, $nomEquipement, $distribue, $typeEquipement, $idLicence, $coupe, $saison]);
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion dans la base de données : " . $e->getMessage();
            $allFieldsFilled = false;
            break;
        }
    }

    // Redirection après succès
    if ($allFieldsFilled) {
        header("Location: pageProtegee.php");
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
    <title>Ajout d'équipement de sortie pour l'éducateur</title>
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
                <label for="idLicence">Numéro de licence :</label>
                <input type="text" id="idLicence" name="idLicence" value="<?php echo htmlspecialchars($idLicence); ?>" readonly>
            </div>
            <br>
            <div class="form">
                <label for="typeEquipement">Type d'équipement :</label>
                <input type="text" id="typeEquipement" name="typeEquipement" value="Entrainement/Senior1" readonly>
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

            <!-- Tableau pour afficher les équipements -->
            <table>
                <thead>
                    <tr>
                        <th>Nom d'équipement</th>
                        <th>Taille</th>
                        <th>Distribué</th>
                        <th>Coupe</th>
                        <th>Date de distribution</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Boucle pour chaque équipement -->
                    <?php foreach ($equipements as $index => $equipement): ?>
                        <tr>
                            <td>
                                <input type="text" name="nomEquipement[]" value="<?php echo htmlspecialchars($equipement['nom']); ?>" readonly>
                            </td>
                            <td>
                                <select name="taille[]" required>
                                    <option value="">Sélectionnez une taille</option>
                                    <?php
                                    $taillesUtilisees = ($equipement['nom'] == '3 Chaussette Basse' || $equipement['nom'] == '2 Chaussette Match Blanche' || $equipement['nom'] == '2 Chaussette Match Noire') ? $tailles2 : $tailles;
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
                                <div class="radio-container">
                                    <label><input type="checkbox" name="coupe[<?php echo $index; ?>]" value="1" <?php echo ($equipement['nom'] == '2 Chaussette Match Blanche' || $equipement['nom'] == '2 Chaussette Match Noire') ? '' : 'disabled'; ?>></label>
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

                                    