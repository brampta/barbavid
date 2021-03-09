<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(isset($_GET['code'])){
    $user_validation = $user->validate_email($_GET['code']);
}

$page_title = __('email validation');
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

$message->show_messages();

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');