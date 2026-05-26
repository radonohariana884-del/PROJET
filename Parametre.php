<?php
include("connect.php");
session_start();

/* SECURITE LOGIN */
if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit();
}
$edit = false;
$data = [];
$users = [];
/* USER CONNECTE */
$id_user = $_SESSION["id_user"];

$sql = "SELECT * FROM utilisateurs WHERE id = :id";
$req = $pdo->prepare($sql);
$req->execute([':id' => $id_user]);
$user = $req->fetch(PDO::FETCH_ASSOC);

/* ADMIN ONLY */
if ($_SESSION["role"] == "admin") {

    if (isset($_GET['delete'])) {
        $sql = "DELETE FROM utilisateurs WHERE id = :id";
        $req = $pdo->prepare($sql);
        $req->execute([':id' => $_GET['delete']]);
        header("Location: Utilisateur.php");
        exit();
    }

    $edit = false;
    $data = [];

    if (isset($_GET['edit'])) {
        $edit = true;
        $sql = "SELECT * FROM utilisateurs WHERE id = :id";
        $req = $pdo->prepare($sql);
        $req->execute([':id' => $_GET['edit']]);
        $data = $req->fetch(PDO::FETCH_ASSOC);
    }

    if (isset($_POST['Ajouter'])) {
        $sql = "INSERT INTO utilisateurs(nom,email,mot_de_passe,role)
                VALUES(:nom,:email,:mot_de_passe,:role)";
        $req = $pdo->prepare($sql);
        $req->execute([
            ':nom' => $_POST['nom'],
            ':email' => $_POST['email'],
            ':mot_de_passe' => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
            ':role' => $_POST['role']
        ]);

        header("Location: Utilisateur.php");
        exit();
    }

    if (isset($_POST['Modifier'])) {
        $sql = "UPDATE utilisateurs SET nom=:nom,email=:email,role=:role WHERE id=:id";
        $req = $pdo->prepare($sql);
        $req->execute([
            ':nom' => $_POST['nom'],
            ':email' => $_POST['email'],
            ':role' => $_POST['role'],
            ':id' => $_POST['id']
        ]);

        header("Location: Utilisateur.php");
        exit();
    }

    $users = $pdo->query("SELECT * FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Utilisateurs</title>

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

        .header .btn {
            padding: 8px 12px;
            margin: 5px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
        }

        .btn-back {
            background: #16a34a;
        }

        .btn-pay {
            background: #7c3aed;
        }

        .btn-logout {
            background: #dc2626;
        }

        /* CONTAINER */
        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        /* CARD */
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* INPUT */
        input,
        select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        /* BUTTON */
        button {
            background: #2563eb;
            display: block;
            width: 30%;
            text-align: center;
            margin-top: 8px;
            padding: 10px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .AA {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        button:hover {
            background: #1d4ed8;
        }

        .btn-cancel {
            background: #ef4444;
            display: block;
            width: 30%;
            text-align: center;
            margin-top: 8px;
            padding: 10px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            background: #dc2626;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #2563eb;
            color: white;
            padding: 10px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        /* ACTION */
        a {
            text-decoration: none;
            margin: 5px;
        }

        .edit {
            color: green;
        }

        .delete {
            color: red;
        }

        /* RESPONSIVE MOBILE */
        @media (max-width: 768px) {

            .header {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .bt {
                margin-top: 20px;
            }

            .header .btn {
                width: 100%;
                text-align: center;
            }

            .container {
                width: 100%;
                padding: 10px;
            }

            .card {
                padding: 15px;
            }

            table {
                font-size: 14px;
            }
        }

        /* VERY SMALL SCREEN */
        @media (max-width: 480px) {
            .header {
                padding: 10px;
            }

            .header .btn {
                font-size: 14px;
                padding: 10px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>

</head>

<body>

    <!-- HEADER -->
    <div class="header">

        <div>
            👤 <?= $_SESSION["nom"] ?> | 🛡️ <?= $_SESSION["role"] ?>
        </div>

        <div class="bt">
            <a href="Accueil.php" class="btn btn-back">⬅ Accueil</a>
            <a href="Utilisateur.php" class="btn btn-pay">👥 Utilisateurs</a>
            <a href="logout.php" class="btn btn-logout"
                onclick="return confirm('Déconnexion ?')">
                Logout
            </a>

        </div>

    </div>

    <div class="container">

        <?php if ($_SESSION["role"] == "admin") { ?>

            <!-- FORMULAIRE -->
            <div class="card">
                <h3><?= $edit ? "Modifier utilisateur" : "Ajouter utilisateur" ?></h3>

                <form method="POST">

                    <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">

                    <input type="text" name="nom" placeholder="Nom" value="<?= $data['nom'] ?? '' ?>" required>

                    <input type="email" name="email" placeholder="Email" value="<?= $data['email'] ?? '' ?>" required>

                    <input type="password" name="mot_de_passe" placeholder="Mot de passe">

                    <select name="role">
                        <option value="admin">Admin</option>
                        <option value="caissier">Caissier</option>
                    </select>
                    <div class="AA">
                        <button type="submit" name="<?= $edit ? 'Modifier' : 'Ajouter' ?>">
                            <?= $edit ? 'Modifier' : 'Ajouter' ?>
                        </button>
                        <a href="Utilisateur.php" class="btn-cancel">
                            Annuler
                        </a>
                    </div>

                </form>
            </div>

            <!-- TABLE -->
            <div class="card">
                <h3>Liste utilisateurs</h3>

                <div class="table-wrapper">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Action</th>
                        </tr>

                        <?php foreach ($users as $u) { ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= $u['nom'] ?></td>
                                <td><?= $u['email'] ?></td>
                                <td><?= $u['role'] ?></td>
                                <td>
                                    <a class="edit" href="Utilisateur.php?edit=<?= $u['id'] ?>">Modifier</a>
                                    <a class="delete" href="Utilisateur.php?delete=<?= $u['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php } ?>

                    </table>
                </div>
            </div>

        <?php } ?>

    </div>

</body>

</html>