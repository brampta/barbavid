<?php
include('settings.php');

ini_set('session.cookie_domain', '.'.$main_domain);
session_start();
$nowtime = time();

include('get_language.php');
include('dat_system_functions.php');
include('video_library_manip.php');



if (!isset($_GET['upload']))
{ die('error, upload not specified'); } else
{ $upload_hash = $_GET['upload']; }

//get upload info
$datfile_num = find_place_according_to_index($upload_hash, 'uploads_index.dat');
$upload_info = get_element_info($upload_hash, $datfile_num);
if ($upload_info === false)
{ die('invalid url.'); }
$upload_info = unserialize($upload_info);
//print_r($upload_info);

$badvid = 0;
if ($upload_info['suspend'] == '' && $upload_info['file_md5'] != '0')
{
    //get video info
    //echo '$upload_info[\'file_md5\']: '.$upload_info['file_md5'].'<br />';
    $datfile_num = find_place_according_to_index($upload_info['file_md5'], 'videos_index.dat');
    $video_info = get_element_info($upload_info['file_md5'], $datfile_num);
    if ($video_info === false)
    { die('error, video not found!'); }
    $video_info = unserialize($video_info);
    //print_r($video_info);

    $howmany_chunks = count($video_info['chunks']);
    //echo '$howmany_chunks: '.$howmany_chunks.'<br />';
    if ($howmany_chunks == 1)
    { $chunknumber = 1; }

    //echo '$chunknumber: '.$chunknumber.'<br />';
} else
{ $badvid = 1; }


$new_title_must_not_be_blank = 0;
$imagever_bad = 0;
$upload_duplicated_successfully = 0;
if (isset($_POST['duplicate_upload']) && $badvid == 0)
{
    $errors = 0;

    if ($_POST['new_title'] == '')
    { $new_title_must_not_be_blank = 1; $errors = 1; }
    if (md5($_POST['numba']) != $_SESSION['img_ver_dup'])
    { $imagever_bad = 1; $errors = 1; }
    unset($_SESSION['img_ver_dup']);

    if ($errors == 0)
    {
        $maxtitlelen = 100;
        $maxdesclen = 3000;
        $new_upload_info['file_md5'] = $upload_info['file_md5'];
        $new_upload_info['title'] = base64_encode(mb_substr($_POST['new_title'], 0, $maxtitlelen));
        $new_upload_info['description'] = base64_encode(mb_substr($_POST['new_description'], 0, $maxdesclen));
        $new_upload_info['popup'] = base64_encode(mb_substr($_POST['new_popup'], 0, $maxdesclen));
        $new_upload_info['suspend'] = '';
        $new_upload_info['time'] = $nowtime;


        include('create_unique_hash.php');
        $datfile_num = find_place_according_to_index($arandomhash, 'uploads_index.dat');
        add_or_update_element($arandomhash, serialize($new_upload_info), $datfile_num, 'uploads_index.dat');
		add_upload($upload_info['file_md5'],$arandomhash);
        $upload_duplicated_successfully = 1;
    }
}



echo '<html>
    <head>
        <title>'.$text[3000].'</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="/style.css" />
        <script type="text/javascript" src="/maketimus.js"></script>
        <script type="text/javascript" src="/language_maketimus_js.php?lang=' . urlencode($language) . '"></script>
        <script type="text/javascript" src="/cookiz_js.js"></script>
        <script type="text/javascript" src="/upload_js.js"></script> <!--for populate popup URL functions-->
        '.$mobile_stuff_for_head.'
    </head>
    <body>';
include('header.php');

$nameforlink = htmlspecialchars(base64_decode($upload_info['title']));
if ($nameforlink == '')
{ $nameforlink = '<i>'.$text[3001].'</i>'; }


if ($upload_duplicated_successfully == 1)
{
    echo '<br /><br ><center><table><tr><td>
    '.$text[3002].':<br />
    '.$text[3003].': <a href="/video/' . $_GET['upload'] . '" target="_blank">' . $nameforlink . '</a><br />
    '.$text[3004].': <a href="/video/' . $arandomhash . '" target="_blank">' . htmlspecialchars($_POST['new_title']) . '</a>
    <div style="font-size:80%;">'.explain_expiration2($upload_exire_after_x_days_of_inactiv).'</div>
    </td></tr></table></center>';
} else
{
    if ($badvid == 1)
    {
        echo '<br /><br ><center><table><tr><td>
    '.explain_badvid($_GET['upload'],$nameforlink).'<br />
    </td></tr></table></center>';
    } else
    {

        $title_errorer = '';
        if ($new_title_must_not_be_blank == 1)
        { $title_errorer = ' <div style="color:red;">'.$text[3005].'</div>'; }
        $imagever_errorer = '';
        if ($imagever_bad == 1)
        { $imagever_errorer = ' <div style="color:red;">'.$text[3006].'</div>'; }


        if (!isset($_POST['new_title']))
        { $_POST['new_title'] = base64_decode($upload_info['title']); }
        if (!isset($_POST['new_description']))
        { $_POST['new_description'] = base64_decode($upload_info['description']); }
        if (!isset($_POST['new_popup']))
        { $_POST['new_popup'] = ''; }

        echo '<br /><br ><center><table><tr><td>
    <form method="post">
    '.explain_dupvid($_GET['upload'],$nameforlink).'<br />
    <table class="vidform">
		<tr><td class="labezl">'.$text[3007].':</td><td>' . $title_errorer . '<input type="text" name="new_title" value="' . htmlspecialchars($_POST['new_title']) . '" class="tittx" /></td></tr>
		<tr><td><!-- --></td><td class="xplain">'.$text[10].'</td></tr>
		<tr><td class="labezl">'.$text[3008].':</td><td><textarea type="text" name="new_description" class="descrtx">' . htmlspecialchars($_POST['new_description']) . '</textarea></td></tr>
		<tr><td><!-- --></td><td class="xplain">'.$text[11].'</td></tr>
		<tr><td class="labezl">'.$text[3011].' <div style="position:relative;display:inline;"><span onmouseover="document.getElementById(\'popup_explain\').style.display=\'block\';" onmouseout="document.getElementById(\'popup_explain\').style.display=\'none\';" style="cursor:pointer;" class="poppy_button">?</span><div id="popup_explain" onmouseout="document.getElementById(\'popup_explain\').style.display=\'none\';" style="display:none;position:absolute;" class="poppy_info">'.$text[17].'</div></div>:</td><td><input type="text" name="new_popup" id="new_popup" value="' . htmlspecialchars($_POST['new_popup']) . '" class="popupx" onchange="remember_popup_URL(&quot;new_popup&quot;)" /><script>populate_popup_URL("new_popup");</script></td></tr>
		<tr><td><!-- --></td><td class="xplain">'.$text[16].'</td></tr>
		<tr><td class="labezl">'.$text[3009].':</td><td>' . $imagever_errorer . '<img src="image_verification?name=dup" /> => <input type="number" name="numba" style="width:120px;" /></td></tr>
		<tr><td colspan="2" style="text-align:center;"><input type="submit" name="duplicate_upload" value="'.$text[3010].'" /></td></tr>
    </table>
    </form>
    <br />
    </td></tr></table></center>';
    }
}



include('bottom.php');
echo '</body>
</html>';
?>
