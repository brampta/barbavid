<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
include(BP.'/include/function/forms.php');

$password_reset_code_data=false;
if(isset($_GET['code'])){
    $password_reset_code_data = $db->load_by('password_reset_codes','code',$_GET['code']);
}

if($password_reset_code_data && isset($_POST['password'])){
    $user_password_reset = $user->reset_password($_GET['code'],$_POST['password'],$_POST['password2']);
}

$page_title = __('reset password');
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

$message->show_messages();

if(!isset($user_password_reset) || $user_password_reset['success']!=true){

    if($password_reset_code_data){
        ?>
        <div class="user_form">
            <h1><?php echo $page_title ?></h1>
            <form method="post">
                <div class="user_form_input"><label for="password"><?php echo __('new password:') ?></label><input id="password" name="password" type="password" value="<?php echo reget_post('password') ?>"></div>
                <div class="user_form_input"><label for="password2"><?php echo __('retype new password:') ?></label><input id="password2" name="password2" type="password" value="<?php echo reget_post('password2') ?>"></div>
                <div class="user_form_submit><input type="submit" value="<?php echo __('save') ?>"></div>
            </form>
        </div>
        <?php
    }
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');