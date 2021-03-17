<?php
require(dirname(dirname(dirname(__FILE__))).'/include/init.php');
//include(BP.'/include/dat_system/dat_system_functions.php');
//include(BP.'/include/dat_system/video_library_manip.php');


$linebreak="<br>";
if(isset($argv[0])){$linebreak="\n";}

$goforreal=1;
$dat_system_testing=1; //this does not seem to be used anywhere, even in the inluded files nothing about that
$nowtime=time();
$expired_thresh=$nowtime-($upload_exire_after_x_days_of_inactiv*24*3600);
$expired_thresh_for_unseen=$nowtime-($remove_unseen_vids_after_x_days*24*3600);



function logstuff($logmessage){
	$expire_log=dirname(dirname(dirname(__FILE__))).'/log/expire_log.log';
	if(!file_exists($expire_log)){
		file_put_contents($expire_log,'<script type="text/javascript" src="/js/maketimus.js"></script>');
	}
	file_put_contents($expire_log,$logmessage.$linebreak,FILE_APPEND);
}


echo '<html>
<head>
<script type="text/javascript" src="/js/maketimus.js"></script>
<script type="text/javascript" src="/js/language_maketimus_js.php?lang=fr"></script>
</head>
<body>';


echo '$nowtime: '.$nowtime.', $expired_thresh: '.$expired_thresh.$linebreak;




$green='<span style="color:green;">';
$red='<span style="color:red;">';
$close='</span>';

//new ingest index:
function ingest_index($index_file_name)
{
	$ingest_index=array();
	$file = fopen(DAT_SYSTEM_HOME."/dat_system/".$index_file_name, "r") or exit("Unable to open file!");
	flock($file, LOCK_SH) or exit("Unable to lock file!");
	$count=0;
	while(!feof($file))
	{
		$count++;
		$line_data=trim(fgets($file));
		if($line_data!='')
		{
			$exploded_line_data=explode(' ',$line_data);
			$ingest_index[$count]=$exploded_line_data[1];
		}
	}
	flock($file, LOCK_UN) or exit("Unable to unlock file!");
	fclose($file);
	return $ingest_index;
}


