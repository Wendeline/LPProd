
<?php
session_start();

if(isset($_GET['submit'])){
    $aime = $_GET['Aime'];
    $idSerie = $GET['idSerie'];
}

$dejaVu = "select * from regarder where idSerie = $idSerie and idUtilisateur = $_SESSION['idUtilisateur']";
$repVu = $bdd->prepare($dejaVu);
$repVu->execute();
$Vu = $repVu->fetch();
$repVu->closeCursor();

    if ((empty($Vu['vu']))){
        $query = "INSERT INTO `regarder`(idSerie, idUtilisateur, vu, aime) VALUES ($idSerie, $_SESSION['idUtilisateur'], 1, $aime)";
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();
        
    }else{
        $query = "UPDATE `regarder` set vu = 1 and aime = $aime where idSerie=$idSerie and idUtilisateur = $_SESSION['idUtilisateur'])";
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();
}
?>