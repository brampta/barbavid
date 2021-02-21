<?php


ini_set('session.cookie_domain', '.barbavid.com');
session_start();

$nowtime=time();

include('get_language.php');

//erase invalidated users that are older than 24h

$email_invalid=0;
$email_taken=0;
$password_too_short=0;
$password_dont_match=0;
$public_name_invalid=0;
$public_name_taken=0;
$img_ver_invalid=0;
$register_suxxess=0;
$register_suxxess_and_validated=0;



if(isset($_POST['register']))
{
    
}




echo '<html>
    <head>
        <title>Regsiter an account - Barbavid</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="/style.css" />
    </head>
    <body>';
include('header.php');



$boxwidth='240px';
echo '<br />
    <br />
    <center>
    <form>
    <h3>Create a Barbavid account</h3>
    <table>
    <tr><td>username:</td><td><input type="text" name="username" value="'.$_POST['username'].'" style="width:'.$boxwidth.';" /></td></tr>
    <tr><td>password:</td><td><input type="text" name="password" value="'.$_POST['password'].'" style="width:'.$boxwidth.';" /></td></tr>
    <tr><td>repeat password:</td><td><input type="text" name="password2" value="'.$_POST['password2'].'" style="width:'.$boxwidth.';" /></td></tr>
    <tr><td>email address:</td><td><input type="text" name="email" value="'.$_POST['email'].'" style="width:'.$boxwidth.';" /></td></tr>
    </table>
    <input type="submit" name="register" value="register" />
    </form>
    </center>
    <br />
    <br />';

include('bottom.php');
echo '</body>
</html>';


?>