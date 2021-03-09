<?php
include(dirname(dirname(__FILE__)).'/include/init.php');


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

$UPLOAD_IDENTIFIER=base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);

//create or update upload code for user
if(isset($_SESSION['user_id'])){
    $upload_code_data_array = $db->load_by('upload_codes','user_id',$_SESSION['user_id']);
    if($upload_code_data_array){
        $upload_code = $upload_code_data_array['code'];
        $db->update('upload_codes',$upload_code_data_array['id'],array());
    }else{
        $upload_code = bin2hex(random_bytes(32));
        $upload_code_data_array=array();
        $upload_code_data_array['user_id']=$_SESSION['user_id'];
        $upload_code_data_array['code']=$upload_code;
        $db->insert('upload_codes',$upload_code_data_array);
    }

    //keep clean, delete upload codes older than 24h
    $query='DELETE FROM upload_codes WHERE created < (NOW() - INTERVAL 24 HOUR)';
    $params=array();
    $db->query($query,$params);
}

include(BP.'/include/head_start.php');
echo '<script type="text/javascript">
            var upload_server = "'.$upload_server.'";
            var main_domain = "'.$main_domain.'";
        </script>
        <script type="text/javascript" src="/js/cookiz_js.js"></script>
        <script type="text/javascript" src="/js/upload_js.js"></script>
        <script type="text/javascript" src="/js/language_upload_js.php?lang='.urlencode($language).'"></script>
        <script type="text/javascript" src="/js/maketimus.js"></script>
        <script type="text/javascript" src="/js/language_maketimus_js.php?lang='.urlencode($language).'"></script>';
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');


if(!isset($_SESSION['user_id'])){
    ?>
    <p class="message error"><?php echo __('please login to upload') ?></p>
    <?php
}else {

    echo '<center>';
    echo '<br /><br /><div id="f1_upload_process" style="display:none;">
        <div>' . $text[12] . '</div>
        <div id="upload_progress"><!-- --></div>
        </div>
        <div id="result"><!-- --></div>
        <form action="https://' . $upload_server . '.' . $main_domain . '/upload" method="post" enctype="multipart/form-data"  ';
echo 'target="upload_target" onsubmit="startUpload();"';
    echo '>
            <h4>' . $text[14] . '</h4>
            <input type="hidden" name="UPLOAD_IDENTIFIER" id="UPLOAD_IDENTIFIER" value="' . $UPLOAD_IDENTIFIER . '" />
            <input type="hidden" name="upload_code" id="upload_code" value="' . $upload_code . '" />
            <table class="vidform">
            <tr><td class="labezl">' . $text[1] . ':</td><td><input name="file" type="file" class="uploadthing" /></td></tr>
            <tr><td><!-- --></td><td class="xplain">' . $text[9] . '</td></tr>
            <tr><td class="labezl">' . $text[2] . ':</td><td><input name="title" type="text" class="tittx" /></td></tr>
            <tr><td><!-- --></td><td class="xplain">' . $text[10] . '</td></tr>
            <tr><td class="labezl">' . $text[3] . ':</td><td><textarea name="description" class="descrtx"></textarea></td></tr>
            <tr><td><!-- --></td><td class="xplain">' . $text[11] . '</td></tr>';
    //echo '<tr><td class="labezl">' . $text[15] . ' <div style="position:relative;display:inline;"><span onmouseover="document.getElementById(\'popup_explain\').style.display=\'block\';" onmouseout="document.getElementById(\'popup_explain\').style.display=\'none\';" style="cursor:pointer;" class="poppy_button">?</span><div id="popup_explain" onmouseout="document.getElementById(\'popup_explain\').style.display=\'none\';" style="display:none;position:absolute;" class="poppy_info">' . $text[17] . '</div></div>:</td><td><input name="popup_URL" type="text" class="popupx" onchange="remember_popup_URL(&quot;popup_URL_input&quot;)" id="popup_URL_input" /><script>populate_popup_URL("popup_URL_input");</script></td></tr>
    //        <tr><td><!-- --></td><td class="xplain">' . $text[16] . '</td></tr>';
    echo '</table>
            <input type="submit" name="submitBtn" value="' . $text[4] . '" />
        </form>
        <iframe name="upload_target" id="upload_target" src="/gif.gif" ';
    if ($admin_ip == $_SERVER['REMOTE_ADDR'] || 1 == 2) {
        echo 'style="width:100%;height:600px;background: #FFFFFF;"';
    } else {
        echo 'class="negaten"';
    }
    echo '></iframe>';

//not sure we need this iframe anymore, not sure what it was for...
//echo '<iframe name="upload_progress_iframe" id="upload_progress_iframe" src="/gif.gif" class="negaten"></iframe>';

    echo '<div id="uploads_log"><!-- --></div>
    <script type="text/javascript">showlastsuccess(getCookie("uploads_log"));</script>';


    echo '<img src="/barbapic_resized.png" class="fluid_bigpic" />';

    echo '</center>';
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');