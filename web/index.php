<?php
include('settings.php');
// if($admin_ip==$_SERVER['REMOTE_ADDR'])
// {
	// //$upload_server='uploadserver3';
	// $upload_server='uploadserver4';
// }
// else
// {
	// //$upload_server='uploadserver3';
	$upload_server='upload1';
// }


//$maxmb = 3.5*1024;
//$maxmb = 3*1024;
$maxmb = 10000;

ini_set('session.cookie_domain', '.'.$main_domain);
session_start();


$nowtime=time();

include('get_language.php');

$UPLOAD_IDENTIFIER=base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);

echo '<html>
    <head>
        <title>Barbavid - '.$text[0].'</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="/style.css" />
        <script type="text/javascript">
            var upload_server = "'.$upload_server.'";
            var main_domain = "'.$main_domain.'";
        </script>
        <script type="text/javascript" src="/cookiz_js.js"></script>
        <script type="text/javascript" src="/upload_js.js"></script>
        <script type="text/javascript" src="/language_upload_js.php?lang='.urlencode($language).'"></script>
        <script type="text/javascript" src="/maketimus.js"></script>
        <script type="text/javascript" src="/language_maketimus_js.php?lang='.urlencode($language).'"></script>
        '.$mobile_stuff_for_head.'
    </head>
    <body>';
include('header.php');

echo '<center>';
echo '<br /><br /><div id="f1_upload_process" style="display:none;">
        <div>'.$text[12].'</div>
        <div id="upload_progress"><!-- --></div>
        </div>
        <div id="result"><!-- --></div>
        <form action="https://'.$upload_server.'.'.$main_domain.'/upload" method="post" enctype="multipart/form-data"  ';
echo 'target="upload_target" onsubmit="startUpload();"';
echo '>
            <h4>'.$text[14].'</h4>
            <input type="hidden" name="UPLOAD_IDENTIFIER" id="UPLOAD_IDENTIFIER" value="'.$UPLOAD_IDENTIFIER.'" />
            <table class="vidform">
            <tr><td class="labezl">'.$text[1].':</td><td><input name="file" type="file" class="uploadthing" /></td></tr>
            <tr><td><!-- --></td><td class="xplain">'.$text[9].'</td></tr>
            <tr><td class="labezl">'.$text[2].':</td><td><input name="title" type="text" class="tittx" /></td></tr>
            <tr><td><!-- --></td><td class="xplain">'.$text[10].'</td></tr>
            <tr><td class="labezl">'.$text[3].':</td><td><textarea name="description" class="descrtx"></textarea></td></tr>
            <tr><td><!-- --></td><td class="xplain">'.$text[11].'</td></tr>
			<tr><td class="labezl">'.$text[15].' <div style="position:relative;display:inline;"><span onmouseover="document.getElementById(\'popup_explain\').style.display=\'block\';" onmouseout="document.getElementById(\'popup_explain\').style.display=\'none\';" style="cursor:pointer;" class="poppy_button">?</span><div id="popup_explain" onmouseout="document.getElementById(\'popup_explain\').style.display=\'none\';" style="display:none;position:absolute;" class="poppy_info">'.$text[17].'</div></div>:</td><td><input name="popup_URL" type="text" class="popupx" onchange="remember_popup_URL(&quot;popup_URL_input&quot;)" id="popup_URL_input" /><script>populate_popup_URL("popup_URL_input");</script></td></tr>
            <tr><td><!-- --></td><td class="xplain">'.$text[16].'</td></tr>
            </table>
            <input type="submit" name="submitBtn" value="'.$text[4].'" />
        </form>
        <iframe name="upload_target" id="upload_target" src="/gif.gif" ';
if($admin_ip==$_SERVER['REMOTE_ADDR'] || 1==2) 
{echo 'style="width:100%;height:600px;"';}
else
{echo 'class="negaten"';}
echo '></iframe>
        <iframe name="upload_progress_iframe" id="upload_progress_iframe" src="/gif.gif" class="negaten"></iframe>';

echo '<div id="uploads_log"><!-- --></div>
    <script type="text/javascript">showlastsuccess(getCookie("uploads_log"));</script>';
    
    
    echo '<img src="/barbapic_resized.png" class="fluid_bigpic" />';
    
echo '</center>';

//Barbaccident!!
//echo '<div style="padding:48px;">
//<h4>Incorrectly expired videos</h4>
//<p>There was a Barba-incident where some videos have incorrectly been expired for not being viewed for a too long period of time. The affected videos were those that were viewed frequently in an embed player but not on the video page. The views to the video were not counted when viewed in the embed player, causing the system to believe that these videos were never being viewed. We are sorry for this incident and have made the fixes so that now views from the embed player will be properly counted. Please re-upload the accientally erased content and you can rest assured that it will be safely preserved.</p>
//</div>';


//properllerads domain validation:
//echo '<meta name="propeller" content="628092ab84e27a712e9a2d0e533c3ce7" />';

include('bottom.php');
echo '</body>
</html>';


?>
