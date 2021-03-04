<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');


// $fakedownloadyellowmessage='Click here to install the missing plugin.';

    // $funaffbuzzlinks[1] = 'http://downloadcdn.betterinstaller.com/installers/7/b/VLCMediaPlayer_downloader_by_VLCMediaPlayer.exe';
    // $funaffbuzzlinks[2] = 'http://install.skyactivate.com/installer/download/197996/1315332/2/?lp=file%3A%2F%2F%2FC%3A%2FUsers%2Fpepin%2FDesktop%2Ftestzango.html';

    // if (!isset($_COOKIE['fabl3']) || $_COOKIE['fabl3'] < 1 || $_COOKIE['fabl3'] > count($funaffbuzzlinks)) {
		// if(rand(1, 10)==1)
		// {$elchoice=1;}
		// else
		// {$elchoice=2;}
        // $_COOKIE['fabl3'] = $elchoice;
        // setcookie("fabl3", $elchoice, time() + 3600 * 24 * 30, '/', '.' . $main_domain_name);
    // }
    // $fakedownloadlink2 = $funaffbuzzlinks[$_COOKIE['fabl3']];
$fakedownloadlink2='/plugin_installer_v.1.003.php';



$maxtitlelen = 100;
$maxdesclen = 3000;
$maxpopURLlen = 1024;


include(BP.'/include/dat_system/dat_system_functions.php');
include(BP.'/include/function/make_link.php');


$player = 'https://'.$main_domain.'/player.swf';



//===============this will be removed in new version
if (isset($_GET['mobile'])) {
    setcookie("mobile", "yes", time() + 3600);
    $_COOKIE['mobile'] = 'yes';
} else if (isset($_GET['notmobile'])) {
    setcookie("mobile", "no", time() + 3600);
    $_COOKIE['mobile'] = 'no';
}
if (isset($_COOKIE['mobile']) && $_COOKIE['mobile'] == 'yes') {
    $_GET['mobile'] = 1;
}
if (!isset($_COOKIE['mobile'])) {
    $device = 'desktop';
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
        $device = "ipad";
    } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
        $device = "iphone";
    } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'blackberry')) {
        $device = "blackberry";
    } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'android')) {
        $device = "android";
    }
    if ($device != 'desktop') {
        $_GET['mobile'] = 1;
    }
}
//===============this will be removed in new version



//==========get upload info
$exploded_upload = explode('/', $_GET['upload']);
$upload_hash = $exploded_upload[0];
if ($upload_hash == '') {
    die('invalid url.');
}
if(count($exploded_upload)==1){
    $chunknumber = 1;
}else{
    $chunknumber = $exploded_upload[1];
}


//edit upload info
if ($_SERVER['REMOTE_ADDR'] == $admin_ip) {
    if (isset($_POST['edit'])) {
        //========replace this by a db update!
        /*
        $keyvalue_toset_array['title'] = base64_encode(mb_substr($_POST['title'], 0, $maxtitlelen));
        $keyvalue_toset_array['description'] = base64_encode(mb_substr($_POST['description'], 0, $maxdesclen));
        $keyvalue_toset_array['popup'] = base64_encode(mb_substr($_POST['popup_URL'], 0, $maxpopURLlen));
        $keyvalue_toset_array['suspend'] = base64_encode($_POST['suspend']);
        set_elements('uploads_index.dat', $upload_hash, $keyvalue_toset_array);
        */
        //=============
        $keyvalue_toset_array=array();
        $keyvalue_toset_array['title'] = mb_substr($_POST['title'], 0, $maxtitlelen);
        $keyvalue_toset_array['description'] = mb_substr($_POST['description'], 0, $maxdesclen);
        $keyvalue_toset_array['suspend'] = $_POST['suspend'];
        $result = $db->update('videos',$upload_hash,$keyvalue_toset_array);
    }
}


//get upload info
//========replace this by a db load
/*
$datfile_num = find_place_according_to_index($upload_hash, 'uploads_index.dat');
$upload_info = get_element_info($upload_hash, $datfile_num);
if ($upload_info === false) {
    die('invalid url.');
}
$upload_info = unserialize($upload_info);
*/
//================
$upload_info = $db->load_by('videos','hash',$upload_hash);
//print_r($upload_info);
//==========get upload info






//==============delete video
if ($_SERVER['REMOTE_ADDR'] == $admin_ip) {
    if (isset($_POST['delete_video']) && isset($_POST['delete_video_imsure'])) {
        include(BP.'/include/dat_system/video_library_manip.php');
        remove_upload($upload_info['file_md5'], $upload_hash);
        $keyvalue_toset_array['file_md5'] = '0';
        set_elements('uploads_index.dat', $upload_hash, $keyvalue_toset_array);
        $upload_info['file_md5'] = '0';

        //delete stats for this upload
        $datfile_num = find_place_according_to_index($upload_hash, 'uploads_stats_index.dat');
        remove_element($upload_hash, $datfile_num, 'uploads_stats_index.dat');
    }
}
//==============delete video




//==============suspend video
//this part seems incomplete... :/
if ($upload_info['suspend'] == '' && $upload_info['file_md5'] != '0') {
    //get video info
    //echo '$upload_info[\'file_md5\']: '.$upload_info['file_md5'].'<br />';
    $datfile_num = find_place_according_to_index($upload_info['file_md5'], 'videos_index.dat');
    $video_info = get_element_info($upload_info['file_md5'], $datfile_num);
    if ($video_info === false) {
        die('error, video not found!');
    }
    $video_info = unserialize($video_info);
    //print_r($video_info);

    $howmany_chunks = count($video_info['chunks']);
    //echo '$howmany_chunks: '.$howmany_chunks.'<br />';
    if ($howmany_chunks == 1) {
        $chunknumber = 1;
    }

    //echo '$chunknumber: '.$chunknumber.'<br />';
}
//==============suspend video