//new special tasks:
//check if any upload is missing an entry in uploads_stats_index, if yes, create it!
//by the same occasion check if it is correctly figuring as associated upload in its video
echo '<h4>check if any upload is missing an entry in uploads_stats_index, if yes, create it!</h4>';
$index_file_name='uploads_index.dat';
$ingest_index=ingest_index($index_file_name);
foreach($ingest_index as $key=>$value)
{
    echo $value.'.dat'.$linebreak;
    //first verify that dat is still in the index in case of long execution
    $gotit=0;
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/".$index_file_name, "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");
    while(!feof($file))
    {
        $line_data=trim(fgets($file));
        $exploded_line_data=explode(' ',$line_data);
        if($exploded_line_data[1]==$value)
        {
            echo $value.'.dat is still in index, will then open and read it'.$linebreak;
            $gotit=1;
            break;
        }
    }
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);

    if($gotit==1)
    {
        //read dat
        $file = fopen(DAT_SYSTEM_HOME."/dat_system/".$value.".dat", "r") or exit("Unable to open file!");
        flock($file, LOCK_SH) or exit("Unable to lock file!");
        while(!feof($file))
        {
            $line_data=trim(fgets($file));
            if($line_data!='')
            {
                $exploded_line_data=explode(' ',$line_data);
                if($exploded_line_data[0]!='0')
                {
                    $data=unserialize($exploded_line_data[1]);
                    echo 'upload: '.$exploded_line_data[0].$linebreak;
                    echo 'data: '; print_r($data); echo $linebreak;
					
					if($data['file_md5']!=='0')
					{
						
					
						//check if it is listed as upload in its associated video
						$datfile_num = find_place_according_to_index($data['file_md5'], 'videos_index.dat');
						$video_info = get_element_info($data['file_md5'], $datfile_num);
						$video_info = unserialize($video_info);
						if($video_info!==false)
						{
							$associated_uploads=$video_info['uploads'];
							$isfiguring=0;
							foreach($associated_uploads as $key_uploads => $value_uploads)
							{
								if($value_uploads===$exploded_line_data[0])
								{
									echo $green.'this upload is figuring as upload in its associated video!'.$close.$linebreak;
									$isfiguring=1;
									break;
								}
							}
							if($isfiguring==0)
							{
								echo $red.'upload was not figuring in video, will add'.$close.$linebreak;
								logstuff('upload '.$exploded_line_data[0].' was not figuring in video, will add');
								echo 'add_upload('.$data['file_md5'].','.$exploded_line_data[0].');'.$linebreak;
								if($goforreal==1){add_upload($data['file_md5'],$exploded_line_data[0]);}
							}
							
							
							//check if upload stats exsists
							$datfile_num = find_place_according_to_index($exploded_line_data[0], 'uploads_stats_index.dat');
							$upload_stats = get_element_info($exploded_line_data[0], $datfile_num);
							if($upload_stats==false)
							{
								echo $red.'this upload ('.$exploded_line_data[0].') has not uplaod stats so it cannot expire, one must be created!!'.$close.$linebreak;
								logstuff('upload '.$exploded_line_data[0].' has not uplaod stats so it cannot expire, one must be created!!');
								//create upload stats
								$upload_stats_info['h'] = 0;
								$upload_stats_info['l'] = $nowtime;
								echo 'add_or_update_element('.$exploded_line_data[0].','.serialize($upload_stats_info).','.$datfile_num.', \'uploads_stats_index.dat\');'.$linebreak;
								if($goforreal==1){add_or_update_element($exploded_line_data[0],serialize($upload_stats_info),$datfile_num, 'uploads_stats_index.dat');}
							}
							else
							{echo $green.'good, '.$exploded_line_data[0].' has an upload stats entry'.$close.$linebreak;}
						}
						else
						{
							echo $red.'error, the video associated with this upload does not exist!!'.$close.$linebreak;
							//logstuff('upload '.$exploded_line_data[0].' error, the video associated with this upload does not exist!!');
						}
					}
					else
					{echo 'file_md5 was 0, this upload is removed, moving on...'.$linebreak;}
				}
            }

        }
        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);
    }
}






