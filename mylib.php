<?php

$tns = "  
(DESCRIPTION =
   (ADDRESS_LIST =
     (ADDRESS = (PROTOCOL = TCP)(HOST = telline.univ-tlse3.fr)(PORT = 1521))
   )
   (CONNECT_DATA =
     (SID = etupre)
   )
  )
       ";
$db_username = "SWW2940A";
$db_password = "MdpSuper4";
try{
    $bdd = new PDO("oci:dbname=".$tns,$db_username,$db_password);
}catch(PDOException $e){
    echo ($e->getMessage());
}