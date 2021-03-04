<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}


if((isset($_POST['code']) && $_POST['code']!='')){

    $upload_code_data_array = $db->load_by('upload_codes','code',$_POST['code']);
    if(!$upload_code_data_array){
        echo 'error';
    }else{
        echo 'ok:'.$upload_code_data_array['user_id'];
    }
}

$db->disconnect();