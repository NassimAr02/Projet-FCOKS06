<?php
// Démarre la session si elle n'est pas déjà démarrée
session_start();

// Détruit toutes les données de la session
$_SESSION = array(); // Vide le tableau de session

// Détruit la session
session_destroy();

// Assurez-vous que toutes les sessions sont bien fermées
// if (ini_get("session.use_cookies")) {
//     $params = session_get_cookie_params();
//     setcookie(session_name(), '', time() - 42000,
//         $params["path"], $params["domain"],
//         $params["secure"], $params["httponly"]
//     );
// }

// Redirige l'utilisateur vers une page de connexion ou une autre page appropriée
header("Location: index.php");
exit();
?>