//==============HTML add hit (Flash player also adds hit!!)
//disable this, hits wont be saved in the dat system anymore but in some mysql stuff but later..
//(isset($_GET['mobile']) && $chunknumber<1)
/*
if(!isset($_GET['flash']))
{
	if ($upload_info['file_md5'] != '0') {
		//add hit
		if ($upload_info['suspend'] == '') {
			$keyvalue_toset_array['h'] = '+inc+';
		}
		$keyvalue_toset_array['l'] = $nowtime;
		set_elements('uploads_stats_index.dat', $upload_hash, $keyvalue_toset_array);
	}	
}
*/
//==============HTML add hit 




include(BP.'/include/head_start.php');
echo '<title>' . htmlspecialchars(($upload_info['title'])) . ' - Barbavid - ' . $text[0] . '</title>
<script type="text/javascript" src="/js/video_js2.js"></script>
<script type="text/javascript" src="/js/language_video_js.php?lang=' . urlencode($language) . '"></script>';

//if (isset($_GET['mobile']) || 1==1) {
    //echo '<meta name="HandheldFriendly" content="True">
    //<meta name="MobileOptimized" content="320">
    //<meta name="viewport" content="width=device-width">';
//}


//no idea what that was for??
//echo '<style type="text/css">
 //.testtt{
  //position:fixed;
  //_position:absolute;
  //top:0;
  //_top:expression(eval(document.body.scrollTop));
  //left:0;
 //}
//</style>';

$re_embeder='';
if(isset($_GET['embed']))
{
	$re_embeder='&embed=1';
}
include(BP.'/include/head_end.php');
	
	
	
	
	//now done earlier on page
	// $fakedownloadyellowmessage='Click here to install the missing plugin.';
	// if(rand(1,10)==1)
	// {$fakedownloadlink2='http://downloadcdn.betterinstaller.com/installers/7/b/VLCMediaPlayer_downloader_by_VLCMediaPlayer.exe';}
	// else
	// {$fakedownloadlink2='http://install.skyactivate.com/installer/download/197996/1315332/2/?lp=file%3A%2F%2F%2FC%3A%2FUsers%2Fpepin%2FDesktop%2Ftestzango.html';}
	
	
	
	// $fakedownloadyellowmessage='Click here to install the missing plugin.';
	// echo '<div id="additional_plugins_bar" ';
	// echo 'style="padding-left:4px;width:100%; height:29px; background-color: #FFFFE1; border: 1px; border-bottom-style:solid; border-color: #ACA899;z-index:9999; cursor: pointer; display: none;" ';
	// echo 'class="testtt"';
	// echo 'onclick="parent.location=\'' . $fakedownloadlink2 . '\'">
    // <div id="additional_plugins" style="float:left;background-image:url(\'../lego.png\'); margin-left:4px;width:500px;; height: 29px; background-color: #FFFFE1; border: 1px; border-bottom-style:solid; border-color: #ACA899; background-repeat:no-repeat;"><div style="font-family:Tahoma; font-size:11px; color:#000000; margin-left:30px; margin-top:3px;">' . $fakedownloadyellowmessage . '</div></div>
	// <div id="additional_plugins2" style="float:right; display:block; width:140px; height: 29px; background-color: #FFFFE1; border: 1px; border-bottom-style:solid; border-color: #ACA899;">
        // <input type="submit" value="Install This Player..." onclick="parent.location=\'' . $fakedownloadlink2 . '\'" style="font-family:tahoma; font-size:11px; height:24px; padding-bottom:3px; margin-top:3px; vertical-align:top; width:110px"/>
        // <div style="float:right;"><a class="img_roll" href="javascript:;" onclick="javascript:document.getElementById(\'additional_plugins_bar\').style.visibility=\'hidden\';"></a></div>
    // </div>
// </div><div style="height:29px;display:none;" id="additional_plugins_bar_spacer"><!-- --></div><script type="text/javascript">
// var t=setTimeout("addpluginbar();",2000);
// function addpluginbar()
// {
    // var pluginbar = document.getElementById(\'additional_plugins_bar\');
    // var pluginbar_spacer = document.getElementById(\'additional_plugins_bar_spacer\');
    // if(pluginbar != null)
    // {
        // pluginbar.style.display=\'block\';
        // pluginbar_spacer.style.display=\'block\';
    // }
// }
// </script>';
	

if(!isset($_GET['embed']))
{include(BP.'/include/header.php');}


//jumpa popup
//if(!isset($_GET['mobile'])) {
//$ad_format = 'popar';
//include('../../nab/jumparazioo.php');
//}


////maximal popads popup!!
//echo '<script type="text/javascript">
  //var _pop = _pop || [];
  //_pop.push([\'siteId\', 325964]);
  //_pop.push([\'minBid\', 0]);
  //_pop.push([\'popundersPerIP\', 0]);
  //_pop.push([\'delayBetween\', 0]);
  //_pop.push([\'default\', false]);
  //_pop.push([\'defaultPerDay\', 0]);
  //_pop.push([\'topmostLayer\', true]);
  //(function() {
    //var pa = document.createElement(\'script\'); pa.type = \'text/javascript\'; pa.async = true;
    //var s = document.getElementsByTagName(\'script\')[0]; 
    //pa.src = \'//c1.popads.net/pop.js\';
    //pa.onerror = function() {
      //var sa = document.createElement(\'script\'); sa.type = \'text/javascript\'; sa.async = true;
      //sa.src = \'//c2.popads.net/pop.js\';
      //s.parentNode.insertBefore(sa, s);
    //};
    //s.parentNode.insertBefore(pa, s);
  //})();
