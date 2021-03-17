<?php

if(!defined('SETTINGS_RUN')){

    define('SETTINGS_RUN',true);

    $main_domain='barba.local';
    $site_name='Barbavid';

    $admin_ip='x.x.x.x'; //your home ip
    $admin_ids=array(1=>2,2=>1,3=>1,4=>1); //user_id=>admin level.
    //progressively moving to user_id based admin rights which will be safer than ip based

    //$upload_exire_after_x_days_of_inactiv=120;
    //$upload_exire_after_x_days_of_inactiv=365*2;
    //due to budget restrictions I have to lower that a lot for the site to be able to survive :(
    $upload_exire_after_x_days_of_inactiv=30;
    $remove_unseen_vids_after_x_days=4;
    $videokey_exire_after_x_hours=10;
    $dat_system_keep_last_x_baks = 9;


    $dat_system_home=dirname(dirname(__FILE__)).'/dat_system';
    define('DAT_SYSTEM_HOME',$dat_system_home);
    $dat_system_admin_pass='super ventru';

    $mobile_stuff_for_head='<meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width">
    <script>
    document.domain = "'.$main_domain.'";
    </script>';

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

}


