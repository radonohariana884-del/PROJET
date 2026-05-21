<?php
    include ("connect.php");
if(isset($_GET['delete']))
{
    $IM = $_GET['delete'];

    $sql = "DELETE FROM etudiant WHERE IM = :IM";
    $req = $pdo->prepare($sql);
    $req->execute([
        ':IM' => $IM
    ]);

    header("Location: Etudiant.php");
}
    $modifier = false;

if(isset($_GET['IM']))
{
    $modifier = true;

    $IMmodifier = $_GET['IM'];

    $sqlModifier = "SELECT * FROM etudiant WHERE IM = :IM";

    $reqModifier = $pdo->prepare($sqlModifier);

    $reqModifier->execute([
        ':IM' => $IMmodifier
    ]);

    $dataModifier = $reqModifier->fetch(PDO::FETCH_ASSOC);
}

    if (isset ($_POST["Ajouter"])){
        $IM=$_POST['IM'];
        $Nom=$_POST['Nom'];
        $Prenom=$_POST['Prenom'];
        $DateNaiss=$_POST['DateNaiss'];
        $Sexe=$_POST['Sexe'];
        $LieuNaiss=$_POST['LieuNaiss'];
        $CIN=$_POST['CIN'];
        $Email=$_POST['Email'];
        $Phone=$_POST['Phone'];
        $Id_niv=$_POST['Id_niv'];
        $Mention=$_POST['Mention'];

        $Sql="INSERT INTO etudiant(IM,Nom,Prenom,DateNaiss,Sexe,LieuNaiss,CIN,Email,Phone,Id_niv,Mention) VALUES(:IM,:Nom,:Prenom,:DateNaiss,:Sexe,:LieuNaiss,:CIN,:Email,:Phone,:Id_niv,:Mention)";
        $verifie=$pdo->prepare($Sql);
        $verifie->execute(
            [
                ':IM'=>$IM,
                ':Nom'=> $Nom,
                ':Prenom'=> $Prenom,
                ':DateNaiss'=> $DateNaiss,
                ':Sexe'=> $Sexe,
                ':LieuNaiss'=>$LieuNaiss,
                ':CIN'=>$CIN,
                ':Email'=> $Email,
                ':Phone'=> $Phone,
                ':Id_niv'=>$Id_niv,
                ':Mention'=>$Mention
            ]
        );
    }
    if(isset($_POST["Modifier"]))
{
    $IM=$_POST['IM'];
    $Nom=$_POST['Nom'];
    $Prenom=$_POST['Prenom'];
    $DateNaiss=$_POST['DateNaiss'];
     $Sexe=$_POST['Sexe'];
    $LieuNaiss=$_POST['LieuNaiss'];
    $CIN=$_POST['CIN'];
    $Email=$_POST['Email'];
    $Phone=$_POST['Phone'];
    $Id_niv=$_POST['Id_niv'];
    $Mention=$_POST['Mention'];

    $sqlUpdate = "UPDATE etudiant SET

    Nom = :Nom,
    Prenom = :Prenom,
    DateNaiss = :DateNaiss,
    Sexe=:Sexe,
    LieuNaiss = :LieuNaiss,
    CIN = :CIN,
    Email = :Email,
    Phone = :Phone,
    Id_niv = :Id_niv,
    Mention = :Mention

    WHERE IM = :IM";

    $reqUpdate = $pdo->prepare($sqlUpdate);

    $reqUpdate->execute([
        ':IM'=>$IM,
        ':Nom'=>$Nom,
        ':Prenom'=>$Prenom,
        ':DateNaiss'=>$DateNaiss,
        ':Sexe'=>$Sexe,
        ':LieuNaiss'=>$LieuNaiss,
        ':CIN'=>$CIN,
        ':Email'=>$Email,
        ':Phone'=>$Phone,
        ':Id_niv'=>$Id_niv,
        ':Mention'=>$Mention
    ]);

    header("Location: Etudiant.php");
}

    // recherche
$motcle = $_GET['search'] ?? '';

