<?php
session_start();
// On inclus la connexion à la base de données
include('mylib.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Historique - LPProd</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="all.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<a class="retour" href="http://localhost/LPProd/index.php">Revenir au menu principal</a>
<br><br>

<div class="page">

<?php

$req = "select recherche from historique h, historiqueutilisateur hu where h.idrecherche = hu.idrecherche and hu.idutilisateur =".$_SESSION['idUtilisateur']. " order by nbrecherche desc";
$repBdd = $bdd->prepare($req);
$repBdd->execute();
$result = $repBdd->fetchAll();
$repBdd->closeCursor();

// Pour le moment on affiche juste le nom des séries selon leur score de pertinence
echo("<h1>Vos recherches : </h1><br><br>");
for ($i = 0; $i <= count($result)-1; $i++){
    echo($result[$i][0]."<br>");
}


?>

</div>

</body>
</html>

<a class="link-histo" href="http://localhost/LPProd/MentionsLegales.php">Mentions légales</a>