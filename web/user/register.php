<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(isset($_POST['email'])){
    $user_registration = $user->register($_POST['email'],$_POST['name'],$_POST['password'],$_POST['password2']);
}

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

if(isset($user_registration) && $user_registration['success']==true){
    ?>
    <p>successfully registered</p>
    <p>a confirmation email has been sent</p>
    <?php
}else{
    if(isset($user_registration) && in_array('email_regsitered',$user_registration['errors'])){
        ?>
        <p>email already registered</p>
        <?php
    }
    if(isset($user_registration) && in_array('insert_error',$user_registration['errors'])){
        ?>
        <p>database error</p>
        <?php
    }
    if(isset($user_registration) && in_array('passwords_dont_match',$user_registration['errors'])){
        ?>
        <p>passwords don't match</p>
        <?php
    }
    ?>
    <h1>register account</h1>
    <form method="post">
        <div><label for="email">email:</label><input id="email" name="email" type="text"></div>
        <div><label for="name">name:</label><input id="name" name="name" type="text"></div>
        <div><label for="password">password:</label><input id="password" name="password" type="password"></div>
        <div><label for="password2">retype password:</label><input id="password2" name="password2" type="password"></div>
        <div><input type="submit" value="register"></div>
    </form>
    <?php
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');