if(!empty($motcle)){
    $Sql3="SELECT e.IM,e.Nom,e.Prenom,e.DateNaiss,e.Sexe,e.LieuNaiss,e.CIN,e.Email,e.Phone,e.Mention,
     n.Nom_niv,n.Montant_paye,
     COALESCE (SUM(p.Montant_total),0) AS total_paye,
     (n.Montant_paye - COALESCE (SUM(p.Montant_total),0))AS Reste,
     CASE

WHEN COALESCE(SUM(p.Montant_total),0) = 0
THEN 'Non payé'

WHEN COALESCE(SUM(p.Montant_total),0) < n.Montant_paye
THEN 'Partiel'

WHEN COALESCE(SUM(p.Montant_total),0) >= n.Montant_paye
THEN 'Payé'

END AS Statut

     FROM etudiant e 
     JOIN niveau n ON e.Id_niv=n.Id_niv 
    LEFT JOIN payement p ON e.IM=p.IM 
    WHERE e.Nom LIKE :motcle
    OR e.Prenom LIKE :motcle
    OR e.IM LIKE :motcle
   GROUP BY e.IM
    
    ";
    
    $requete=$pdo->prepare($Sql3);
    $requete->execute([
        'motcle'=> "%$motcle%"
    ]);
    $resultats= $requete->fetchAll();
    
   }

$tri = $_GET['tri'] ?? '';

$orderBy = " ";

if($tri == "alpha")
{
    $orderBy = " ORDER BY e.Nom ASC ";
}
elseif($tri == "im")
{
    $orderBy = " ORDER BY e.IM ASC ";
}
elseif($tri == "age")
{
    $orderBy = " ORDER BY e.DateNaiss DESC ";
}

//affichage de tout la liste
    
$search = $_GET['search'] ?? '';
$sexe = $_GET['sexe'] ?? 'all';
$niveau = $_GET['niveau'] ?? 'all';
$mention = $_GET['mention'] ?? 'all';
$statut = $_GET['statut'] ?? 'all';

$Sql2 = "SELECT 

e.IM,
e.Nom,
e.Prenom,
e.DateNaiss,
e.Sexe,
e.LieuNaiss,
e.CIN,
e.Phone,
e.Mention,

n.Nom_niv,
n.Montant_paye,

COALESCE(SUM(p.Montant_total),0) AS total_paye,

(n.Montant_paye - COALESCE(SUM(p.Montant_total),0)) AS Reste,

CASE

WHEN COALESCE(SUM(p.Montant_total),0) = 0
THEN 'Non payé'

WHEN COALESCE(SUM(p.Montant_total),0) < n.Montant_paye
THEN 'Partiel'

WHEN COALESCE(SUM(p.Montant_total),0) >= n.Montant_paye
THEN 'Payé'

END AS Statut

FROM etudiant e

JOIN niveau n
ON e.Id_niv = n.Id_niv

LEFT JOIN payement p
ON e.IM = p.IM

WHERE 1=1
";

$params = [];

if($search != ''){
    $Sql2 .= " AND (
        e.Nom LIKE :search
        OR e.Prenom LIKE :search
        OR e.IM LIKE :search
    )";

    $params[':search'] = "%$search%";
}

if($sexe != 'all'){
    $Sql2 .= " AND e.Sexe = :sexe";

    $params[':sexe'] = $sexe;
}

if($niveau != 'all'){
    $Sql2 .= " AND n.Nom_niv = :niveau";

    $params[':niveau'] = $niveau;
}

if($mention != 'all'){
    $Sql2 .= " AND e.Mention = :mention";

    $params[':mention'] = $mention;
}
if($statut != 'all'){

    if($statut == 'Payé'){
        $Sql2 .= "
        GROUP BY e.IM
        HAVING total_paye >= n.Montant_paye
        ";
    }

    elseif($statut == 'Partiel'){
        $Sql2 .= "
        GROUP BY e.IM
        HAVING total_paye > 0
        AND total_paye < n.Montant_paye
        ";
    }

    elseif($statut == 'Non payé'){
        $Sql2 .= "
        GROUP BY e.IM
        HAVING total_paye = 0
        ";
    }

}
else{
    $Sql2 .= " GROUP BY e.IM ";
}

$Sql2 .= $orderBy;

         $stmt=$pdo->prepare ($Sql2);
         $stmt->execute($params);
         $Etudiant=$stmt->fetchAll();
         
