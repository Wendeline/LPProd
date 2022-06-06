<?php
session_start();

$http_method = $_SERVER['REQUEST_METHOD'];
switch ($http_method) {
    case "GET":
        if ($_GET['_method'] == "GET") {
            $pseudo = $_GET['pseudo'];
            $mdp = $_GET['mdp'];

            //$pseudo et $mdp contiennent les informations récupérées dans le formulaire
            // Lien de l'API 
            $result = file_get_contents("http://localhost/LPProd/ApiConnexion.php?pseudo=$pseudo&mdp=$mdp",
            false, stream_context_create(array('http' => array('method' => 'GET'))) //Changer par DELETE si besoin
            );

            header('Location: http://localhost/LPProd/index.php');
            exit();

        }else {
            $pseudo = $_GET['pseudo'];
            $mdp = $_GET['mdp'];
    
            $result = file_get_contents("http://localhost/LPProd/ApiConnexion.php?pseudo=$pseudo&mdp=$mdp",
            false, stream_context_create(array('http' => array('method' => 'DELETE'))) 
            );
    
        }
        break;
    case "POST":
        if ($_POST['_method'] == "POST") {
            // récupération des informations de l'utilisateur
            $data = array("pseudo" => $_POST['pseudo'], "mdp" => $_POST['mdp'], "email" => $_POST['email']);
            $data_string = json_encode($data);
            ///Envoi de la requête
            $result = file_get_contents(
                'http://localhost/LPProd/ApiConnexion.php', //lien de l'API
                null,
                stream_context_create(array(
                    'http' => array('method' => 'POST', //la méthode à changer pour PUT si besoin
                        'content' => $data_string,
                        'header' => array('Content-Type: application/json' . "\r\n"
                            . 'Content-Length: ' . strlen($data_string) . "\r\n"))))
            );
        }
        else {
            $data = array("pseudo" => $_POST['pseudo'], "mdp" => $_POST['mdp'] ,"email" => $_POST['email'] );
            $data_string = json_encode($data);

            ///Envoi de la requête
            $result = file_get_contents(
                'http://localhost/LPProd/ApiConnexion.php',
                null,
                stream_context_create(array(
                    'http' => array('method' => 'PUT',
                        'content' => $data_string,
                        'header' => array('Content-Type: application/json'."\r\n"
                            .'Content-Length: '.strlen($data_string)."\r\n"))))
            );
        }
        break;
}