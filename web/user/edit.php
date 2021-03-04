<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(isset($_SESSION['user_id']) && isset($_POST['name'])){
    $user_update = $user->update($_SESSION['user_id'],$_POST['name']);
}

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

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

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');