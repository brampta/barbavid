<?php
include('../includes/init.php');

if(isset($_POST['email'])){
    $user_password_reset_email_sending = $user->send_password_reset_email($_POST['email']);
}

include('../templates/head_start.php');
include('../templates/head_end.php');
include('../header.php');

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

include('../templates/page_end.php');