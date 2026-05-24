<?php

session_start();
require_once "connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["nom"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM utilisateurs WHERE nom = ? OR email = ?";
    $req = $pdo->prepare($sql);
    $req->execute([$login, $login]);

    $user = $req->fetch(PDO::FETCH_ASSOC);

    if (!$user) {

        $message = "Utilisateur introuvable";
    } else {
        if (password_verify($password, $user["mot_de_passe"])) {

            $_SESSION["id_user"] = $user["id"];
            $_SESSION["nom"] = $user["nom"];
            $_SESSION["role"] = $user["role"];

            header("Location: Accueil.php");
            exit();
        } else {
            $message = "Mot de passe incorrect";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f172a, #1e3a8a, #2563eb);
            overflow: hidden;
        }

        body::before {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            background: #2563eb;
            filter: blur(180px);
            opacity: 0.3;
            border-radius: 50%;
            bottom: -150px;
            left: -100px;
        }

        .login-box {
            width: 420px;
            padding: 40px;
            border-radius: 25px;
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.4);
            color: white;
        }

        .login-box h1 {
            text-align: center;
            font-size: 45px;
            margin-bottom: 10px;
        }

        .login-box p {
            text-align: center;
            color: #cbd5e1;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            height: 55px;
            border: none;
            outline: none;
            border-radius: 50px;
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0 50px;
            color: white;
            font-size: 15px;
        }

        .input-box input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }

        .input-box i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .left-icon {
            left: 18px;
        }

        .right-icon {
            right: 18px;
            cursor: pointer;
            color: #3b82f6;
        }

        .btn-login {
            width: 100%;
            height: 55px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        .error {
            background: #7f1d1d;
            color: white;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="login-box">

        <h1>Log in</h1>

        <p>
            Application de gestion des frais de scolaritéde l'EMIT
            (Ecole de Management et d'Innovation Téchnologique)
        </p>

        <?php if ($message != "") { ?>
            <div class="error">
                <?= $message ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="input-box">

                <i class="fa-solid fa-user left-icon"></i>

                <input type="text"
                    name="nom"
                    placeholder="Nom utilisateur ou Email"
                    required>

            </div>

            <div class="input-box">

                <i class="fa-solid fa-lock left-icon"></i>

                <input type="password"
                    name="password"
                    id="password"
                    placeholder="Entrer votre mot de passe"
                    required>

                <i class="fa-solid fa-eye-slash right-icon"
                    onclick="togglePassword()"></i>

            </div>

            <button type="submit" class="btn-login">
                Se connecter
            </button>

        </form>

    </div>

    <script>
        function togglePassword() {

            let password = document.getElementById("password");

            if (password.type === "password") {
                password.type = "text";
            } else {
                password.type = "password";
            }

        }
    </script>

</body>

</html>