<?php


//function set_element($hash,$element_key,$element_value)
//{
//    $datfile_num=find_place_according_to_index($hash,'videos_index.dat');
//    $data=get_element_info($hash,$datfile_num);
//    $data_array=unserialize($data);
//    $data_array[$element_key]=$element_value;
//
//    $datfile_num=find_place_according_to_index($hash,'videos_index.dat');
//    add_or_update_element($hash,serialize($data_array),$datfile_num,'videos_index.dat');
//}
function add_upload($hash,$upload)
{
    $datfile_num=find_place_according_to_index($hash,'videos_index.dat');
    $data=get_element_info($hash,$datfile_num);
    $data_array=unserialize($data);

    $isalreadyin=array_search($upload,$data_array['uploads']);
    if($isalreadyin===false)
    {array_push($data_array['uploads'],$upload);}

    $datfile_num=find_place_according_to_index($hash,'videos_index.dat');
    add_or_update_element($hash,serialize($data_array),$datfile_num,'videos_index.dat');
}
function remove_upload($hash,$upload)
{
    global $main_domain;
    $datfile_num=find_place_according_to_index($hash,'videos_index.dat');
    $data=get_element_info($hash,$datfile_num);
    if($data==false)
    {return false;}
    $data_array=unserialize($data);
	//print_r($data_array);
    $wanted_key=array_search($upload,$data_array['uploads']);
    unset($data_array['uploads'][$wanted_key]);
	//print_r($data_array);

    $how_many_uploads=count($data_array['uploads']);
	//die('test1');
    if($how_many_uploads==0)
    {
        //delete video from server
        if(!function_exists("get_content_of_url"))
        {include(dirname(dirname(__FILE__)).'/function/curl.php');}
        $video_deleted='';
        $countturns=0;
        $maxturns=10;
		
		$actual_server=$data_array['server'];
		
		if(stripos($actual_server,'failedencoding')!==false || stripos($actual_server,'uploadserver')!==false)
		{$actual_server='videohost'.preg_replace("/[^0-9]/","", $actual_server);}
		
        $instruct_url='https://'.$actual_server.'.'.$main_domain.'/delete.php?video='.urlencode($hash);
		//echo '$instruct_url: '.$instruct_url.'<br />';
        while($video_deleted=='' && $countturns<$maxturns)
        {
            $countturns++;
            $video_deleted=get_content_of_url($instruct_url);
        }
        if($video_deleted!='ok')
        {
			echo 'instruct url was: '.$instruct_url.' \nserver answer was:'.htmlspecialchars($video_deleted).'\n';
			die('error, could not communicate with video server (1)');
		}

        //now delete row
        $datfile_num=find_place_according_to_index($hash,'videos_index.dat');
        remove_element($hash,$datfile_num,'videos_index.dat');
    }
    else
    {
        //update row
        $datfile_num=find_place_according_to_index($hash,'videos_index.dat');
        add_or_update_element($hash,serialize($data_array),$datfile_num,'videos_index.dat');
    }
}



?>