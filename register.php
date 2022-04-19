<?php
echo "<pre>";
print_r($_POST);
echo "</pre>";

require 'recaptchaValid.php';

if(
    isset($_POST["email"]) &&
    isset($_POST["password"]) &&
    isset($_POST["confirm-password"]) &&
    isset($_POST["pseudonym"]) &&
    isset($_POST["g-recaptcha-response"])
){

    //verification mail
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $errors[] = "Email invalide !";
    }

    //verification mot de passe
    if(!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/u', $_POST['password'])){
        $errors[] = 'Le mot de passe doit comprendre au moins 8 caractères dont 1 lettre minuscule, 1 majuscule, un chiffre et un caractère spécial.';
    }

    //verification confirmation mot de passe
    if($_POST['password'] != $_POST['confirm-password']){
        $errors[] = 'La confirmation ne correspond pas au mot de passe !';
    }

    //verification pseudo
    if(mb_strlen($_POST['pseudonym']) < 1 || mb_strlen($_POST['pseudonym']) > 50 ){
        $errors[] = "Le pseudonyme doit contenir entre 1 et 50 caractères";
    }

    //verification captcha
    if(!recaptchaValid($_POST["g-recaptcha-response"], $_SERVER['REMOTE_ADDR']) ){
        $errors[] = "Veuillez remplir correctement le captcha";
    }

    //verification réussite
    if(!isset($errors)){
        $success = "Votre compte a bien été créé !";

        // Connexion à la BDD
        require 'core/db.php';

        //Hash du mot de passe
        $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);

        //Requete SQL
        $insertNewUser = $db->prepare("INSERT INTO users(email, password, pseudonym, register_date) VALUES(?, ?, ?, ?)");

        $querySucces = $insertNewUser->execute([
            $_POST['email'],
            $hashedPassword,
            $_POST['pseudonym'],
            date('Y-m-d H:i:s'),
        ]);
    }

}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Wikifruit</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="css/styles.css">

    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<body>
    <?php include 'core/menu.php' ?>

    <div class="container-fluid">

<div class="row">

    <div class="col-12 col-md-8 offset-md-2 py-5">
        <h1 class="pb-4 text-center">Créer un compte sur Wikifruit</h1>
        <div class="col-12 col-md-6 mx-auto">

        <?php
        if(isset($errors)){
            foreach($errors as $error){
            echo '<div class="alert alert-danger" role="alert">'
            . $error .
        '</div>';
        }
        }

        if(isset($success)){
            echo '<div class="alert alert-success" role="alert">'
            . $success .
        '</div>';
        }else{
        ?>

                <form action="register.php" method="POST">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input id="email" type="text" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input id="password" type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirmation mot de passe <span class="text-danger">*</span></label>
                        <input id="confirm-password" type="password" name="confirm-password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="pseudonym" class="form-label">Pseudonyme <span class="text-danger">*</span></label>
                        <input id="pseudonym" type="text" name="pseudonym" class="form-control">
                    </div>
                    <div class="mb-3">
                        <p class="mb-2">Captcha <span class="text-danger">*</span></p>
                        <div class="g-recaptcha" data-sitekey="6LflYncfAAAAAHLOdGLh3iwqQeO1lyYPndIdjVaC"></div>
                    </div>
                    <div>
                        <input value="Créer mon compte" type="submit" class="btn btn-success col-12">
                    </div>

                    <p class="text-danger mt-4">* Champs obligatoires</p>

                </form>
        </div>

        <?php } ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>