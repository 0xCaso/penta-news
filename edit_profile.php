<?php
require_once("php/validSession.php");
require_once("php/UserController.php");

// variables
$content = "";  // html code to send to the page
$errors = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    // take the data
    $firstname      = $_POST['firstname'];
    $lastname       = $_POST['lastname'];
    $email          = $_POST['email'];
    $password       = $_POST['password'];
    $rep_password   = $_POST['repeated_password'];
    $profile_img    = NULL;
    // ['name in form']['imported file name']

    if($_FILES['profile_img']['name']){
        $target_dir = "images/user_profiles/";
        //$profile_img = basename($_FILES["profile_img"]["name"]);
        $target_file = $target_dir . basename($_FILES["profile_img"]["name"]);
        //$target_file = $target_dir . $_SESSION['username'] . $imageFileType;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $profile_img = $_SESSION['username'] . "." . $imageFileType;
        $uploadOk = 1;

        // Check if image file is a actual image or fake image
        
        $check = getimagesize($_FILES["profile_img"]["tmp_name"]);
        if($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $errors = "<li>File is not an image.</li>";
            $uploadOk = 0;
        }

        // Check if file already exists => NO NOI VOGLIAMO IL REPLACE
        /*
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        */

        // Check file size
        if ($_FILES["profile_img"]["size"] > 1000000) {
            $errors .= "<li>Sorry, your file is too large.</li>";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $errors .= "<li>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</li>";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $errors .= "<li>Sorry, your file was not uploaded.</li>";
            // if everything is ok, try to upload file
        } else {
            if (!move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_dir . $profile_img)) {
                $errors .= "<li>There was an error during profile image uploading.</li>";
            }
        }
    }
    
    if(!$errors){
        $result = update($_SESSION['username'], $firstname, $lastname, $email, $password, $rep_password, $profile_img);
        if($result === TRUE){
            redirect('../profile.php', false, 201);
        } else {
            $errors = $result;
        }
    }

}

// TAKE OLD USER INFO
$oldUserData = show($_SESSION['username']);

$content = "
    <div class='form-wrapper' id='profileEditForm'>
            <formErrors/>
            <form action='edit_profile.php' method='POST' enctype='multipart/form-data' id='form'>
                <div class='input-wrapper'>
                    <img src='images/user_profiles/" . ($oldUserData['profile_img']?$oldUserData['profile_img']:'default.png') . "' id='user-profile-img' alt='user profile image'>
                    <input type='file' id='profile_img' class='custom-file-input' name='profile_img'>
                    <div class='error'></div>
                </div>
                <div class='input-wrapper'>
                    <label for='username'>username</label>
                    <input placeholder='username' type='text' id='username' name='username' value='" . $oldUserData['username'] . "' disabled>
                    <div class='error'></div>
                </div>
                <div class='input-wrapper'>
                    <label for='firstname'>firstname</label>
                    <input placeholder='Jack' type='text' id='firstname' name='firstname' value='" . $oldUserData["firstName"] . "'>
                    <div class='error'></div>
                </div>
                <div class='input-wrapper'>
                    <label for='lastname'>lastname</label>
                    <input placeholder='Red' type='text' id='lastname' name='lastname' value='" . $oldUserData["lastName"] . "'>
                    <div class='error'></div>
                </div>                
                <div class='input-wrapper'>
                    <label for='email'>email</label>
                    <input placeholder='jackred@email.com' type='text' id='email' name='email' value='" . $oldUserData["email"] . "'>
                    <div class='error'></div>
                </div>
                <div class='input-wrapper'>
                    <label for='password'>password</label>
                    <input type='password' name='password' id='password'>
                    <div class='error'></div>
                </div>
                <div class='input-wrapper'>
                    <label for='repeated_password'>repeat password</label>
                    <input type='password' name='repeated_password' id='repeated_password'>
                    <div class='error'></div>
                </div>
                <div id='btnBlock'>
                    <a class='btn-std' href='profile.php' id='undoBtn'>Discard</a>
                    <button class='btn-std' type='submit' id='saveBtn'>Update</button>
                </div>
            </form>
        </div>";


// paginate the content
// page structure
$htmlPage = file_get_contents("html/edit_profile.html");

//header footer and dynamic navbar all at once (^^^ sostituisce il commento qua sopra ^^^)
require_once('php/full_sec_loader.php');

$htmlPage = str_replace('<editForm/>', $content, $htmlPage);
$htmlPage = str_replace('<formErrors/>', $errors, $htmlPage);

echo $htmlPage;

?>