<?php

include("connect.php");

// graphique
$sqlStat = "
SELECT 
SUM(CASE WHEN Statut='Payé' THEN 1 ELSE 0 END) AS paye,
SUM(CASE WHEN Statut='Partiel' THEN 1 ELSE 0 END) AS partiel,
SUM(CASE WHEN Statut='Non payé' THEN 1 ELSE 0 END) AS nonpaye
FROM (
    SELECT 
    CASE
        WHEN COALESCE(SUM(p.Montant_total),0) = 0 THEN 'Non payé'
        WHEN COALESCE(SUM(p.Montant_total),0) < n.Montant_paye THEN 'Partiel'
        ELSE 'Payé'
    END AS Statut
    FROM etudiant e
    JOIN niveau n ON e.Id_niv=n.Id_niv
    LEFT JOIN payement p ON e.IM=p.IM
    GROUP BY e.IM
) t
";

$stmtStat = $pdo->prepare($sqlStat);
$stmtStat->execute();
$stat = $stmtStat->fetch();


// retour au login
session_start();

if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit();
}


/*    NOMBRE ETUDIANTS */

$sql1 = "SELECT COUNT(*) AS totalEtudiant FROM etudiant";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute();
$etudiant = $stmt1->fetch();

/* TOTAL PAIEMENT*/

$sql2 = "SELECT SUM(Montant_total) AS totalPaiement FROM payement";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute();
$paiement = $stmt2->fetch();

/* TOTAL RESTE */

$sql3 = "
SELECT 
SUM(n.Montant_paye - COALESCE(p.total_paye,0)) AS resteTotal

FROM etudiant e

JOIN niveau n 
ON e.Id_niv = n.Id_niv

LEFT JOIN
(
    SELECT IM,
    SUM(Montant_total) AS total_paye
    FROM payement
    GROUP BY IM
) p

ON e.IM = p.IM
";

$stmt3 = $pdo->prepare($sql3);
$stmt3->execute();
$reste = $stmt3->fetch();

/*  NOMBRE PAIEMENT */

$sql4 = "SELECT COUNT(*) AS nombrePaiement FROM payement";
$stmt4 = $pdo->prepare($sql4);
$stmt4->execute();
$nombrePaiement = $stmt4->fetch();


// liste derniere payement
$Sql5 = "SELECT 

e.IM,
e.Nom,
e.Prenom,
e.Mention,
n.Nom_niv,
p.Montant_total



FROM payement p

JOIN etudiant e
ON p.IM = e.IM

JOIN niveau n
ON e.Id_niv = n.Id_niv

ORDER BY p.DateCreation DESC, p.Id DESC

LIMIT 3
";
$stmt = $pdo->prepare($Sql5);
$stmt->execute();
$Derniere = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="Accueil.css?v=<?= time() ?>">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>Accueil</title>
</head>

<body>
    <div class="sidebar">
        <center>
            <img src="IMG.jpg">
        </center>
        <div class="menu">
            <a href="Accueil.php">
                <i class="fa-solid fa-house"></i> Accueil
            </a>

            <a href="Etudiant.php">
                <i class="fa-solid fa-user-graduate"></i> Étudiants
            </a>

            <a href="Payement.php">
                <i class="fa-solid fa-money-bill-transfer"></i> Paiement
            </a>

            <a href="Utilisateur.php">
                <i class="fa-solid fa-users-gear"></i> Utilisateurs
            </a>

            <a href="Parametre.php">
                <i class="fa-solid fa-gear"></i> Paramètres
            </a>

            <a href="logout.php" onclick="return confirm('Se déconnecter ?')">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </div>
    <!-- CONTENU -->

    <div class="content">
        <div class="topbar">

            <h1>Tableau de Bord</h1>

            <div class="admin">
                👨‍💼 Administrateur
            </div>

        </div>

        <div class="titre">

            <h1>Système de gestion des frais de scolarité de l'EMIT</h1>


        </div>

        <!-- CARTES -->

        <div class="cards">

            <!-- ETUDIANTS -->

            <div class="card">

                <i class="fa fa-user-graduate"></i>

                <h2>
                    <?= $etudiant['totalEtudiant'] ?>
                </h2>

                <p>Etudiants</p>

            </div>

            <!-- TOTAL PAIEMENT -->

            <div class="card">

                <i class="fa fa-money-bill-wave"></i>

                <h2>
                    <?= $paiement['totalPaiement'] ?> Ar
                </h2>

                <p>Total Paiement</p>

            </div>

            <!-- RESTE -->

            <div class="card">

                <i class="fa fa-clock"></i>

                <h2>
                    <?= $reste['resteTotal'] ?> Ar
                </h2>

                <p>Reste à payer</p>

            </div>

            <!-- NOMBRE PAIEMENT -->

            <div class="card">

                <i class="fa fa-file-invoice-dollar"></i>

                <h2>
                    <?= $nombrePaiement['nombrePaiement'] ?>
                </h2>

                <p>Paiements effectués</p>

            </div>

        </div>
        <!-- statistique -->
        <div class="graph-box">

            <h2>Statistique Paiement par Niveau</h2>

            <canvas id="myChart" height="120"></canvas>

        </div>
        <div class="table-box">
            <!-- graphique rond -->
            <div class="charts">

                <!-- BAR CHART -->
                <div class="chart-box">
                    <canvas id="barChart"></canvas>
                </div>

                <!-- DONUT CHART -->
                <div class="chart-box">
                    <canvas id="donutChart"></canvas>
                </div>

            </div>
            <h2>Derniers Paiements</h2>

            <table>

                <tr>
                    <th>IM</th>
                    <th>Nom</th>
                    <th>Pénom</th>
                    <th>Mention</th>
                    <th>Niveau</th>
                    <th>Montant</th>

                </tr>
                <?php foreach ($Derniere as $e) { ?>
                    <tr>
                        <td><?= $e['IM'] ?></td>
                        <td><?= $e['Nom'] ?></td>
                        <td><?= $e['Prenom'] ?></td>
                        <td><?= $e['Mention'] ?></td>
                        <td><?= $e['Nom_niv'] ?></td>
                        <td><?= $e['Montant_total'] ?></td>

                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <script>
        const barChart = new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: ['Etudiants', 'Paiements', 'Reste'],
                datasets: [{
                    label: 'Statistiques',
                    data: [
                        <?= $etudiant['totalEtudiant'] ?>,
                        <?= $paiement['totalPaiement'] ?>,
                        <?= $reste['resteTotal'] ?>
                    ],
                    backgroundColor: ['#2563eb', '#22c55e', '#f59e0b']
                }]
            }
        });
        const donutChart = new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: ['Payé', 'Partiel', 'Non payé'],
                datasets: [{
                    data: [
                        <?= $stat['paye'] ?>,
                        <?= $stat['partiel'] ?>,
                        <?= $stat['nonpaye'] ?>
                    ],
                    backgroundColor: ['#22c55e', '#f59e0b', '#ef4444']
                }]
            }
        });
    </script>
</body>

</html>