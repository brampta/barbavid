<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
include(BP.'/include/function/forms.php');

if(isset($_POST['email'])){
    $user_validation_email_sending = $user->send_validation_email($_POST['email']);
}

$page_title = __('reset password');
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

$message->show_messages();

?>
<div class="user_form">
    <h1><?php echo $page_title ?></h1>
    <form method="post">
        <div class="user_form_input"><label for="email"><?php echo __('email:') ?></label><input id="email" name="email" type="text" value="<?php echo reget_post('email') ?>"></div>
        <div class="user_form_submit"><input type="submit" value="<?php echo __('resend confirmation email') ?>"></div>
    </form>
</div>
<?php

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');