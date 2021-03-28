<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}

if($_GET['upload']!='' && $_GET['video']!='')
{
    //include(BP.'/include/dat_system/dat_system_functions.php');
    //include(BP.'/include/dat_system/video_library_manip.php');
    add_upload($_GET['video'],$_GET['upload']);
    echo 'ok';

    //now get video info to determine if we should make this upload ready
    $datfile_num = find_place_according_to_index($_GET['video'], 'videos_index.dat');
    $video_info = get_element_info($_GET['video'], $datfile_num);
    if($video_info){
        $video_info = unserialize($video_info);
        if (
            (substr($video_info['server'], 0, 6) == 'upload')
                ||
            (substr($video_info['server'], 0, 15) == 'failedencoding_')
        ){
            //seems that this video is not properly encoded, it is either not finished encoding or encoding failed
        }else{
            $upload_data_array=array();
            $upload_data_array['ready']=1;
            $db->update_by('videos','hash',$_GET['upload'],$upload_data_array);
        }
    }
}

$db->disconnect();