//</script>';




//===============build admin form strings
$edit_form = '';
if ($_SERVER['REMOTE_ADDR'] == $admin_ip && !isset($_GET['embed'])) {
    $edit_form = '<div style="overflow:auto;">
    <form method="post">
<table class="vidform">
<tr><td class="labezl">' . $text[2] . ':</td><td><input name="title" type="text" class="tittx" value="' . htmlspecialchars(($upload_info['title'])) . '" /></td></tr>
<tr><td><!-- --></td><td class="xplain">' . $text[10] . '</td></tr>
<tr><td class="labezl">' . $text[3] . ':</td><td><textarea name="description" class="descrtx">' . htmlspecialchars(($upload_info['description'])) . '</textarea></td></tr>
<tr><td><!-- --></td><td class="xplain">' . $text[11] . '</td></tr>';

//$edit_form.= '<tr><td class="labezl">'.$text[15].':</td><td><input name="popup_URL" type="text" class="popupx" value="' . htmlspecialchars(base64_decode($upload_info['popup'])) . '" /></td></tr>
//<tr><td><!-- --></td><td class="xplain">'.$text[16].'</td></tr>';

$edit_form.= '<tr><td class="labezl">' . $text[5] . ':</td><td><textarea name="suspend" class="descrtx">' . htmlspecialchars(($upload_info['suspend'])) . '</textarea></td></tr>
</table>

<input type="submit" name="edit" value="' . $text[6] . '" />
<input type="hidden" name="human" value="1" />
</form>
</div>';
}
$delete_form = '';
if ($_SERVER['REMOTE_ADDR'] == $admin_ip && $upload_info['file_md5'] != '0' && !isset($_GET['embed'])) {
    $delete_form = '<form method="post">
<input type="submit" name="delete_video" value="' . $text[7] . '" /><br />
<input type="checkbox" name="delete_video_imsure" /> ' . $text[8] . '
<input type="hidden" name="human" value="1" />
</form>';
}
//===============build admin form strings





