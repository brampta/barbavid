<?php
//include('allowed_ips.php');
include('settings.php');

if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}

//test1, video insert...
/*
$_POST['table_name']='videos';
$upload_info=array();
$upload_info['file_md5']='xxxxxxxxxxxxx';
$upload_info['title']='supa titre';
$upload_info['description']='asfasdfasdfasdfasdfasdf asf asf asf asd ffsdafasdfasfasdf asf asd ';
$upload_info['user_id']=1; //change when users are implemented..
$_POST['data']=serialize($upload_info);
*/

//test2, some update....
/*
$_POST['table_name']='videos';
$_POST['id']=7;
$upload_info=array();
$upload_info['file_md5']='yyyyyyyy';
$upload_info['title']='supa titre2';
$_POST['data']=serialize($upload_info);
*/

if((isset($_POST['hash']) && $_POST['hash']!='') && (isset($_POST['data']) && $_POST['data']!='') && (isset($_POST['index_file']) && $_POST['index_file']!=''))
{
    include('dat_system_functions.php');
    $datfile_num=find_place_according_to_index($_POST['hash'],$_POST['index_file']);
    add_or_update_element($_POST['hash'],$_POST['data'],$datfile_num,$_POST['index_file']);
    echo 'ok';
}else if((isset($_POST['data']) && $_POST['data']!='') && (isset($_POST['table_name']) && $_POST['table_name']!='')){
    include('includes/db.php');
    $db = new Db();

    if(isset($_POST['id']) && $_POST['id']!=''){
        //update
        $db->connect();
        $result = $db->update($_POST['table_name'],$_POST['id'],unserialize($_POST['data']));
        $db->disconnect();
        if($result==1){
            echo 'ok';
        }
    }else{
        //create
        $db->connect();
        $id = $db->insert($_POST['table_name'],unserialize($_POST['data']));
        $db->disconnect();
        echo 'ok:'.$id;
    }
}

?>