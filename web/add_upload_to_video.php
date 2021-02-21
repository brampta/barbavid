<?php
include('allowed_ips.php');
if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}

if($_GET['upload']!='' && $_GET['video']!='')
{
    include('dat_system_functions.php');
    include('video_library_manip.php');
    add_upload($_GET['video'],$_GET['upload']);
    echo 'ok';
}

?>