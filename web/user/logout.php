<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

$user_logout = $user->logout();

$page_title = __('logout');
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

$message->show_messages();

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');