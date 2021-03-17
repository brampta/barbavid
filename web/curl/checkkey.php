<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');//echo $_SERVER['REMOTE_ADDR'].'<br />';
if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}


if($_GET['kn']!='' && $_GET['k']!='')
{
    //include(BP.'/include/dat_system/dat_system_functions.php');
    $datfile_num=find_place_according_to_index($_GET['kn'],'videokeys_index.dat');
    $current_key_data=get_element_info($_GET['kn'],$datfile_num);
    if($current_key_data!==false)
    {
        $key_data_array=unserialize($current_key_data);
        if($key_data_array['k']==$_GET['k'])
        {echo 'ok';}
    }
}

$db->disconnect();