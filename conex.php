<?php

try{
    $con = new PDO("mysql:host=localhost;dbname=gestionmed","root","");
}catch(PDOException $e){
    echo "Ereeur de connexion" .$e->getMessage();
}

?>