<?php
include(dirname(dirname(__FILE__)).'/include/init.php');

if(isset($_POST['admin_pass']) && $_POST['admin_pass']==$dat_system_admin_pass)
{$_SESSION['admin']=1;}
if(!isset($_SESSION['admin']))
{
	echo '<form method="post"><input type="password" name="admin_pass" /><input type="submit" value="admin password" /></form>';
	die();
}





$indexes = array('uploads_index', 'videos_index', 'uploads_stats_index', 'videokeys_index');


//changing for password protect!! IP protect is shit annoying because my IP keeps changing like a beast!!
//if ($_SERVER['REMOTE_ADDR'] != $admin_ip) {
//    die('unauthorized');
//}
$nowtime = time();
//require(BP.'/include/dat_system/dat_system_functions.php');
//include(BP.'/include/dat_system/video_library_manip.php');

echo '<html>
    <head>
    <title>Barbavid Dat System Admin Panelzz</title>
    <script type="text/javascript" src="/js/maketimus.js"></script>
    <script type="text/javascript" src="/js/language_maketimus_js.php?lang=' . urlencode($language) . '"></script>
    <link rel="stylesheet" type="text/css" href="/css/style.css" />
    </head>
    <body>
    <h1>Barbavid Dat System Admin Panelzz</h1>';





if (isset($_POST['reset_dat_system']) && isset($_POST['reset_dat_system_imsure1']) && isset($_POST['reset_dat_system_imsure2']) && isset($_POST['reset_dat_system_imsure3'])) {
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/busy.dat", "w") or exit("Unable to open file!");
    flock($file, LOCK_EX) or exit("Unable to lock file!");
    fwrite($file, $nowtime);
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);

    sleep($sleep_howlong_after_settingtobusy);

    exec('rm -R '.DAT_SYSTEM_HOME.'/dat_system; mkdir '.DAT_SYSTEM_HOME.'/dat_system; cp -R '.DAT_SYSTEM_HOME.'/dat_system_orig/* '.DAT_SYSTEM_HOME.'/dat_system/');
    echo 'Dat System has been reset!<br />';

    $file = fopen(DAT_SYSTEM_HOME."/dat_system/busy.dat", "w") or exit("Unable to open file!");
    flock($file, LOCK_EX) or exit("Unable to lock file!");
    fwrite($file, 0);
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
}
echo '<form method="post">
    <h4>Reset Dat System</h4>
    <input type="submit" name="reset_dat_system" value="reset" /><br />
    <input type="checkbox" name="reset_dat_system_imsure1" />im sure<br />
    <input type="checkbox" name="reset_dat_system_imsure2" />im really sure<br />
    <input type="checkbox" name="reset_dat_system_imsure3" />im really really, really sure<br />
    </form><hr />';






if (isset($_GET['showdat'])) {
    echo '<a href="/dat_system_admin">exit</a>';
    echo '<h4>' . $_GET['showdat'] . '.dat</h4>';
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/" . $_GET['showdat'] . ".dat", "r");
    flock($file, LOCK_SH);
    echo '<table style="background-color:black;">';
    while ($file && !feof($file)) {
        $line_data = trim(fgets($file));
        if ($line_data != '') {
            $xploded_stuff = explode(' ', $line_data);
            //echo $line_data.'<br />';
            echo '<tr><td style="background-color:#00062F;vertical-align:top;">' . $xploded_stuff[0] . '</td><td style="background-color:black;">' . htmlspecialchars($xploded_stuff[1]) . '</td></tr>';
        }
    }
    echo '</table>';
    flock($file, LOCK_UN);
    fclose($file);
} else {
    echo '<a href="/dat_system_admin">reload</a>';
}

