<?php
//include('allowed_ips.php');
include(dirname(dirname(__FILE__)).'/settings.php');

if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}


if((isset($_POST['code']) && $_POST['code']!='')){
    include(dirname(dirname(__FILE__)).'/includes/db.php');
    $db = new Db();
    $db->connect();

    $upload_code_data_array = $db->load_by('upload_codes','code',$_POST['code']);
    if(!$upload_code_data_array){
        echo 'error';
    }else{
        echo 'ok:'.$upload_code_data_array['user_id'];
    }

    $db->disconnect();
}