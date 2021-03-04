<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}

if($_GET['upload']!='' && $_GET['video']!='')
{
    include(BP.'/include/dat_system/dat_system_functions.php');
    include(BP.'/include/dat_system/video_library_manip.php');
    add_upload($_GET['video'],$_GET['upload']);
    echo 'ok';
}

$db->disconnect();