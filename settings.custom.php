<?php

$logofile=''; //ie /custom/mylogofile.png

//$upload_exire_after_x_days_of_inactiv=120;
//$upload_exire_after_x_days_of_inactiv=365*2;
//due to budget restrictions I have to lower that a lot for the site to be able to survive :(
$upload_exire_after_x_days_of_inactiv=30;
$remove_unseen_vids_after_x_days=4;
$videokey_exire_after_x_hours=10;
$dat_system_keep_last_x_baks = 9;


$dat_system_home=dirname(dirname(__FILE__)).'/dat_system'; //you need to make sure this path works, but if you place this file at /custom/settings.php and leave the dat_system at its default location this should be ok!
define('DAT_SYSTEM_HOME',$dat_system_home);


//max upload size in MB as shown on homepage
$maxmb = 10000;