//echo '<table><tr><td style="vertical-align:top;">';
//
//echo '<h4>uploads_index</h4>';
//$remember_url2md5shot_index_dats=array();
//$file = fopen("/home/goblet/barbavid/dat_system/uploads_index.dat", "r");
//flock($file, LOCK_SH);
//while($file && !feof($file))
//{
//    $line_data=trim(fgets($file));
//    $xploded_stuff=explode(' ',$line_data);
//    array_push($remember_url2md5shot_index_dats,$xploded_stuff[1]);
//
//    echo '<a href="?showdat='.$xploded_stuff[1].'" style="text-decoration:none;font-size:80%;">'.$line_data.'</a><br />';
//}
//flock($file, LOCK_UN);
//fclose($file);
//
//echo '</td><td style="vertical-align:top;">';
//
//echo '<h4>videos_index</h4>';
//$remember_md5shot2urls_index_dats=array();
//$file = fopen("/home/goblet/barbavid/dat_system/videos_index.dat", "r");
//flock($file, LOCK_SH);
//while($file && !feof($file))
//{
//    $line_data=trim(fgets($file));
//    $xploded_stuff=explode(' ',$line_data);
//    array_push($remember_md5shot2urls_index_dats,$xploded_stuff[1]);
//
//    echo '<a href="?showdat='.$xploded_stuff[1].'" style="text-decoration:none;font-size:80%;">'.$line_data.'</a><br />';
//}
//flock($file, LOCK_UN);
//fclose($file);
//
//
//echo '</td><td style="vertical-align:top;">';
//
//
//echo '<h4>uploads_stats_index</h4>';
//$remember_md5shot2urls_index_dats=array();
//$file = fopen("/home/goblet/barbavid/dat_system/uploads_stats_index.dat", "r");
//flock($file, LOCK_SH);
//while($file && !feof($file))
//{
//    $line_data=trim(fgets($file));
//    $xploded_stuff=explode(' ',$line_data);
//    array_push($remember_md5shot2urls_index_dats,$xploded_stuff[1]);
//
//    echo '<a href="?showdat='.$xploded_stuff[1].'" style="text-decoration:none;font-size:80%;">'.$line_data.'</a><br />';
//}
//flock($file, LOCK_UN);
//fclose($file);
//
//
//echo '</td><td style="vertical-align:top;">';
//
//
//echo '<h4>videokeys_index</h4>';
//$remember_md5shot2urls_index_dats=array();
//$file = fopen("/home/goblet/barbavid/dat_system/videokeys_index.dat", "r");
//flock($file, LOCK_SH);
//while($file && !feof($file))
//{
//    $line_data=trim(fgets($file));
//    $xploded_stuff=explode(' ',$line_data);
//    array_push($remember_md5shot2urls_index_dats,$xploded_stuff[1]);
//
//    echo '<a href="?showdat='.$xploded_stuff[1].'" style="text-decoration:none;font-size:80%;">'.$line_data.'</a><br />';
//}
//flock($file, LOCK_UN);
//fclose($file);
//
//
//
//echo '</td></tr><tr><td style="vertical-align:top;">';


echo '<h5>Barbavid tables</h5>';
foreach ($indexes as $key => $value) {
    echo '<h4>' . $value . '</h4>';
    $remember_index_dats[$value] = array();
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/" . $value . ".dat", "r");
    flock($file, LOCK_SH);
    while ($file && !feof($file)) {
        $line_data = trim(fgets($file));
        $xploded_stuff = explode(' ', $line_data);
        if(isset($xploded_stuff[1])){
            array_push($remember_index_dats[$value], $xploded_stuff[1]);
            echo '<a href="?showdat=' . $xploded_stuff[1] . '" style="text-decoration:none;font-size:80%;">' . $line_data . '</a><br />';
        }
    }
    flock($file, LOCK_UN);
    fclose($file);
}



echo '<h5>system tables</h5>';
echo '<table><tr><td style="vertical-align:top;">';
echo '<h4>unique_number</h4>';
$file = fopen(DAT_SYSTEM_HOME."/dat_system/unique_number.dat", "r");
flock($file, LOCK_SH);
while ($file && !feof($file)) {
    $line_data = trim(fgets($file));
    $xploded_stuff = explode(' ', $line_data);
    echo $line_data . '<br />';
}
flock($file, LOCK_UN);
fclose($file);
echo '</td><td style="vertical-align:top;">';
echo '<h4>unique_numbers_to_reuse</h4>';
$file = fopen(DAT_SYSTEM_HOME."/dat_system/unique_numbers_to_reuse.dat", "r");
flock($file, LOCK_SH);
while ($file && !feof($file)) {
    $line_data = trim(fgets($file));
    $xploded_stuff = explode(' ', $line_data);
    echo $line_data . '<br />';
}
flock($file, LOCK_UN);
fclose($file);
echo '</td><td style="vertical-align:top;">';
echo '<h4>busy</h4>';
$file = fopen(DAT_SYSTEM_HOME."/dat_system/busy.dat", "r") or exit("Unable to open file!");
flock($file, LOCK_SH) or exit("Unable to lock file!");
while (!feof($file)) {
    $line_data = trim(fgets($file));
    echo $line_data . '<br />';
}
flock($file, LOCK_UN) or exit("Unable to unlock file!");
fclose($file);
echo '</td></tr></table>';

