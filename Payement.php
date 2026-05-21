<?php 
include("connect.php");
if(isset($_GET['delete']))
{
    $id = $_GET['delete'];

    $sql = "DELETE FROM payement WHERE Id = :id";
    $req = $pdo->prepare($sql);
    $req->execute([
        ':id' => $id
    ]);

    header("Location: Payement.php");
}
$modifier = false;
if(isset($_GET['id']))
{
    $modifier = true;

    $id = $_GET['id'];

    $sql = "SELECT * FROM payement WHERE Id = :id";
    $req = $pdo->prepare($sql);
    $req->execute([':id' => $id]);

    $data = $req->fetch(PDO::FETCH_ASSOC);
}
if(isset($_POST["Ajouter"])){
    
     $IM=$_POST['IM'];
     $Date_payement=$_POST['Date_payement'];
     $Montant_total=$_POST['Montant_total'];

    $Sql= "INSERT INTO payement(IM,Date_payement,Montant_total) VALUES(:IM,:Date_payement,:Montant_total)";
    $verifie=$pdo->prepare($Sql);
    $verifie->execute(
            [
                
                ':IM'=>$IM,
                ':Date_payement'=>$Date_payement,
                ':Montant_total'=> $Montant_total
            ] );
}
if(isset($_POST["Modifier"]))
{
    $id_old = $_POST['id_old'];
    $Id = $_POST['Id'];
    $IM = $_POST['IM'];
    $Date_payement = $_POST['Date_payement'];
    $Montant_total = $_POST['Montant_total'];

    $sql = "UPDATE payement SET
            Id = :Id,
            IM = :IM,
            Date_payement = :Date_payement,
            Montant_total = :Montant_total
            WHERE Id = :id_old";

    $req = $pdo->prepare($sql);
    $req->execute([
        ':Id' => $Id,
        ':IM' => $IM,
        ':Date_payement' => $Date_payement,
        ':Montant_total' => $Montant_total,
        ':id_old' => $id_old
    ]);

    header("Location: Payement.php");
}

 //affichage de tout la liste
    
 $search = $_GET['search'] ?? '';

$Sql2 = "SELECT

p.Id,
p.IM,
e.Nom,
e.Prenom,
e.Mention,
n.Nom_niv,
p.Date_payement,
p.Montant_total

FROM payement p

JOIN etudiant e
ON p.IM = e.IM

JOIN niveau n
ON e.Id_niv = n.Id_niv

WHERE 1=1
";

$params = [];

if($search != ''){

    $Sql2 .= " AND p.IM LIKE :search ";

    $params[':search'] = "%$search%";
}
          $stmt=$pdo->prepare ($Sql2);
         $stmt->execute($params);
         $Payement=$stmt->fetchAll();
     
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="Payement.css">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Payement</title>
</head>
<body>
    <!-- SIDEBAR -->
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
            <a href="logout.php" onclick="return confirm('Se déconnecter ?')">
                  <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </div>
    <!-- CONTENU -->
    <div class="content">

                <div class="topbar">

                    <div class="title">
                        <h1>Gestion des Paiements</h1>
                        
                    </div>

                

                </div>
    
             <div class="form-card">

                    <div class="card-header">
                        <h2>
                            <?= $modifier ? 'Modifier Paiement' : 'Ajouter Paiement' ?>
                        </h2>

                
                    </div>

                    <form action="" method="POST">
                        <div class="form-grid">

                        

                        <div class="input-group">

                            <label>IM</label>

                            <input type="text"
                            name="IM"
                            value="<?= isset($data) ? $data['IM'] : '' ?>">

                        </div>

                        <div class="input-group">

                            <label>Date de paiement</label>

                            <input type="date"
                            name="Date_payement"
                            value="<?= isset($data) ? $data['Date_payement'] : '' ?>"
                            required>

                        </div>

                    <div class="input-group">

                        <label>Montant total</label>

                        <input type="text"
                        name="Montant_total"
                        value="<?= isset($data) ? $data['Montant_total'] : '' ?>"
                        required>

                    </div>

                    <input type="hidden"
                    name="id_old"
                    value="<?= isset($data) ? $data['Id'] : '' ?>">
                    </div>

                    <button
                        type="submit"
                        class="btn-submit"
                        name="<?= $modifier ? 'Modifier' : 'Ajouter' ?>">

                        <?= $modifier ? 'Modifier Paiement' : 'Ajouter Paiement' ?>

                    </button>

                 </form>

            </div>
        <?php if(!empty($Payement)){?> 
        <div class="table-card">

    <div class="table-header">

    <h2>Liste des Paiements</h2>

    <form method="GET" class="search-box">

        <input type="text"
        name="search"
        placeholder="Rechercher IM..."
        value="<?= $_GET['search'] ?? '' ?>">

        <button type="submit">
            🔍
        </button>

        <a href="Payement.php">
            ↺
        </a>

    </form>

</div>

        <div class="table-container">

            <table>

                    <thead>
                        <tr>
                            <th>IM</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Niveau</th>
                            <th>Mention</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach($Payement as $p){ ?>

                        <tr>
                           <td><?= $p['IM'] ?></td>
                            <td><?= $p['Nom'] ?></td>
                            <td><?= $p['Prenom'] ?></td>
                            <td><?= $p['Nom_niv'] ?></td>
                            <td><?= $p['Mention'] ?></td>
                            <td><?= $p['Date_payement'] ?></td>
                            <td><?= $p['Montant_total'] ?> Ar</td>

                            <td class="actions">

                                <a href="Payement.php?id=<?= $p['Id'] ?>" class="icon edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <a href="Payement.php?delete=<?= $p['Id'] ?>"
                                class="icon delete"
                                onclick="return confirm('Supprimer ?')">
                                <i class="fa-solid fa-trash"></i>
                                </a>
                                <a href="reçu.php?id=<?= $p['Id'] ?>" target="_blank" class ="icon recu">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>

                            </td>
                        </tr>

                        <?php } ?>

                    </tbody>

        </table>

    </div>

</div>
        <?php }?>
        </div>
    </div>

</body>
</html>