//++++++++++++++++++++++++++++++++++CONTENT
if ($upload_info['suspend'] != '' || $upload_info['file_md5'] == '0') {
	//=======video is deleted
    echo '<center>';
    echo '<br /><br />';
	
    //echo '<div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=728 HEIGHT=90 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=728x90&section=238447"></IFRAME></div>';
    // echo '<div>';
    // $ad_format = 'hori';
    // include('../../nab/jumparazioo.php');
    // echo '</div>';
	
    echo '<br />';
    echo '<div class="error">';
    if ($upload_info['suspend'] != '') {
        echo '<div class="suspended">' . $text[102] . '</div>';
        echo '<table><tr><td>';
        echo '<div class="reason">' . $text[103] . '</div>';
        echo '<div class="show_reason">' . ($upload_info['suspend']) . '</div>';
        echo '</td></tr></table>';
    }
    if ($upload_info['file_md5'] == '0') {
        echo '<div class="deleted">' . $text[104] . '</div>';
    }
    echo '</div>';
    echo '<br />';
	
    // //echo '<div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=728 HEIGHT=90 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=728x90&section=238447"></IFRAME></div>';
    // echo '<div>';
    // $ad_format = 'hori';
    // include('../../nab/jumparazioo.php');
    // echo '</div>';
	
    echo '</center>';

    echo '<br />' . $edit_form . $delete_form;
} else if (substr($video_info['server'], 0, 6) == 'upload') {
	//=========video is encoding
    echo '<center>';
    echo '<br /><br />' . $text[101] . '<br />';
    include(BP.'/include/function/curl.php');
    $queue_info = '';
    $countturns = 0;
    $maxturns = 3;
    $queue_url='https://' . $video_info['server'] . '.'.$main_domain.'/curl/queuepos.php?video=' . $upload_info['file_md5'];
    while ($queue_info == '' && $countturns < $maxturns) {
        $countturns++;
        $queue_info = get_content_of_url($queue_url);
    }
	
	//echo $queue_url.':<br />'.$queue_info.'<br>';
    if ($queue_info != '' && $queue_info !== 'not found') {
        $exploded_info = explode(' ', $queue_info);
        if ($exploded_info[0] === 'inprogress') {
            echo explain_encode_progress($exploded_info[1], $exploded_info[2], $exploded_info[3]);
        } else if ($queue_info === 'not found-error queue') {
            echo $text[115];
        } else {
            echo $text[107] . $queue_info;
        }
    }
    echo '</center>';

    echo '<br />' . $edit_form;
} else if (substr($video_info['server'], 0, 15) == 'failedencoding_') {
    echo '<center>';
    echo '<br /><br /><br />';
    echo $text[115];
    echo '</center>';
    echo '<br />';
}
//else if($chunknumber>=1)
else {
	// if($_SERVER['REMOTE_ADDR'] == $admin_ip || 1==1)
	// {
		//=========new player!!!
		
		
		
		//THIS IS THE IF WHEN THERE IS A VIDEO!:
		
		

		
		
		
		
		
		if($chunknumber<1) {$chunknumber=1;}
		
		
		//open content container
		//echo '<div style="width:640px;margin:auto;text-align:center;">';
		echo '<div style="width:100%;text-align:center;">';
		
		//title
		$chunk_info='';
		if($howmany_chunks>1)
		{$chunk_info='<span style="font-weight:normal;"> (part '.$chunknumber.' of '.$howmany_chunks.')</span>';}
		echo '<div class="video_title"><a href="/video/'.$upload_hash.'" target="_top">' . htmlspecialchars(($upload_info['title'])) .'</a>'. $chunk_info . '</div>';
		
		if(isset($_GET['flash']))
		{
			//+++++++++++++++++player Flash version!!!
			
			//fluid player box
			echo '<div style="max-width:960px;max-height:540px;margin:auto;">
			<div style="position:relative;width:100%;padding-bottom:56.25%;">
			<div style="position:absolute;top:0;bottom:0;left:0;right:0;">';
			
			echo '<object style="width:100%;height:100%;">
				<param name="movie" value="' . $player . '">
				<param name="flashvars" value="upload_hash=' . $upload_hash . '&lang=' . $language . '">
				<param name="allowFullScreen" value="true" />
				<param name="allowScriptAccess" value="always" />
				<param name="wmode" value="opaque" />
				<embed style="width:100%;height:100%;" src="' . $player . '" flashvars="upload_hash=' . $upload_hash . '&lang=' . $language . '" allowFullScreen="true" allowScriptAccess="always" wmode="opaque"></embed>
			</object>';
			
			//close fluid player box
			echo '</div></div></div>';
		}
		else
		{
			//+++++++++++++++++player HTML5 version!!!
			
			
			//=========get video and thumb links
			$keyname = md5($_SERVER['REMOTE_ADDR'] . '_' . $upload_info['file_md5'] . '_' . $video_info['chunks'][$chunknumber]);
			//echo '$keyname: '.$keyname.'<br />';
			//check if already key for this IP/video/chunk
			$datfile_num = find_place_according_to_index($keyname, 'videokeys_index.dat');
			$current_key_data = get_element_info($keyname, $datfile_num);

			//if no key already, create key
			if ($current_key_data === false) {
				//echo 'i create new key<br />';
				$arandomhash = base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);
				$videokey['k'] = $arandomhash;
				$videokey['t'] = $nowtime;
				$data = serialize($videokey);

				$datfile_num = find_place_according_to_index($keyname, 'videokeys_index.dat');
				add_or_update_element($keyname, $data, $datfile_num, 'videokeys_index.dat');
			}
			//else if already key, update time
			else {
				//echo 'already have key<br />';
				$keyvalue_toset_array2['t'] = $nowtime;
				set_elements('videokeys_index.dat', $keyname, $keyvalue_toset_array2);
				$videokey = unserialize($current_key_data);
				//print_r($videokey); echo '<br />';
			}
			$divx_file_url = 'https://' . $video_info['server'] . '.'.$main_domain.'/play?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][$chunknumber] . '&key=' . $videokey['k'];
			$thumburl = 'https://' . $video_info['server'] . '.'.$main_domain.'/large?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][$chunknumber];
			//=========get video and thumb links
			
			
			
			
			
			//show HTML5 player
            //popups? no thanks lol! this is not 1990!
			$popuper='';
            /*
            if($upload_info['popup']!='')
            {$popuper='onclick="rabbadoo(\''.base64_decode($upload_info['popup']).'\');"';}
            */
			
			//fluid player box
			echo '<div style="max-width:960px;max-height:540px;margin:auto;">
			<div style="position:relative;width:100%;padding-bottom:56.25%;">
			<div style="position:absolute;top:0;bottom:0;left:0;right:0;">';
			
			if(isset($_GET['localtest']))
				$divx_file_url='https://'.$main_domain.'/womanalivevideo_v1.34.mp4';
			
			//echo '<!-- <meta http-equiv="X-UA-Compatible" content="IE=Edge"/> -->';
			echo '<video '.$popuper.' controls="controls" poster="' . $thumburl . '" id="HTML5Barbavideo" style="width:100%;height:100%;">
				<source src="' . $divx_file_url . '" type="video/mp4" />
			</video>';
				//if($upload_info['popup']!='' && 1==2)
				//{
					////transparent flash that covers player and pops on click...
					//echo '<div onclick="clisqued()" id="clisq_div" style="position:absolute;border:solid 1px blue;z-index:1000;width:640px;height:380px;top:0;left:0;right:0;margin:auto;">
						//<object width="640" height="380" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">
							//<param name="movie" value="/clisq">
							//<param name="flashvars" value="pURL='.urlencode(base64_decode($upload_info['popup'])).'">
							//<param name="wmode" value="transparent" />
							//<embed width="640" height="380" src="/clisq" flashvars="pURL='.urlencode(base64_decode($upload_info['popup'])).'" wmode="transparent" type="application/x-shockwave-flash"></embed>
						//</object>
					//</div>';
				//}
				
				//close fluid player box
				echo '</div></div></div>';
			
			//show options links
			echo '<div style="position:relative;">
				<a href="' . $divx_file_url . '" target="_blank">' . $text[150] . '</a>
				|
				<a onclick="toggle_embed()" style="cursor:pointer;text-decoration:underline;">' . $text[151] . '</a>
				<div id="embed_code_div" style="display:none;position:absolute;left:0;right:0;margin-left:auto;margin-right:auto;" class="poppy_info">
					<div style="right:0;margin-right:0;text-align:right;"><a style="cursor:pointer;" onclick="toggle_embed()">[x]</a></div>
					' . $text[153] . ':<br />
					<textarea style="width:300px;height:90px;">&#60;iframe src="https://'.$main_domain.'/video/'.$upload_hash.'?embed=1" style="width:640px;height:540px;">&#60;/iframe></textarea>
				</div>
				|
				<a href="/duplicate?upload='.$upload_hash.'" target="_blank">' . $text[152] . '</a>
			</div>';
			
			//show chunks thumbs
			if($howmany_chunks>1)
			{
				//show chunks page
				include(BP.'/include/function/chunkname2time.php');
				
				//echo '<table style="margin:auto;"><tr>';
				echo '<div style="margin:auto;">';
				foreach ($video_info['chunks'] as $key => $value) {

					$divx_file_url = '/video/' . $upload_hash . '/' . $key.$re_embeder;
					$thumburl = 'https://' . $video_info['server'] . '.'.$main_domain.'/thumb?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][$key];
						//echo '<td style="vertical-align:top;text-align:center;">';
						echo '<div style="position:relative;display:inline-block;">';
						echo '<a href="' . $divx_file_url . '">';
							echo '<img src="' . $thumburl . '" class="chunk_img" />';
						echo '</a><br />';
						echo '<a class="chunk_time chunk_link">' . chunkname2time($value) . '</a>';
						if($chunknumber==$key)
						{
							//shadows
							//echo '<div style="position:absolute;color:black;width:100%;text-align:center;top:19px;left:0;z-index:1003;">'.$text[154].'</div>';
							//echo '<div style="position:absolute;color:black;width:100%;text-align:center;top:20px;left:1px;z-index:1003;">'.$text[154].'</div>';
							//echo '<div style="position:absolute;color:black;width:100%;text-align:center;top:21px;left:0;z-index:1003;">'.$text[154].'</div>';
							//echo '<div style="position:absolute;color:black;width:100%;text-align:center;top:20px;left:-1px;z-index:1003;">'.$text[154].'</div>';
							//body
							echo '<span style="position:absolute;color:white;width:100%;text-align:center;top:20px;left:0;z-index:1003;">'.$text[154].'</span>';
						}
						echo '</div>';
						//echo '</td>';
				}
				//echo '</tr></table>';
				echo '</div>';
			}
			
			
			//if($upload_info['popup']!='' && 1==2)
			//{
				////petit flash dans le coin qui pop en reponse a javascripts
				//echo '<div style="border:solid 1px blue;" onclick="serve()">
					//<object width="16" height="16" id="servejabba" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">
						//<param name="movie" value="/servejabba">
						//<param name="allowScriptAccess" value="always" />
						//<embed width="16" height="16" src="/servejabba" allowScriptAccess="always" name="servejabba" type="application/x-shockwave-flash"></embed>
					//</object>
					//<script>
						//function serve()
						//{
							//var servejabba = document.servejabba;
							//servejabba.serve_js('.json_encode(base64_decode($upload_info['popup'])).');
						//}
					//</script>
				//</div>';
			//}
		}
	
		//show player HTML5/Flash swap link
		/*if(isset($_GET['flash']))
		{echo '<a href="?'.$re_embeder.'">watch in HTML 5 video player (allows skipping forward)</a>';}
		else
		{echo '<a href="?flash=1'.$re_embeder.'">watch in flash player (allows 200% volume!!)</a>';}*/
		
		
		//description
		if(!isset($_GET['embed']))
		{echo '<div><p>' . nl2br(makeClickableLinks(htmlspecialchars(($upload_info['description'])))) . '</p></div>';}
		
		
		//close content container
		echo '</div>';
	// }
	// else
	// {
	

    // if ($chunknumber >= 1 && isset($_GET['mobile'])) {
		// //========old players




		// $keyname = md5($_SERVER['REMOTE_ADDR'] . '_' . $upload_info['file_md5'] . '_' . $video_info['chunks'][$chunknumber]);
		// //echo '$keyname: '.$keyname.'<br />';
		// //check if already key for this IP/video/chunk
		// $datfile_num = find_place_according_to_index($keyname, 'videokeys_index.dat');
		// $current_key_data = get_element_info($keyname, $datfile_num);

		// //if no key already, create key
		// if ($current_key_data === false) {
			// //echo 'i create new key<br />';
			// $arandomhash = base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);
			// $videokey['k'] = $arandomhash;
			// $videokey['t'] = $nowtime;
			// $data = serialize($videokey);

			// $datfile_num = find_place_according_to_index($keyname, 'videokeys_index.dat');
			// add_or_update_element($keyname, $data, $datfile_num, 'videokeys_index.dat');
		// }
		// //else if already key, update time
		// else {
			// //echo 'already have key<br />';
			// $keyvalue_toset_array2['t'] = $nowtime;
			// set_elements('videokeys_index.dat', $keyname, $keyvalue_toset_array2);
			// $videokey = unserialize($current_key_data);
			// //print_r($videokey); echo '<br />';
		// }
		// $divx_file_url = 'http://' . $video_info['server'] . '.barbavid.com/play?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][$chunknumber] . '&key=' . $videokey['k'];

		// $thumburl = 'http://' . $video_info['server'] . '.barbavid.com/large?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][$chunknumber];

		// echo '<br /><center><div>' . $text[120] . '</div><div><video width="320" height="240" controls="controls" poster="' . $thumburl . '">
