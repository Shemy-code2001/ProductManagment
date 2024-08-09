<?php
include('conex.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errors = [];
    extract($_POST);
    if (empty($ref) || empty($lib) || empty($qte) || empty($cat) || empty($pu)) {
        $errors['infos'] = "Tous les champs sont obligatoires.";
    }
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $errors["image"] = "Erreur de chargement de l'image.";
    } else {
        $tab_exts = ['image/jpg', 'image/jpeg', 'image/png', 'image/svg'];
        if (!in_array($_FILES['image']['type'], $tab_exts)) {
            $errors["image"] = "Le type de fichier n'est pas valide.";
        }
        if ($_FILES['image']['size'] > 4000000) {
            $errors["image"] = "L'image ne doit pas dépasser 4 Mo.";
        }
    }

    if (empty($errors)) {
        move_uploaded_file($_FILES['image']['tmp_name'], ".\\images\\" .$_FILES['image']['name']);

        try {
            if (isset($_GET['idex'])) {
                $req = $con->prepare("UPDATE produit SET libelle=?, quantite=?, prix_u=?, Photo=?, Id_categorie=? WHERE reference=?");
                $req->execute([$lib, $qte, $pu, ".\\images\\" .$_FILES['image']['name'], $cat, $ref]);
                header("Location: Tableaubord.php?MsgModif=Produit bien modifié");
                exit();
            } 
            
        } catch (PDOException $e) {
            echo "Erreur d'insertion : " .$e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insérer un produit pharmaceutique</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

:root {
  --primary-color: #3498db;
  --secondary-color: #2ecc71;
  --background-color: #ecf0f1;
  --text-color: #2c3e50;
  --error-color: #e74c3c;
}

body {
  font-family: 'Roboto', sans-serif;
  background-color: var(--background-color);
  color: var(--text-color);
  margin: 0;
  padding: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}

.container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  padding: 30px;
  width: 100%;
  max-width: 600px;
  transform-style: preserve-3d;
  transition: transform 0.3s ease;
}

.container:hover {
  transform: rotateX(5deg) rotateY(5deg);
}

h1 {
  color: var(--primary-color);
  text-align: center;
  margin-bottom: 30px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
}

form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

label {
  font-weight: bold;
  display: flex;
  align-items: center;
}

label i {
  margin-right: 10px;
  color: var(--primary-color);
}

input[type="text"],
input[type="number"],
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input[type="text"]:focus,
input[type="number"]:focus,
select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
  outline: none;
}

input[type="file"] {
  border: 1px dashed var(--primary-color);
  padding: 10px;
  border-radius: 4px;
  cursor: pointer;
}

input[type="submit"] {
  background-color: var(--primary-color);
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1px;
  transition: background-color 0.3s ease, transform 0.3s ease;
}

input[type="submit"]:hover {
  background-color: #2980b9;
  transform: translateY(-2px);
}

.error {
  color: var(--error-color);
  background-color: rgba(231, 76, 60, 0.1);
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 20px;
}

img {
  max-width: 100%;
  height: auto;
  border-radius: 4px;
  margin-top: 10px;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.container {
  animation: fadeIn 0.5s ease-out;
}
    </style>
</head>
<body>
    <?php
    if (isset($_GET['idex'])) {
        try {
            $req = $con->prepare("SELECT * FROM produit WHERE reference=?");
            $req->execute([$_GET['idex']]);
            $prod = $req->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erreur d'extraction : " .$e->getMessage();
        }
    }
    ?>

<div class="container">
        <h1>modifier un produit pharmaceutique</h1>
        <?php if (isset($errors["infos"])) { echo '<div class="error">' .$errors["infos"]. '</div>'; } ?>
        <form method="POST" enctype="multipart/form-data">
            <label><i class="fas fa-barcode"></i> Référence :</label>
            <input type="text" name="ref" value="<?=$prod['reference'] ?>">
            
            <label><i class="fas fa-tag"></i> Libellé :</label>
            <input type="text" name="lib" value="<?=$prod['libelle'] ?>">
            
            <label><i class="fas fa-box"></i> Quantité :</label>
            <input type="number" name="qte" value="<?=$prod['quantite'] ?>">
            
            <label><i class="fas fa-folder"></i> Catégorie :</label>
            <select name="cat">
                <?php 
                    try{
                        $req = $con->prepare("SELECT * FROM categorie");
                        $req->execute();
                        $categories = $req->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($categories as $cat) {
                            $lib = $cat['libelle'];
                            $id = $cat['Id_categorie'];
                            $selected = ($id == ($prod['Id_categorie']?? ''))? "selected": "";


                            echo " <option value='$id' $selected>$lib</option>";
                        }
                    }catch(PDOException $e){
                        echo "Erreur d'extraction : " .$e->getMessage();
                    }
                ?>
            </select>
            
            <label><i class="fas fa-dollar-sign"></i> Prix unitaire :</label>
            <input type="number" name="pu" value="<?= $prod['prix_u'] ?>">
            
            <label><i class="fas fa-image"></i> Image :</label>
            <?php if (isset($errors["image"])) { echo '<div class="error">' .$errors["image"]. '</div>'; } ?>
            <?php if (isset($prod['Photo'])) { echo "<img src='" .$prod['Photo']. "' alt='Image du produit'>"; } ?>
            <input type="file" name="image">
            
            <input type="submit" name="ins" value="Modifier">
        </form>
    </div>
</body>
</html>
