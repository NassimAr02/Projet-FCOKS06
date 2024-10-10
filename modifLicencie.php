<?php
include "connPDO.php";
session_start();

$nomEquipe = $nomJoueur = $codeCat = "";

// Récupérer les critères de recherche depuis l'URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $nomEquipe = isset($_GET['nomEquipe']) ? htmlspecialchars($_GET['nomEquipe'], ENT_QUOTES) : "";
    $nomJoueur = isset($_GET['nomJoueur']) ? htmlspecialchars($_GET['nomJoueur'], ENT_QUOTES) : "";
    $codeCat = isset($_GET['codeCat']) ? htmlspecialchars($_GET['codeCat'], ENT_QUOTES) : "";
}

// Récupérer les catégories
$sqlCat = $pdo->prepare("SELECT * FROM categorie");
$sqlCat->execute();
$categories = $sqlCat->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les équipes
$sqlEquipe = $pdo->prepare("SELECT * FROM equipe");
$sqlEquipe->execute();
$equipes = $sqlEquipe->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les licenciés en fonction des critères de recherche
$sql = "SELECT * FROM licencie WHERE 1";

if (!empty($nomEquipe)) {
    $sql .= " AND nomEquipe = :nomEquipe";
}

if (!empty($nomJoueur)) {
    $sql .= " AND nom LIKE :nomJoueur";
}

if (!empty($codeCat)) {
    $sql .= " AND codeCat = :codeCat";
}

$stmt = $pdo->prepare($sql);

if (!empty($nomEquipe)) {
    $stmt->bindParam(':nomEquipe', $nomEquipe, PDO::PARAM_STR);
}

if (!empty($nomJoueur)) {
    $nomJoueurLike = "%{$nomJoueur}%";
    $stmt->bindParam(':nomJoueur', $nomJoueurLike, PDO::PARAM_STR);
}

