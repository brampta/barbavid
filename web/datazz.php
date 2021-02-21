<?php

//$_POST['upload_hash']='3d19rynb7134';
//$_POST['chunk_num']='3';


if(isset($_POST['upload_hash']) && isset($_POST['chunk_num']))
{
    include('dat_system_functions.php');


    //get upload info
    $datfile_num=find_place_according_to_index($_POST['upload_hash'],'uploads_index.dat');
    $upload_info=get_element_info($_POST['upload_hash'],$datfile_num);
    if($upload_info===false)
    {
        die('var1=test1&var2=test2&var3=test3');
    }
    $upload_info=unserialize($upload_info);



    if($upload_info['suspend']=='' && $upload_info['file_md5']!='0')
    {
        //get video info
        $datfile_num=find_place_according_to_index($upload_info['file_md5'],'videos_index.dat');
        $video_info=get_element_info($upload_info['file_md5'],$datfile_num);
        if($video_info===false)
        {
            die('var1=test1&var2=test2&var3=test3');
        }
        $video_info=unserialize($video_info);
    }
}

echo 'var1='.$upload_info['file_md5'].'&var2='.$video_info['chunks'][$_POST['chunk_num']].'&var3='.$video_info['server'];
//echo 'var1=test1&var2=test2&var3=test3';
?>