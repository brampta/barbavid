<?php
$sleep_howlong_after_settingtobusy=5;

require(dirname(dirname(dirname(__FILE__))).'/settings.php');
if($_SERVER['REMOTE_ADDR']!=$admin_ip)
{die('unauthorized');}
$nowtime=time();

$file = fopen(DAT_SYSTEM_HOME."/dat_system/busy.dat", "w") or exit("Unable to open file!");
flock($file, LOCK_EX) or exit("Unable to lock file!");
fwrite($file,$nowtime);
flock($file, LOCK_UN) or exit("Unable to unlock file!");
fclose($file);

sleep($sleep_howlong_after_settingtobusy);

set_time_limit(30000000);
exec('rm -R '.DAT_SYSTEM_HOME.'/dat_system; cp -R '.escapeshellarg($_GET['bak']).'/dat_system '.DAT_SYSTEM_HOME.'/');
echo 'rolled back to backup '.$_GET['bak'];


$file = fopen(DAT_SYSTEM_HOME."/dat_system/busy.dat", "w") or exit("Unable to open file!");
flock($file, LOCK_EX) or exit("Unable to lock file!");
fwrite($file,0);
flock($file, LOCK_UN) or exit("Unable to unlock file!");
fclose($file);
?>