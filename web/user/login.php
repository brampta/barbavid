<?php
include('../includes/init.php');

if(isset($_POST['email'])){
    $user_login = $user->login($_POST['email'],$_POST['password'],isset($_POST['remember_me']));
}

include('../templates/head_start.php');
include('../templates/head_end.php');
include('../header.php');

if(isset($user_login) && $user_login['success']==true){
    ?>
    <p>successfully logged in</p>
    <?php
}else{
    if(isset($user_login) && in_array('email_not_validated',$user_login['errors'])){
        ?>
        <p>email not validated</p>
        <?php
    }
    if(isset($user_login) && in_array('invalid_login',$user_login['errors'])){
        ?>
        <p>invalid login information</p>
        <?php
    }
    ?>
    <h1>login</h1>
    <form method="post">
        <div><label for="email">email:</label><input id="email" name="email" type="text"></div>
        <div><label for="password">password:</label><input id="password" name="password" type="password"></div>
        <div><input type="checkbox" name="remember_me"><label for="remember_me">remember me:</label></div>
        <div><input type="submit" value="register"></div>
    </form>
    <a href="forgotten_password">forgotten password</a>
    <?php
}

include('../templates/page_end.php');