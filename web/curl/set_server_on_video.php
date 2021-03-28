<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
//echo $_SERVER['REMOTE_ADDR'].'<br />';
if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}

if($_GET['server']!='' && $_GET['video']!='')
{
    //include(BP.'/include/dat_system/dat_system_functions.php');
    $keyvalue_toset_array['server']=$_GET['server'];
    if($_GET['chunks']!='')
    {$keyvalue_toset_array['chunks']=unserialize($_GET['chunks']);}
    if($_GET['reso']!='')
    {$keyvalue_toset_array['reso']=$_GET['reso'];}
    if(isset($_GET['time']) && $_GET['time']!='')
    {$keyvalue_toset_array['time']=$_GET['time'];}

    set_elements('videos_index.dat',$_GET['video'],$keyvalue_toset_array);
    echo 'ok';

    //if ready=1 set ready on all associated videos
    if(isset($_GET['ready']) && $_GET['ready']==1){
        $query='SELECT * FROM videos WHERE file_md5 = :filedm5';
        $params=array(':filedm5'=>$_GET['video']);
        $associated_uploads=$db->query($query,$params);
        foreach($associated_uploads['request_result'] as $associated_upload){
            $upload_data_array=array();
            $upload_data_array['ready']=1;
            $db->update('videos',$upload_data_array['id'],$upload_data_array);
        }
    }
}

$db->disconnect();