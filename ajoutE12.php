<?php
include "connPDO.php";
session_start();

$idLicence = isset($_GET['idLicence']) ? htmlspecialchars($_GET['idLicence']) : "";
$equipements = [
    ['nom' => 'Pull-Maillot', 'type' => 'Entrainement'],
    ['nom' => 'Pantalon', 'type' => 'Entrainement'],
    ['nom' => 'Short', 'type' => 'Entrainement'],
    ['nom' => 'Chaussette', 'type' => 'Entrainement']
]; // Tableau des équipements prédéfinis

// Tableau des tailles disponibles (exemple)
$tailles = ['164','176','S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
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



// Traitement du formulaire soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idLicence = isset($_POST['idLicence']) ? htmlspecialchars($_POST['idLicence']) : "";
    $saison = isset($_POST['saison']) ? htmlspecialchars($_POST['saison']) : "";
    $allFieldsFilled = true;
    $errors = [];

    foreach ($_POST['nomEquipement'] as $index => $nomEquipement) {
        $tailleEquipement = isset($_POST['taille'][$index]) ? htmlspecialchars($_POST['taille'][$index]) : "";
        $distribue = isset($_POST['distribue_entrainement'][$index]) ? htmlspecialchars($_POST['distribue_entrainement'][$index]) : "";
        $dateDistribution = isset($_POST['date'][$index]) ? htmlspecialchars($_POST['date'][$index]) : null;

        if (empty($tailleEquipement)) {
            $allFieldsFilled = false;
            $errors[] = "Tous les champs sont obligatoires.";
            break;
        }
    }

    if ($allFieldsFilled) {
        try {
            $pdo->beginTransaction();
            foreach ($_POST['nomEquipement'] as $index => $nomEquipement) {
                $tailleEquipement = htmlspecialchars($_POST['taille'][$index]);
                $distribue = htmlspecialchars($_POST['distribue_entrainement'][$index]);
                $dateDistribution = isset($_POST['date'][$index]) ? htmlspecialchars($_POST['date'][$index]) : null;

                if (empty($distribue)){
                    $dateDistribution = null;
                    $distribue = 0;
                }
                // Préparation de la requête d'insertion
                $stmt = $pdo->prepare("INSERT INTO equipement(tailleEquipement, dateDistribution, nomEquipement, distribue, typeEquipement, idLicence, saison) VALUES (?, ?, ?, ?, ?, ?, ?)");
                // Exécution de la requête avec les valeurs correspondantes
                $stmt->execute([$tailleEquipement, $dateDistribution, $nomEquipement, $distribue, $equipements[$index]['type'], $idLicence, $saison]);
            }
            $pdo->commit();
            // Redirection après l'insertion réussie
            header("Location: pageProtegee.php?idLicence=" . urlencode($idLicence));
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Erreur lors de l'insertion de l'équipement : " . $e->getMessage();
        }
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
                <input type="text" id="typeEquipement" name="typeEquipement" value="Entrainement" readonly>
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
                                    // Utilisation du tableau de tailles approprié en fonction de l'équipement
                                    $taillesUtilisees = ($equipement['nom'] == 'Chaussette') ? $tailles2 : $tailles;
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
