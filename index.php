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
</head>

<body>

  <div class="header-div">
    <span class="title">LP PROD</span>
  </div>

    <?php if(empty($_SESSION['idUtilisateur']))
          echo '<div class="connexion">';
     else
          echo '<div class="deconnexion">';
     ?>
        
      <?php if(empty($_SESSION['idUtilisateur'])){ ?>

      <div class="seconnecter">
        <form action="TraitementConnexion.php" method="GET"  >
            <h3>Se connecter </h3>
            <label class="form-label">Login:</label> <input type="text" size="50" name="pseudo" class="form-control" /></br>
            <label class="form-label"> Mot de passe:</label> <input type="password" size="50" name="mdp" class="form-control" /></br>
            <input name="_method" type="hidden" value="GET"  />
            <input type="submit" value="Valider" class="btn btn-primary" /> </p>
           <!-- <! --<p> Mot de passe oublié </p> à coder plus tard, ça sera un lien vers un formulaire qui dde le mail et envoie donc un mail -->
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

      <?php }else{  ?>

        <div>
          <form class="deco-btn" action="deconnexion.php" method="POST" >
            <input name="idUtilisateur" type="hidden" />
            <input type="submit" value="Déconnexion" class="btn btn-primary" />
          </form>

          <form action="ProfilUtilisateur.php" method="POST" >
            <input name="idUtilisateur" type="hidden" value="<?php $_SESSION['idUtilisateur'] ?>" />
            <input type="submit" value="Profil" class="btn btn-primary" />
          </form>
        </div>

      <?php } ?>

    </div>

<div class="page-content">

    <div class="search-bar">
        <form class="search-form" action="search.php" method="GET" >
            <input class="input-bar" type="text" size="50" name="mots" class="form-control" placeholder="Rechercher une série (par mots-clés) :"/></br>
            <input type="submit" value="Valider" class="btn btn-primary" /> </p>
        </form>
    </div>

    <div class="catalogue">

      <h1>Notre catalogue</h1>

      <div class="list">

        <?php
            
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
                echo('<div class="element-connect">');
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
                <input type="submit" value="J'aime" class="btn btn-outline-success" />
                </form>
                <div class="element-text"><?php echo($resultNonVu[$i][1]) ?></div>
                <form class="dislike" action="SaveLike.php" method="POST" >
                <?php echo("<input name='serie' value ='".$idS."' type='hidden' />") ?>
                <input name="aime" value ="0" type="hidden" />
                <input type="submit" value="Je n'aime pas" class="btn btn-outline-error" />
                </form>

                <?php
                echo('</div>');
                echo('<br>');
              }

            }else{
              for ($i = 0; $i <= count($result)-1; $i++){
                echo($result[$i][1].'<br>');
              }
                
            }
          
          if(!empty($_SESSION['idUtilisateur'])){    
          
          ?>

        </div>

          <h1> Pour vous </h1> <!-- Ici on lui affiche les séries qu'on lui recommande en fonction de celles qu'il a aimé ou non -->

          <?php // On ne recommande pas de série déjà vu 
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
            and s.idSerie not in (select idSerie from regarder where vu = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].") 
            group by p.idSerie, titre
            order by 1 desc, 3 asc";
            $repReco = $bdd->prepare($reco);
            $repReco->execute();
            $resultReco = $repReco->fetchAll();
            $repReco->closeCursor();

            if (!empty($resultReco)){ // Si un utilisateur a aimé et n'a pas aimé au moins une série
              for ($i = 0; $i <= count($resultReco)-1; $i++){
                echo($resultReco[$i][2].'<br>');
              }
            }else{ // sinon on commence par vérifier si il a aimé au moins une série
              $recoAime = "select idSerie from regarder where aime = 1 and idUtilisateur =".$_SESSION['idUtilisateur'];
              $repAime = $bdd->prepare($recoAime);
              $repAime->execute();
              $resultAime = $repAime->fetchAll();
              $repAime->closeCursor();

              if (!empty($resultAime)){
                $getLike = "select sum(nbmots) , p.idSerie, titre
                from posseder p, serie s
                where p.idserie = s.idserie
                and idmot in (select idmot from regarder r, posseder p where r.idserie = p.idserie and aime = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].")
                and s.idSerie not in (select idSerie from regarder where vu = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].")
                having sum(nbmots) > 50000 
                group by p.idSerie, titre
                order by 1 desc, 3 asc"; // le 50 000 est totalement subjectif ça me semblait juste pas trop mal 
                $repLike = $bdd->prepare($getLike); // pour pas avoir les séries qui ont peu de mots en communs avec celles que l'utilisateur a aimé
                $repLike->execute();
                $resultLike = $repLike->fetchAll();
                $repLike->closeCursor();
                
                for ($i = 0; $i <= count($resultLike)-1; $i++){
                  echo($resultLike[$i][2].'<br>');
                }
              }else{ // Si il n'a pas encore aimé une série la recommandation se fera en fonction des séries qu'il n'a pas aimé 
                $recoAimePas = "select idSerie from regarder where aime = 0 and idUtilisateur =".$_SESSION['idUtilisateur'];
                $repAimePas = $bdd->prepare($recoAimePas);
                $repAimePas->execute();
                $resultAimePas = $repAimePas->fetchAll();
                $repAimePas->closeCursor();

                if (!empty($resultAimePas)){
                  $getNotLike = "select sum(nbmots) , p.idSerie, titre
                  from posseder p, serie s
                  where p.idserie = s.idserie
                  and idmot not in (select idmot from regarder r, posseder p where r.idserie = p.idserie and aime = 0 and idUtilisateur = ".$_SESSION['idUtilisateur'].")
                  and s.idSerie not in (select idSerie from regarder where vu = 1 and idUtilisateur = ".$_SESSION['idUtilisateur'].")
                  having sum(nbmots) > 10000 
                  group by p.idSerie, titre
                  order by 1 desc, 3 asc"; // le 10 000 est totalement subjectif ça me semblait juste pas trop mal 
                  $getNotLike = $bdd->prepare($getNotLike); // pour quand même qu'on lui recommande des trucs même si il aime pas grand chose
                  $getNotLike->execute();
                  $resultNotLike = $getNotLike->fetchAll();
                  $getNotLike->closeCursor();
                  
                  for ($j = 0; $j <= count($resultNotLike)-1; $j++){
                    echo($resultNotLike[$j][2].'<br>');
                  }
                }else{ // si il n'a pas encore donné son avis on va juste bêtement lui afficher toutes les séries qu'il n'a pas encore vu
                  echo("Aimez vos séries préférés pour de meilleures recommandations <br>");
                  for ($i = 0; $i <= count($resultNonVu)-1; $i++){
                    echo($resultNonVu[$i][1]. "<br>");
                  }
                }
              }
              
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
    
    <a class="link" href="http://localhost/LPProd/MentionsLegales.php">Mentions légales</a>
    
  </div>

</body>

</html>
