<?php
session_start();

include('mylib.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Profil - LPProd</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="all.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<a class="retour" href="http://localhost/LPProd/index.php">Revenir au menu principal</a>
<br><br>

<body>

  <form class="histo" action="historique.php" method="POST" >
    <input name="idUtilisateur" type="hidden" value="<?php $_SESSION['idUtilisateur'] ?>" />
    <input type="submit" value="Historique" class="btn btn-primary" />
  </form>

  <div class="profil">
    <div>
    <form action="TraitementConnexion.php" method="POST" >
          <h3> Modifier votre mot de passe ou email : </h3>
          Identifiant : <input type="text" size="50" name="pseudo" /> </br>
          Mot de passe : <input type="password" size="50" name="mdp" /> </br>
          Email : <input type="email" size="50" name="email" /> </br>
          <input name="_method" type="hidden" value="PUT" />
          <input type="submit" value="Valider" class="btn btn-primary"/> </p>
      </form>

      <form action="TraitementConnexion.php" method="GET" >
          <h3>Supprimer son compte : </h3>
          Login: <input type="text" size="50" name="pseudo" /></br>
          Mot de passe: <input type="password" size="50" name="mdp"  /></br>
          <input name="_method" type="hidden" value="DELETE" />
          <input type="submit" value="Valider"class="btn btn-primary" /> </p>
      </form>
    </div>
  </div>

  <div class="link-histo" href="http://localhost/LPProd/MentionsLegales.php">Mentions légales</div>

</body>
</html>