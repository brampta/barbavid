<?php
include('settings.php');
ini_set('session.cookie_domain', '.barbavid.com');
session_start();


$nowtime=time();

include('get_language.php');




echo '<html>
    <head>
        <title>'.$text[4000].'</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="/style.css" />
        '.$mobile_stuff_for_head.'
    </head>
    <body>';
include('header.php');

echo '<br /><br />';

echo '<table class="fluidtable"><tr><td>';

echo '<img src="/barbapic_resized.png" style="float:right;" class="fluidbigpic" />';

echo '<h1>'.$text[4000].'</h1>';
echo '<p>'.$text[4001].'</p>';
echo '<p>'.$text[4002].'</p>';








echo '</td></tr></table>';


include('bottom.php');
echo '</body>
</html>';


?>
