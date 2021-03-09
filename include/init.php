<?php
if(!defined('INIT_RUN')) {
    define('INIT_RUN', true);

    define('BP', dirname(dirname(__FILE__))); //base path

    include(BP . '/settings.php');

    ini_set('session.cookie_domain', '.' . $main_domain);
    session_start();


    $nowtime = time();

    include(BP . '/include/procedure/get_language.php');


    if(!isset($nodb)){
        include(BP . '/include/class/db.php');
        $db = new Db();
        $db->connect();

        include(BP . '/include/class/user.php');
        $user = new User();
        if(!isset($_SESSION['user_id'])){
            $user->autologin();
        }
    }

    include(BP . '/include/class/message.php');
    $message = new Message();
}