if(!empty($resultats)&& isset($resultats)){
    $Etudiant = $resultats;
}
   

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="Etudiant.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion etudiant</title>
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
            <a href="logout.php" onclick="return confirm('Se déconnecter ?')">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </div>
    
    <div class="Formulaire">
        <div class="titre">
            <h1>Gestion etudiant</h1>
        </div>
    <form action="" method="POST" class="formul" >
        <div class= "input-box">

        <label for="">IM :</label>
        <input type="text"
        name="IM"value="<?= isset($dataModifier)
            ? $dataModifier['IM']
            : '' ?>"required><br><br>
        </div>
        <div class= "input-box">
        
        <label for="">Nom :</label>
        <input type="text"
        name="Nom"value="<?= isset($dataModifier)
        ? $dataModifier['Nom']
        : '' ?>"
        required><br><br>
        </div>
        <div class= "input-box">

        <label for="">Prenom :</label>
      <input type="text"
        name="Prenom"
        value="<?= isset($dataModifier)
        ? $dataModifier['Prenom']
        : '' ?>"
        ><br><br>
        </div>
        <div class= "input-box">

        <label for="">Date de Naissance :</label>
       <input type="date"
        name="DateNaiss"
        value="<?= isset($dataModifier)
        ? $dataModifier['DateNaiss']
        : '' ?>"
        required><br><br>
        </div>
        <div class="input-box">

<label for="">Sexe :</label>

<select name="Sexe" required>

<option value="M"
<?= (isset($dataModifier)
&& $dataModifier['Sexe']=="M")
? 'selected'
: '' ?>>
Masculin
</option>

<option value="F"
<?= (isset($dataModifier)
&& $dataModifier['Sexe']=="F")
? 'selected'
: '' ?>>
Féminin
</option>

</select>

</div>
        <div class= "input-box">
        <label for="">Lieu de Naissance :</label>
       <input type="text"
        name="LieuNaiss"
        value="<?= isset($dataModifier)
        ? $dataModifier['LieuNaiss']
        : '' ?>"
        required><br><br>
        </div>

        <div class= "input-box">
         <label for="">CIN :</label>
        <input type="text"
        name="CIN"
        value="<?= isset($dataModifier)
        ? $dataModifier['CIN']
        : '' ?>"><br><br>
        </div>

        <div class= "input-box">
         <label for="">Email :</label>
        <input type="text"
        name="Email"
        value="<?= isset($dataModifier)
        ? $dataModifier['Email']
        : '' ?>"><br><br>
        </div>

        <div class= "input-box">
         <label for="">Phone :</label>
        <input type="text"
        name="Phone"
        value="<?= isset($dataModifier)
        ? $dataModifier['Phone']
        : '' ?>"><br><br>
        </div>
        <div class= "input-box">
        <label for="">Niveau :</label>
        <select name="Id_niv">


<option value="1"
<?= (isset($dataModifier) && $dataModifier['Id_niv']==1)
? 'selected'
: '' ?>>
L1
</option>
<option value="2"
<?= (isset($dataModifier) && $dataModifier['Id_niv']==2)
? 'selected'
: '' ?>>
L2
</option>
<option value="3"
<?= (isset($dataModifier) && $dataModifier['Id_niv']==3)
? 'selected'
: '' ?>>
L2 Misabaka
</option>
<option value="4"
<?= (isset($dataModifier) && $dataModifier['Id_niv']==4)
? 'selected'
: '' ?>>
L3
</option>
<option value="5"
<?= (isset($dataModifier) && $dataModifier['Id_niv']==5)
? 'selected'
: '' ?>>
L3 Misabaka
</option>
<option value="6"
<?= (isset($dataModifier) && $dataModifier['Id_niv']==6)
? 'selected'
: '' ?>>
M1
</option>
<option value="7"
<?= (isset($dataModifier) && $dataModifier['Id_niv']==7)
? 'selected'
: '' ?>>
M2
</option>
</select> <br><br>
        </div>
        <div class= "input-box">

        <label for="">Mention :</label>
       <select name="Mention">
