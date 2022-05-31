<?php

$dsn = "oci:dbname=//telline.univ-tlse3.fr:1521/telline";

try {
    $bdd = new PDO($dsn, "sww2940a", "MdpSUper4");
    //echo "<p> Connexion OK </p>";
} catch (PDOException $e) {
    echo "<p> Erreur Connexion </p>";
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}
