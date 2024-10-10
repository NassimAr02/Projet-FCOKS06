<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Se connecter - FCOSK 06 Strasbourg</title>
        <link rel="stylesheet" type="text/css" href="style1.css">
        <link rel="stylesheet" type="text/css" href="styleHeader.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    </head>
    <body>

    <header class="fixed-header">
        <div class="logo">
            <img src="logo.png" alt="Logo FCOSK 06">
        </div>
        <!-- <nav class="main-nav">
            <ul>
                <li><a href="pageProtegee.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="ajout.php"><i class="fas fa-user-plus"></i> Ajouter</a></li>
                <li><a href="rechercheJoueur.php"><i class="fas fa-tshirt"></i> Equipement</a></li>
                <li><a href="rechercheJoueur2.php"><i class="fas fa-pencil-alt"></i> Consulter</a></li>
                <li><a href="imprime.php"><i class="fas fa-print"></i> Imprimer</a></li>
                <li><a href="cotisations.php"><i class="fas fa-money-check-alt"></i> Cotisations</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav> -->
    </header>

        <main>
            <div class="login-container">
                <h2>Se connecter</h2>
    
                <form action="traitementConnexion.php" method="post" class="formI">
                    <fieldset>
                        <legend>Se connecter</legend>
                        <label for="login-user">Nom utilisateur</label>
                        <input type="text" id="login-user" name="user" required/>
                        
                        <label for="login-password">Mot de passe</label>
                        <input type="password" id="login-password" name="password" required/>
                        
                        <div class="button-container">
                            <input type="submit" value="Se connecter">
                            <input type="reset" value="Annuler">
                        </div>
                    </fieldset>
                </form>
            </div>
        </main>
    </body>
</html>
