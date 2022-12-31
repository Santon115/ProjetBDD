<?php
  function query($link,$requete)
  { 
    $resultat=mysqli_query($link,$requete) or die("$requete : ".mysqli_error($link));
	  return($resultat);
  }

  function remplacer_guillemet_par_apostrophe($text){
    $text_formater = str_replace('"', '\'',$text);
    return($text_formater);
  }

  function supprimer_apostrophe($text){
    $text_formater = str_replace("'"," ",$text);
    return($text_formater);
  }

  function seConnecter($bdd,$login,$mdp){
      if($login != "" && $mdp != ""){
        $sql_connexion ="SELECT * FROM `utilisateur` WHERE `login`='$login' and mot_de_passe='$mdp'";
        $utilisateur = query($bdd,$sql_connexion);
        $info_utilisateur = mysqli_fetch_assoc($utilisateur);
        if ($login == $info_utilisateur['login']) {
            if ($mdp == $info_utilisateur['mot_de_passe'] ) {
                $_SESSION["isConnected"] = true;
                $_SESSION["Login"] = $info_utilisateur['login'];
            }else{
              echo "Mauvais mot de passe";
            }
        } else {
            echo "Mauvais login";
        }
    }
  }
  
  function ajouterUser($bdd){
      if(   verifierLogin($_POST['login']) == true && 
            verifierSiLoginExiste($bdd,$_POST['login']) == false &&
            isset($_POST["mdp"]) &&
            isset($_POST["prenom"]) &&
            verifierPrenom($_POST["prenom"]) == true &&
            isset($_POST["age"]) &&
            verifierAge($_POST["age"]) == true
            )
        {
          $info_user = '("'.$_POST['login'].'","'.$_POST["mdp"].'","'.$_POST["prenom"].'","'.$_POST["age"].'")';
          query($bdd,"INSERT INTO `utilisateur` (`login`, `mot_de_passe`, `prenom`, `age`) VALUES ".$info_user);
        }
  }

  function changerProfil($bdd){
        $login = $_SESSION["Login"];
        $sql_connexion ="SELECT * FROM `utilisateur` WHERE `login`='$login'";
        $utilisateur = query($bdd,$sql_connexion);
        $info_utilisateur = mysqli_fetch_assoc($utilisateur);
      if (isset($_POST["mdp"]) && $_POST["mdp"] != "") {
        $info_user = '("'.$info_utilisateur['id_user'].'","'.$info_utilisateur['login'].'","'.$_POST["mdp"].'","'.$info_utilisateur['prenom'].'","'.$info_utilisateur['age'].'")';
        query($bdd,"REPLACE INTO `utilisateur` (`id_user`,`login`,`mot_de_passe`, `prenom`, `age`) VALUES".$info_user);
      }
      if (isset($_POST["prenom"]) && $_POST["prenom"] != "") {
          if(verifierPrenom($_POST["prenom"]) == true){
            $info_user = '("'.$info_utilisateur['id_user'].'","'.$info_utilisateur['login'].'","'.$_POST["mdp"].'","'.$info_utilisateur['prenom'].'","'.$info_utilisateur['age'].'")';
            query($bdd,"REPLACE INTO `utilisateur` (`id_user`,`login`,`mot_de_passe`, `prenom`, `age`) VALUES".$info_user);
          }
      }
      if (isset($_POST["age"]) && $_POST["age"] != "") {
        if(verifierAge($_POST["age"]) == true){
          $info_user = '("'.$info_utilisateur['id_user'].'","'.$info_utilisateur['login'].'","'.$_POST["mdp"].'","'.$info_utilisateur['prenom'].'","'.$info_utilisateur['age'].'")';
          query($bdd,"REPLACE INTO `utilisateur` (`id_user`,`login`,`mot_de_passe`, `prenom`, `age`) VALUES".$info_user);
        }
    }
  }

  function verifierLogin($un_login){
      $pattern_login = "/^[a-zA-Z0-9]+$/";
      $verifie = preg_match($pattern_login,$un_login);
          if($verifie == 1){
              return true;
          }else{
              return false;
          }
  }

  function verifierPrenom($un_prenom){
      $pattern_prenom = "/^[a-zA-Z '-]+$/";
      $verifie = preg_match($pattern_prenom,$un_prenom);
          if($verifie == 1){
              return true;
          }else{
              return false;
          }
  }

  function verifierAge($un_age){
        if($un_age >= 18 && $un_age <=99){
            return true;
        }else{
            return false;
        }
}

  function verifierSiLoginExiste($bdd,$un_login){
      $sql_login = query($bdd,"SELECT `login` FROM `utilisateur` WHERE `login`='$un_login'");
      if (mysqli_num_rows($sql_login)>0) {
          return true;
      }else{
          return false;
      }
  }

  function recetteDetailler($bdd,$id_recette){
    $sql_recette = "SELECT * FROM `recette` WHERE `id_recette`='$id_recette'";
    $recette =  query($bdd,$sql_recette);
    $recette_detailler = mysqli_fetch_assoc($recette);
    echo "<h2>".$recette_detailler['titre']."</h2><br>";
    echo "<p>".$recette_detailler['preparation']."</p><br>";
    listeIngredients($bdd,$id_recette);
  }

  function recettesDeIngredient($bdd,$id_ingredient){
    $sql_ingredient="SELECT `nom` FROM `ingredient` WHERE id_ingredient='$id_ingredient'";
		$un_ingredient= query($bdd,$sql_ingredient);
		$nom_ingredient=mysqli_fetch_assoc($un_ingredient);
    echo "<h2>".$nom_ingredient['nom']."</h2><br>";

    $sql_recettes = "SELECT `id_recette` FROM `conteneur` WHERE id_ingredient='$id_ingredient'";
    $recettes =  query($bdd,$sql_recettes);
    while($liste_recettes = mysqli_fetch_assoc($recettes)){
      $un_id_recette = $liste_recettes['id_recette'];
      $sql_recette="SELECT `titre` FROM `recette` WHERE id_recette='$un_id_recette'";
			$recette= query($bdd,$sql_recette);
			$nom_recette=mysqli_fetch_assoc($recette);
      $un_titre = $nom_recette['titre'];
      echo '<li><a href="index.php?id_recette='.$un_id_recette.'">'.$un_titre.'</a></li><br>';
    }
    
  }
  
  function listeIngredients($bdd,$id_recette){
      $sql_id_ingredient="SELECT `id_ingredient` FROM `conteneur` WHERE id_recette='$id_recette'";
			$id_ingredient_array = query($bdd,$sql_id_ingredient);
			while($id_ingredients = mysqli_fetch_assoc($id_ingredient_array)){
				$un_id_ingredient = $id_ingredients['id_ingredient'];
				// Récuperez le nom de l'ingrédient et le print
				$sql_ingredient="SELECT `nom` FROM `ingredient` WHERE id_ingredient='$un_id_ingredient'";
				$un_ingredient= query($bdd,$sql_ingredient);
				$nom_ingredient=mysqli_fetch_assoc($un_ingredient);
        echo '<li><a href="index.php?id_ingredient='.$un_id_ingredient.'">'.$nom_ingredient['nom'].'</a></li></br>';
      }
  }

  function recettes($bdd){
    $sqlTitre = "SELECT `titre` FROM `recette`";
		$titreArray= query($bdd,$sqlTitre);
		while($titres= mysqli_fetch_assoc($titreArray)){
			$un_titre = $titres['titre'];
            
			// Récuperez le ID de la recette
			$sql_id_Recette ="SELECT `id_recette` FROM `recette` WHERE titre='$un_titre'";
			$id_recette_array = query($bdd,$sql_id_Recette);
			$id_recettes = mysqli_fetch_assoc($id_recette_array);
			$un_id_recette = $id_recettes['id_recette'];
      echo '<h2><a href="index.php?id_recette='.$un_id_recette.'">'.$un_titre.'</a></h2><br>';
      listeIngredients($bdd,$un_id_recette);
    }
  }
?>

