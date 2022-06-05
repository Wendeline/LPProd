<?php
session_start();
// On inclus la connexion à la base de données
include('mylib.php');
//include('pythonFonctions.py');

$accents  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
$SansAccents = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');

// On récupère ce qui a été saisi par l'utilisateur
$mots = $_GET['mots'];
$mots = str_replace($accents, $SansAccents, $mots);
// On défini un tableau qui permettra d'enlever les éléments inutiles saisis
$patern = array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/');
// on remplace tout ce qui n'est pas "un mot" par des espaces
$deleteUseless = preg_replace($patern, " ", $mots);
// Nos mots sont mis dans un tableau
$Alemme = explode(" ",$deleteUseless);
$string = "['".$Alemme[0]."'";
for ($i = 1; $i <= count($Alemme)-1; $i++) {
    $string = $string . ",'".$Alemme[$i]."'";
}
$string = $string . "]";
$comand = 'C:\wamp64\www\LPProd\pythonFonctions.py';
$python = 'C:\Users\Wendy\AppData\Local\Microsoft\WindowsApps\python.exe';
exec("$python $comand $string", $Splitmots);
//exec('python pythonFonctions.py ' . $string , $Splitmots); 
var_dump($Splitmots);
// On instancie une string dans laquelle nous mettrons les mots recherchés selon un certain paterne 
$SearchWord = "'".$Splitmots[0]."'";
// Parcours du tableau 
for ($i = 1; $i <= count($Splitmots)-1; $i++) {
    $SearchWord = $SearchWord.",'".strtolower($Splitmots[$i])."'";
}

// Requête : sélectionne le nom d'une série pour les mots clés saisie et les affiche selon leur pertinence
// Leur pertinence dépend du nombre d'occurence des mots saisies au sein de la série
$req = "SELECT titre, sum(nbmots) as compter from posseder p ,mot m ,serie s where p.idSerie = s.idSerie and m.idMot = p.idMot and libelle in ($SearchWord) group by titre ORDER BY 2 DESC";
$repBdd = $bdd->prepare($req);
$repBdd->execute();
$result = $repBdd->fetchAll();
$repBdd->closeCursor();

// Pour le moment on affiche juste le nom des séries selon leur score de pertinence
echo("Votre recherche : ".$_GET['mots']);
echo("<br>Résultat.s : <br>");
for ($i = 0; $i <= count($result)-1; $i++){
    echo($result[$i]['titre']."<br>");
}

if($_SESSION['idUtilisateur'] != 'Null'){
    
    $requete = "select idRecherche from historique where recherche = ".$_GET['mots'];
    $repBd = $bdd->prepare($requete);
    $repBd->execute();
    $idRecherche = $repBd->fetch();
    $repBd->closeCursor();

    if (!(empty($idRecherche))){
        $requete = "select nbRecherche from historiqueutilisateur where idRecherche = ".$idRecherche." and idUtilisateur = ".$_SESSION['idUtilisateur'];
        $repBd = $bdd->prepare($requete);
        $repBd->execute();
        $nbRecherche = $repBd->fetch();
        $repBd->closeCursor();
        
        if (!(empty($nbRecherche))){
            $updateRelation = "update historiqueutilisateur set nbRecherche = ".$nbRecherche."+1 where idRecherche = ".$idRecherche." and idUtilisateur = ".$_SESSION['idUtilisateur'];
            $repUpdate = $bdd->prepare($updateRelation);
            $repUpdate->execute();
            $repUpdate->closeCursor();
        }else{
            $query = "INSERT INTO `historiqueutilisateur`(idRecherche, idUtilisateur, nbRecherche) VALUES ('".$idRecherche."','".$_SESSION['idUtilisateur']."',1)";
            $repBdd = $bdd->prepare($query);
            $repBdd->execute();
        }

    }else{
        $query = "INSERT INTO `historique`(recherche) VALUES ('".$_GET['mots']."')";
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();

        $requete = "select idRecherche from historique where recherche = ".$_GET['mots'];
        $repBd = $bdd->prepare($requete);
        $repBd->execute();
        $idRecherche = $repBd->fetch();
        $repBd->closeCursor();

        $query = "INSERT INTO `historiqueutilisateur`(idRecherche, idUtilisateur, nbRecherche) VALUES ('".$idRecherche."','".$_SESSION['idUtilisateur']."',1)";
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();
    }
}

?>