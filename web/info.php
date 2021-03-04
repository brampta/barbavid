<?php
include(dirname(dirname(__FILE__)).'/include/init.php');



echo '<html>
    <head>
        <title>'.$text[2009].'</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="/css/style.css" />
        '.$mobile_stuff_for_head.'
    </head>
    <body>';
include(BP.'/include/header.php');

echo '<br /><br />';

echo '<table class="fluidtable"><tr><td>';

echo '<h1>'.$text[2001].'</h1>';
echo '<p>'.$text[2002].'</p>';
echo '<p>'.$text[2003].'</p>';




//echo '<h5>'.$text[2004].'</h5>';
echo '<p>'.$text[2005].'<br />
    <a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img src="160x41_Get_Flash_Player.jpg" alt="'.$text[2006].'" style="border:none;" /></a></p>';



echo '<h5>'.$text[2007].'</h5>';
echo '<p>'.make_writeto_string('<script type="text/javascript">
        document.write(\'<a h\');
        document.write(\'ref="mai\');
        document.write(\'lto:info\');
        document.write(\'@\');
        document.write(\'barbavid.com">info\');
        document.write(\'@\');
        document.write(\'barbavid.com</a>\');
    </script>').'</p>';



echo '</td></tr></table>';


include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');