<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
include(BP.'/include/function/forms.php');

if(isset($_SESSION['user_id']) && isset($_POST['name'])){
    $user_update = $user->update($_SESSION['user_id'],$_POST['name']);
}
$user_data_array = $db->load('users',$_SESSION['user_id']);

$page_title = __('edit account');
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

$message->show_messages();

if(!isset($_SESSION['user_id'])){
    ?>
    <p class="message error"><?php echo __('unauthorized') ?></p>
    <?php
}else{
    ?>
    <div class="user_form">
        <h1><?php echo $page_title ?></h1>
        <form method="post">
            <div class="user_form_input"><label for="name"><?php echo __('name:') ?></label><input id="name" name="name" type="text" value="<?php echo reget_post('name',$user_data_array['name']) ?>"></div>
            <div class="user_form_submit"><input type="submit" value="<?php echo __('update') ?>"></div>
        </form>
        <div class="user_form_link"><a href="change_password"><?php echo __('change password') ?></a></div>
    </div>
    <?php
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');