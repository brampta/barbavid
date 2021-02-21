<?php
ini_set('session.cookie_domain', '.barbavid.com');
session_start();


$nowtime=time();

include('get_language.php');




echo '<html>
    <head>
        <title>'.$text[2010].'</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="/style.css" />
    </head>
    <body>';
include('header.php');

echo '<br /><br />';

echo '<center><table style="width:600px;"><tr><td>';

echo '<h1>'.$text[2010].'</h1>';
echo '<p>'.$text[2011].'</p>';




echo '</td></tr></table></center>';


include('bottom.php');
echo '</body>
</html>';


?>