<?php

//make a copy of that file at /settings.php and adjust it.
///settings.php will not be committed and will probably be different on each environment you setup (such as local, dev, prod..)
/// /settings.php will probably contain sensitive information

$main_domain='barba.local';
$site_name='Barbavid';
$admin_ip='x.x.x.x'; //your home ip
$admin_ids=array(1=>2,2=>1,3=>1,4=>1); //user_id=>admin level.
//progressively moving to user_id based admin rights which will be safer than ip based


$dat_system_admin_pass='super ventru';


//must list allow upload servers ips here
$allowed_ips=array(
    '127.0.0.1',
    '::1',
    'x.x.x.y', //upload or video server
    'x.x.x.z', //another upload or video server
);

$sendgrid_api_key='xxxxxxxxxxxxxxx';
$email_from='no-reply@barba.local';
$email_fromname='Barbavid system mail';

$customization_folder=dirname(dirname(__FILE__)).'/barbavid_customization';



$logofile=''; //ie /mylogofile.png

//$upload_exire_after_x_days_of_inactiv=120;
//$upload_exire_after_x_days_of_inactiv=365*2;
//due to budget restrictions I have to lower that a lot for the site to be able to survive :(
$upload_exire_after_x_days_of_inactiv=30;
$remove_unseen_vids_after_x_days=4;
$videokey_exire_after_x_hours=10;
$dat_system_keep_last_x_baks = 9;


$dat_system_home=dirname(dirname(__FILE__)).'/dat_system';
define('DAT_SYSTEM_HOME',$dat_system_home);


//max upload size in MB as shown on homepage
$maxmb = 10000;

