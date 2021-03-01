<?php
include('../includes/init.php');

$user_logout = $user->logout();

include('../templates/head_start.php');
include('../templates/head_end.php');
include('../header.php');

if(isset($user_logout) && $user_logout['success']==true){
    ?>
    <p>successfully logged out</p>
    <?php
}

include('../templates/page_end.php');