<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
include(BP.'/include/function/forms.php');

if(isset($_POST['email'])){
    $user_registration = $user->register($_POST['email'],$_POST['name'],$_POST['password'],$_POST['password2']);
}

$page_title = __('register account');
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

$message->show_messages();

if(!isset($user_registration) || $user_registration['success']!=true){
    ?>
    <div class="user_form">
        <h1><?php echo $page_title ?></h1>
        <form method="post">
            <div class="user_form_input"><label for="email"><?php echo __('email:') ?></label><input id="email" name="email" type="text" value="<?php echo reget_post('email') ?>"></div>
            <div class="user_form_input"><label for="name"><?php echo __('name:') ?></label><input id="name" name="name" type="text" value="<?php echo reget_post('name') ?>"></div>
            <div class="user_form_input"><label for="password"><?php echo __('password:') ?></label><input id="password" name="password" type="password" value="<?php echo reget_post('password') ?>"></div>
            <div class="user_form_input"><label for="password2"><?php echo __('retype password:') ?></label><input id="password2" name="password2" type="password" value="<?php echo reget_post('password2') ?>"></div>
            <div class="user_form_submit"><input type="submit" value="<?php echo __('register') ?>"></div>
        </form>
    </div>
    <?php
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');