if (!empty($codeCat)) {
    $stmt->bindParam(':codeCat', $codeCat, PDO::PARAM_STR);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les fonctions
// Récupérer les fonctions ENUM de la table licencie
$sqlFonctions = "SHOW COLUMNS FROM licencie LIKE 'fonction'";
$stmt = $pdo->prepare($sqlFonctions);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Extraire les valeurs ENUM de la colonne 'fonction'
$regex = "/'(.*?)'/";
preg_match_all($regex, $row['Type'], $matches);
$fonctions = $matches[1];


// Fonction pour activer l'édition des champs dans une ligne de tableau
function enableEdit($row) {
    global $categories, $equipes;

    // PHP pour générer les champs d'entrée éditables
    $inputs = $row->getElementsByTagName('input');
    foreach ($inputs as $input) {
        $input->removeAttribute('readonly');
    }

    $selects = $row->getElementsByTagName('select');
    foreach ($selects as $select) {
        $select->removeAttribute('disabled');
    }

    // Cacher le bouton d'édition et afficher le bouton de sauvegarde
    $row->querySelector('.edit-btn')->setAttribute('style', 'display: none;');
    $row->querySelector('.save-btn')->setAttribute('style', 'display: inline;');

    // Générer les options pour les sélecteurs (catégorie et équipe)
    foreach ($categories as $categorie) {
        $selected = ($categorie['codeCat'] == $row->querySelector('select[name="codeCat"]')->value) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($categorie['codeCat'], ENT_QUOTES) . "' $selected>" . htmlspecialchars($categorie['codeCat'], ENT_QUOTES) . "</option>";
    }

    foreach ($equipes as $equipe) {
        $selected = ($equipe['nomEquipe'] == $row->querySelector('select[name="nomEquipe"]')->value) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($equipe['nomEquipe'], ENT_QUOTES) . "' $selected>" . htmlspecialchars($equipe['nomEquipe'], ENT_QUOTES) . "</option>";
    }
}

// Fonction pour sauvegarder les modifications effectuées
function saveChanges($row) {
    global $pdo;

    // PHP pour sauvegarder les modifications
    $formData = $_POST; // Assurez-vous que les données sont envoyées via POST
    $inputs = $row->getElementsByTagName('input');
    foreach ($inputs as $input) {
        $formData[$input->getAttribute('name')] = $input->getAttribute('value');
    }

    // Exemple d'utilisation de fetch en PHP (alternative à XMLHttpRequest en JavaScript)
    $sqlUpdate = "UPDATE licencie SET fonction = :fonction, nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, codeCat = :codeCat, nomEquipe = :nomEquipe WHERE idLicence = :idLicence";
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->execute(array(
        ':fonction' => $formData['fonction'],
        ':nom' => $formData['nom'],
        ':prenom' => $formData['prenom'],
        ':dateNaissance' => $formData['dateNaissance'],
        ':codeCat' => $formData['codeCat'],
        ':nomEquipe' => $formData['nomEquipe'],
        ':idLicence' => $formData['idLicence']
    ));

    echo 'Success'; // Réponse à la requête AJAX
}

// Fonction pour supprimer un licencié en fonction de son numéro de licence
function deleteLicencie($idLicence) {
    global $pdo;

    // PHP pour supprimer un licencié en fonction de son ID
    $sqlDelete = "DELETE FROM licencie WHERE idLicence = :idLicence";
    $stmt = $pdo->prepare($sqlDelete);
    $stmt->execute(array(':idLicence' => $idLicence));

    echo 'Success'; // Réponse à la requête AJAX
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter les informations des licenciés</title>
    <link rel="stylesheet" type="text/css" href="styleModif.css">
    <link rel="stylesheet" type="text/css" href="styleHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .actions button {
            margin-right: 10px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: orange;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }
        .actions button:hover {
            background-color: #0000e6;
        }
        .actions button:hover i {
            animation: bounce 0.5s ease-in-out;
        }
    </style>
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
<div class="main">
    <?php
    if ($results) {
        echo "<div class='table'>
    <table>
        <tr>
            <th></th> <!-- Ajout d'une colonne pour la case à cocher -->
            <th>Identifiant de licence</th>
            <th>Fonction</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date de naissance</th>
            <th>Catégorie</th>
            <th>Equipe</th>
            <th></th>
        </tr>";
foreach ($results as $result) {
    echo "<tr>
        <td><input type='checkbox' class='checkbox-row'></td> <!-- Ajout de la case à cocher -->
        <td><input type='text' name='idLicence' value='" . htmlspecialchars($result['idLicence'], ENT_QUOTES) . "' readonly></td>
        <td>
            <select name='fonction' disabled>
                <option value=''>Sélectionner une fonction</option>";
                foreach ($fonctions as $fonction) {
                    $selected = ($fonction == $result['fonction']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($fonction, ENT_QUOTES) . "' $selected>" . htmlspecialchars($fonction, ENT_QUOTES) . "</option>";
                }
            echo "</select>

        </td>
        <td><input type='text' name='nom' value='" . htmlspecialchars($result['nom'], ENT_QUOTES) . "' readonly></td>
        <td><input type='text' name='prenom' value='" . htmlspecialchars($result['prenom'], ENT_QUOTES) . "' readonly></td>
        <td><input type='date' name='dateNaissance' value='" . htmlspecialchars($result['dateNaissance'], ENT_QUOTES) . "' readonly></td>
        <td>
            <select name='codeCat' disabled>
                <option value=''>Sélectionner une catégorie</option>";
                foreach ($categories as $categorie) {
                    $selected = ($categorie['codeCat'] == $result['codeCat']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($categorie['codeCat'], ENT_QUOTES) . "' $selected>" . htmlspecialchars($categorie['codeCat'], ENT_QUOTES) . "</option>";
                }
echo "</select>
        </td>
        <td>
            <select name='nomEquipe' disabled>
                <option value=''>Sélectionner une équipe</option>";
                foreach ($equipes as $equipe) {
                    $selected = ($equipe['nomEquipe'] == $result['nomEquipe']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($equipe['nomEquipe'], ENT_QUOTES) . "' $selected>" . htmlspecialchars($equipe['nomEquipe'], ENT_QUOTES) . "</option>";
                }
echo "</select>
        </td>
        <td style='display: none;'> <!-- Champs invisibles -->
            <input type='hidden' name='adresse' value='" . htmlspecialchars($result['adresse'], ENT_QUOTES) . "'>
            <input type='hidden' name='cp' value='" . htmlspecialchars($result['cp'], ENT_QUOTES) . "'>
            <input type='hidden' name='ville' value='" . htmlspecialchars($result['ville'], ENT_QUOTES) . "'>
            <input type='hidden' name='numTel' value='" . htmlspecialchars($result['numTel'], ENT_QUOTES) . "'>
            <input type='hidden' name='mail' value='" . htmlspecialchars($result['mail'], ENT_QUOTES) . "'>
            <input type='hidden' name='dateSaisieLicence' value='" . htmlspecialchars($result['dateSaisieLicence'], ENT_QUOTES) . "'>
            <input type='hidden' name='nomSaisieLicence' value='" . htmlspecialchars($result['nomSaisieLicence'], ENT_QUOTES) . "'>
        </td>
        <td>
            <button type='button' onclick='redirectToPage(" . htmlspecialchars($result['idLicence'], ENT_QUOTES) . ")'><i class='fas fa-user-edit'></i> </button>
        </td>
    </tr>";
}
echo "</table>
</div>";

// Boutons d'actions en dehors du tableau
echo "<div class='actions'>
    <button type='button' class='edit-btn' onclick='enableEdit(this.parentNode.parentNode);'>
        <i class='fas fa-edit'></i> 
    </button>
    <button type='button' class='save-btn' style='display: none;' onclick='saveChanges(this.parentNode.parentNode);'>
        <i class='fas fa-save'></i> 
    </button>
    <button type='button' class='delete-btn' onclick='deleteLicencie();'>
        <i class='fas fa-trash'></i>
    </button>
</div>";

    } else {
        echo "<p>Aucun résultat trouvé.</p>";
    }
    ?>
</div>

<script>
    // Fonction pour rediriger vers une autre page avec l'idLicence dans l'URL
    function redirectToPage(idLicence) {
        window.location.href = 'modifInfoPerso.php?idLicence=' + idLicence;
    }

    // Fonction pour activer l'édition des champs dans une ligne de tableau
    function enableEdit(row) {
        var inputs = row.getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute('readonly');
        }

        var selects = row.getElementsByTagName('select');
        for (var j = 0; j < selects.length; j++) {
            selects[j].removeAttribute('disabled');
        }

        // Cacher le bouton d'édition et afficher le bouton de sauvegarde
        row.querySelector('.edit-btn').style.display = 'none';
        row.querySelector('.save-btn').style.display = 'inline';
    }

    // Fonction pour sauvegarder les modifications effectuées
    function saveChanges(row) {
        var formData = new FormData();
        var inputs = row.getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++) {
            formData.append(inputs[i].getAttribute('name'), inputs[i].value);
        }

        var selects = row.getElementsByTagName('select');
        for (var j = 0; j < selects.length; j++) {
            formData.append(selects[j].getAttribute('name'), selects[j].value);
        }

        formData.append('idLicence', row.querySelector('input[name="idLicence"]').value); // Ajouter l'ID de licence

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'modifierLicencie.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                if (xhr.responseText.trim() === 'Success') {
                    alert('Modifications sauvegardées avec succès.');
                    // Recharger la page après la sauvegarde
                    location.reload();
                } else {
                    alert('Erreur lors de la sauvegarde des modifications.');
                    console.log(xhr.responseText); // Afficher la réponse pour le débogage
                }
            } else {
                alert('Erreur lors de la communication avec le serveur.');
            }
        };
        xhr.onerror = function () {
            alert('Erreur lors de la communication avec le serveur.');
        };
        xhr.send(formData);
    }

    // Fonction pour supprimer un licencié
    function deleteLicencie() {
        var checkboxes = document.getElementsByClassName('checkbox-row');
        var selectedIds = [];
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selectedIds.push(checkboxes[i].parentNode.nextSibling.querySelector('input[name="idLicence"]').value);
            }
        }

        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un licencié à supprimer.');
            return;
        }

        if (confirm('Êtes-vous sûr de vouloir supprimer les licenciés sélectionnés ?')) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'supprimerLicencie.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    if (xhr.responseText === 'Success') {
                        alert('Licenciés supprimés avec succès.');
                        location.reload(); // Recharger la page après suppression
                    } else {
                        alert('Erreur lors de la suppression des licenciés.');
                    }
                }
            };
            xhr.send('idLicences=' + JSON.stringify(selectedIds));
        }
    }
</script>


</body>
</html>
