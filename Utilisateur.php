<?php
include("connect.php");
session_start();

/* SECURITE */
if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["id_user"];

/* GET USER */
$sql = "SELECT * FROM utilisateurs WHERE id = :id";
$req = $pdo->prepare($sql);
$req->execute([':id' => $id]);
$user = $req->fetch(PDO::FETCH_ASSOC);

/* UPDATE PROFIL */
if (isset($_POST["update"])) {

    $nom = $_POST["nom"];
    $email = $_POST["email"];

    $sql = "UPDATE utilisateurs SET nom=:nom, email=:email WHERE id=:id";
    $req = $pdo->prepare($sql);
    $req->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':id' => $id
    ]);

    $_SESSION["nom"] = $nom;

    header("Location: Parametre.php");
    exit();
}

/* CHANGE PASSWORD SECURE */
if (isset($_POST["change_pass"])) {

    $current = $_POST["current_pass"];
    $new = $_POST["new_pass"];
    $confirm = $_POST["confirm_pass"];

    // Vérification ancien mot de passe
    if (!password_verify($current, $user["mot_de_passe"])) {

        $error = "Mot de passe actuel incorrect";
    } elseif ($new != $confirm) {

        $error = "Les mots de passe ne correspondent pas";
    } else {

        // Hash nouveau mot de passe
        $newHash = password_hash($new, PASSWORD_DEFAULT);

        $sql = "UPDATE utilisateurs 
                SET mot_de_passe = :pass 
                WHERE id = :id";

        $req = $pdo->prepare($sql);

        $req->execute([
            ':pass' => $newHash,
            ':id' => $id
        ]);

        $success = "Mot de passe modifié avec succès";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Paramètres</title>

    <style>
        body {
            font-family: Arial;
            background: #f1f5f9;
            margin: 0;
        }

        /* HEADER */
        .header {
            background: #1d4ed8;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .accueil-btn {
            color: white;
            text-decoration: none;
            margin-left: 10px;
            background: #16a34a;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .user-btn {
            background: #7c3aed;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            margin-left: 10px;
            transition: 0.3s;
        }

        .user-btn:hover {
            background: #6d28d9;
        }

        /* CONTAINER */
        .container {
            width: 90%;
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }

        /* CARD */
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* USER INFO */
        .user-box {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .badge {
            background: #2563eb;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }

        /* INPUT */
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background: #2563eb;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-cancel {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #ef4444;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            background: #dc2626;
        }

        .logout-btn {
            background: #dc2626;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            margin-left: 10px;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #b91c1c;
        }


        button:hover {
            background: #1d4ed8;
        }

        /* MESSAGE */
        .error {
            color: red;
        }

        .success {
            color: green;
        }

        /* RESPONSIVE */
        @media(max-width:600px) {
            .header {
                flex-direction: column;
                padding: 20px;
                text-align: center;
            }

            .bt {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div>
            👤 <?= $_SESSION["nom"] ?> |
            🛡️ <?= $_SESSION["role"] ?>
        </div>

        <div class="bt">

            <a href="Accueil.php" class="accueil-btn">
                ⬅ Accueil
            </a>

            <?php if ($_SESSION["role"] == "admin") { ?>

                <a href="Parametre.php" class="user-btn">
                    ➕ Ajouter Utilisateur
                </a>

            <?php } ?>

            <a href="logout.php"
                class="logout-btn"
                onclick="return confirm('Se déconnecter ?')">

                Logout

            </a>

        </div>
    </div>

    <div class="container">

        <h2>⚙️ Paramètres du compte</h2>

        <!-- INFO USER -->
        <div class="card user-box">
            <div>
                <h3>Profil connecté</h3>
                <p><b>Nom:</b> <?= $user["nom"] ?></p>
                <p><b>Email:</b> <?= $user["email"] ?></p>
            </div>

            <div>
                <span class="badge"><?= $user["role"] ?></span>
            </div>
        </div>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

        <!-- PROFIL -->
        <div class="card">
            <h3>Modifier Profil</h3>

            <form method="POST">
                <input type="text" name="nom" value="<?= $user['nom'] ?>" required>
                <input type="email" name="email" value="<?= $user['email'] ?>" required>

                <button name="update">Modifier</button>

                <a href="Utilisateur.php" class="btn-cancel">Annuler</a>
            </form>
        </div>

        <!-- PASSWORD -->
        <div class="card">
            <h3>Changer mot de passe</h3>

            <form method="POST">
                <input type="password" name="current_pass" placeholder="Ancien mot de passe" required>
                <input type="password" name="new_pass" placeholder="Nouveau mot de passe" required>
                <input type="password" name="confirm_pass" placeholder="Confirmer" required>

                <button name="change_pass">Changer</button>

                <a href="Utilisateur.php" class="btn-cancel">Annuler</a>
            </form>
        </div>

    </div>

</body>

</html>