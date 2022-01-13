<?php
require_once("php/validSession.php");
require_once("php/UserController.php");

// variables
$content = "";  // html code to send to the page

// SHOW USER INFO
$userData = getUser($_SESSION['username']);

if($userData){
    $role = ($userData['role']==1) ? 'Admin' : 'User';
    $content = "<img src='images/user_profiles/" . ($userData['profile_img']?$userData['profile_img']:'default.png') . "' id='user-profile-img' alt='user profile image'>
        <span id='user-username'>" . $userData['username'] . "</span>
        <ul id='user-specs'>
            <li>" . $userData['firstName'] . " " . $userData['lastName'] . "
            <li>Email: " . $userData['email'] . "</li>
            <li>Role: " . $role . "</li>
            <li>Subscription Date: " . $userData['subscription_date'] . "</li>
            <li><a href='edit_profile.php' class='action-button'>Edit</a></li>
        </ul>";
}

// paginate the content
// page structure
$htmlPage = file_get_contents("html/profile.html");

//header footer and dynamic navbar all at once (^^^ sostituisce il commento qua sopra ^^^)
require_once('php/full_sec_loader.php');

if ($userData['role'] == 1) {
    $adminButton = '<h3 class="admin-link-info">Click the link below to get to the administration page:</h3>    
    <a href="administration.php" class="center action-button">Manage site</a>';
    $htmlPage = str_replace('<adminButton/>', $adminButton, $htmlPage);
}else{
    $htmlPage = str_replace('<adminButton/>', '', $htmlPage);
}

echo str_replace('<userInfo/>', $content, $htmlPage);

?>