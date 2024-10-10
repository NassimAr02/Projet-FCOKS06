<?php
include "connPDO.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous de récupérer toutes les données nécessaires depuis $_POST
    $idLicence = isset($_POST['idLicence']) ? $_POST['idLicence'] : null;
    $fonction = isset($_POST['fonction']) ? $_POST['fonction'] : null;
    $nom = isset($_POST['nom']) ? $_POST['nom'] : null;
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : null;
    $dateNaissance = isset($_POST['dateNaissance']) ? $_POST['dateNaissance'] : null;
    $codeCat = isset($_POST['codeCat']) ? $_POST['codeCat'] : null;
    $nomEquipe = isset($_POST['nomEquipe']) ? $_POST['nomEquipe'] : null;

    // Vérifiez si toutes les données requises sont présentes
    if (!$idLicence || !$fonction || !$nom || !$prenom || !$dateNaissance || !$codeCat || !$nomEquipe) {
        echo "Toutes les données requises ne sont pas fournies.";
        exit();
    }

    // Préparez la requête SQL pour mettre à jour les données du licencié
    $sql = "UPDATE licencie SET 
        fonction = :fonction, 
        nom = :nom, 
        prenom = :prenom, 
        dateNaissance = :dateNaissance, 
        codeCat = :codeCat, 
        nomEquipe = :nomEquipe
    WHERE idLicence = :idLicence";

    // Préparez et exécutez la requête SQL avec PDO
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        ':fonction' => $fonction,
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':dateNaissance' => $dateNaissance,
        ':codeCat' => $codeCat,
        ':nomEquipe' => $nomEquipe,
        ':idLicence' => $idLicence
    ]);

    if (!$success) {
        echo "Erreur lors de la mise à jour : ";
        print_r($stmt->errorInfo());
        exit();  // Exit after displaying the error
    }

    // Assurez-vous que la mise à jour s'est effectuée avec succès avant de retourner la réponse
    if ($stmt->rowCount() > 0) {
        echo 'Success';
    } else {
        echo 'Erreur : Aucune donnée mise à jour.';
    }
} else {
    // Redirigez si la méthode de requête n'est pas POST
    header("Location: rechercheJoueur2.php");
    exit();
}
?>