//run every video in the videos dat table and make sure that at least one of their associated uploads actually exists, if not, delete!
echo '<h4>run every video in the videos dat table and make sure that at least one of their associated uploads actually exists, if not, delete!</h4>';
$index_file_name='videos_index.dat';
$ingest_index=ingest_index($index_file_name);
foreach($ingest_index as $key=>$value)
{
    echo $value.'.dat'.$linebreak;
    //first verify that dat is still in the index in case of long execution
    $gotit=0;
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/".$index_file_name, "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");
    while(!feof($file))
    {
        $line_data=trim(fgets($file));
        $exploded_line_data=explode(' ',$line_data);
        if($exploded_line_data[1]==$value)
        {
            echo $value.'.dat is still in index, will then open and read it'.$linebreak;
            $gotit=1;
            break;
        }
    }
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);

    if($gotit==1)
    {
        //read dat
        $file = fopen(DAT_SYSTEM_HOME."/dat_system/".$value.".dat", "r") or exit("Unable to open file!");
        flock($file, LOCK_SH) or exit("Unable to lock file!");
		$countzzz=0;
        while(!feof($file))
        {
            $line_data=trim(fgets($file));
            if($line_data!='')
            {
                $exploded_line_data=explode(' ',$line_data);
                if($exploded_line_data[0]!='0')
                {
					$countzzz++;
                    $data=unserialize($exploded_line_data[1]);
                    echo 'hash: '.$exploded_line_data[0].$linebreak;
                    echo 'data: '; print_r($data); echo $linebreak;
					$associated_uploads=$data['uploads'];
					echo 'uploads: '; print_r($associated_uploads); echo $linebreak;
					//for each associated upload, check that it exists, if not remove from video
					//function will automatically remove video if last upload gets removed
					if(!$associated_uploads)
					{
						echo $red.'this video has no associated uploads, will remove!!!'.$close.$linebreak;
						logstuff('video '.$exploded_line_data[0].' this video has no associated uploads, will remove!!!');
						$uploads_to_remove[$countzzz]['hash']=$exploded_line_data[0];
						$uploads_to_remove[$countzzz]['upload']=0;
						//echo 'remove_upload('.$exploded_line_data[0].',0);'.$linebreak;
						//if($goforreal==1){remove_upload($exploded_line_data[0],0);}
					}
					else
					{
						foreach($associated_uploads as $key_uploads => $value_uploads)
						{
							$datfile_num = find_place_according_to_index($value_uploads, 'uploads_index.dat');
							$upload_stats = get_element_info($value_uploads, $datfile_num);
							if($upload_stats===false)
							{
								echo $red.'this upload does not actually exists, will remove from video!'.$close.$linebreak;
								logstuff('video '.$exploded_line_data[0].', upload '.$value_uploads.' this upload does not actually exists, will remove from video!');
								$uploads_to_remove[$countzzz]['hash']=$exploded_line_data[0];
								$uploads_to_remove[$countzzz]['upload']=$value_uploads;
								//echo 'remove_upload('.$exploded_line_data[0].','.$value_uploads.');'.$linebreak;
								//if($goforreal==1){remove_upload($exploded_line_data[0],$value_uploads);}
							}
						}
					}
                }
            }

        }
        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);
    }
}

if($uploads_to_remove)
{
	foreach($uploads_to_remove as $key_utm => $value_utr)
	{
		echo 'remove_upload('.$value_utr['hash'].','.$value_utr['upload'].');'.$linebreak;
		if($goforreal==1){remove_upload($value_utr['hash'],$value_utr['upload']);}
	}
}


//now expire videos that havent been watched for too long
//new!! also expire videos that have never been watched and have been uploaded for already a few days!!
//ingest index
// $ingest_index=array();
// $index_file_name='uploads_stats_index.dat';
// $file = fopen("/home/goblet/barbavid/dat_system/".$index_file_name, "r") or exit("Unable to open file!");
// flock($file, LOCK_SH) or exit("Unable to lock file!");
// $count=0;
// while(!feof($file))
// {
    // $count++;
    // $line_data=trim(fgets($file));
    // if($line_data!='')
    // {
        // $exploded_line_data=explode(' ',$line_data);
        // $ingest_index[$count]=$exploded_line_data[1];
    // }
// }
// flock($file, LOCK_UN) or exit("Unable to unlock file!");
// fclose($file);


echo '<h4>delete expired and unseen videos</h4>';
$index_file_name='uploads_stats_index.dat';
$ingest_index=ingest_index($index_file_name);


