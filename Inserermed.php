<?php
include('conex.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errors = [];

    // Extraction des données du formulaire
    extract($_POST);

    // Validation des champs
    if (empty($ref) || empty($lib) || empty($qte) || empty($cat) || empty($pu)) {
        $errors['infos'] = "Tous les champs sont obligatoires.";
    }

    // Validation de l'image
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $errors["image"] = "Erreur de chargement de l'image.";
    } else {
        $image = $_FILES['image'];
        $tab_exts = ['image/jpg', 'image/jpeg', 'image/png', 'image/svg+xml'];
        if (!in_array($image['type'], $tab_exts)) {
            $errors["image"] = "Le type de fichier n'est pas valide.";
        }
        if ($image['size'] > 4000000) {
            $errors["image"] = "L'image ne doit pas dépasser 4 Mo.";
        }
    }

    if (empty($errors)) {
        move_uploaded_file($image['tmp_name'], ".\\images\\" .$_FILES['image']['name']);

        try {
            $req = $con->prepare("INSERT INTO produit (reference, libelle, quantite, prix_u, Photo, Id_categorie) VALUES (?, ?, ?, ?, ?, ?)");
            $req->execute([$ref, $lib, $qte, $pu, ".\\images\\" .$_FILES['image']['name'], $cat]);
            header("Location: Tableaubord.php");
            exit();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --error-color: #ff4757;
            --text-color: #333;
            --bg-color: #f4f4f4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            transform: perspective(1000px) rotateY(0deg);
            transition: transform 0.6s ease-in-out;
        }

        .container:hover {
            transform: perspective(1000px) rotateY(5deg);
        }

        h1 {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-color);
        }

        input[type="text"],
        input[type="number"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        input[type="submit"] {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        input[type="submit"]:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .error {
            color: var(--error-color);
            margin-bottom: 1rem;
            text-align: center;
            font-weight: bold;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }

        .shake {
            animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-pills"></i> Insérer un produit pharmaceutique</h1>
        <?php if (isset($errors["infos"])) { echo '<div class="error shake">' . htmlspecialchars($errors["infos"], ENT_QUOTES, 'UTF-8') . '</div>'; } ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <i class="fas fa-barcode"></i>
                <input type="text" name="ref" placeholder="Référence" value="<?= htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <i class="fas fa-tag"></i>
                <input type="text" name="lib" placeholder="Libellé" value="<?= htmlspecialchars($lib ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <i class="fas fa-cubes"></i>
                <input type="number" name="qte" placeholder="Quantité" value="<?= htmlspecialchars($qte ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <i class="fas fa-folder"></i>
                <select name="cat">
                    <?php
                    try {
                        $req = $con->prepare("SELECT * FROM categorie");
                        $req->execute();
                        $categories = $req->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($categories as $categorie) {
                            $libelle = htmlspecialchars($categorie['libelle'], ENT_QUOTES, 'UTF-8');
                            $id = htmlspecialchars($categorie['Id_categorie'], ENT_QUOTES, 'UTF-8');
                            echo "<option value='$id'>$libelle</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<div class='error'>Erreur d'extraction des catégories : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</div>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <i class="fas fa-euro-sign"></i>
                <input type="number" name="pu" placeholder="Prix unitaire" value="<?= htmlspecialchars($pu ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <?php if (isset($errors["image"])) { echo '<div class="error shake">' . htmlspecialchars($errors["image"], ENT_QUOTES, 'UTF-8') . '</div>'; } ?>
            <div class="form-group">
                <i class="fas fa-image"></i>
                <input type="file" name="image">
            </div>
            <input type="submit" name="ins" value="INSÉRER">
        </form>
    </div>
</body>
</html>
