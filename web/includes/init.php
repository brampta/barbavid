<?php


include(dirname(dirname(__FILE__)).'/settings.php');

ini_set('session.cookie_domain', '.'.$main_domain);
session_start();


$nowtime=time();

include(dirname(dirname(__FILE__)).'/get_language.php');


include(dirname(__FILE__).'/db.php');
$db = new Db();
$db->connect();

include(dirname(__FILE__).'/user.php');
$user = new User();
$user->autologin();