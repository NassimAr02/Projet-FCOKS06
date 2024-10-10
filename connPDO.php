<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "root";
$dbname = "listeLicencie";
$port = "3306"; // Spécifiez le port MySQL que vous avez configuré dans MAMP

try {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur lors de la connexion à la base de données : " . $e->getMessage());
}
?>
