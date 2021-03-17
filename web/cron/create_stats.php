<?php
//this script create the missing uploads_stats for uploads that dont have it. run once in a while to make sure expire script doesnt miss anything...

require(dirname(dirname(dirname(__FILE__))).'/include/init.php');
//include(BP.'/include/dat_system/dat_system_functions.php');

//ingest index
$ingest_index=array();
$index_file_name='uploads_index.dat';
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


foreach($ingest_index as $key=>$value)
{
    echo $value.'.dat<br />';
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
            echo $value.'.dat is still in index, will then open and read it<br />';
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
                    echo 'upload: '.$exploded_line_data[0].'<br />';
                    echo 'data: '; print_r($data); echo '<br />';
					
					$datfile_num = find_place_according_to_index($exploded_line_data[0], 'uploads_stats_index.dat');
					$upload_stats_info = get_element_info($exploded_line_data[0], $datfile_num);
					if ($upload_stats_info === false)
					{
						$keyvalue_toset_array['h'] = 0;
						$keyvalue_toset_array['l'] = $data['time'];
						echo '$keyvalue_toset_array: ';
						print_r($keyvalue_toset_array);
						echo '<br />';
						echo "================set_elements('uploads_stats_index.dat', '".$exploded_line_data[0]."', \$keyvalue_toset_array);<br />";
						set_elements('uploads_stats_index.dat', $exploded_line_data[0], $keyvalue_toset_array);
					}
                }
            }

        }
        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);
    }
}

$db->disconnect();