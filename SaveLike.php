
<?php
session_start();
include('mylib.php');

$aime = $_POST['aime'];
$idSerie = $_POST['serie'];


$dejaVu = "select * from regarder where idSerie =". $idSerie." and idUtilisateur =". $_SESSION['idUtilisateur'];
$repVu = $bdd->prepare($dejaVu);
$repVu->execute();
$Vu = $repVu->fetch();
$repVu->closeCursor();

    if ($Vu == false){
        $bdd->beginTransaction();
        $query = "INSERT INTO `regarder`(idSerie, idUtilisateur, vu, aime) VALUES (".$idSerie.", ".$_SESSION['idUtilisateur'].", 1, ".$aime.")";
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();
        $bdd->commit();
        
    }else{
        $bdd->beginTransaction();
        $query = "UPDATE `regarder` set vu = 1 and aime = ".$aime." where idSerie=".$idSerie." and idUtilisateur = ".$_SESSION['idUtilisateur'];
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();
        $bdd->commit();
}


header('Location: http://localhost/LPProd/index.php');
exit();
?>