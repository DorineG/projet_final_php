<?php

session_start();

if(
    // appelle des variable
    isset($_POST['email']) &&
    isset($_POST['password'])
){
    // Vérification mail
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $errors[] = 'Email invalide';
    }

    // Vérification mot de passe
    if(!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/u', $_POST['password'])){
        $errors[] = 'Mot de passe invalide';
    }

    if(!isset($errors)){

        //Connexion BDD
        require 'core/db.php';

        //Requete SQL
        $queryUser = $db->prepare("SELECT * FROM users WHERE email = ?");

        $queryUser->execute([
            $_POST['email']
        ]);

        $user = $queryUser->fetch(PDO::FETCH_ASSOC);

        $queryUser->closeCursor();


        // Vérification de l'existance du compte
        if(!empty($user)){

            // Vérification du mot de passe
            if(password_verify($_POST['password'], $user['password'])){

                $success = "Vous êtes bien connecté !";
                $_SESSION['user'] = $user;

            } else {
                $errors[] = 'Mauvais mot de passe !';
            }

        } else {
            $errors[] = 'Ce compte n\'existe pas !';
        }

    }

}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexion - Wikifruit</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<body>

<?php include "core/menu.php" ?>


<div class="container-fluid">

    <div class="row">

        <div class="col-12 col-md-8 offset-md-2 py-5">

            <h1 class="pb-4 text-center">Connexion</h1>


            <div class="col-12 col-md-6 offset-md-3">
    <?php
                if(isset($errors)){
                    foreach($errors as $error){

                        echo '<div class="alert alert-danger" role="alert">'. htmlspecialchars($error) .'</div>';
                    }
                }

                if(isset($success)){

                    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($success) . '</div>';
                }else{ ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="text" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input id="password" type="password" name="password" class="form-control">
                        </div>
                        <div>
                            <input value="Connexion" type="submit" class="btn btn-primary col-12">
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
                <?php } ?>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>