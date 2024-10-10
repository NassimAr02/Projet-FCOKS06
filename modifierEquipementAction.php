<?php
include "connPDO.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idLicence = htmlspecialchars($_POST['idLicence']);

    $pdo->beginTransaction();

    try {
        $equipements = $_POST['idEquipement'];
        $tailles = $_POST['taille'];
        $distribues = $_POST['distribue'];
        $dates = $_POST['dateDistribution'];
        $chaussetteTypes = $_POST['chaussette_type']; // Nouvelle variable pour les types de chaussettes (coupé ou non coupé)

        $sql = $pdo->prepare("UPDATE equipement SET tailleEquipement = :taille, distribue = :distribue, coupe = :coupe, dateDistribution = :dateDistribution WHERE idEquipement = :idEquipement");

        foreach ($equipements as $index => $equipementId) {
            $taille = $tailles[$index];
            $distribue = $distribues[$index];
            $dateDistribution = !empty($dates[$index]) ? $dates[$index] : null;

            // Vérification et conversion de la valeur de coupe
            if (isset($chaussetteTypes[$index]) && ($chaussetteTypes[$index] === '0' || $chaussetteTypes[$index] === '1')) {
                $coupe = intval($chaussetteTypes[$index]); // Convertit en entier
            } else {
                $coupe = 0; // Valeur par défaut si non définie ou incorrecte
            }

            $sql->bindParam(':taille', $taille);
            $sql->bindParam(':distribue', $distribue);
            $sql->bindParam(':coupe', $coupe); // Liaison du paramètre coupe
            $sql->bindParam(':dateDistribution', $dateDistribution);
            $sql->bindParam(':idEquipement', $equipementId);
            $sql->execute();
        }

        $pdo->commit();

        header("Location: modifierEquipement.php?idLicence=$idLicence&success=true");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed: " . $e->getMessage();
    }
}
?>
