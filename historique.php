<?php
session_start();
// On inclus la connexion à la base de données
include('mylib.php');


$req = "select recherche from historique h, historiqueutilisateur hu where h.idrecherche = hu.idrecherche and hu.idutilisateur =".$_SESSION['idUtilisateur']. " order by nbrecherche desc";
$repBdd = $bdd->prepare($req);
$repBdd->execute();
$result = $repBdd->fetchAll();
$repBdd->closeCursor();

// Pour le moment on affiche juste le nom des séries selon leur score de pertinence
echo("Vos recherches : ");
var_dump($result);
for ($i = 0; $i <= count($result)-1; $i++){
    echo($result[$i]."<br>");
}