<?php
include "connPDO.php";

$idLicence = isset($_GET['idLicence']) ? htmlspecialchars($_GET['idLicence']) : '';
$nom = isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : '';
$prenom = isset($_GET['prenom']) ? htmlspecialchars($_GET['prenom']) : '';
$equipements = [];
$category = "";
$nomEquipe = "";
$tailles = [];
$taillesChaussettes = [];
$taillesChaussures = [];
$taillesSac = [];
$saisonSelectionnee = '';
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idLicence = isset($_POST['idLicencie']) ? htmlspecialchars($_POST['idLicencie']) : '';
    $saisonSelectionnee = isset($_POST['saison']) ? htmlspecialchars($_POST['saison']) : '';

    try {
        // Récupérer les informations sur la licence
        $stmt = $pdo->prepare("SELECT l.codeCat, l.nomEquipe, l.nom, l.prenom FROM licencie l WHERE l.idLicence = :idLicence");
        $stmt->bindParam(':idLicence', $idLicence, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $category = $result['codeCat'];
            $nomEquipe = $result['nomEquipe'];
            $nom = $result['nom'];
            $prenom = $result['prenom'];
        }

        // Déterminer les tailles disponibles en fonction de la catégorie et de l'équipe
        if (in_array($category, ['U7', 'U9', 'U11', 'U13'])) {
            $tailles = ['116', '128', '140', '152', '164', '176', 'S'];
            $taillesChaussettes = ['27/30', '31/34', '35/38', '39/42', '43/45'];
        } elseif (in_array($category, ['U14', 'U15', 'U16', 'U17', 'U18', 'U19', 'Vétérans', 'Super vétérans'])) {
            $tailles = ['164', '176', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        } elseif ($category === 'Seniors') {
            if ($nomEquipe === 'Seniors-1' || $nomEquipe === 'Seniors-2') {
                $tailles = ['164', '176', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
                $taillesChaussettes = ['39/42', '43/45', '46/48', '49/52'];
                $taillesChaussures = ['39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50'];
                $taillesSac = ['Unique'];
            }
        }

        // Requête pour récupérer les équipements de type "Sorti/"
        $stmt = $pdo->prepare("SELECT * FROM equipement WHERE idLicence = :idLicence AND saison = :saison");
        $stmt->bindParam(':idLicence', $idLicence, PDO::PARAM_INT);
        $stmt->bindParam(':saison', $saisonSelectionnee, PDO::PARAM_INT);
        $stmt->execute();
        $equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier équipement du licencié</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
    <link rel="stylesheet" type="text/css" href="styleHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        .radio-container {
            display: flex;
            gap: 10px;
        }
        .hidden {
            display: none;
        }
    </style>
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
        <form action="modifierEquipement1.php" method="POST">
            <div class="form">
                <label for="idLicencie">ID du licencié :</label>
                <input type="text" id="idLicencie" name="idLicencie" value="<?php echo htmlspecialchars($idLicence); ?>" readonly>
            </div>
            <div class="form">
                <label for="nom">Nom du licencié :</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" readonly>
            </div>
            <div class="form">
                <label for="prenom">Prénom du licencié :</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" readonly>
            </div>
            <br>
            <div class="form">
                <label for="saison">Sélectionnez la saison :</label>
                <select name="saison" id="saison">
                    <option value="">Choisissez une saison</option>
                    <?php foreach ($saisons as $option): ?>
                        <option value="<?php echo $option; ?>" <?php echo ($option === $saisonSelectionnee) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>
            <div class="terminer">
                <button type="submit">Afficher</button>
            </div>
        </form>

        <?php if (!empty($equipements)): ?>
            <form action="modifierEquipementAction.php" method="POST">
                <input type="hidden" name="idLicence" value="<?php echo htmlspecialchars($idLicence); ?>">
                <input type="hidden" name="saison" value="<?php echo htmlspecialchars($saisonSelectionnee); ?>">
                <div id="page1">
                    <h2>Équipements de Sortie</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Nom d'équipement</th>
                                <th>Type d'équipement</th>
                                <th>Taille</th>
                                <th>Distribué</th>
                                <th>Coupé</th>
                                <th>Date de distribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equipements as $equipement): ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="idEquipement[]" value="<?php echo htmlspecialchars($equipement['idEquipement']); ?>">
                                        <input type="text" name="nomEquipement[]" value="<?php echo htmlspecialchars($equipement['nomEquipement']); ?>" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="typeEquipement[]" value="<?php echo htmlspecialchars($equipement['typeEquipement']); ?>" readonly>
                                    </td>
                                    <td>
                                    <select id="taille" name="taille[]" required>
                                        <?php
                                        $tailleEquipement = htmlspecialchars($equipement['tailleEquipement']);
                                        // Déterminer les options de taille en fonction du type d'équipement
                                        $tailleOptions = ($equipement['nomEquipement'] === 'Chaussette match' || $equipement['nomEquipement'] === '3 Chaussette Basse' || $equipement['nomEquipement'] === '2 Chaussette Match Blanche' || $equipement['nomEquipement'] === '2 Chaussette Match Noire' || $equipement['nomEquipement'] === 'Chaussette Match Blanche' || $equipement['nomEquipement'] === '2 Chaussette Match Noire' || $equipement['nomEquipement'] === 'Chaussette Basse') ? $taillesChaussettes : (($equipement['nomEquipement'] === 'Claquette' || $equipement['nomEquipement'] === 'Basket') ? $taillesChaussures : ($equipement['nomEquipement'] === 'Sac' ? $taillesSac : $tailles));
                                        foreach ($tailleOptions as $taille) {
                                            $selected = ($taille === $tailleEquipement) ? 'selected' : '';
                                            echo "<option value=\"$taille\" $selected>$taille</option>";
                                        }
                                        ?>
                                    </select>
                                    <td>
                                        <input type="hidden" name="distribue[<?php echo htmlspecialchars($equipement['idEquipement']); ?>]" value="0">
                                        <input type="checkbox" name="distribue[<?php echo htmlspecialchars($equipement['idEquipement']); ?>]" value="1" <?php echo ($equipement['distribue'] == 1) ? 'checked' : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="hidden" name="coupe[<?php echo htmlspecialchars($equipement['idEquipement']); ?>]" value="0">
                                        <input type="checkbox" name="coupe[<?php echo htmlspecialchars($equipement['idEquipement']); ?>]" value="1" <?php echo ($equipement['coupe'] == 1) ? 'checked' : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="date" name="dateDistribution[]" value="<?php echo htmlspecialchars($equipement['dateDistribution']); ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="radio-container">
                        <button type="submit">Valider</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
