<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout de l'équipement d'entraînement du licencié</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
    <link rel="stylesheet" type="text/css" href="styleHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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

<script type="text/javascript">
    function getParameterByName(name) {
        var url = window.location.href; // Récupère l'URL actuelle
        name = name.replace(/[\[\]]/g, '\\$&'); // Échappe les caractères spéciaux dans le nom du paramètre
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'); // Crée une expression régulière pour rechercher le paramètre
        var results = regex.exec(url); // Exécute la regex sur l'URL
        if (!results) return null; // Si le paramètre n'est pas trouvé, retourne null
        if (!results[2]) return ''; // Si le paramètre est trouvé mais n'a pas de valeur, retourne une chaîne vide
        return decodeURIComponent(results[2].replace(/\+/g, ' ')); // Retourne la valeur du paramètre en décodant les caractères spéciaux
    }


    function redirectToPage(selectObj) {
        var selectedValue = selectObj.options[selectObj.selectedIndex].value; // Obtient l'URL sélectionnée dans la liste déroulante
        var idLicence = getParameterByName('idLicence'); // Obtient l'ID de licence de l'URL actuelle
        if (selectedValue && idLicence) { // Si une page est sélectionnée et l'ID de licence est présent
            window.location.href = selectedValue + "?idLicence=" + encodeURIComponent(idLicence); // Redirige vers l'URL sélectionnée avec l'ID de licence
        }
    }

</script>

<form>
    <div class="formulaireRecherche">
    <div class="form">
        <label for="pageSelector">Choisissez une page :</label>
        <select id="pageSelector" name="pages" onchange="redirectToPage(this)">
            <option value="">Sélectionner le type d'équipement</option>
            <option value="ajoutE1.php">Equipement de sortie</option>
            <option value="ajoutE12.php">Equipement d'entraînement</option>
        </select>
    </div>
</div>
</form>

</body>
</html>
