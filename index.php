<?php
session_start();

include('mylib.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Acceuil - LPProd</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="all.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <h1> LP PROD </h1>
</head>
<body>

<?php 
 
if(empty($_SESSION['idUtilisateur'])){
  ?>
  <div class="connexion">
   
    <div class="seconnecter">
      <form action="TraitementConnexion.php" method="GET"  >
          <h3>Se connecter </h3>
          <label class="form-label">Login:</label> <input type="text" size="50" name="pseudo" class="form-control" /></br>
          <label class="form-label"> Mot de passe:</label> <input type="password" size="50" name="mdp" class="form-control" /></br>
          <input name="_method" type="hidden" value="GET"  />
          <input type="submit" value="Valider" class="btn btn-primary" /> </p>
          <p> Mot de passe oublié </p> <!-- à coder plus tard, ça sera un lien vers un formulaire qui dde le mail et envoie donc un mail -->
      </form>
    </div>
    <div class="sinscrire">
      <form action="TraitementConnexion.php" method="POST" >
          <h3>S'inscrire </h3>
          <label class="form-label">Login :</label> <input type="text" size="50" name="pseudo" class="form-control" /> </br>
          <label class="form-label">Mot de passe :</label> <input type="password" size="50" name="mdp" class="form-control"/> </br>
          <label class="form-label">Email : </label><input type="email" size="50" name="email" class="form-control"/> </br>
          <input name="_method" type="hidden" value="POST" />
          <input type="submit" value="Valider" class="btn btn-primary"/> </p>
      </form>
    </div>
  </div>
<?php }else{
  ?>
  <form action="deconnexion.php" method="POST" >
  <input name="idUtilisateur" type="hidden" />
  <input type="submit" value="Déconnexion" class="btn btn-primary" />
  </form>

  <form action="ProfilUtilisateur.php" method="POST" >
  <input name="idUtilisateur" type="hidden" value="<?php $_SESSION['idUtilisateur'] ?>" />
  <input type="submit" value="Profil" class="btn btn-primary" />
  </form>
  <?php
} ?>

<div class="SearchKeywords">
  <h1> Rechercher une série (par mots-clés) : </h1>

    <form action="search.php" method="GET" >
        <input type="text" size="50" name="mots" class="form-control" /></br>
        <input type="submit" value="Valider" class="btn btn-primary" />  </p>
    </form>
</div>

<div class="catalogue">

  <h1>Notre catalogue</h1>

    <?php

        //var_dump($_SESSION['idUtilisateur']);
        $req = "SELECT * from serie";
        $repBdd = $bdd->prepare($req);
        $repBdd->execute();
        $result = $repBdd->fetchAll();
        $repBdd->closeCursor();

        
        if(!empty($_SESSION['idUtilisateur'])){
          // recommandation bateau on affiche juste les séries qu'il n'a pas vu
          // Dans une futur version on peut afficher les séries en fonction de leur popularité
          $SeriesNonVu = "select * from serie where idSerie not in (select idSerie from regarder where vu = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].")";
          $repNonVU = $bdd->prepare($SeriesNonVu);
          $repNonVU->execute();
          $resultNonVu = $repNonVU->fetchAll();
          $repNonVU->closeCursor();

          for ($i = 0; $i <= count($resultNonVu)-1; $i++){
            echo($resultNonVu[$i][1]);
            $idS = $resultNonVu[$i][0];
            ?>
            <!--<form action="SaveLike.php?serie=$resultNonVu[$i]['idSerie']&" method="get">
            <label>
              <input type="radio" name="Aime" value="1">J'aime
            </label>
            <label>
              <input type="radio" name="Aime" value="0">Je n'aime pas
            </label> -->
            <!--<select name="Aime"> 
            <option type="submit" name="submit" value="1">J'aime</option>
            <option type="submit" name="submit" value="0">Je n'aime pas</option>-->

            
            <form action="SaveLike.php" method="POST" >
            <?php echo("<input name='serie' value ='".$idS."' type='hidden' />") ?>
            <input name="aime" value ="1" type="hidden" />
            <input type="submit" value="J'aime" class="btn btn-primary" />
            </form>
            <form action="SaveLike.php" method="POST" >
            <?php echo("<input name='serie' value ='".$idS."' type='hidden' />") ?>
            <input name="aime" value ="0" type="hidden" />
            <input type="submit" value="Je n'aime pas" class="btn btn-primary" />
            </form>

            <?php
            echo('<br>');
          }

        }else{
          for ($i = 0; $i <= count($result)-1; $i++){
            echo($result[$i][1].'<br>');
          }
            
        }
      
    if(!empty($_SESSION['idUtilisateur'])){    
      ?>

      <h1> Pour vous </h1> <!-- Ici on lui affiche les séries qu'on lui recommande en fonction de celles qu'il a aimé ou non -->

      <?php
        $reco = "select sum(nbmots) , p.idSerie, titre
        from posseder p, serie s
        where p.idserie = s.idserie
        and idmot in (select p1.idmot
        from posseder p1, posseder p2
        where p1.idmot = p2.idmot
        and p1.idSerie in(select idSerie from regarder where aime = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].")
        and p2.idSerie in (select idSerie from regarder where aime = 0 and idUtilisateur = ".$_SESSION['idUtilisateur'].")
        group by p1.idmot
        having sum(p1.nbmots) > sum(p2.nbmots))
        group by p.idSerie, titre
        order by 1 desc, 3 asc";
        $repReco = $bdd->prepare($reco);
        $repReco->execute();
        $resultReco = $repReco->fetchAll();
        $repReco->closeCursor();

        for ($i = 0; $i <= count($resultReco)-1; $i++){
          echo($resultReco[$i][2].'<br>');
        }

        ?>
        <h1> À revoir </h1> <!-- Ici on lui affiche les séries qu'il a déjà vu parce qu'il pourrait avoir envie de les revoir -->

        <?php
          $revoir = "select titre from serie s, regarder r where s.idSerie = r.idSerie and vu = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'];
          $repRevoir = $bdd->prepare($revoir);
          $repRevoir->execute();
          $resultRevoir = $repRevoir->fetchAll();
          $repRevoir->closeCursor();
    
          for ($j = 0; $j <= count($resultRevoir)-1; $j++){
            echo($resultRevoir[$j][0].'<br>');
          }

  }

  ?>

</div>

</body>
</html>

<a href="http://localhost/LPProd/MentionsLegales.php">Mentions légales</a>
