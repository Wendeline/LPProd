<?php
/// Librairies éventuelles (pour la connexion à la BDD, etc.)
session_start();
include('mylib.php');


/// Envoi de la réponse au Client
function deliver_response($status, $status_message, $data)
{
    /// Paramétrage de l'entête HTTP, suite
    //header("HTTP/1.1 $status $status_message");

    /// Paramétrage de la réponse retournée
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response['data']);
    echo $json_response;
}

/// Paramétrage de l'entête HTTP (pour la réponse au Client)
header("Content-Type:application/json");
/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];
switch ($http_method) {
    /// Cas de la méthode GET
    case "GET" :
        /// Récupération des critères de recherche envoyés par le Client
		if (!empty($_GET['pseudo'])){
		    /// Traitement
            //$pseudo = htmlspecialchars($_GET['pseudo']);
            $pseudo = filter_input(INPUT_GET, 'pseudo');
            //$pseudo=$_GET['pseudo'];
            //$mdp = htmlspecialchars($_GET['mdp']);
            $mdp = filter_input(INPUT_GET, 'mdp');
            //$mdp=$_GET['mdp'];

            $query = "select * from utilisateur where pseudo = :pseudo and motdepasse = :mdp";
            $repBdd = $bdd->prepare($query);
            $repBdd->execute([ 'pseudo' => $pseudo , 'mdp' => $mdp]);
            $result = $repBdd->fetch();
            $repBdd->closeCursor();
            if ($result[1] == $pseudo) {
                deliver_response(200, 'Data' , $result[0]);
            } else {
                deliver_response(404, '404',$result);
            }
        }
        break;
    /// Cas de la méthode POST
    case "POST" :
        /// Récupération des données envoyées par le Client
        $postedData = file_get_contents('php://input');

        /// Traitement
        $data = json_decode($postedData);
        $pseudo = $data->pseudo;
        $mdp = $data->mdp;
        $email = $data->email;

        $query = "INSERT INTO `utilisateur`(pseudo,motdepasse,mail) VALUES ('$pseudo','$mdp','$email')";
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();
        oci_commit($bdd);
        $result = $repBdd->fetch();
        $repBdd->closeCursor();
        if ($result == "Duplicata du champ '$pseudo' pour la clef 'PRIMARY'") {
            deliver_response(400, "Nom d'utilisateur deja present", $data);
        } else {
            /// Envoi de la réponse au Client
            deliver_response(201, "Inscription OK", $data);
        }
        break;
        /// Cas de la méthode PUT
    case "PUT" :
        /// Récupération des données envoyées par le Client
        $postedData = file_get_contents('php://input');

        /// Traitement
        $data = json_decode($postedData);
        $pseudo = $data->pseudo;
        $mdp = $data->mdp;
        $email = $data->email;
        $idinit = '';
        $mdpinit = '';
        $emailinit = '';

        $query = "select * from `utilisateur` WHERE pseudo = '$pseudo'";
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();
        $records = $repBdd->fetch();
        $repBdd->closeCursor();
        $query = "UPDATE `utilisateur` SET `motdepasse`='$mdp',`mail`='$email' WHERE pseudo = '$pseudo'";
        $repBdd = $bdd->prepare($query);
        $repBdd->execute();
        oci_commit($bdd);
        while($result = $repBdd->fetch())
        {
            $idinit= $result['pseudo'];
            $mdpinit=$result['motdepasse'];
            $emailinit= $result['mail'];
        }
        $repBdd->closeCursor();

        /// Envoi de la réponse au Client
        deliver_response(200, "Modification de l'user $idinit $mdpinit $emailinit en :", $data);
        break;
    /// Cas de la méthode DELETE
    default :
        /// Récupération des critères de recherche envoyés par le Client
        if (!empty($_GET['pseudo'])) {
            /// Traitement
            $pseudo = $_GET['pseudo'];
            $mdp = $_GET['mdp'];
            
            $query = "DELETE from `utilisateur` where pseudo='$pseudo' and motdepasse ='$mdp'";
            $repBdd = $bdd->prepare($query);
            $repBdd->execute();
            $repBdd->closeCursor();
            unset($_SESSION["idUtilisateur"]);
            deliver_response(200, "Suppresion ok $pseudo",NULL);
            
        }
        break;
}