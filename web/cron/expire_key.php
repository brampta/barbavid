<?php
require(dirname(dirname(dirname(__FILE__))).'/include/init.php');
$nowtime=time();
$expired_thresh=$nowtime-($videokey_exire_after_x_hours*3600);
echo '$nowtime: '.$nowtime.', $expired_thresh: '.$expired_thresh.'<br />';

//ingest index
$ingest_index=array();
$index_file_name='videokeys_index.dat';
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
        $file = fopen(DAT_SYSTEM_HOME."/dat_system/".$value.".dat", "r+") or exit("Unable to open file!");
        flock($file, LOCK_EX) or exit("Unable to lock file!");
        $rewrite='';
        $expiredes=0;
        while(!feof($file))
        {
            $pure_line_data=fgets($file);
            $line_data=trim($pure_line_data);
            if($line_data!='')
            {
                $exploded_line_data=explode(' ',$line_data);
                if($exploded_line_data[0]!='0')
                {
                    $data=unserialize($exploded_line_data[1]);
                    echo 'keyname: '.$exploded_line_data[0].'<br />';
                    echo 'data: '; print_r($data); echo '<br />';
                    if($data['t']<$expired_thresh)
                    {
                        echo 'is expired.<br />';
                        $expiredes++;
                    }
                    else
                    {
                        echo 'is NOT expired.<br />';
                        $rewrite=$rewrite.$pure_line_data;
                    }
                }
            }

        }

        if($expiredes>=1)
        {
            fseek($file,0);
            ftruncate($file,0);
            fwrite($file,'0 0
'.$rewrite);
        }

        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);
    }
}

$db->disconnect();