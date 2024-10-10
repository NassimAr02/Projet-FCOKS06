<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un licencié</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <header class="fixed-header">
        <h1>FCOSK 06</h1>
        <a href="pageProtegee.php"><button class="custom-btn home-btn"><i class="fas fa-home"></i></button></a>
        <a href="ajout.php"><button class="custom-btn add-btn"><i class="fa-solid fa-user-plus"></i></button></a>
        <a href="imprime.php"><button class="custom-btn print-btn"><i class="fa-solid fa-print"></i></button></a>
        <a href="cotisations.php"><button class="custom-btn money-btn"><i class="fa-solid fa-money-check-alt"></i></button></a>
        <a href="logout.php"><button class="custom-btn logout-btn"><i class="fa-solid fa-sign-out-alt"></i></button></a>
    </header>
    <main>
    <?php
include "connPDO.php";
session_start();

$numLicence = $Nom = $Prénom = $dateDeNaissance = $nomEquipe = $adresse = $CP = $mail = $numTél = $Ville = $codeCat = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['numLicence'])) {
    $numLicence = $_POST['numLicence'] ?? "";
    $Nom = $_POST['Nom'] ?? "";
    $Prénom = $_POST['Prénom'] ?? "";
    $dateDeNaissance = $_POST['dateDeNaissance'] ?? "";
    $codeCat = $_POST['codeCat'] ?? "";
    $nomEquipe = $_POST['nomEquipe'] ?? "";
    $adresse = $_POST['Adresse'] ?? "";
    $CP = $_POST['CP'] ?? "";
    $Ville = $_POST['Ville'] ?? "";
    $numTél = $_POST['numTel'] ?? "";
    $mail = $_POST['mail'] ?? "";

    // Convert date format from DD/MM/YYYY to YYYY-MM-DD
    $dateDeNaissance = implode('-', array_reverse(explode('/', $dateDeNaissance)));

    // Vérifie que tous les champs nécessaires sont remplis
    if (!empty($numLicence) && !empty($Nom) && !empty($Prénom) && !empty($dateDeNaissance) && !empty($adresse) && !empty($CP) && !empty($Ville) && !empty($numTél) && !empty($codeCat)) {
        try {
            // Check if numLicence already exists
            $checkLicence = $pdo->prepare("SELECT numLicence FROM Licencié WHERE numLicence = :numLicence");
            $checkLicence->bindParam(':numLicence', $numLicence, PDO::PARAM_INT);
            $checkLicence->execute();

            if ($checkLicence->rowCount() > 0) {
                echo "Numéro de licence déjà existant. Veuillez en choisir un autre.";
            } else {
                // Check if nomEquipe exists if not empty
                if (!empty($nomEquipe)) {
                    $checkEquipe = $pdo->prepare("SELECT nomEquipe FROM equipe WHERE nomEquipe = :nomEquipe AND codeCat = :codeCat");
                    $checkEquipe->bindParam(':nomEquipe', $nomEquipe, PDO::PARAM_STR);
                    $checkEquipe->bindParam(':codeCat', $codeCat, PDO::PARAM_STR);
                    $checkEquipe->execute();

                    if ($checkEquipe->rowCount() == 0) {
                        echo "L'équipe sélectionnée n'existe pas.";
                    } else {
                        // Insert data into Licencié table
                        $stmt = $pdo->prepare("INSERT INTO Licencié (numLicence, Nom, Prénom, dateNaissance, nomEquipe, adresse, CP, Ville, numTél, mail, codeCat) VALUES (:numLicence, :Nom, :Prénom, :dateNaissance, :nomEquipe, :adresse, :CP, :Ville, :numTel, :mail, :codeCat)");
                        $stmt->bindParam(':numLicence', $numLicence, PDO::PARAM_INT);
                        $stmt->bindParam(':Nom', $Nom, PDO::PARAM_STR);
                        $stmt->bindParam(':Prénom', $Prénom, PDO::PARAM_STR);
                        $stmt->bindParam(':dateNaissance', $dateDeNaissance, PDO::PARAM_STR);
                        $stmt->bindParam(':nomEquipe', $nomEquipe, PDO::PARAM_STR);
                        $stmt->bindParam(':adresse', $adresse, PDO::PARAM_STR);
                        $stmt->bindParam(':CP', $CP, PDO::PARAM_STR);
                        $stmt->bindParam(':Ville', $Ville, PDO::PARAM_STR);
                        $stmt->bindParam(':numTel', $numTél, PDO::PARAM_STR);
                        $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
                        $stmt->bindParam(':codeCat', $codeCat, PDO::PARAM_STR);

                        try {
                            $stmt->execute();

                            // Affichage du nombre de lignes affectées
                            echo "Nombre de lignes insérées : " . $stmt->rowCount();

                            // Redirection si l'insertion réussit
                            if ($stmt->rowCount() > 0) {
                                header("Location: ajoutE.php?numLicence=" . urlencode($numLicence));
                                exit();
                            } else {
                                echo "Erreur lors de l'ajout de l'enregistrement.";
                            }
                        } catch (PDOException $e) {
                            // Affichage des erreurs PDO
                            echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
        }
    }
}
?>


    <div class="formulaire">
        <form action="" method="POST">
            <label>Numéro de licence :</label>
            <input type="number" id="numLicence" name="numLicence" value="<?php echo htmlspecialchars($numLicence); ?>" required>
            <label>Nom :</label>
            <input type="text" id="Nom" name="Nom" value="<?php echo htmlspecialchars($Nom); ?>" required>
            <label>Prénom :</label>
            <input type="text" id="Prénom" name="Prénom" value="<?php echo htmlspecialchars($Prénom); ?>" required>
            <label>Date de naissance :</label>
            <input type="date" id="dateDeNaissance" name="dateDeNaissance" value="<?php echo htmlspecialchars($dateDeNaissance); ?>" required>
            <label for="codeCat">Catégorie :</label>
            <select id="codeCat" name="codeCat" onchange="this.form.submit()">
                <option value="">Choisissez une catégorie</option>
                <?php
                    $sql = "SELECT DISTINCT codeCat FROM equipe";
                    $result = $pdo->query($sql);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($codeCat == $row['codeCat']) ? "selected" : "";
                        echo '<option value="' . $row['codeCat'] . '" ' . $selected . '>' . $row['codeCat'] . '</option>';
                    }
                ?>
            </select>
            <?php
                if (!empty($codeCat)) {
                    $sqlNom = "SELECT nomEquipe FROM equipe WHERE codeCat = :codeCat";
                    $stmtNom = $pdo->prepare($sqlNom);
                    $stmtNom->bindParam(":codeCat", $codeCat, PDO::PARAM_STR);
                    $stmtNom->execute();
                    $resultNom = $stmtNom->fetchAll(PDO::FETCH_ASSOC);
                    if (count($resultNom) > 0) {
                        echo "<label for='nomEquipe'>Equipe :</label>";
                        echo "<select id='nomEquipe' name='nomEquipe'>";
                        echo "<option value=''>Choisissez une équipe</option>";
                        foreach ($resultNom as $row1) {
                            $selected = ($nomEquipe == $row1['nomEquipe']) ? "selected" : "";
                            echo '<option value="' . $row1['nomEquipe'] . '" ' . $selected . '>' . $row1['nomEquipe'] . '</option>';
                        }
                        echo "</select>";
                    } else {
                        echo '<option value="">Aucune équipe trouvée pour cette catégorie</option>';
                    }
                }
            ?>
            <label>Adresse :</label>
            <input type="text" id="Adresse" name="Adresse" value="<?php echo htmlspecialchars($adresse); ?>" required>
            <label>Code postal :</label>
            <input type="text" id="CP" name="CP" value="<?php echo htmlspecialchars($CP); ?>" required>
            <label>Ville :</label>
            <input type="text" id="Ville" name="Ville" value="<?php echo htmlspecialchars($Ville); ?>" required>
            <label>Numéro de téléphone :</label>
            <input type="text" id="numTel" name="numTel" value="<?php echo htmlspecialchars($numTél); ?>" required>
            <label>Mail de contact :</label>
            <input type="text" id="mail" name="mail" value="<?php echo htmlspecialchars($mail); ?>"required>
            <div class="terminer">
                <button class="custom-btn" type="submit"><i class="fa-solid fa-plus"></i></button>
                <button class="custom-btn" type="reset"><i class="fa-solid fa-times"></i></button>
            </div>
        </form>
    </div>
</main>
</body>
</html>

