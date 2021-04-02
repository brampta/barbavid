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







