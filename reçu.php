<?php
include("connect.php");

if (!isset($_GET['id'])) {
    die("ID manquant");
}

$id = $_GET['id'];

$sql = "SELECT p.*, e.Nom, e.Prenom, e.IM, n.Nom_niv,e.Mention
        FROM payement p
        JOIN etudiant e ON p.IM = e.IM
        JOIN niveau n ON e.Id_niv = n.Id_niv
        WHERE p.Id = :id";

$req = $pdo->prepare($sql);
$req->execute([':id' => $id]);

$data = $req->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Paiement introuvable");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Reçu de paiement</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
        }

        .recu {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border: 2px solid #000;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .ligne {
            margin: 10px 0;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            cursor: pointer;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            font-size: 13px;
        }

        .signature .line {
            width: 150px;
            border-bottom: 1px solid #000;
            margin-top: 30px;
        }

        .print {
            background: #1565ff;
            color: white;
            border: none;
        }

        .back {
            background: #e74c3c;
            color: white;
        }

        button {
            padding: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="recu">

        <h2>REÇU DE PAIEMENT</h2>

        <div class="ligne"><b>Réf :</b> <?= $data['Id'] ?></div>
        <div class="ligne"><b>IM :</b> <?= $data['IM'] ?></div>
        <div class="ligne"><b>Nom :</b> <?= $data['Nom'] ?> <?= $data['Prenom'] ?></div>
        <div class="ligne"><b>Niveau :</b> <?= $data['Nom_niv'] ?></div>
        <div class="ligne"><b>Mention :</b> <?= $data['Mention'] ?></div>
        <div class="ligne"><b>Date :</b> <?= $data['Date_payement'] ?></div>
        <div class="ligne"><b>Montant :</b> <?= $data['Montant_total'] ?> Ar</div>
        <div class="signature">
            <div>
                <p>Signature Étudiant</p>
                <div class="line"></div>
            </div>

            <div>
                <p>Signature Administration</p>
                <div class="line"></div>
            </div>
        </div>

    </div>
    <div class="btn-group">
        <button onclick="window.print()" class="btn print">Imprimer</button>
        <a href="Payement.php" class="btn back">Retour</a>
    </div>

</body>

</html>