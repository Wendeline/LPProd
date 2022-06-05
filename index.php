<?php
session_start();
$_SESSION['idUtilisateur'] = 'Null';

include('mylib.php');
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
  <h1> Rechercher une série (par mots-clés) : </h1>

    <form action="search.php" method="GET" >
        <input type="text" size="50" name="mots" /></br>
        <input type="submit" value="Valider" /> </p>
    </form>
</div>

<div class="catalogue">

  <h1>Notre catalogue</h1>

    <?php

        $req = "SELECT * from serie";
        $repBdd = $bdd->prepare($req);
        $repBdd->execute();
        $result = $repBdd->fetchAll();
        $repBdd->closeCursor();

        
        if($_SESSION['idUtilisateur'] != 'Null'){
          // recommandation bateau on affiche juste les séries qu'il n'a pas vu
          // Dans une futur version on peut afficher les séries en fonction de leur popularité
          $SeriesNonVu = "select * from serie where idSerie not in (select idSerie from regarder where vu = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].")";
          $repNonVU = $bdd->prepare($SeriesNonVu);
          $repNonVU->execute();
          $resultNonVu = $repNonVU->fetchAll();
          $repNonVU->closeCursor();

          for ($i = 0; $i <= count($resultNonVu)-1; $i++){
            echo($resultNonVu[$i]['titre']);
            ?>
            <form action="SaveLike.php?serie=$resultNonVu[$i]['idSerie']&" method="get">
            <select name="Aime"> 
            <option type="submit" name="submit" value="1">J'aime</option>
            <option type="submit" name="submit" value="0">Je n'aime pas</option>
            <?php
          }

        }else{
          for ($i = 0; $i <= count($result)-1; $i++){
            echo($result[$i][1].'<br>');
          }
            
        }
      
    if($_SESSION['idUtilisateur'] != 'Null'){    
      ?>

      <h1> Pour vous </h1> <!-- Ici on lui affiche les séries qu'on lui recommande en fonction de celles qu'il a aimé ou non -->

      <?php
        $reco = "select sum(nbmots) , idSerie
        from posseder p
        where idmot in (select p1.idmot
        from posseder p1, posseder p2
        where p1.idmot = p2.idmot
        and p1.idSerie in(select idSerie from regarder where aime = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].")
        and p2.idSerie in (select idSerie from regarder where aime = 0 and idUtilisateur = ".$_SESSION['idUtilisateur'].")
        group by p1.idmot
        having sum(p1.nbmots) > sum(p2.nbmots))
        group by idSerie
        order by 1 desc, 2 asc";
        $repReco = $bdd->prepare($reco);
        $repReco->execute();
        $resultReco = $repReco->fetchAll();
        $repReco->closeCursor();

        $sugest = "select titre from serie where idSerie in (resultReco) and idSerie not in (select idSerie from regarder where vu = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].")";
        $repSugest = $bdd->prepare($sugest);
        $repSugest->execute();
        $resultSugest = $repSugest->fetchAll();
        $repSugest->closeCursor();

        for ($i = 0; $i <= count($resultSugest)-1; $i++){
          echo($resultSugest[$i].'<br>');
        }

        ?>
        <h1> À revoir </h1> <!-- Ici on lui affiche les séries qu'il a déjà vu parce qu'il pourrait avoir envie de les revoir

        <?php
          $reco = "select titre from serie s, regarder r where s.idSerie = r.idSerie and vu = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'];
          $repReco = $bdd->prepare($reco);
          $repReco->execute();
          $resultReco = $repReco->fetchAll();
          $repReco->closeCursor();
    
          for ($i = 0; $i <= count($resultReco)-1; $i++){
            echo($resultSugest[$i].'<br>');
          }

  }

  ?>

</div>

</body>
</html>