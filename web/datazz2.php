<?php

//$_POST['upload_hash']='3d19rynb7134';
//$_POST['chunk_num']='3';


if(isset($_POST['upload_hash']))
{
	$nowtime = time();
    include('dat_system_functions.php');


    //get upload info
    $datfile_num=find_place_according_to_index($_POST['upload_hash'],'uploads_index.dat');
    $upload_info=get_element_info($_POST['upload_hash'],$datfile_num);
    if($upload_info===false)
    {
        die('var1=test1&var2=test2&var3=test3');
    }
    $upload_info=unserialize($upload_info);


	if ($upload_info['file_md5'] != '0') {
    //add hit
    if ($upload_info['suspend'] == '') {
			$keyvalue_toset_array['h'] = '+inc+';
		}
		$keyvalue_toset_array['l'] = $nowtime;
		set_elements('uploads_stats_index.dat', $_POST['upload_hash'], $keyvalue_toset_array);
	}

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
    else
    {
        $video_info['server']='suspended';
        $video_info['total_chunks']=0;
    }
}




$rezu_arr['file_md5']=$upload_info['file_md5'];
$rezu_arr['server']=$video_info['server'];
$countchunks=0;
if($video_info['chunks'])
{
    foreach($video_info['chunks'] as $key => $value)
    {
        $countchunks++;
        $rezu_arr['chunk_'.$countchunks]=$value;
    }
}
$rezu_arr['total_chunks']=$countchunks;
$rezu_arr['upload_title']=base64_decode($upload_info['title']);
$rezu_arr['upload_description']=base64_decode($upload_info['description']);
$rezu_arr['upload_popup']=base64_decode($upload_info['popup']);
$rezu_arr['suspend']=str_replace('<hr />','
',base64_decode($upload_info['suspend']));
echo http_build_query($rezu_arr);
?>
