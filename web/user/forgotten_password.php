<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(isset($_POST['email'])){
    $user_password_reset_email_sending = $user->send_password_reset_email($_POST['email']);
}

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

if(isset($user_password_reset_email_sending) && $user_password_reset_email_sending['success']==true){
    ?>
    <p>if this email exists in the database, you will receive an email with a link to reset your password.</p>
    <?php
}
?>
<h1>reset password</h1>
<form method="post">
    <div><label for="email">email:</label><input id="email" name="email" type="text"></div>
    <div><input type="submit" value="send password reset"></div>
</form>
<?php

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');