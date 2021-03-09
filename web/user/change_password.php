<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(isset($_SESSION['user_id']) && isset($_POST['old_password'])){
    $password_change = $user->change_password($_SESSION['user_id'],$_POST['old_password'],$_POST['password'],$_POST['password2']);
}

$page_title = __('change password');
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

$message->show_messages();

if(!isset($_SESSION['user_id'])){
    ?>
    <p class="message error"><?php echo __('unauthorized') ?></p>
    <?php
}else{
    if(!isset($password_change) || $password_change['success']!=true){
        ?>
        <div class="user_form">
            <h1><?php echo $page_title ?></h1>
            <form method="post">
                <div class="user_form_input"><label for="old_password"><?php echo __('old password:') ?></label><input id="old_password" name="old_password" type="password"></div>
                <div class="user_form_input"><label for="password"><?php echo __('new password:') ?></label><input id="password" name="password" type="password"></div>
                <div class="user_form_input"><label for="password2"><?php echo __('retype new password:') ?></label><input id="password2" name="password2" type="password"></div>
                <div class="user_form_submit"><input type="submit" value="<?php echo __('save new password') ?>"></div>
            </form>
        </div>
        <?php
    }
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');