<?php
// Inclure le fichier de connexion PDO
include "connPDO.php";

// Fonction pour vérifier et convertir la date au format MySQL
function convertDate($date) {
    // Vérifier si la date est au format "jour/mois/année" et la convertir en format MySQL "année-mois-jour"
    $dateParts = explode('/', $date);
    if (count($dateParts) == 3 && checkdate($dateParts[1], $dateParts[0], $dateParts[2])) {
        return $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
    } else {
        return false; // Retourner false si la date n'est pas valide
    }
}

// Fonction pour générer aléatoirement une fonction (Joueur, Gardien, Educateur)
function randomFonction() {
    $fonctions = ['Joueur', 'Gardien', 'Educateur'];
    $index = array_rand($fonctions);
    return $fonctions[$index];
}

// Vérification de la soumission du formulaire en POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si le fichier CSV a été correctement téléchargé sans erreur
    if (isset($_FILES["csvfile"]) && $_FILES["csvfile"]["error"] == 0) {
        $fileName = $_FILES["csvfile"]["tmp_name"];

        // Ouvrir le fichier CSV avec les points-virgules comme délimiteurs
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            // Préparer la requête d'insertion avec gestion des doublons
            $sql = "INSERT IGNORE INTO licencie (numLicence, fonction, nom, prenom, dateNaissance, numTel, mail) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            // Lire les lignes du fichier CSV
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                // Vérifier que la ligne a exactement 6 colonnes comme attendu
                if (count($data) != 6) {
                    echo "Erreur de format pour la ligne : " . implode(", ", $data) . "<br>";
                    continue; // Passer à la ligne suivante du fichier CSV
                }

                // Assumer que votre CSV a 6 colonnes correspondant aux valeurs dans l'INSERT INTO

                // Exemple d'insertion avec données du CSV
                $numLicence = $data[0];
                $fonction = randomFonction(); // Générer aléatoirement la fonction (Joueur, Gardien, Educateur)
                $nom = $data[1];
                $prenom = $data[2];
                $dateNaissance = convertDate($data[3]);

                // Vérifier que la date a été correctement convertie
                if ($dateNaissance === false) {
                    echo "Erreur de format de date pour la ligne : " . implode(", ", $data) . "<br>";
                    continue; // Passer à la ligne suivante du fichier CSV
                }

                $numTel = $data[4];
                $mail = $data[5];

                // Exécuter l'insertion
                try {
                    $stmt->execute([$numLicence, $fonction, $nom, $prenom, $dateNaissance, $numTel, $mail]);
                } catch (PDOException $e) {
                    echo "Erreur lors de l'insertion : " . $e->getMessage() . "<br>";
                    continue; // Passer à la ligne suivante du fichier CSV en cas d'erreur
                }
            }
            fclose($handle);

            // Redirection après l'insertion
            header('Location: pageProtegee.php');
            exit(); // Arrêter le script après la redirection
        } else {
            echo "Impossible d'ouvrir le fichier.";
        }
    } else {
        echo "Erreur de fichier.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ajout de l'équipement d'entraînement du licencié</title>
        <link rel="stylesheet" type="text/css" href="styleHeader.css">
        <link rel="stylesheet" type="text/css" href="styleCSV.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    </head>
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
    <body>
        <h2>Importation CSV vers Base de données</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="csvfile">Sélectionner un fichier CSV :</label>
            <input type="file" name="csvfile" id="csvfile" accept=".csv">
            <button type="submit">Importer</button>
        </form>
    </body>
</html>