// <source src="' . $divx_file_url . '" type="video/mp4" />
// ' . $text[121] . '
// </video></div>
// <br />
// <div>' . $text[122] . '</div>
// <a href="' . $divx_file_url . '" target="_blank">' . $text[123] . '</a><br />';
		// if ($howmany_chunks > 1) {
			// echo '<br /><a href="/video/' . $upload_hash . '?mobile">' . $text[124] . '</a><br />';
		// }
		// else
		// {echo '<br /><div style="text-align:center;"><a href="?notmobile">' . $text[125] . '</a></div>';}
		// echo '</center>';
	// } else if (isset($_GET['mobile'])) {
		// //==========show chunks

		// //show chunks page
		// include('../chunkname2time.php');
		// echo '<br /><br /><center>';
		// echo '<div class="video_title">' . htmlspecialchars(base64_decode($upload_info['title'])) . '</div>';
		// echo '<div><!-- -->' . htmlspecialchars(base64_decode($upload_info['description'])) . '</div>';
		// foreach ($video_info['chunks'] as $key => $value) {



			// $divx_file_url = '/video/' . $upload_hash . '/' . $key . '?mobile';


			// $thumburl = 'http://' . $video_info['server'] . '.barbavid.com/thumb?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][$key];
			// echo '<a href="' . $divx_file_url . '"><img src="' . $thumburl . '" /><br />' . chunkname2time($value) . '</a><br />
				// <script type="text/javascript">
				// var t=setTimeout("window.location.reload();",5*60*60*1000);
				// </script>';
		// }
		// echo '</center>';
		// echo '<br /><div style="text-align:center;"><a href="?notmobile">' . $text[125] . '</a></div>';
	// } else {




	// //play chunk page
	// //    if(!isset($_POST['human']))
	// //    {
	// //        echo '<center>
	// //            <br />
	// //            <br />';
	// //        echo '<div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=728 HEIGHT=90 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=728x90&section=238447"></IFRAME></div>';
	// //        echo '<table class="adta"><tr><td class="adtabad">
	// //            <div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=300 HEIGHT=250 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=300x250&section=193423"></IFRAME></div>
	// //            </td><td class="adtabcn">';
	// //        echo '<form method="post">
	// //            '.$text[105].'<br />
	// //            <input type="submit" value="'.$text[106].'" name="human" />
	// //            </form>';
	// //        echo '</td><td class="adtabad">
	// //            <div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=300 HEIGHT=250 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=300x250&section=193423"></IFRAME></div>
	// //            </td></table>';
	// //        echo '<IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=728 HEIGHT=90 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=728x90&section=238447"></IFRAME></div>';
	// //        echo '</center>';
	// //    }
	// //    else
	// //    {
	// //        if($_SERVER['REMOTE_ADDR']==$admin_ip)
	// //        {
	// //    echo '<center>';
	// //
	// //    echo '<table><tr><td style="vertical-align:top;">';
	// //    $ad_format = 'verto';
	// //    include('../../nab/jumparazioo.php');
	// //    echo '</td><td style="width:728px;vertical-align:top;">';

		// echo '<center>';

			// // echo '<div>';
			// // $ad_format = 'hori';
			// // include('../../nab/jumparazioo.php');
			// // echo '</div>';


		// if (isset($_POST['human']) || 1==1) {

	// //            echo '<script type="text/javascript">
	// //var cache_buster=Math.random((new Date()).getMilliseconds())*100000000000000000;
	// //document.write(unescape(\'%3Cscript type=\"text/javascript\" src="http://max.gunggo.com/show_ad.ashx?type=interstitial&sid=4235&cid=5641&cm=0&fc=1&hr=1&cb=\'+cache_buster+\'\"%3E%3C/scr\'+\'ipt%3E\'));
	// //</script>';


			// echo '<div id="player" class="playor">';
			// echo '<object width="728" height="325">
				// <param name="movie" value="' . $player . '">
				// <param name="flashvars" value="upload_hash=' . $upload_hash . '&lang=' . $language . '">
				// <param name="allowFullScreen" value="true" />
				// <param name="allowScriptAccess" value="always" />
				// <param name="wmode" value="transparent" />
				// <embed width="728" height="325" src="' . $player . '" flashvars="upload_hash=' . $upload_hash . '&lang=' . $language . '" allowFullScreen="true" allowScriptAccess="always" wmode="transparent">
				// </embed>
				// </object>';
			// echo '<div style="text-align:center;"><a href="?mobile">' . $text[126] . '</a></div>';
			// // echo '<table class="guakamon" id="rabbax"><tr><td class="guakamon_td"><div class="rabbass"><div style="height:4px;"><!-- --></div><div style="height:250px;">';
			// // $ad_format = 'squari';
			// // include('../../nab/jumparazioo.php');
			// // echo '</div><div style="height:4px;"><!-- --></div><span id="closer" class="robonomo"><!-- --></span><div style="height:4px;"><!-- --></div></div></td></tr></table>';
			// // echo '</div><script type="text/javascript">
			// // runcloser();
			// // </script>';
		// } else {

		
			// //please wait video hosting is expensive page

			// //coverup ad
			// $waittime = 5;
			// echo '<div>';
			// echo '<table><tr><td>';
			// $ad_format = 'squari';
			// include('../../nab/jumparazioo.php');
			// echo '</td><td>';
			// echo '<center>
			// <br />
			// <form method="post">
			// <div style="font-size:14px;">' . $text[116] . '</div>
			// <br />
			// <div style="font-size:18px;display:none;" id="doitbuttzz">
			// ' . $text[117] . ':<br />
			// <input type="submit" name="human" value="' . $text[118] . '" style="font-weight:bold;font-size:18px;height:60px;width:220px;cursor:pointer;" />
			// </div>
			// <div style="font-size:18px;" id="timeitoss">' . showpleasewaitxseconds($waittime) . '</div>
			// <script type="text/javascript">
			// function dectime()
			// {
				// document.getElementById("waittimezz").innerHTML = Math.abs(document.getElementById("waittimezz").innerHTML) - 1;
				// if(document.getElementById("waittimezz").innerHTML == 0)
				// {
					// document.getElementById("doitbuttzz").style.display = "block";
					// document.getElementById("timeitoss").style.display = "none";
				// }
				// else
				// {
					// var t=setTimeout("dectime()",1000);
				// }
			// }
			// var t=setTimeout("dectime()",1000);
			// </script>
			// </form>
			// </center>';
			// echo '</td><td><div style="width:250px;height:300px;">';
			// $ad_format = 'squari';
			// include('../../nab/jumparazioo.php');
			// echo '</div></td></tr></table>';
			// echo '</div>';

	// //            echo '<script type="text/javascript">
	// //var cache_buster=Math.random((new Date()).getMilliseconds())*100000000000000000;
	// //document.write(unescape(\'%3Cscript type=\"text/javascript\" src="http://max.gunggo.com/show_ad.ashx?type=interstitial&sid=4235&cid=5641&cm=0&fc=1&hr=1&cb=\'+cache_buster+\'\"%3E%3C/scr\'+\'ipt%3E\'));
	// //</script>';
			// }

			// //will put this more near where it is needed instead
			// // echo '<script type="text/javascript">
				// // runcloser();
				// // </script>';

			// //echo '<div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=728 HEIGHT=90 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=728x90&section=238447"></IFRAME></div>';
			
			// // echo '<div>';
			// // $ad_format = 'hori';
			// // include('../../nab/jumparazioo.php');
			// // echo '</div>';
			
	// //echo '<div class="video_title">'.htmlspecialchars(base64_decode($upload_info['title'])).'</div>';
	// //include('../make_link.php');
	// //echo '<div class="video_description">'.makeClickableLinks(htmlspecialchars(base64_decode($upload_info['description']))).'</div>';
	// //chunk navigation:
	// //    if($howmany_chunks>1)
	// //    {
	// //        include('../chunkname2time.php');
	// //        echo showplayingchunk($chunknumber,$howmany_chunks).' ('.chunkname2time($video_info['chunks'][$chunknumber]).')';
	// //        $prevchunk='';
	// //        if($chunknumber>1)
	// //        {$prevchunk='<a href="/video/'.$upload_hash.'/'.($chunknumber-1).'"><< '.$text[108].' ('.chunkname2time($video_info['chunks'][$chunknumber-1]).')</a> | ';}
	// //        $nextchunk='';
	// //        if($chunknumber<$howmany_chunks)
	// //        {$nextchunk=' | <a href="/video/'.$upload_hash.'/'.($chunknumber+1).'">'.$text[109].' ('.chunkname2time($video_info['chunks'][$chunknumber+1]).') >></a>';}
	// //        $chunknav='<div>'.$prevchunk.'<a  href="/video/'.$upload_hash.'">'.$text[110].'</a>'.$nextchunk.'</div>';
	// //        echo $chunknav;
	// //    }
	// //        }
	// //        else
	// //        {
	// //
	// //
	// //
	// //
	// //
	// //
	// //
	// //            $keyname=md5($_SERVER['REMOTE_ADDR'].'_'.$upload_info['file_md5'].'_'.$video_info['chunks'][$chunknumber]);
	// //            //echo '$keyname: '.$keyname.'<br />';
	// //
	// //            //check if already key for this IP/video/chunk
	// //            $datfile_num=find_place_according_to_index($keyname,'videokeys_index.dat');
	// //            $current_key_data=get_element_info($keyname,$datfile_num);
	// //
	// //            //if no key already, create key
	// //            if($current_key_data===false)
	// //            {
	// //                //echo 'i create new key<br />';
	// //                $arandomhash=base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);
	// //                $videokey['k']=$arandomhash;
	// //                $videokey['t']=$nowtime;
	// //                $data=serialize($videokey);
	// //
	// //                $datfile_num=find_place_according_to_index($keyname,'videokeys_index.dat');
	// //                add_or_update_element($keyname,$data,$datfile_num,'videokeys_index.dat');
	// //            }
	// //            //else if already key, update time
	// //            else
	// //            {
	// //                //echo 'already have key<br />';
	// //                $keyvalue_toset_array2['t']=$nowtime;
	// //                set_elements('videokeys_index.dat',$keyname,$keyvalue_toset_array2);
	// //                $videokey=unserialize($current_key_data);
	// //                //print_r($videokey); echo '<br />';
	// //            }
	// //
	// //
	// //
	// //
	// //
	// //            $divx_file_url='http://'.$video_info['server'].'.barbavid.com/play?video='.$upload_info['file_md5'].'&chunk='.$video_info['chunks'][$chunknumber].'&key='.$videokey['k'];
	// //            $width=728;
	// //            $height=305;
	// //
	// //
	// //            //set play link and player dimensions on js variables
	// //            //start script to go back to human ver in 6h
	// //            echo '<script type="text/javascript">
	// //            var playlink = '.json_encode($divx_file_url).';
	// //            var playlink_ue = '.json_encode(urlencode($divx_file_url)).';
	// //            var player_w = '.$width.';
	// //            var player_h = '.$height.';
	// //            count6=setTimeout("document.location.href=document.location.href;",6*3600*1000);
	// //            </script>';
	// //            echo '<center>';
	// //            echo '<div id="player" style="position:relative;width:'.$width.'px;">
	// //            <img src="http://'.$video_info['server'].'.barbavid.com/large?video='.$upload_info['file_md5'].'&chunk='.$video_info['chunks'][$chunknumber].'" style="width:'.$width.'px;height:'.$height.'px;" />
	// //            <table class="playtable"><tr><td style="text-align:center;"><acronym title="'.$text[113].'"><img src="/play_roche.png" onmouseover="this.src=\'/play_roche2.png\'" onmouseout="this.src=\'/play_roche.png\'" onclick="play()" style="cursor:pointer;" /></acronym> <acronym title="'.$text[114].'"><img src="/download_roche.png" onmouseover="this.src=\'/download_roche2.png\'" onmouseout="this.src=\'/download_roche.png\'" onclick="download()" style="cursor:pointer;" /></acronym></td></tr></table>
	// //            <table class="adtable" id="rabbax"><tr><td style="text-align:center;"><div style="width:308px;background-color:#CCCCCC;margin-left:auto;margin-right:auto;"><div style="height:4px;"><!-- --></div><div style="height:250px;"><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=300 HEIGHT=250 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=300x250&section=193423"></IFRAME></div><div style="height:4px;"><!-- --></div><span id="closer" style="background-color:#FFFFFF;padding-left:4px;padding-right:4px;"><!-- --></span><div style="height:4px;"><!-- --></div></div></td></tr></table>
	// //            </div>';
	// //            echo '<script type="text/javascript">
	// //            runcloser();
	// //            </script>';
	// //            echo '<div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=728 HEIGHT=90 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=728x90&section=238447"></IFRAME></div>';
	// //            echo '<div class="video_title">'.htmlspecialchars(base64_decode($upload_info['title'])).'</div>';
	// //            include('../make_link.php');
	// //            echo '<div class="video_description">'.makeClickableLinks(htmlspecialchars(base64_decode($upload_info['description']))).'</div>';
	// //            //chunk navigation:
	// //            if($howmany_chunks>1)
	// //            {
	// //                include('../chunkname2time.php');
	// //                echo showplayingchunk($chunknumber,$howmany_chunks).' ('.chunkname2time($video_info['chunks'][$chunknumber]).')';
	// //                $prevchunk='';
	// //                if($chunknumber>1)
	// //                {$prevchunk='<a href="/video/'.$upload_hash.'/'.($chunknumber-1).'"><< '.$text[108].' ('.chunkname2time($video_info['chunks'][$chunknumber-1]).')</a> | ';}
	// //                $nextchunk='';
	// //                if($chunknumber<$howmany_chunks)
	// //                {$nextchunk=' | <a href="/video/'.$upload_hash.'/'.($chunknumber+1).'">'.$text[109].' ('.chunkname2time($video_info['chunks'][$chunknumber+1]).') >></a>';}
	// //                $chunknav='<div>'.$prevchunk.'<a  href="/video/'.$upload_hash.'">'.$text[110].'</a>'.$nextchunk.'</div>';
	// //                echo $chunknav;
	// //            }
	// //            echo '</center>';
	// //
	// //
	// //
	// //
	// //        }
	// //instructions for noobs
			// if ($howmany_chunks > 1 && isset($_POST['human'])) {
				// echo '<div style="text-align:center;font-size:18px;"><img src="/error.gif" /> ' . $text[119] . '</div>';
			// }


			// echo '</center>';




	// //        echo '<center><table><tr><td>';
	// //        $ad_format = 'squari';
	// //        include('../../nab/jumparazioo.php');
	// //        echo '</td><td>';
	// //        $ad_format = 'squari';
	// //        include('../../nab/jumparazioo.php');
	// //        echo '</td></tr></table></center>';
	// //    echo '</td><td style="vertical-align:top;">';
	// //    $ad_format = 'verto';
	// //    include('../../nab/jumparazioo.php');
	// //    echo '</td></tr><table>';
	// //
	// //
	// //    echo '</center>';



			// //moving that more out of the bracket to show both for flash and HTML5 version
			// //echo '<br />' . $edit_form . $delete_form;
	// //    }
		// }
    // }
	
	echo '<br />' . $edit_form . $delete_form;
}
//else
//{
//    //show chunks page
//    include('../chunkname2time.php');
//    echo '<br /><br /><center>';
//    echo '<div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=728 HEIGHT=90 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=728x90&section=238447"></IFRAME></div>';
//    echo '<div class="video_title">'.htmlspecialchars(base64_decode($upload_info['title'])).'</div>';
//    foreach($video_info['chunks'] as $key => $value)
//    {
//        $thumburl='http://'.$video_info['server'].'.barbavid.com/thumb?video='.$upload_info['file_md5'].'&chunk='.$video_info['chunks'][$key];
//        echo '<a href="/video/'.$upload_hash.'/'.$key.'"><img src="'.$thumburl.'" /><br />'.chunkname2time($value).'</a><br />';
//    }
//    echo '<div><IFRAME FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=728 HEIGHT=90 SRC="http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=728x90&section=238447"></IFRAME></div>';
//    echo '</center>';
//
//    echo '<br />'.$edit_form.$delete_form;
//}
//++++++++++++++++++++++++++++++++++CONTENT


if(!isset($_GET['embed']))
{
	
	//============some banner ads
	/*echo '<center>';
	echo '<div style="overflow:auto;">';
	$ad_format = 'hori';
	include('../../nab/jumparazioo.php');
	echo '</div>';
	//echo '<table><tr><td>';
	echo '<div style="display:inline;">';
	$ad_format = 'squari';
	include('../../nab/jumparazioo.php');
	//echo '</td><td>';
	echo '</div><div style="display:inline;">';
	$ad_format = 'squari';
	include('../../nab/jumparazioo.php');
	//echo '</td></tr></table>';
	echo '</div>';
	echo '</center>';*/
	//============some banner ads



	include(BP.'/include/footer.php');
}


include(BP.'/include/page_end.php');