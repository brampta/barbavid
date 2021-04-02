<?php
if(!defined('INIT_RUN')) {
    define('INIT_RUN', true);

    define('BP', dirname(dirname(__FILE__))); //base path

    $custom_settings_file=BP . '/custom/settings.php';
    if(file_exists($custom_settings_file)){
        include $custom_settings_file;
    }
    include(BP . '/settings.php');

    $mobile_stuff_for_head='<meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width">
        <script>
        document.domain = "'.$main_domain.'";
        </script>';

    ini_set('session.cookie_domain', '.' . $main_domain);
    session_start();


    $nowtime = time();

    //ok include this in every page because after all its a dat_system based video site.
    include(BP.'/include/dat_system/dat_system_functions.php');
    include(BP.'/include/dat_system/video_library_manip.php');

    //include stuff here that most page would probably need like translation...
    include(BP . '/include/procedure/get_language.php');

    //but lets try to avoid including stuff that is not needed on too many pages to keep the app fast!
    //include(BP.'/include/function/get_path_var.php');


    if(!isset($nodb)){
        include(BP . '/include/class/db.php');
        $db = new Db();
        $db->connect();

        include(BP . '/include/class/user.php');
        $user = new User();
        if(!isset($_SESSION['user_id'])){
            $user->autologin();
        }

        include(BP . '/include/class/video.php');
        $video = new Video();

        include(BP . '/include/class/channel.php');
        $channel = new Channel();
    }

    include(BP . '/include/class/message.php');
    $message = new Message();
}