<?php
session_start();
include("conex.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $err = [];
    if (!isset($email)|| empty($email) || !isset($password) || empty($password)) {
        $_SESSION['error'] = "Les données d'authentification sont incorrectes.";
        header("Location: Authentification.php");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "L'email est invalide.";
        header("Location: Authentification.php");
        exit();
    }
    try {
        $req = $con->prepare("SELECT * FROM compte WHERE Email=? AND Mot_pass=?");
        $req->execute([$email,$password]);
        $user = $req->fetch(PDO::FETCH_ASSOC);

        if (!empty($user)) {
            $_SESSION['admin_name'] = $user['nom'];
            header("Location: Tableaubord.php");
            exit();
        } else {
            $_SESSION['error'] = "Email ou mot de passe invalide.";
            header("Location: Authentification.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de connexion à la base de données.";
        header("Location: Authentification.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
            padding: 2rem;
            width: 350px;
            text-align: center;
            transform: perspective(1000px) rotateY(0deg);
            transition: transform 0.6s ease-in-out;
        }

        .container:hover {
            transform: perspective(1000px) rotateY(5deg);
        }

        h1 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: none;
            border-bottom: 2px solid #ddd;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .input-group i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        button {
            background-color: #667eea;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #764ba2;
            transform: translateY(-2px);
        }

        .error-message {
            color: #ff4757;
            margin-bottom: 1rem;
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
        <h1>Connexion</h1>
        <form method="POST">
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p class='error-message shake'>" . htmlspecialchars($_SESSION['error']) . "</p>";
                unset($_SESSION['error']);
                echo "<script>document.querySelector('.container').classList.add('shake');</script>";
            }
            ?>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <button type="submit" name="env">
                <i class="fas fa-sign-in-alt"></i> S'authentifier
            </button>
        </form>
    </div>
</body>
</html>