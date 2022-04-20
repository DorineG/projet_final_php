<?php
    session_start();


  // Taille maximum du fichier en octets
$maxFileSize = 5242880;

// Types MIME autorisés
$allowedMIMETypes = [
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
];

$countriesList = [
    'fr' => 'france',
    'es' => 'espagne',
    'de' => 'allemagne',
    'jp' => 'japon',
    'it' => 'italie',
];

if(
    isset($_POST['name']) &&
    isset($_FILES['picture']) &&
    isset($_POST['description'])
){

    // Verif nom du fruit
    if(mb_strlen($_POST['name']) < 1 || mb_strlen($_POST['name']) > 50){
        $errors[] = 'Le nom doit contenir entre 1 et 50 caractères !';
    }

    // Verif menu déroulant
    if(!isset($_POST['origin']) || !array_key_exists($_POST['origin'], $countriesList)){
        $errors[] = 'Le pays est invalide';
    }

    $fileErrorCode = $_FILES['picture']['error'];

    if($fileErrorCode != 4){

        // 1er niveau de vérification du fichier : son code d'erreur et sa taille
        if($fileErrorCode == 1 || $fileErrorCode == 2 || $_FILES['picture']['size'] > $maxFileSize){
            $errors[] = 'Le fichier est trop volumineux.';

        } elseif($fileErrorCode == 3){
            $errors[] = 'Le fichier n\'a pas été chargé correctement, veuillez ré-essayer.';

        } elseif($fileErrorCode == 6 || $fileErrorCode == 7 || $fileErrorCode == 8){
            $errors[] = 'Problème serveur, veuillez ré-essayer plus tard.';

        } elseif($fileErrorCode == 0){

            // 2eme niveau de vérification du fichier : son type MIME
            // On a besoin de faire cette vérification dans un 2eme temps sinon on risque d'essayer de tester le type MIME d'un fichier qui n'existe pas, ce qui ferait une erreur PHP

            // Récupération du vrai type MIME du fichier
            $fileMIMEType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES['picture']['tmp_name']);

            if(!in_array($fileMIMEType, $allowedMIMETypes)){
                $errors[] = 'Seuls les fichiers jpg, png sont autorisés !';
            }


        } else {

            // Si on rentre ici, c'est qu'il y a un autre code d'erreur inconnu (peut-être PHP en ajoutera un jour ?)
            // On fait donc une erreur pour mettre le formulaire en échec quand même
            $errors[] = 'Problème inconnu';
        }

    }

    if(mb_strlen($_POST['description']) >= 1 && mb_strlen($_POST['description']) < 5 || mb_strlen($_POST['description']) > 20000){
        $errors[] = 'La description doit contenir entre 5 et 20 000 caractères !';
    }

    if(!isset($errors)){


        if($fileErrorCode == 0){

            $ext = array_search($fileMIMEType, $allowedMIMETypes);

            // On génère un hash md5 d'une chaînes aléatoire d'une taille de 50 pour le nom du nom de fichier
            do{
                $newFileName = md5( random_bytes(50) ) . '.' . $ext;
            } while(file_exists('images/uploads/' . $newFileName));

            // Sauvegarde du fichier avec son nouveau nom
            move_uploaded_file($_FILES['picture']['tmp_name'], 'images/uploads/' . $newFileName);

        }

         //Connexion BDD
        require 'core/db.php';

        if(empty($_POST['description'])){
            $descriptionToSave = null;
        } else {
            $descriptionToSave = $_POST['description'];
        }

        $insertNewFruit = $db->prepare("INSERT INTO fruits(name, origin, description, picture_name, user_id ) VALUES(?, ?, ?, ?, ?)");

        $querySuccess = $insertNewFruit->execute([
            $_POST['name'],
            $_POST['origin'],
            $descriptionToSave,
            $newFileName ?? null,
            $_SESSION['user']['id'],
        ]);

        $insertNewFruit->closeCursor();
    }
}

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ajout de fruit- Wikifruit</title>
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
        <h1 class="pb-4 text-center">Ajouter un fruit</h1>

        <div class="col-12 col-md-6 offset-md-3">

            <?php
                if(isset($errors)){
                    foreach($errors as $error){

                        echo '<div class="alert alert-danger" role="alert">'. htmlspecialchars($error) .'</div>';
                    }
                }
            ?>
            <form action="addfruit.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input placeholder="Banane" id="name" type="text" name="name" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="origin" class="form-label">Pays d'origine <span class="text-danger">*</span></label>
                    <select id="origin" name="origin" class="form-select">
                        <option selected disabled>Sélectionner un pays</option>
                        <?php

                        foreach($countriesList as $key => $country){
                            echo '<option value="' . $key . '">' . ucfirst($country) . '</option>';
                        }

                        ?>
                    </select>
                </div>
                <input type="hidden" name="MAX_FILE_SIZE" value=$maxFileSize>
                <div class="mb-3">
                    <label for="picture" class="form-label">Photo</label>
                    <input type="file" class="form-control" id="picture" name="picture" accept="image/png, image/jpeg">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger"></span></label>
                    <textarea class="form-control" name="description" id="description" cols="30" rows="10" placeholder="Description..."></textarea>
                </div>
                <div>
                    <input value="Créer le fruit" type="submit" class="btn btn-primary col-12">
                </div>

                <p class="text-danger mt-4">* Champs obligatoires</p>

            </form>

        </div>
    </div>

</div>

</div>


<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>