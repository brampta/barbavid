<?php
//include('allowed_ips.php');
include('settings.php');

if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}

if($_POST['hash']!='' && $_POST['data']!='' && $_POST['index_file']!='')
{
    include('dat_system_functions.php');
    $datfile_num=find_place_according_to_index($_POST['hash'],$_POST['index_file']);
    add_or_update_element($_POST['hash'],$_POST['data'],$datfile_num,$_POST['index_file']);
    echo 'ok';
}

?>