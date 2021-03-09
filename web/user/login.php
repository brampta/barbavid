<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
include(BP.'/include/function/forms.php');

if(isset($_POST['email'])){
    $user_login = $user->login($_POST['email'],$_POST['password'],isset($_POST['remember_me']));
}

$page_title = __('login');
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

$message->show_messages();

if(!isset($user_login['success']) || $user_login['success']!=true){
    ?>
    <div class="user_form">
        <h1><?php echo $page_title ?></h1>
        <form method="post">
            <div class="user_form_input"><label for="email"><?php echo __('email:') ?></label><input id="email" name="email" type="text" value="<?php echo reget_post('email') ?>"></div>
            <div class="user_form_input"><label for="password"><?php echo __('password:') ?></label><input id="password" name="password" type="password" value="<?php echo reget_post('password') ?>"></div>
            <div class="user_form_input"><label for="remember_me"><?php echo __('remember me:') ?></label><input type="checkbox" id="remember_me" name="remember_me" <?php echo reget_checkbox('remember_me') ?>></div>
            <div class="user_form_submit"><input type="submit" value="<?php echo __('login') ?>"></div>
        </form>
        <div class="user_form_link"><a href="forgotten_password"><?php echo __('forgotten password') ?></a></div>
    </div>
    <?php
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');