$countnotexpired=0;
$countexpired=0;
$countbug=0;
foreach($ingest_index as $key=>$value)
{
    echo $value.'.dat'.$linebreak;
    //first verify that dat is still in the index in case of long execution
    $gotit=0;
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/".$index_file_name, "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");
    while(!feof($file))
    {
        $line_data=trim(fgets($file));
        $exploded_line_data=explode(' ',$line_data);
        if($exploded_line_data[1]==$value)
        {
            echo $value.'.dat is still in index, will then open and read it'.$linebreak;
            $gotit=1;
            break;
        }
    }
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);

    if($gotit==1)
    {
        //read dat
        $file = fopen(DAT_SYSTEM_HOME."/dat_system/".$value.".dat", "r") or exit("Unable to open file!");
        flock($file, LOCK_SH) or exit("Unable to lock file!");
        while(!feof($file))
        {
            $line_data=trim(fgets($file));
            if($line_data!='')
            {
                $exploded_line_data=explode(' ',$line_data);
                if($exploded_line_data[0]!='0')
                {
                    $data=unserialize($exploded_line_data[1]);
                    echo 'upload: '.$exploded_line_data[0].$linebreak;
					$data["human_l"]="<script>document.write(maketimus(".$data["l"]."))</script>";
                    echo 'data: '; print_r($data); echo $linebreak;
					if($data['l']=='')
					{
						$countbug++;
						echo 'is bugged!'.$linebreak;
						$bugged[$exploded_line_data[0]]='x';
					}
                    else if($data['l']<$expired_thresh)
                    {
						$countexpired++;
                        echo 'is expired. '.$data['h'].' views, last <script>document.write(maketimus('.$data['l'].'))</script>'.$linebreak;
                        $expired[$exploded_line_data[0]]='x';
						logstuff('upload '.$exploded_line_data[0].' is expired. '.$data['h'].' views, last <script>document.write(maketimus('.$data['l'].'))</script>');
                    }
					else if($data['h']===0 && $data['l']<$expired_thresh_for_unseen)
					{
						$countexpired++;
						echo 'is unseen and over x days old. '.$data['h'].' views, last <script>document.write(maketimus('.$data['l'].'))</script>'.$linebreak;
						$expired[$exploded_line_data[0]]='x';
						logstuff('upload '.$exploded_line_data[0].' is unseen and over x days old. '.$data['h'].' views, last <script>document.write(maketimus('.$data['l'].'))</script>');
					}
                    else
                    {
						$countnotexpired++;
						echo 'is NOT expired.'.$linebreak;
					}
                }
            }

        }
        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);
    }
}

echo $countnotexpired.' NOT expired.'.$linebreak;
echo $countexpired.' expired.'.$linebreak;
echo $countbug.' bugged.'.$linebreak;

if($bugged)
{
	foreach($bugged as $key => $value)
	{
		echo 'unbugging '.$key.$linebreak;
		
		$keyvalue_toset_array['l'] = $nowtime;
		set_elements('uploads_stats_index.dat', $key, $keyvalue_toset_array);
	}
}

if($expired)
{	
    //include('video_library_manip.php'); already included at the beginning now, needed for other things...
    foreach($expired as $key => $value)
    {
        echo 'expiring '.$key.$linebreak;
        //find video file_md5 associated with this upload
        $datfile_num=find_place_according_to_index($key,'uploads_index.dat');
        $upload_info=get_element_info($key,$datfile_num);
		
        if($upload_info===false)
        {echo 'upload '.$key.' not found, moving on...'.$linebreak;}
		else
		{
			$upload_info=unserialize($upload_info);
			echo '$upload_info: '; print_r($upload_info); echo $linebreak;
			
			
				
				
			if($upload_info['file_md5']==0)
			{echo 'file_md5 is 0, moving on...'.$linebreak;}
			else
			{
				//$upload_info=unserialize($upload_info); need to do that before....
				
		
				//remove upload from that video (which could possibly delete video if no other associated uploads)
				echo 'remove_upload('.$upload_info['file_md5'].','.$key.');'.$linebreak;
				if($goforreal==1){remove_upload($upload_info['file_md5'],$key);}
				
			}
			
			//set upload file to 0 and suspend to reason that explains expiration
			$keyvalue_toset_array['file_md5']='0';
			$keyvalue_toset_array['suspend']=base64_encode('This video has expired because it has not been viewed for a too long period of time.<hr />Ce vidéo a expiré parcequ\'il n\'a pas été regardé pendant une trop longue période de temps.');
			if($goforreal==1){set_elements('uploads_index.dat',$key,$keyvalue_toset_array);}
		}
		
		//delete stats for this upload
		$datfile_num=find_place_according_to_index($key,'uploads_stats_index.dat');
		if($goforreal==1){remove_element($key,$datfile_num,'uploads_stats_index.dat');}
    }
}

echo '</body>
</html>';

$db->disconnect();