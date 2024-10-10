<?php
include "connPDO.php";  // Inclure le fichier de connexion PDO

function convertDate($date) {
    // Vérifier si la date est au format "jour/mois/année" et la convertir en format MySQL "année-mois-jour"
    $dateParts = explode('/', $date);
    if (count($dateParts) == 3 && checkdate($dateParts[1], $dateParts[0], $dateParts[2])) {
        return $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
    } else {
        return false; // Retourner false si la date n'est pas valide
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["csvfile"]) && $_FILES["csvfile"]["error"] == 0) {
        $fileName = $_FILES["csvfile"]["tmp_name"];

        // Ouvrir le fichier CSV avec les points-virgules comme délimiteurs
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            // Préparer la requête d'insertion
            $sql = "INSERT INTO Licencié (numLicence, Nom, Prénom, dateNaissance, numTél, mail) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            // Lire les lignes du fichier
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                // Vérifier si le tableau $data a exactement 6 éléments
                if (count($data) != 6) {
                    echo "Erreur de format pour la ligne : " . implode(", ", $data) . "<br>";
                    continue; // Passer à la ligne suivante du fichier CSV
                }

                // Assumer que votre CSV a 6 colonnes : numLicence, Nom, Prénom, dateNaissance, numTél, mail
                $numLicence = $data[0];
                $nom = $data[1];
                $prenom = $data[2];
                $dateNaissance = convertDate($data[3]);

                // Vérifier que la date a été correctement convertie
                if ($dateNaissance === false) {
                    echo "Erreur de format de date pour la ligne : " . implode(", ", $data) . "<br>";
                    continue; // Passer à la ligne suivante du fichier CSV
                }

                $numTel = $data[4]; // Pas de modification du numéro de téléphone ici
                $mail = $data[5];

                // Exécuter l'insertion
                if (!$stmt->execute([$numLicence, $nom, $prenom, $dateNaissance, $numTel, $mail])) {
                    echo "Erreur d'insertion : " . implode(", ", $stmt->errorInfo()) . "<br>";
                }
            }
            fclose($handle);

            // Redirection vers pageprotegee.php après l'insertion
            header('Location: pageProtegee.php');
            exit(); // Assurer l'arrêt du script après la redirection
        } else {
            echo "Impossible d'ouvrir le fichier.";
        }
    } else {
        echo "Erreur de fichier.";
    }
}
?>
