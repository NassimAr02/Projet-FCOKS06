<?php
include "connPDO.php";

// Activer les messages d'erreur
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$codeCat = $nomEquipe = $nomJoueur = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $codeCat = isset($_POST['codeCat']) ? htmlspecialchars($_POST['codeCat']) : "";
    $nomEquipe = isset($_POST['nomEquipe']) ? htmlspecialchars($_POST['nomEquipe']) : "";
    $nomJoueur = isset($_POST['nomJoueur']) ? htmlspecialchars($_POST['nomJoueur']) : "";

    $sql = "SELECT licencie.idLicence, licencie.numLicence, licencie.nom, licencie.prenom, equipe.nomEquipe, categorie.codeCat 
            FROM licencie 
            LEFT JOIN equipe ON licencie.nomEquipe = equipe.nomEquipe 
            LEFT JOIN categorie ON licencie.codeCat = categorie.codeCat 
            WHERE 1=1";

    if (!empty($nomJoueur)) {
        $sql .= " AND (licencie.nom LIKE :nomJoueur OR licencie.prenom LIKE :nomJoueur)";
    }
    if (!empty($nomEquipe)) {
        $sql .= " AND equipe.nomEquipe = :nomEquipe";
    }
    if (!empty($codeCat)) {
        $sql .= " AND categorie.codeCat = :codeCat";
    }

    $stmt = $pdo->prepare($sql);

    if (!empty($nomJoueur)) {
        $nomJoueur = "%$nomJoueur%";
        $stmt->bindParam(':nomJoueur', $nomJoueur, PDO::PARAM_STR);
    }
    if (!empty($nomEquipe)) {
        $stmt->bindParam(':nomEquipe', $nomEquipe, PDO::PARAM_STR);
    }
    if (!empty($codeCat)) {
        $stmt->bindParam(':codeCat', $codeCat, PDO::PARAM_STR);
    }

    try {
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            $queryString = [];
            if (!empty($nomJoueur)) {
                $queryString[] = "nomJoueur=" . urlencode($nomJoueur);
            }
            if (!empty($nomEquipe)) {
                $queryString[] = "nomEquipe=" . urlencode($nomEquipe);
            }
            if (!empty($codeCat)) {
                $queryString[] = "codeCat=" . urlencode($codeCat);
            }
            $queryString = implode('&', $queryString);
            header("Location: modifLicencie.php?$queryString");
            exit();
        } else {
            $message = "Aucun joueur trouvé pour les critères spécifiés.";
        }
    } catch (PDOException $e) {
        $message = "Erreur de requête : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recherche du licencié</title>
        <link rel="stylesheet" type="text/css" href="styleRecherche2.css">
        <link rel="stylesheet" type="text/css" href="styleHeader.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const codeCatSelect = document.getElementById("codeCat");
                const nomEquipeSelect = document.getElementById("nomEquipe");

                codeCatSelect.addEventListener("change", function() {
                    const selectedCategory = this.value;

                    for (let option of nomEquipeSelect.options) {
                        if (option.value === "" || option.dataset.category === selectedCategory) {
                            option.style.display = "block";
                        } else {
                            option.style.display = "none";
                        }
                    }

                    nomEquipeSelect.value = ""; // Reset the team select
                });
            });
        </script>
    </head>
    <body>
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
        <div class="formulaire">
            <div class="formulaire2">
                <form method="post" action="">
                    <label for="nomJoueur">Nom ou Prénom du joueur :</label>
                    <input type="text" id="nomJoueur" name="nomJoueur" value="<?php echo htmlspecialchars($nomJoueur); ?>" placeholder="Nom ou Prénom">

                    <label for="codeCat">Catégorie :</label>
                    <select id="codeCat" name="codeCat">
                        <option value="">Choisissez une catégorie</option>
                        <?php
                        // On recupère les différentes catégories
                        $sql = "SELECT DISTINCT codeCat FROM categorie";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($codeCat == $row['codeCat']) ? "selected" : "";
                            echo '<option value="' . $row['codeCat'] . '" ' . $selected . '>' . $row['codeCat'] . '</option>';
                        }
                        ?>
                    </select>

                    <label for="nomEquipe">Equipe :</label>
                    <select id="nomEquipe" name="nomEquipe">
                        <option value="">Choisissez une équipe</option>
                        <?php
                        // On recupère les différentes équipes et leurs catégories correspondantes
                        $sql = "SELECT nomEquipe, codeCat FROM equipe";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($nomEquipe == $row['nomEquipe']) ? "selected" : "";
                            echo '<option value="' . $row['nomEquipe'] . '" data-category="' . $row['codeCat'] . '" ' . $selected . '>' . $row['nomEquipe'] . '</option>';
                        }
                        ?>
                    </select>

                    <button type="submit" class="custom-btn"><i class="fas fa-search"></i></button>
                </form>
                <?php
                if (isset($message)) {
                    echo '<p>' . $message . '</p>';
                }
                if (!empty($results)) {
                    echo '<table>';
                    echo '<tr><th>Identifiant de Licence</th><th>Nom</th><th>Prénom</th><th>Equipe</th><th>Catégorie</th></tr>';
                    foreach ($results as $row) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['numLicence']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['numLicence']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['nom']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['prenom']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['nomEquipe']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['codeCat']) . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
        </div>
    </body>
</html>
