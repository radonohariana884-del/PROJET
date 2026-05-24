<?php
include("connect.php");
session_start();

if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["id_user"];

/* USER */
$sql = "SELECT * FROM utilisateurs WHERE id = :id";
$req = $pdo->prepare($sql);
$req->execute([':id' => $id]);
$user = $req->fetch(PDO::FETCH_ASSOC);

/* UPDATE PROFIL */
if (isset($_POST["update"])) {

    $sql = "UPDATE utilisateurs SET nom=:nom, email=:email WHERE id=:id";
    $req = $pdo->prepare($sql);
    $req->execute([
        ':nom' => $_POST["nom"],
        ':email' => $_POST["email"],
        ':id' => $id
    ]);

    $_SESSION["nom"] = $_POST["nom"];

    header("Location: Parametre.php");
    exit();
}

/* PASSWORD */
if (isset($_POST["change_pass"])) {

    $current = $_POST["current_pass"];
    $new = $_POST["new_pass"];
    $confirm = $_POST["confirm_pass"];

    if ($current != $user["mot_de_passe"]) {
        $error = "Mot de passe actuel incorrect";
    } elseif ($new != $confirm) {
        $error = "Les mots de passe ne correspondent pas";
    } else {

        $sql = "UPDATE utilisateurs SET mot_de_passe=:pass WHERE id=:id";
        $req = $pdo->prepare($sql);
        $req->execute([
            ':pass' => $new,
            ':id' => $id
        ]);

        $success = "Mot de passe modifié";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Paramètres</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial;
        }

        /* BACKGROUND */
        body {
            background: #0f172a;
            color: white;
        }

        /* TOPBAR */
        .topbar {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .topbar h2 {
            margin: 0;
        }

        .topbar small {
            color: #dbeafe;
        }

        /* BUTTONS */
        .btn {
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            margin: 5px;
            display: inline-block;
        }

        .green {
            background: #16a34a;
        }

        .red {
            background: #dc2626;
        }

        .blue {
            background: #2563eb;
        }

        /* CONTAINER */
        .container {
            width: 40%;
            margin: auto;
            margin-top: 30px;
        }

        /* CARD */
        .card {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        /* INPUT */
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        /* BUTTON */
        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        /* MESSAGE */
        .error {
            background: #dc2626;
            padding: 10px;
            border-radius: 8px;
        }

        .success {
            background: #16a34a;
            padding: 10px;
            border-radius: 8px;
        }

        /* RESPONSIVE */
        @media(max-width:768px) {

            .container {
                width: 95%;
            }

            .topbar {
                flex-direction: column;
                text-align: center;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>

</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar">

        <div>
            <h2>👤 <?= $_SESSION["nom"] ?></h2>
            <small><?= $user["email"] ?> | <?= $_SESSION["role"] ?></small>
        </div>

        <div>
            <a class="btn blue" href="Accueil.php">Accueil</a>
            <a class="btn green" href="Utilisateur.php">Utilisateurs</a>
            <a class="btn red" href="logout.php">Logout</a>
        </div>

    </div>

    <!-- CONTAINER -->
    <div class="container">

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>

        <!-- PROFIL -->
        <div class="card">

            <h3>Modifier Profil</h3>

            <form method="POST">

                <input type="text" name="nom" value="<?= $user['nom'] ?>">
                <input type="email" name="email" value="<?= $user['email'] ?>">

                <button name="update">Modifier</button>

            </form>

        </div>

        <!-- PASSWORD -->
        <div class="card">

            <h3>Changer mot de passe</h3>

            <form method="POST">

                <input type="password" name="current_pass" placeholder="Actuel">
                <input type="password" name="new_pass" placeholder="Nouveau">
                <input type="password" name="confirm_pass" placeholder="Confirmer">

                <button name="change_pass">Changer</button>

            </form>

        </div>

    </div>

</body>

</html>