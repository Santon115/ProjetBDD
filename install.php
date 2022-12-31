<?php
include("config.inc.php");
include("functions.inc.php");
include("Donnees.inc.php");
    
if (file_exists("Donnees.inc.php")) {
    $mysqli = mysqli_connect($host, $user, $pass) or die('Problème de création de la base :');
    query($mysqli, 'DROP DATABASE IF EXISTS ' . $base);
    query($mysqli, 'CREATE DATABASE ' . $base);
    if (mysqli_select_db($mysqli, $base) or die("Impossible de sélectionner la base : $base")) {
        //creer table utilisateur
        query($mysqli,'DROP TABLE IF EXISTS `utilisateur`;');
        query($mysqli,
        'CREATE TABLE IF NOT EXISTS `utilisateur` (
        `id_user` INT(6) NOT NULL AUTO_INCREMENT,
        `login` VARCHAR(50) NOT NULL,
        `mot_de_passe` VARCHAR(50) NOT NULL,
        `prenom` VARCHAR(50) NOT NULL,
        `age` INT(3) NOT NULL,
        PRIMARY KEY (`id_user`)
        )');
        //creer table ingrédients
        
        query($mysqli,'DROP TABLE IF EXISTS `ingredient`;');
        query($mysqli,
        'CREATE TABLE IF NOT EXISTS `ingredient` (
        `id_ingredient` INT(6) NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(50) NOT NULL ,
        PRIMARY KEY (`id_ingredient`)
        )'); 
        
        //creer table recettes
        query($mysqli,'DROP TABLE IF EXISTS `recette`;');
        query($mysqli,
        'CREATE TABLE IF NOT EXISTS `recette` (
        `id_recette` INT(6) NOT NULL AUTO_INCREMENT,
        `titre` TEXT NOT NULL ,
        `preparation` TEXT NOT NULL ,
        PRIMARY KEY (`id_recette`)
        )');

        //creer table conteneur
        query($mysqli,'DROP TABLE IF EXISTS `conteneur`;');
        query($mysqli,
        'CREATE TABLE IF NOT EXISTS `conteneur` (
        `id_recette` INT(6),
        `id_ingredient` INT(6),
        INDEX (id_recette),
        INDEX (id_ingredient),
        FOREIGN KEY (id_recette) REFERENCES `recette`(id_recette),
        FOREIGN KEY (id_ingredient) REFERENCES `ingredient`(id_ingredient)
        )');
        
        //Initialiser les Cocktails et les ingrédients dans la base de données
        foreach($Recettes as $cocktail){
            $titre = supprimer_apostrophe($cocktail["titre"]);
            $preparation = remplacer_guillemet_par_apostrophe($cocktail["preparation"]);
            $un_cocktail = '("'.$titre.'","'.$preparation.'")';
            query($mysqli,"INSERT INTO `" . $base . "`.`recette` (`titre`, `preparation`) VALUES ".$un_cocktail);
            for($i=0;$i<count($cocktail["index"]);$i++){
                $ingredient = supprimer_apostrophe($cocktail["index"][$i]);
                $cherche = query($mysqli,"SELECT `id_ingredient` FROM `ingredient` WHERE nom='$ingredient'");
                $idExiste= mysqli_num_rows($cherche);
                if($idExiste==0)
                {
                    $un_ingredient = '("'.supprimer_apostrophe($cocktail["index"][$i]).'")';
                    query($mysqli,"INSERT INTO `" . $base . "`.`ingredient` (`nom`) VALUES ".$un_ingredient);
                }
            }     
        }
        
        //liées les Cocktails avec les ingrédients
        foreach($Recettes as $cocktail){
            $titre = supprimer_apostrophe($cocktail["titre"]);
            $getIdRecette=query($mysqli,"SELECT `id_recette` FROM `recette` WHERE titre='$titre'");
            $idRecette=mysqli_fetch_row($getIdRecette);
            for($i=0;$i<count($cocktail["index"]);$i++){
                $un_ingredient = supprimer_apostrophe($cocktail["index"][$i]);
                $getIdIngredient = query($mysqli,"SELECT `id_ingredient` FROM `ingredient` WHERE nom='$un_ingredient'");
                $idIngredient=mysqli_fetch_row($getIdIngredient);
                query($mysqli,"INSERT INTO `" . $base . "`.`conteneur` (`id_recette`,`id_ingredient`) VALUES ('$idRecette[0]','$idIngredient[0]')");
            }
        }  
        echo "Base de données bien initialiser<br>";  
        mysqli_close($mysqli);
        echo "<a href="."./index.php".">Retour à l'acceuil</a><br>";
    }else{
        echo "La base de données ne sait pas initialiser";
    }
}else{
    echo "Le Fichier 'Donnees.inc.php' est introuvable";
}
?>