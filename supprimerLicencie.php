<?php
include "connPDO.php"; // Ensure this file initializes the $pdo connection correctly

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve idLicence from POST data
    $idLicence = isset($_POST['idLicence']) ? $_POST['idLicence'] : null;

    // Check if idLicence is provided
    if (empty($idLicence)) {
        echo "Erreur : idLicence non fourni.";
        exit;
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Delete from `ajoutePaiement` first as it references `cotisation`
        $sql = $pdo->prepare("DELETE FROM ajoutePaiement WHERE numCotisation IN (SELECT numCotisation FROM cotisation WHERE idLicence = :idLicence)");
        $sql->bindParam(':idLicence', $idLicence);
        $sql->execute();

        // Delete from `cotisation`
        $sql = $pdo->prepare("DELETE FROM cotisation WHERE idLicence = :idLicence");
        $sql->bindParam(':idLicence', $idLicence);
        $sql->execute();

        // Delete from `equipement`
        $sql = $pdo->prepare("DELETE FROM equipement WHERE idLicence = :idLicence");
        $sql->bindParam(':idLicence', $idLicence);
        $sql->execute();

        // Delete from `licencie`
        $sql = $pdo->prepare("DELETE FROM licencie WHERE idLicence = :idLicence");
        $sql->bindParam(':idLicence', $idLicence);
        $sql->execute();

        // Commit transaction
        $pdo->commit();

        // Send success message to JavaScript
        echo "Success";
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();

        // Send error message to JavaScript
        echo "Erreur : " . $e->getMessage();
    }
} else {
    // Handle cases where POST method is not used
    echo "Erreur : Méthode HTTP incorrecte.";
}
?>
