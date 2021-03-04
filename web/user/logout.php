<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

$user_logout = $user->logout();

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

if(isset($user_logout) && $user_logout['success']==true){
    ?>
    <p>successfully logged out</p>
    <?php
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');