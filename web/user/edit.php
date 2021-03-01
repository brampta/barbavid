<?php
include('../includes/init.php');

if(isset($_SESSION['user_id']) && isset($_POST['name'])){
    $user_update = $user->update($_SESSION['user_id'],$_POST['name']);
}

include('../templates/head_start.php');
include('../templates/head_end.php');
include('../header.php');

if(!isset($_SESSION['user_id'])){
    ?>
    <p>unauthorized</p>
    <?php
}else{

    if(isset($user_update) && $user_update['success']==true){
        ?>
        <p>successfully udpated</p>
        <?php
    }
    if(isset($user_update) && in_array('update_error',$user_update['errors'])){
        ?>
        <p>error updating the database</p>
        <?php
    }
    ?>
    <h1>edit account</h1>
    <form method="post">
        <div><label for="name">name:</label><input id="name" name="name" type="text"></div>
        <div><input type="submit" value="update"></div>
    </form>
    <a href="change_password">change password</a>
    <?php
}

include('../templates/page_end.php');