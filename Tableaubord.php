<?php
session_start();
include("conex.php");

if(!isset($_SESSION['admin_name'])){
    header("Location: Authentification.php");
    exit;
}

if(!isset($_SESSION['visits'])){
    $_SESSION['visits'] = 0;
}
$_SESSION['visits']++;

try {
    $req = $con->prepare("SELECT produit.*, categorie.libelle AS categorie_libelle FROM produit JOIN categorie ON produit.Id_categorie=categorie.Id_categorie");
    $req->execute();
    $produits = $req->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur d'extraction des données :" . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
            padding: 2rem;
            transform: perspective(1000px) rotateX(0deg);
            transition: transform 0.6s ease-in-out;
        }
        .container:hover {
            transform: perspective(1000px) rotateX(2deg);
        }
        h1 {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .add-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 1rem;
            transition: background-color 0.3s, transform 0.3s;
        }
        .add-button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        img {
            border-radius: 5px;
            transition: transform 0.3s;
        }
        img:hover {
            transform: scale(1.1);
        }
        .action-links a {
            color: #333;
            text-decoration: none;
            margin-right: 10px;
            transition: color 0.3s;
        }
        .action-links a:hover {
            color: #4CAF50;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
    </style>
</head>
<body>
    <div class="container fade-in">
        <h1>
            <i class="fas fa-user-circle"></i> Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!
        </h1>
        <a href="Inserermed.php" class="add-button"><i class="fas fa-plus"></i> Ajouter un produit</a>
        <table>
            <tr>
                <th>Photo Produit</th>
                <th>Reference Produit</th>
                <th>Libelle Produit</th>
                <th>Catégorie</th>
                <th>Quantité en stock</th>
                <th>Prix unitaire</th>
                <th>Actions</th>
            </tr>
            <?php
            foreach ($produits as $p) {
                echo "<tr>";
                echo "<td><img src='" .$p['Photo']. "' width='100px' height='100'></td>";
                echo "<td>" . htmlspecialchars($p['reference']) . "</td>";
                echo "<td>" . htmlspecialchars($p['libelle']) . "</td>";
                echo "<td>" . htmlspecialchars($p['categorie_libelle']) . "</td>";
                echo "<td>" . htmlspecialchars($p['quantite']) . "</td>";
                echo "<td>" . htmlspecialchars($p['prix_u']) . "</td>";
                echo "<td class='action-links'><a href='modifier.php?idex=" . $p['reference'] . "'>Modifier</a>";
                echo "<a href='supprimer.php?idex=" . $p['reference'] . "' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer..?');\">Supprimer</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
