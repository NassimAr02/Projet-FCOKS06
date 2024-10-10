<?php
include 'connPDO.php';

// Récupérer les types d'équipements
try {
    $stmtType = $pdo->prepare("SELECT DISTINCT typeEquipement FROM Equipement");
    $stmtType->execute();
    $types = $stmtType->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}

// Récupérer les noms d'équipements
$selectedType = isset($_POST['typeEquipement']) ? $_POST['typeEquipement'] : '';
if ($selectedType) {
    try {
        $stmtEquip = $pdo->prepare("SELECT nomEquipement, codeEquipement FROM Equipement WHERE typeEquipement = :typeEquipement");
        $stmtEquip->bindParam(':typeEquipement', $selectedType, PDO::PARAM_STR);
        $stmtEquip->execute();
        $noms = $stmtEquip->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . htmlspecialchars($e->getMessage());
        exit;
    }
} else {
    $noms = [];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout de l'équipement du licencié</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
<header class="fixed-header">
    <h1>FCOSK 06</h1>
    <a href="pageProtegee.php"><button class="custom-btn" id="ajt"><i class="fas fa-home"></i></button></a>
    <a href="ajout.php"><button class="custom-btn" id="ajt"><i class="fa-solid fa-user-plus"></i></button></a>
    <a href="imprime.php"><button class="custom-btn" id="ajt"><i class="fa-solid fa-print"></i></button></a>
    <a href="cotisations.php"><button class="custom-btn" id="ajt"><i class="fa-solid fa-money-check-alt"></i></button></a>
    <a href="logout.php"><button class="custom-btn" id="ajt"><i class="fa-solid fa-sign-out-alt"></i></button></a>
</header>
<main>
    <div class="listeDeroulante">
        <form action="" method="POST">
        <?php
include 'connPDO.php';
if(isset($_GET['numLicence'])) {
    $numLicence = $_GET['numLicence'];
} else {
    // Affichage d'un message d'erreur si la variable n'est pas présente
    echo "Numéro de licence non spécifié.";
    exit; // Arrête l'exécution du script
}
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numLicence'])) {
    try {
        // Récupérer les données du formulaire
        
        $tailleEquipement = isset($_POST['taille']) ? htmlspecialchars($_POST['taille']) : "";
        $distribue = isset($_POST['distribue']) ? htmlspecialchars($_POST['distribue']) : "";
        $dateDistribution = isset($_POST['date']) ? htmlspecialchars($_POST['date']) : "";
        $nomEquipement = isset($_POST['nomEquipement']) ? htmlspecialchars($_POST['nomEquipement']) : '';

        // Récupérer le codeEquipement correspondant au nomEquipement sélectionné
        $stmtCode = $pdo->prepare("SELECT codeEquipement FROM Equipement WHERE nomEquipement = :nomEquipement");
        $stmtCode->bindParam(':nomEquipement', $nomEquipement, PDO::PARAM_STR);
        $stmtCode->execute();
        $codeEquipement = $stmtCode->fetchColumn();

        // Insérer les données dans la table distribEquipement
        $stmt = $pdo->prepare("INSERT INTO distribEquipement (codeEquipement, tailleEquipement, distribué, dateDistribution, numLicence) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$codeEquipement, $tailleEquipement, $distribue, $dateDistribution, $numLicence]);

        // Redirection si l'insertion réussit
        if ($stmt->rowCount() > 0) {
            header("Location: pageProtegee.php?numLicence=" . urlencode($numLicence));
            exit();
        } else {
            echo "Erreur lors de l'ajout de l'enregistrement.";
        }
    } catch (PDOException $e) {
        // Affichage des erreurs PDO
        echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
    }
}
?>

            <label for="numLicence">Numéro de licence :</label>
            <input type="text" id="numLicence" name="numLicence" value="<?php echo htmlspecialchars($numLicence); ?>" readonly>
            <label for="typeEquipement">Type d'équipement :</label>
            <select id="typeEquipement" name="typeEquipement" required onchange="this.form.submit()">
                <option value="">Sélectionnez un type</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?php echo htmlspecialchars($type['typeEquipement']); ?>" <?php echo ($type['typeEquipement'] === $selectedType) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($type['typeEquipement']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="nomEquipement">Nom d'équipement :</label>
            <select id="nomEquipement" name="nomEquipement" required>
                <option value="">Sélectionnez un nom</option>
                <?php if (!empty($noms)): ?>
                    <?php foreach ($noms as $nom): ?>
                        <option value="<?php echo htmlspecialchars($nom['nomEquipement']); ?>" data-code="<?php echo htmlspecialchars($nom['codeEquipement']); ?>">
                            <?php echo htmlspecialchars($nom['nomEquipement']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <!-- Champ caché pour stocker le code d'équipement -->
            <input type="hidden" id="codeEquipement" name="codeEquipement" value="">
            <label for="taille">Taille :</label>
            <input type="text" id="taille" name="taille">
            <label for="radio-container">Distribué</label>
            <div class="radio-container">
                <label><input type="radio" id="distribue_oui" name="distribue" value="1" required>Oui</label>
                <label><input type="radio" id="distribue_non" name="distribue" value="0" required>Non</label>
            </div>
            
            <label for="date">Date de distribution :</label>
            <input type="date" id="date" name="date">
            <div class="terminer">
                <button class="custom-btn" id="ajt" type="submit"><i class="fa-solid fa-plus"></i></button>
                <button class="custom-btn" id="ajt" type="reset"><i class="fa-solid fa-times"></i></button>
                        </div>
                    </form>
            </div>
        </main>
    </body>
</html>
