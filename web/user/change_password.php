<?php
include('../includes/init.php');

if(isset($_SESSION['user_id']) && isset($_POST['old_password'])){
    $user_data_array = array();
    $user_data_array['name']=$_POST['name'];
    $password_change = $user->change_password($_SESSION['user_id'],$_POST['old_password'],$_POST['password'],$_POST['password2']);
}

include('../templates/head_start.php');
include('../templates/head_end.php');
include('../header.php');

if(!isset($_SESSION['user_id'])){
    ?>
    <p>unauthorized</p>
    <?php
}else{

    if(isset($password_change) && $password_change['success']==true){
        ?>
        <p>successfully changed password</p>
        <?php
    }else{

        if(isset($password_change) && in_array('passwords_dont_match',$password_change['errors'])){
            ?>
            <p>passwords don't match</p>
            <?php
        }
        if(isset($password_change) && in_array('invalid_login',$password_change['errors'])){
            ?>
            <p>old password is incorrect</p>
            <?php
        }
        if(isset($password_change) && in_array('update_error',$password_change['errors'])){
            ?>
            <p>error updating the database</p>
            <?php
        }
        ?>
        <h1>change password</h1>
        <form method="post">
            <div><label for="old_password">old password:</label><input id="old_password" name="old_password" type="password"></div>
            <div><label for="password">new password:</label><input id="password" name="password" type="password"></div>
            <div><label for="password2">retype new password:</label><input id="password2" name="password2" type="password"></div>
            <div><input type="submit" value="register"></div>
        </form>
        <?php
    }
}

include('../templates/page_end.php');