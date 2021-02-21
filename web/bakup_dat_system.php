<?php
$sleep_howlong_after_settingtobusy=5;

require('/var/www/barbavid/settings.php');
if($_SERVER['REMOTE_ADDR']!=$admin_ip && $_SERVER['REMOTE_ADDR']!='')
{die('unauthorized');}
$nowtime=time();

$file = fopen("/home/goblet/barbavid/dat_system/busy.dat", "w") or exit("Unable to open file!");
flock($file, LOCK_EX) or exit("Unable to lock file!");
fwrite($file,$nowtime);
flock($file, LOCK_UN) or exit("Unable to unlock file!");
fclose($file);

sleep($sleep_howlong_after_settingtobusy);

$nowtime=time();
set_time_limit(30000000);
exec('mkdir /home/goblet/barbavid/dat_system_baks/'.$nowtime.'; cp -R /home/goblet/barbavid/dat_system /home/goblet/barbavid/dat_system_baks/'.$nowtime.'/  2>&1',$outz);
print_r($outz);
echo 'backup #'.$nowtime.' is saved';


$file = fopen("/home/goblet/barbavid/dat_system/busy.dat", "w") or exit("Unable to open file!");
flock($file, LOCK_EX) or exit("Unable to lock file!");
fwrite($file,0);
flock($file, LOCK_UN) or exit("Unable to unlock file!");
fclose($file);
?>