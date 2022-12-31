<?php
session_start();
include("config.inc.php");
include("functions.inc.php");
?>	
<html>

<head>
	<title>Initialisation de la base de données</title>
	<meta charset="utf-8" />
	<style>
        header {
            border-style: groove;
            text-align: right;
        }
        #acceuil{
            float: left;
        }
    </style>
</head>

<body>


<header>
<form id="acceuil" action="./index.php">
         <button  type="submit">Acceuil</button>
</form>
<?php
//Zone de connexion
        if (isset($_POST['connexion'])) {
            if (isset($_POST['login']) && isset($_POST['mdp'])) {
				$mysqli = mysqli_connect($host, $user, $pass, $base) or die('Problème de création de la base :');
                seConnecter($mysqli,$_POST['login'],$_POST['mdp']);
				mysqli_close($mysqli);
            }
        }
        if (isset($_POST['deconnecter'])) {
            unset($_SESSION["isConnected"]);
            unset($_SESSION["login"]);
        }
        if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"] == true) {
        ?>
            <form method="post">
                    <input type="submit" name="profil" value="Profil"/>
                    <input type="submit" name="deconnecter" value="Se déconnecter"/>
            </form>
        <?php
        echo $_SESSION["Login"];
        } else {
        ?>
            <form method="post">
                Login <input type="text" name="login" />
                Mot de passe <input type="text" name="mdp" />
                <input type="submit" name="connexion" value="Connexion" />
                <input type="submit" name="inscrire" value="Inscrire" />
            </form>
        <?php
        }
        ?>      
</header>

<?php
//Boutons de la zone de connexion
    if (isset($_POST['inscrire'])) {
        include("inscrire.php");
    }
    if (isset($_POST['inscrit'])) {
		$mysqli = mysqli_connect($host, $user, $pass, $base) or die('Problème de création de la base :');
        ajouterUser($mysqli);
		mysqli_close($mysqli);
    }
    if (isset($_POST['profil'])) {
        include("profil.php");
    }
    if (isset($_POST['modifier'])) {
		$mysqli = mysqli_connect($host, $user, $pass, $base) or die('Problème de création de la base :');
        changerProfil($mysqli);
		mysqli_close($mysqli);
    }
?>
<?php
//Partie affichage des Cocktails
$mysqli = mysqli_connect($host, $user, $pass) or die('Problème de création de la base :');
if(mysqli_select_db($mysqli, $base) == false){
    mysqli_close($mysqli);
?>
        <a href="./install.php">Créer la base de données</a><br>
<?php
}else{
    if(isset($_GET['id_recette'])){
        $mysqli = mysqli_connect($host, $user, $pass, $base) or die('Problème de création de la base :');
        recetteDetailler($mysqli,$_GET['id_recette']);
        mysqli_close($mysqli);

    }if(isset($_GET['id_ingredient'])){
        $mysqli = mysqli_connect($host, $user, $pass, $base) or die('Problème de création de la base :');
        recettesDeIngredient($mysqli,$_GET['id_ingredient']);
        mysqli_close($mysqli);

    }if(!isset($_GET['id_recette']) && !isset($_GET['id_ingredient']) && !isset($_POST['inscrire']) && !isset($_POST['profil'])){
        //print tout les recettes avec leurs ingrédients
            $mysqli = mysqli_connect($host, $user, $pass, $base) or die('Problème de création de la base :');
            recettes($mysqli);
		    mysqli_close($mysqli);
    }
}
?>
    
</body>
</html>