echo '<hr />';

echo '<h4>Backups</h4>';
echo '<a href="/bakup_dat_system" target="_blank">Backup Dat System</a><br />';
echo '<a onclick="keep_only_x_baks();" style="cursor:pointer;">Remove All Backups Except last ' . $dat_system_keep_last_x_baks . '</a>
    <script type="text/javascript">
    function keep_only_x_baks()
    {
        var maybe = confirm(\'are you shurio that you wanged to remove all the bagubbs exxepp the last ' . $dat_system_keep_last_x_baks . '??\');
        if(maybe)
        {window.open("/keep_only_x_baks");}
    }
    function restore_bak(thistimez,gofor)
    {
        var maybe = confirm(\'are you shure that you want to roll back the Dat System to \' + thistimez);
        if(maybe)
        {window.open("/restore_bak?bak=" + gofor);}
    }
    </script><br />';

echo 'current baks:<br />';
$allcurrentbaks = glob(DAT_SYSTEM_HOME.'/dat_system_baks/*');
foreach ($allcurrentbaks as $key => $value) {
    $last_slash = strripos($value, '/');
    $numz = substr($value, $last_slash + 1);
    echo '<script type="text/javascript">
        var thistimez=maketimus(' . $numz . ');
        document.write(thistimez + " (<a onclick=\"restore_bak(\'" + thistimez + "\',\'' . $value . '\');\" style=\"cursor:pointer;\">restore</a>)<br />");
        </script>';
}

echo '<hr />';






//echo '<table><tr><td>';
//
//if(isset($_POST['combine_uploads_index']) && isset($_POST['combine_uploads_index_imsure1']))
//{
//    echo 'happenz1<br />';
//
//    $isbudy=dat_system_is_busy();
//    if($isbudy==true)
//    {die('dat system busy.<br />');}
//
//    $file = fopen("/home/goblet/barbavid/dat_system/busy.dat", "w") or exit("Unable to open file!");
//    flock($file, LOCK_EX) or exit("Unable to lock file!");
//    fwrite($file,$nowtime);
//    flock($file, LOCK_UN) or exit("Unable to unlock file!");
//    fclose($file);
//
//    sleep($sleep_howlong_after_settingtobusy);
//
//    $count=0;
//
//    $file = fopen("/home/goblet/barbavid/dat_system/temp.dat", "w") or exit("Unable to open file!");
//    fclose($file);
//
//    $maxturns=2000000;
//
//    $dat1=0;
//    $dat2=0;
//    $totalturns=count($remember_url2md5shot_index_dats);
//    foreach($remember_url2md5shot_index_dats as $key => $value)
//    {
//        set_time_limit(300);
//        if($value!='')
//        {
//            $count++;
//            if($count>$maxturns)
//            {
//                echo 'max loops exceeded ('.$maxturns.'), will stop there<br >';
//                break;
//            }
//            else
//            {
//                //echo '$value: '.$value.'<br />';
//                $oneortwo=(($count-1)%2)+1;
//                //echo '$oneortwo: '.$oneortwo.'<br />';
//                if($oneortwo==1)
//                {$dat1=$value;}
//                else if($oneortwo==2)
//                {
//                    $dat2=$value;
//
//
//
//                    $mycommand='cat /home/goblet/barbavid/dat_system/'.$dat1.'.dat /home/goblet/barbavid/dat_system/'.$dat2.'.dat > /home/goblet/barbavid/dat_system/temp.dat; rm /home/goblet/barbavid/dat_system/'.$dat2.'.dat; mv /home/goblet/barbavid/dat_system/temp.dat /home/goblet/barbavid/dat_system/'.$dat1.'.dat';
//                    echo $count.'/'.$totalturns.' - '.$mycommand.' - ';
//                    exec($mycommand);
//
//                    echo 'remove_from_index('.$dat2.',\'uploads_index.dat\') - ';
//                    remove_from_index($dat2,'uploads_index.dat');
//
//                    echo 'will recycle #'.$dat2.'<br />';
//                    recycle_unique_number($dat2);
//
//
//                }
//            }
//        }
//    }
//
//    $file = fopen("/home/goblet/barbavid/dat_system/busy.dat", "w") or exit("Unable to open file!");
//    flock($file, LOCK_EX) or exit("Unable to lock file!");
//    fwrite($file,0);
//    flock($file, LOCK_UN) or exit("Unable to unlock file!");
//    fclose($file);
//
//
//}
//echo '<form method="post">
//    <h4>Combine uploads_index</h4>
//    <input type="submit" name="combine_uploads_index" value="combine" /><br />
//    <input type="checkbox" name="combine_uploads_index_imsure1" />im sure<br />
//    </form>';
//
//
//echo '</td><td>';
//
//
//if(isset($_POST['combine_videos_index']) && isset($_POST['combine_videos_index_imsure1']))
//{
//    echo 'happenz2<br />';
//
//    $isbudy=dat_system_is_busy();
//    if($isbudy==true)
//    {die('dat system busy.<br />');}
//
//    $file = fopen("/home/goblet/barbavid/dat_system/busy.dat", "w") or exit("Unable to open file!");
//    flock($file, LOCK_EX) or exit("Unable to lock file!");
//    fwrite($file,$nowtime);
//    flock($file, LOCK_UN) or exit("Unable to unlock file!");
//    fclose($file);
//
//    sleep($sleep_howlong_after_settingtobusy);
//
//    $count=0;
//
//    $file = fopen("/home/goblet/barbavid/dat_system/temp.dat", "w") or exit("Unable to open file!");
//    fclose($file);
//
//    $maxturns=2000000;
//
//    $dat1=0;
//    $dat2=0;
//    $totalturns=count($remember_md5shot2urls_index_dats);
//    foreach($remember_md5shot2urls_index_dats as $key => $value)
//    {
//        set_time_limit(300);
//        if($value!='')
//        {
//            $count++;
//            if($count>$maxturns)
//            {
//                echo 'max loops exceeded ('.$maxturns.'), will stop there<br >';
//                break;
//            }
//            else
//            {
//                //echo '$value: '.$value.'<br />';
//                $oneortwo=(($count-1)%2)+1;
//                //echo '$oneortwo: '.$oneortwo.'<br />';
//                if($oneortwo==1)
//                {$dat1=$value;}
//                else if($oneortwo==2)
//                {
//                    $dat2=$value;
//
//
//
//                    $mycommand='cat /home/goblet/barbavid/dat_system/'.$dat1.'.dat /home/goblet/barbavid/dat_system/'.$dat2.'.dat > /home/goblet/barbavid/dat_system/temp.dat; rm /home/goblet/barbavid/dat_system/'.$dat2.'.dat; mv /home/goblet/barbavid/dat_system/temp.dat /home/goblet/barbavid/dat_system/'.$dat1.'.dat';
//                    echo $count.'/'.$totalturns.' - '.$mycommand.' - ';
//                    exec($mycommand);
//
//                    echo 'remove_from_index('.$dat2.',\'videos_index.dat\') - ';
//                    remove_from_index($dat2,'videos_index.dat');
//
//                    echo 'will recycle #'.$dat2.'<br />';
//                    recycle_unique_number($dat2);
//
//
//                }
//            }
//        }
//    }
//
//    $file = fopen("/home/goblet/barbavid/dat_system/busy.dat", "w") or exit("Unable to open file!");
//    flock($file, LOCK_EX) or exit("Unable to lock file!");
//    fwrite($file,0);
//    flock($file, LOCK_UN) or exit("Unable to unlock file!");
//    fclose($file);
//
//
//}
//echo '<form method="post">
//    <h4>Combine videos_index</h4>
//    <input type="submit" name="combine_videos_index" value="combine" /><br />
//    <input type="checkbox" name="combine_videos_index_imsure1" />im sure<br />
//    </form>';
//
//
//
//echo '</td></tr></table>';
//






foreach ($indexes as $keyzz => $valuezz) {

    if (isset($_POST['combine_' . $valuezz]) && isset($_POST['combine_' . $valuezz . '_imsure1'])) {
        echo 'happenz' . $valuezz . '<br />';

        $isbudy = dat_system_is_busy();
        if ($isbudy == true) {
            die('dat system busy.<br />');
        }

        $file = fopen(DAT_SYSTEM_HOME."/dat_system/busy.dat", "w") or exit("Unable to open file!");
        flock($file, LOCK_EX) or exit("Unable to lock file!");
        fwrite($file, $nowtime);
        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);

        sleep($sleep_howlong_after_settingtobusy);

        $count = 0;

        $file = fopen(DAT_SYSTEM_HOME."/dat_system/temp.dat", "w") or exit("Unable to open file!");
        fclose($file);

        $maxturns = 2000000;

        $dat1 = 0;
        $dat2 = 0;
        $totalturns = count($remember_index_dats[$valuezz]);
        foreach ($remember_index_dats[$valuezz] as $key => $value) {
            set_time_limit(300);
            if ($value != '') {
                $count++;
                if ($count > $maxturns) {
                    echo 'max loops exceeded (' . $maxturns . '), will stop there<br >';
                    break;
                } else {
                    //echo '$value: '.$value.'<br />';
                    $oneortwo = (($count - 1) % 2) + 1;
                    //echo '$oneortwo: '.$oneortwo.'<br />';
                    if ($oneortwo == 1) {
                        $dat1 = $value;
                    } else if ($oneortwo == 2) {
                        $dat2 = $value;



                        $mycommand = 'cat '.DAT_SYSTEM_HOME.'/dat_system/' . $dat1 . '.dat '.DAT_SYSTEM_HOME.'/dat_system/' . $dat2 . '.dat > '.DAT_SYSTEM_HOME.'/dat_system/temp.dat; rm '.DAT_SYSTEM_HOME.'/dat_system/' . $dat2 . '.dat; mv '.DAT_SYSTEM_HOME.'/dat_system/temp.dat '.DAT_SYSTEM_HOME.'/dat_system/' . $dat1 . '.dat';
                        echo $count . '/' . $totalturns . ' - ' . $mycommand . ' - ';
                        exec($mycommand);

                        echo 'remove_from_index(' . $dat2 . ',\'' . $valuezz . '.dat\') - ';
                        remove_from_index($dat2, $valuezz . '.dat');

                        echo 'will recycle #' . $dat2 . '<br />';
                        recycle_unique_number($dat2);
                    }
                }
            }
        }

        $file = fopen(DAT_SYSTEM_HOME."/dat_system/busy.dat", "w") or exit("Unable to open file!");
        flock($file, LOCK_EX) or exit("Unable to lock file!");
        fwrite($file, 0);
        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);
    }
    echo '<form method="post">
    <h4>Combine ' . $valuezz . '</h4>
    <input type="submit" name="combine_' . $valuezz . '" value="combine" /><br />
    <input type="checkbox" name="combine_' . $valuezz . '_imsure1" />im sure<br />
    </form>';
}




