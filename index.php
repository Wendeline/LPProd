<?php
session_start();
$_SESSION['idUtilisateur'] = 'Null';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Acceuil - LPProd</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="connexion">

  <h1> Formulaires de connexion</h1>

    <form action="TraitementConnexion.php" method="GET" >
        <h3>Se connecter : </h3>
        Login: <input type="text" size="50" name="pseudo" /></br>
        Mot de passe: <input type="password" size="50" name="mdp"  /></br>
        <input name="_method" type="hidden" value="GET" />
        <input type="submit" value="Valider" /> </p>
        <p> Mot de passe oublié </p> <!-- à coder plus tard, ça sera un lien vers un formulaire qui dde le mail et envoie donc un mail -->
    </form>

    <form action="TraitementConnexion.php" method="POST" >
        <h3>S'inscrire : </h3>
        Login : <input type="text" size="50" name="pseudo" /> </br>
        Mot de passe : <input type="password" size="50" name="mdp" /> </br>
        Email : <input type="email" size="50" name="email" /> </br>
        <input name="_method" type="hidden" value="POST" />
        <input type="submit" value="Valider" /> </p>
    </form>

</div>

<div class="SearchKeywords">
  <h1> Rechercher une série : </h1>

    <form action="search.php" method="GET" >
        <input type="text" size="50" name="mots" /></br>
        <input type="submit" value="Valider" /> </p>
    </form>
</div>

<div class="catalogue">

  <h1>Notre catalogue</h1>

    <?php
        include('mylib.php');

        $req = "SELECT titre from serie";
        $repBdd = $bdd->prepare($req);
        $repBdd->execute();
        $result = $repBdd->fetchAll();
        $repBdd->closeCursor();

        for ($i = 0; $i <= count($result)-1; $i++){
            echo($result[$i]['titre']."<br>");
        }

    ?>

</div>

</body>
</html>