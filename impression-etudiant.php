<?php
include("connect.php");

$niveau = $_GET['niveau'] ?? 'all';
$mention = $_GET['mention'] ?? 'all';
$sexe = $_GET['sexe'] ?? 'all';

$sql = "SELECT e.*, n.Nom_niv
        FROM etudiant e
        JOIN niveau n ON e.Id_niv = n.Id_niv
        WHERE 1=1";

$params = [];

if ($niveau != "all") {
    $sql .= " AND n.Nom_niv = :niveau";
    $params[':niveau'] = $niveau;
}

if ($mention != "all") {
    $sql .= " AND e.Mention = :mention";
    $params[':mention'] = $mention;
}

if ($sexe != "all") {
    $sql .= " AND e.Sexe = :sexe";
    $params[':sexe'] = $sexe;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title></title>

    <style>
        body {
            font-family: Arial;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background: #ddd;
        }

        .header {
            text-align: center;
        }

        @media print {

            .btn-retour {
                display: none !important;
            }

            .btn-print {
                display: none !important;
            }
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            /* gauche + droite */
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-retour {
            padding: 10px 16px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-retour:hover {
            background: #cc3e12;
        }

        .btn-print {
            padding: 10px 16px;
            background: #1565ff;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-print:hover {
            background: #060684;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>LISTE ÉTUDIANTS EMIT</h2>
        <p>
            Niveau: <?= $niveau ?> |
            Mention: <?= $mention ?> |
            Sexe: <?= $sexe ?>
        </p>
    </div>

    <table>
        <tr>
            <th>IM</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Niveau</th>
            <th>Mention</th>
            <th>Sexe</th>
        </tr>

        <?php foreach ($data as $e) { ?>
            <tr>
                <td><?= $e['IM'] ?></td>
                <td><?= $e['Nom'] ?></td>
                <td><?= $e['Prenom'] ?></td>
                <td><?= $e['Nom_niv'] ?></td>
                <td><?= $e['Mention'] ?></td>
                <td><?= $e['Sexe'] ?></td>
            </tr>
        <?php } ?>

    </table>

    <br>
    <div class="action-bar">

        <a href="Etudiant.php" class="btn-retour">
            ⬅ Retour
        </a>

        <button onclick="window.print()" class="btn-print">
            🖨 Imprimer
        </button>

    </div>

</body>

</html>