if(isset($_POST['video_todelete']) && isset($_POST['delete_video_imsure1']))
{
	//get list of uploads that this video has
	$datfile_num = find_place_according_to_index($_POST['video_todelete'], 'videos_index.dat');
    $video_info = get_element_info($_POST['video_todelete'], $datfile_num);
    if ($video_info === false) {
        die('error, video not found!');
    }
    $video_info = unserialize($video_info);
	print_r($video_info); echo '<br />';
	$howmany_uploads = count($video_info['uploads']);
	
	//if has associated uploads run remove_upload() for each upload
	if($howmany_uploads>=1)
	{	
		foreach($video_info['uploads'] as $key_uploadsofdeletion  => $value_uploadsofdeletion)
		{
			echo "remove_upload('".$_POST['video_todelete']."','".$value_uploadsofdeletion."');<br />";
			remove_upload($_POST['video_todelete'],$value_uploadsofdeletion);
			$keyvalue_toset_array['file_md5'] = '0';
			set_elements('uploads_index.dat', $value_uploadsofdeletion, $keyvalue_toset_array);
			
			//delete stats for this upload
			$datfile_num = find_place_according_to_index($value_uploadsofdeletion, 'uploads_stats_index.dat');
			remove_element($value_uploadsofdeletion, $datfile_num, 'uploads_stats_index.dat');
		}
	}
	//if it has no associated upload, run remove_upload() for a blank upload
	if($howmany_uploads==0)
	{
		echo "remove_upload('".$_POST['video_todelete']."','');<br />";
		remove_upload($_POST['video_todelete'],'');
	}
}


echo '<form method="post">
    <h4>Delete video</h4>
	<input type="" name="video_todelete" value="';if(isset($_POST['video_todelete'])){echo htmlspecialchars($_POST['video_todelete']);} echo '" /><br />
    <input type="submit" value="delete" /><br />
    <input type="checkbox" name="delete_video_imsure1" />im sure<br />
    </form>';








echo '</body></html>';
?>
