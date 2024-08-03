<?php
$connecter =new mysqli("localhost", "root", "", "gestion_des_assureurs");
session_start();
?>
<?php
if($connecter->connect_error){
	die("la connexion a echoue : " .$connecter->connect_error);
}
//traitement du formulaire de connexion
if($_SERVER["REQUEST_METHOD"]== "POST"){
	$username = $_POST["nom"];
	$password = $_POST["mot_de_passe"];
	
	//preparer la requete SQL
	$sel_uti = $connecter->prepare("SELECT id_user,nom_utilisateur,mot_de_pass,statut,etat FROM users WHERE nom_utilisateur=?");
	$sel_uti->bind_param("s", $username);
	$sel_uti->execute();
	$result = $sel_uti->get_result();
	
	if ($result->num_rows >0){
		$row = $result->fetch_assoc();
		$didipassword = $row["mot_de_pass"];
		 
		 if((sha1($password) === $didipassword)){
			 if($row["etat"] == 1){
				 if($row["statut"] == "admin"){
					 $_SESSION["id_user"] = $row["id_user"];
					 $_SESSION["nom_utilisateur"] = $row["nom_utilisateur"];
					 header("Location: ind.php");
					 exit;
				 }else if($row["statut"] == "caissiere"){
					 $_SESSION["id_user"] = $row["id_user"];
					 $_SESSION["nom_utilisateur"] = $row["nom_utilisateur"];
					 header("Location: form assureur.php");
					 exit;
				 }else{
			 }}else{
					$erreur="votre compte est desactiver.Veuiller contacter l'Administrateur" ;
		 }}else{
					 $erreur ="Nom d'utilisateur ou mot de passe incorrect.";
	}}else{
					 $erreur = "Nom d'utilisateur ou mot de passe incorrect.";
				 }
				 
		
	}
	
?>
<html>
<head>
<meta charset ="utf-8"/>
<style>
/*tr
{
background-color:white;
margin-left:30px;
margin-right:15px;
font-size:20px;
text-align:left;
}
h1
{
margin-left:20px;
color:red;
}
form {
	margin-left:30%;
	margin-top:10%;
	border:2px solid ;
	width:10cm;
}*/
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Form styling */
form {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    width: 300px;
}
</style>
</head

<body>


<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
<h1>FORMULAIRE DE CONNEXION</h1>
<table>
<tr>
<th>Nom d'utilisateur:</th>
<td>
<input type="text"  name="nom" required placeholder="Taper votre nom"/>

</td>
</tr>

<tr>
<th>Mots de passe:</th>
<td>
<input type="password" name="mot_de_passe"  placeholder="Taper votre mot de passe"/>
</td>
</tr>
<tr>
<td colspan="2"><?php if(isset($erreur)){ ?>
<p style="color:brown;"><?php echo $erreur; ?></p>
<?php } ?></td>
</tr>
<tr>
<th></th>
<th>
<button input type="submit" name="valider">Envoyer</button>
<button input type="reset" name="Effacer">Annuler</button>
</th>

</tr>

</table>

<?php


if(isset($_SESSION['login_attempts']))
{
	if($_SESSION['login_attempts'] >=3)
	{
		echo"trop de tentatives de connexion.veuilles reessayer plus tard.";
		exit;
	}
}else{
	$_SESSION['login_attempts']=0;
}
if($_SERVER["REQUEST_METHOD"]=="POST"){
	$nom_utilisateur=$_POST["nom"];
	$mot_de_pass=$_POST["mot_de_passe"];
	
	$stmt=$connecter->prepare("SELECT * FROM users WHERE nom_utilisateur=?");
	$stmt->bind_param('s',$nom_utilisateur);
	$stmt->execute();
	$result=$stmt->fetch();
	
	IF($result&& password_verify($password,$result["mot_de_pass"])){
	$_SESSION['login_attempts']=0;
	$_SESSION["id_user"]=$result["id"];
	$_SESSION['last_activity']=time();
	header("location: index.php");
	exit;
	}else{
		$_SESSION['login_attempts']++;
		echo"nom d'utilisateur ou mot de passe incorect.";
	}
}


?>
</body>
</html>