<?php

if(!defined('SETTINGS_RUN')){

    define('SETTINGS_RUN',true);

    $main_domain='barbavid.com';
    $site_name='Barbavid';
    $logofile='/logo2.png';

    $admin_ip='127.0.0.1'; //to be removed eventually but still used, on local could be 127.0.0.1, on server use your home IP
    $admin_ids=array(13=>2,13=>1); //user_id=>admin level.
    //progressively moving to user_id based admin rights which will be safer than ip based
    //$upload_exire_after_x_days_of_inactiv=120;
    //$upload_exire_after_x_days_of_inactiv=365*2;
    //due to budget restrictions I have to lower that a lot for the site to be able to survive :(
    $upload_exire_after_x_days_of_inactiv=365*30;
    $remove_unseen_vids_after_x_days=30;
    $videokey_exire_after_x_hours=30;
    $dat_system_keep_last_x_baks = 365;

    $dat_system_home=dirname(__FILE__).'/dat_system';
    define('DAT_SYSTEM_HOME',$dat_system_home);
    $dat_system_admin_pass='dfsdfasdfasfas';

//    $mobile_stuff_for_head='<meta name="HandheldFriendly" content="True">
//    <meta name="MobileOptimized" content="320">
//    <meta name="viewport" content="width=device-width">
//    <script>
//    document.domain = "'.$main_domain.'";
//    </script>';

    //must list allow upload servers ips here
    $allowed_ips=array(
        '127.0.0.1',
        '::1',
    );

    define('DB_HOST','localhost');
    define('DB_NAME','barbavid');
    define('DB_USERNAME','root');
    define('DB_PASSWORD','root');

    $sendgrid_api_key='cgsdfsdffsdfgsdfgdsf';
    $email_from='no-reply@barbavid.com';
    $email_fromname='Barbavid system mail';

    $customization_folder=dirname(dirname(__FILE__)).'/barbavid_customization';


}



