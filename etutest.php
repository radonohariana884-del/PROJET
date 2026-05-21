<?php
include("connect.php");

/* =========================
   AJOUT ETUDIANT
========================= */

if(isset($_POST["Ajouter"])){

    $IM=$_POST['IM'];
    $Nom=$_POST['Nom'];
    $Prenom=$_POST['Prenom'];
    $DateNaiss=$_POST['DateNaiss'];
    $LieuNaiss=$_POST['LieuNaiss'];
    $CIN=$_POST['CIN'];
    $Email=$_POST['Email'];
    $Phone=$_POST['Phone'];
    $Id_niv=$_POST['Id_niv'];
    $Mention=$_POST['Mention'];

    $sql="INSERT INTO etudiant
    (IM,Nom,Prenom,DateNaiss,LieuNaiss,CIN,Email,Phone,Id_niv,Mention)
    VALUES
    (:IM,:Nom,:Prenom,:DateNaiss,:LieuNaiss,:CIN,:Email,:Phone,:Id_niv,:Mention)";

    $stmt=$pdo->prepare($sql);

    $stmt->execute([
        ':IM'=>$IM,
        ':Nom'=>$Nom,
        ':Prenom'=>$Prenom,
        ':DateNaiss'=>$DateNaiss,
        ':LieuNaiss'=>$LieuNaiss,
        ':CIN'=>$CIN,
        ':Email'=>$Email,
        ':Phone'=>$Phone,
        ':Id_niv'=>$Id_niv,
        ':Mention'=>$Mention
    ]);
}

/* =========================
   AFFICHAGE ETUDIANTS
========================= */

$sql2="SELECT 

e.IM,e.Nom,e.Prenom,e.DateNaiss,e.LieuNaiss,e.CIN,e.Email,e.Phone,e.Mention,
n.Nom_niv,n.Montant_paye,

COALESCE(SUM(p.Montant_total),0) AS total_paye,

(n.Montant_paye - COALESCE(SUM(p.Montant_total),0)) AS Reste,

CASE
WHEN COALESCE(SUM(p.Montant_total),0)=0 THEN 'Non payé'
WHEN COALESCE(SUM(p.Montant_total),0)<n.Montant_paye THEN 'Partiel'
ELSE 'Payé'
END AS Statut

FROM etudiant e
JOIN niveau n ON e.Id_niv=n.Id_niv
LEFT JOIN payement p ON e.IM=p.IM
GROUP BY e.IM";

$stmt=$pdo->prepare($sql2);
$stmt->execute();
$Etudiants=$stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Gestion Etudiant</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial;
}

body{
    background:#f1f5f9;
}

/* SIDEBAR */

.sidebar{
    position:fixed;
    width:240px;
    height:100vh;
    background:#0f172a;
    color:white;
    padding-top:20px;
}

.sidebar img{
    width:100px;
    height:100px;
    border-radius:50%;
    display:block;
    margin:0 auto 20px auto;
}

.sidebar a{
    display:block;
    padding:15px;
    color:white;
    text-decoration:none;
}

.sidebar a:hover{
    background:#1e293b;
}

/* CONTENU */

.content{
    margin-left:260px;
    padding:20px;
}

/* FORM */

form{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

input,select{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    padding:10px;
    background:#0ea5e9;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

/* TABLE */

table{
    width:100%;
    background:white;
    border-collapse:collapse;
}

th{
    background:#0f172a;
    color:white;
    padding:10px;
}

td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

/* STATUT */

.paye{background:green;color:white;padding:5px;border-radius:5px;}
.partiel{background:orange;color:white;padding:5px;border-radius:5px;}
.non{background:red;color:white;padding:5px;border-radius:5px;}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<center>
<img src="IMG.jpg">
</center>

<a href="Accueil.php">Accueil</a>
<a href="Etudiant.php">Etudiants</a>
<a href="Niveau.php">Niveau</a>
<a href="Payement.php">Paiement</a>

</div>

<!-- CONTENT -->

<div class="content">

<!-- FORM -->

<form method="POST">

<h3>Ajouter Etudiant</h3>

<input type="text" name="IM" placeholder="IM" required>
<input type="text" name="Nom" placeholder="Nom" required>
<input type="text" name="Prenom" placeholder="Prenom">
<input type="date" name="DateNaiss">
<input type="text" name="LieuNaiss">
<input type="text" name="CIN">
<input type="text" name="Email">
<input type="text" name="Phone">

<select name="Id_niv">
<option value="1">L1</option>
<option value="2">L2</option>
<option value="3">L3</option>
<option value="4">M1</option>
<option value="5">M2</option>
</select>

<select name="Mention">
<option>DAII</option>
<option>ICM</option>
<option>AES</option>
</select>

<button type="submit" name="Ajouter">Ajouter</button>

</form>

<!-- TABLE -->

<h3>Liste Etudiants</h3>

<table>

<tr>
<th>IM</th>
<th>Nom</th>
<th>Prenom</th>
<th>Niveau</th>
<th>Total</th>
<th>Payé</th>
<th>Reste</th>
<th>Statut</th>
</tr>

<?php foreach($Etudiants as $e){ ?>

<tr>
<td><?=$e['IM']?></td>
<td><?=$e['Nom']?></td>
<td><?=$e['Prenom']?></td>
<td><?=$e['Nom_niv']?></td>
<td><?=$e['Montant_paye']?></td>
<td><?=$e['total_paye']?></td>
<td><?=$e['Reste']?></td>

<td>
<?php
if($e['Statut']=="Payé"){
echo "<span class='paye'>Payé</span>";
}
elseif($e['Statut']=="Partiel"){
echo "<span class='partiel'>Partiel</span>";
}
else{
echo "<span class='non'>Non payé</span>";
}
?>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>