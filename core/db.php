<?php

// Connexion à la BDD
try{

    $db = new PDO('mysql:host=localhost;dbname=php_final_project;charset=utf8', 'root', '');

    // TODO: à commenter à la fin
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(Exception $e){
    die('Problème avec la base de données : ' . $e->getMessage());
}