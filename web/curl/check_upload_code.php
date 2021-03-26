<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}


if(
    (isset($_POST['code']) && $_POST['code']!='')
        &&
    (isset($_POST['channel_hash']) && $_POST['channel_hash']!='')
){

    $upload_code_data_array = $db->load_by('upload_codes','code',$_POST['code']);
    if(!$upload_code_data_array){
        echo 'error:invalid upload code';
    }else{
        $channel_data_array=$channel->load_by_hash($_POST['channel_hash']);
        if(!in_array($upload_code_data_array['user_id'],$channel_data_array['admin_ids'])){
            echo 'error:invalid channel';
        }else{
            echo 'ok:'.$upload_code_data_array['user_id'].':'.$channel_data_array['id'];
        }
    }
}else{
    echo 'error:parameters missing';
}

$db->disconnect();