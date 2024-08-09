<?php
if(isset($_GET['idex'])){
    include('conex.php');
    extract($_GET);
    try{
        $req = $con->prepare("DELETE FROM produit WHERE reference=?");
        $req->execute([$idex]);
        header("Location: Tableaubord.php?msgSupp=Produit bien supprimé");
        exit;
    }catch(PDOException $e){
        echo "Erreur de Suppression :" .$e->getMessage();
    }  
}


?>