<option value="DAII"
<?= (isset($dataModifier)
&& $dataModifier['Mention']=="DAII")
? 'selected'
: '' ?>>
DAII
</option>
<option value="ICM"
<?= (isset($dataModifier)
&& $dataModifier['Mention']=="ICM")
? 'selected'
: '' ?>>
ICM
</option>
<option value="AES"
<?= (isset($dataModifier)
&& $dataModifier['Mention']=="AES")
? 'selected'
: '' ?>>
AES
</option>
</select><br><br>
        </div>

        <div class="bouton">
            <button type="submit"
            name="<?= $modifier
            ? 'Modifier'
            : 'Ajouter' ?>">
            <?= $modifier
            ? 'Modifier'
            : 'Ajouter' ?>
        </button>
        </form>
       
    </div>
    
     
    <h2>Liste des étudiant</h2>
    <form method="GET" class="recherhe">
         <input type="text"
         id="recherche"
         name = "search"
         placeholder="Rechercher étudiant...">
         <button type="submit" name="rechercher" class="btn-search">
            <i class="fa-solid fa-magnifying-glass"></i>
         </button>
         <a href="Etudiant.php" class="btn-reset">
    <i class="fa-solid fa-rotate-left"></i>
</a>
<div class="tools">

<!-- TRI -->
<div class="tri-box">
    <select name="tri" onchange="this.form.submit()">
    <option value="">Trier par</option>
    <option value="alpha">Par ordre alphabétique</option>
    <option value="im">Par IM</option>
    <option value="age">Par âge</option>
</select>
</div>

<!-- FILTRE -->
<div class="filter-box">

    <select id="fSexe" name="sexe" onchange="this.form.submit()">
        <option value="all">Sexe</option>
        <option value="M">Homme</option>
        <option value="F">Femme</option>
    </select>

   <select id="fNiveau" name="niveau" onchange="this.form.submit()">
        <option value="all">Niveau</option>
        <option value="L1">L1</option>
        <option value="L2">L2</option>
        <option value="L3">L3</option>
        <option value="M1">M1</option>
        <option value="M2">M2</option>
    </select>

    <select id="fMention" name="mention" onchange="this.form.submit()">
        <option value="all">Mention</option>
        <option value="DAII">DAII</option>
        <option value="ICM">ICM</option>
        <option value="AES">AES</option>
    </select>

    <select id="fStatut" name="statut" onchange="this.form.submit()">
        <option value="all">Statut</option>
        <option value="Payé">Payé</option>
        <option value="Partiel">Partiel</option>
        <option value="Non payé">Non payé</option>
    </select>

</form>

</div>

    </div>
     <?php if(!empty($Etudiant)){?> 
   <table border="2" id="tableEtudiant">
        <tr>
            <th>IM</th>
            <th> Nom</th>
            <th> Prenom</th>
            <th> Date de Naissance</th>
            <th>Sexe</th>
            <th>  Lieu de Naissance</th>
            <th> CIN</th>           
           <th> Niveau</th>
            <th> Mention</th>
             <th> Montant total</th>
             <th>Montant total payer</th>
             <th>Reste</th>
             <th>Statut</th>
            <th>Action</th>
        </tr>
      <tbody>

        <?php foreach($Etudiant as $e){?>
        <tr>
            <td><?=$e['IM']?></td>
            <td><?=$e['Nom']?></td>
            <td><?=$e['Prenom']?></td>
            <td><?=$e['DateNaiss']?></td>
            <td><?=$e['Sexe']?></td>
            <td><?=$e['LieuNaiss']?></td>
            <td><?=$e ['CIN']?></td>
            <td><?=$e['Nom_niv']?></td>
            <td><?=$e['Mention']?></td>
             <td><?=$e['Montant_paye']?></td>
             <td><?=$e['total_paye']?></td>       
             <td><?=$e['Reste']?></td>
             <td><?=$e['Statut']?></td>
            <td class="action">
            <a href="Etudiant.php?IM=<?=$e['IM']?>" class="edit">
            <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <a href="Etudiant.php?delete=<?=$e['IM']?>"
            class="delete"
            onclick="return confirm('Voulez-vous vraiment supprimer cet étudiant ?')">
            <i class="fa-solid fa-trash"></i>
            </a></td>
        </tr>
      
       <?php } ?>

<?php } else { ?>

<tr>
    <td colspan="14" style="text-align:center; color:red;">
        Aucun étudiant trouvé
    </td>
</tr>

<?php } ?>
        </tbody>
    </table>
    
</div>
</body>
</html>
