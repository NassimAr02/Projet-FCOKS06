<?php
    session_start(); // Start session at the very beginning

    include "connPDO.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['user'];
        $password = $_POST['password'];

        if (!empty($user) && !empty($password)) {
            try {
                // Préparation de la requête pour prévenir des injections SQL
                $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE nomUtilisateur = :user");
                $stmt->bindParam(':user', $user);
                $stmt->execute();

                $userData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($userData) {
                    if (password_verify($password, $userData['motDePasse'])) {
                        // Début de session et paramétrage des variables de la session
                        session_regenerate_id(true); // Régénérer l'ID de session pour éviter la fixation de session
                        $_SESSION['user'] = $userData['nomUtilisateur'];

                        header("Location: pageProtegee.php");
                        exit();
                    } else {
                        $error = "Nom d'utilisateur ou mot de passe invalide";
                    }
                } else {
                    $error = "Nom d'utilisateur ou mot de passe invalide";
                }
            } catch (PDOException $e) {
                $error = "Erreur: " . $e->getMessage();
            }
        } else {
            $error = "Tous les champs doivent être remplis";
        }
    }

    if (isset($error)) {
        echo $error;
    }
?>
