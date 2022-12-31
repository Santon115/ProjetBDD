
<!DOCTYPE html>
<html>

<head>
      <title>Profil</title>
	  <meta charset="utf-8" />
</head>

<body>
<?php
    $mysqli = mysqli_connect($host, $user, $pass, $base) or die('Problème de création de la base :');
    $login = $_SESSION["Login"];
    $sql_connexion ="SELECT * FROM `utilisateur` WHERE `login`='$login'";
    $utilisateur = query($mysqli,$sql_connexion);
    $info_utilisateur = mysqli_fetch_assoc($utilisateur);
?>

<form method ="post">
<fieldset>
    <legend>Informations personnelles</legend>

    Nom de Profil :
    <?php
        echo $info_utilisateur['login'];
    ?>
    <br />
    Mot de Passe :
    <input type="text" name="mdp"/><br />
    Prénom :
    <input type="text" name="prenom" value="<?php echo $info_utilisateur['prenom'];?>"/> 
	<br />
    Age
    <input type="number" name="naissance" value="<?php echo $info_utilisateur['age'];?>"/>
	<br />
</fieldset>
<input type="submit" name="modifier" value="Valider la modification" />
</form>
</body>
</html>
