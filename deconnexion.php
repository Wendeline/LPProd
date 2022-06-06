<?php
session_start();

unset($_SESSION["idUtilisateur"]);

header('Location: http://localhost/LPProd/index.php');
exit();