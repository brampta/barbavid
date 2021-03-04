<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

$password_reset_code_data=false;
if(isset($_GET['code'])){
    $password_reset_code_data = $db->load_by('password_reset_codes','code',$_GET['code']);
}

if($password_reset_code_data && isset($_POST['password'])){
    $user_password_reset = $user->reset_password($_GET['code'],$_POST['password'],$_POST['password2']);
}

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

if(isset($user_password_reset) && $user_password_reset['success']==true){
    ?>
    <p>password successfully reset</p>
    <?php
}else{

    if(isset($user_password_reset) && in_array('passwords_dont_match',$user_password_reset['errors'])){
        ?>
        <p>passwords don't match</p>
        <?php
    }
    if(isset($user_password_reset) && in_array('update_error',$user_password_reset['errors'])){
        ?>
        <p>error updating the database</p>
        <?php
    }

    if(!$password_reset_code_data){
        ?>
        invalid code
        <?php
    }else{
        ?>
        <h1>reset password</h1>
        <form method="post">
            <div><label for="password">new password:</label><input id="password" name="password" type="password"></div>
            <div><label for="password2">retype new password:</label><input id="password2" name="password2" type="password"></div>
            <div><input type="submit" value="save"></div>
        </form>
        <?php
    }
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');