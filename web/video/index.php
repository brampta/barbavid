<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');



$maxtitlelen = 100;
$maxdesclen = 3000;
$maxpopURLlen = 1024;


include(BP.'/include/function/make_link.php');
include(BP.'/include/function/datetime_to_timestamp.php');

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


//================get upload info
$upload_info = $db->load_by('videos','hash',$upload_hash);
$user_data_array = $db->load('users',$upload_info['user_id']);
//print_r($upload_info);
//print_r($user_data_array);
//==========get upload info

include(BP.'/include/function/can_admin.php');
function can_admin_video(){
    global $upload_info;
    return can_admin($upload_info['user_id'],1);
}


//edit upload info
if (can_admin_video()) {

    if (isset($_POST['edit'])) {
        $keyvalue_toset_array=array();
        $keyvalue_toset_array['title'] = mb_substr($_POST['title'], 0, $maxtitlelen);
        $keyvalue_toset_array['description'] = mb_substr($_POST['description'], 0, $maxdesclen);
        $keyvalue_toset_array['suspend'] = $_POST['suspend'];
        //get channel ID to set:
        $channel_data_array=$channel->load_by_hash($_POST['channel']);
        if(!in_array($_SESSION['user_id'],$channel_data_array['admin_ids'])){
            $message->add_message('error', __('invalid channel'));
        }else {
            $keyvalue_toset_array['channel_id'] = $channel_data_array['id'];
            $result = $db->update_by('videos', 'hash', $upload_hash, $keyvalue_toset_array);
            if ($result == 1) {
                $message->add_message('success', __('successfully updated video info'));
                //update $upload_info with array_merge, will be faster than dumb reget from the database
                $upload_info = array_merge($upload_info, $keyvalue_toset_array);
            } else {
                $message->add_message('error', __('error updating video info'));
            }
        }
    }

    //prepare user channel select for form
    if(!isset($channel_data_array)){
        $channel_data_array=$channel->load($upload_info['channel_id']);
    }
    $channel_select = $channel->get_channel_select('channel','channelgrx',$channel_data_array['hash']);
}




//==============delete video
if (can_admin_video()) {
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




//==============get video info from dat system
if ($upload_info['suspend'] == '0' && $upload_info['file_md5'] != '0') {
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
}
//==============get video info from dat system

//=========determine video state:
$state=false;
if ($upload_info['suspend'] != '0' || $upload_info['file_md5'] == '0') {
    $state='deleted';
} else if (substr($video_info['server'], 0, 6) == 'upload') {
    $state='encoding';
} else if (substr($video_info['server'], 0, 15) == 'failedencoding_') {
    $state='failed_encoding';
} else {
    $state='ok';

    //backup mechanism for videos ready state...
    if($upload_info['ready'] == '0'){
        $upload__update_data_array=array();
        $upload__update_data_array['ready']=1;
        $db->update('videos',$upload_info['id'],$upload__update_data_array);
    }
}

//next video!
//if not last chunk next video will be next chunk
//if channel next video will be next in channel
$previousvideo_url = null;
$nextvideo_url = null;
if($chunknumber<$howmany_chunks){
    $nextvideo_url =  '/video/' . $upload_hash . '/' . ($chunknumber+1).'?autoplay=0'.$re_embeder;
}else if(isset($_GET['channel'])){
    $around_videos = $video->channel_get_around_videos($upload_info['id'],$channel_data_array,7);
    if(isset($around_videos['vid_before'])){
        $previousvideo_url = $video->get_channel_embed_redirect_url($around_videos['vid_before']['hash'],$channel_data_array['hash'],false);
    }
    if(isset($around_videos['vid_after'])){
        $nextvideo_url = $video->get_channel_embed_redirect_url($around_videos['vid_after']['hash'],$channel_data_array['hash'],false);
    }
}

include(BP.'/include/head_start.php');
echo '<title>' . htmlspecialchars(($upload_info['title'])) . ' - Barbavid - ' . $text[0] . '</title>
<script type="text/javascript" src="/js/video_js2.js"></script>
<script type="text/javascript" src="/js/language_video_js.php?lang=' . urlencode($language) . '"></script>
<script type="text/javascript" src="/js/maketimus.js"></script>
<script type="text/javascript" src="/js/language_maketimus_js.php?lang='.urlencode($language).'"></script>
<script>
    var previousvideo_url = '.json_encode($previousvideo_url) .';
    var nextvideo_url = '.json_encode($nextvideo_url) .';
    var autoplay = '.json_encode(isset($_GET['autoplay'])?true:false).';
</script>';

//open graph protocol:
echo '<meta property="og:title" content="'.htmlspecialchars(($upload_info['title'])).'" />';
if($upload_info['ready']==1) {
    $thumburl = 'https://' . $video_info['server'] . '.'.$main_domain.'/large?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][$chunknumber];
    echo '<meta property="og:type" content="video.movie" />
    <meta property="og:image" content="'.$thumburl.'" />';
}

include(BP.'/include/head_end.php');
	
$re_embeder='';
if(isset($_GET['embed']))
{
	$re_embeder='&embed=1';
}

if(!isset($_GET['embed']))
{include(BP.'/include/header.php');}

$message->show_messages();

//===============build admin form strings
$edit_form = '';
if (can_admin_video() && !isset($_GET['embed'])) {
    $edit_form = '<div style="overflow:auto;">
    <form method="post">
<table class="vidform">
<tr><td class="labezl">' . $text[2] . ':</td><td><input name="title" type="text" class="tittx" value="' . htmlspecialchars(($upload_info['title'])) . '" /></td></tr>
<tr><td><!-- --></td><td class="xplain">' . $text[10] . '</td></tr>
<tr><td class="labezl">' . $text[3] . ':</td><td><textarea name="description" class="descrtx">' . htmlspecialchars(($upload_info['description'])) . '</textarea></td></tr>
<tr><td><!-- --></td><td class="xplain">' . $text[11] . '</td></tr>';

$edit_form.='<tr><td class="labezl">' . __('Channel') . ':</td><td>'.$channel_select.'</td></tr>
        <tr><td><!-- --></td><td class="xplain">' . $text[11] . '</td></tr>';

$edit_form.= '<tr><td class="labezl">' . $text[5] . ':</td><td>';
$edit_form.='<div><input type="radio" name="suspend" value="0"';if($upload_info['suspend']==0){$edit_form.=' checked="checked"';}$edit_form.='>ok</div>';
$edit_form.='<div><input type="radio" name="suspend" value="1"';if($upload_info['suspend']==1){$edit_form.=' checked="checked"';}$edit_form.='>suspended</div>';
$edit_form.='</td></tr>
</table>

<input type="submit" name="edit" value="' . $text[6] . '" />
<input type="hidden" name="human" value="1" />
</form>
</div>';
}
$delete_form = '';
if (can_admin_video() && !isset($_GET['embed'])) {
    $delete_form = '<form method="post">
<input type="submit" name="delete_video" value="' . $text[7] . '" /><br />
<input type="checkbox" name="delete_video_imsure" /> ' . $text[8] . '
<input type="hidden" name="human" value="1" />
</form>';
}
//===============build admin form strings





//++++++++++++++++++++++++++++++++++CONTENT
if ($state=='deleted') {

	//=======video is deleted=======

    echo '<center>';
    echo '<br /><br />';
	
    echo '<br />';
    echo '<div class="error">';
    if ($upload_info['suspend'] != '') {
        echo '<div class="suspended">' . $text[102] . '</div>';
    }
    if ($upload_info['file_md5'] == '0') {
        echo '<div class="deleted">' . $text[104] . '</div>';
    }
    echo '</div>';
    echo '<br />';

	
    echo '</center>';

} else if ($state=='encoding') {

    //=========video is encoding=======
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
	
    if ($queue_info != '' && $queue_info !== 'not found') {
        $exploded_info = explode(' ', $queue_info);
        if ($exploded_info[0] === 'inprogress') {
            if(!isset($exploded_info[2])){
                $exploded_info[2]=null;
            }
            if(!isset($exploded_info[3])){
                $exploded_info[3]=null;
            }
            echo explain_encode_progress($exploded_info[1], $exploded_info[2], $exploded_info[3]);
        } else if ($queue_info === 'not found-error queue') {
            echo $text[115];
        } else {
            echo $text[107] . $queue_info;
        }
    }
    echo '</center>';

} else if ($state=='failed_encoding') {

    //=========video encoding failed=======
    echo '<center>';
    echo '<br /><br /><br />';
    echo $text[115];
    echo '</center>';
    echo '<br />';
} else if($state=='ok') {

    //=========video ready=======
    ?>

    <!-- open content container -->
    <div class="video">

        <?php
        //=========new player!!!

        //THIS IS THE IF WHEN THERE IS A VIDEO!:
        if($chunknumber<1) {$chunknumber=1;}

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

        //..........fluid player box..........
        $autoplay='';
        /*if(isset($_GET['autoplay'])){
            $autoplay=' autoplay muted';
        }*/ //meh lets use JS instead
        echo '<div style="max-width:960px;max-height:540px;margin:auto;">
        <div style="position:relative;width:100%;padding-bottom:56.25%;">
        <div style="position:absolute;top:0;bottom:0;left:0;right:0;">';
            echo '<video controls="controls" poster="' . $thumburl . '" id="HTML5Barbavideo" style="width:100%;height:100%;"'.$autoplay.'>
                <source src="' . $divx_file_url . '" type="video/mp4" />
            </video>
            <script>
                attach_video_events();
            </script>';
        //..........close fluid player box..........
        echo '</div></div></div>';

        //========show chunks thumbs
        if($howmany_chunks>1)
        {
            //show chunks page
            include(BP.'/include/function/chunkname2time.php');

            //echo '<table style="margin:auto;"><tr>';
            echo '<div style="margin:auto;">';
            foreach ($video_info['chunks'] as $key => $value) {

                $chunk_url = '/video/' . $upload_hash . '/' . $key.$re_embeder;
                $thumburl = 'https://' . $video_info['server'] . '.'.$main_domain.'/thumb?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][$key];
                    //echo '<td style="vertical-align:top;text-align:center;">';
                    echo '<div style="position:relative;display:inline-block;">';
                    echo '<a href="' . $chunk_url . '">';
                        echo '<img src="' . $thumburl . '" class="chunk_img" />';
                    echo '</a><br />';
                    echo '<a class="chunk_time chunk_link">' . chunkname2time($value) . '</a>';
                    if($chunknumber==$key)
                    {
                        echo '<span style="position:absolute;color:white;width:100%;text-align:center;top:20px;left:0;z-index:1003;">'.$text[154].'</span>';
                    }
                    echo '</div>';
                    //echo '</td>';
            }
            //echo '</tr></table>';
            echo '</div>';
        }
        
        
        
        ?>


        <div class="title">
            <?php
            //title
            $chunk_info='';
            if($howmany_chunks>1)
            {$chunk_info='<span style="font-weight:normal;"> (part '.$chunknumber.' of '.$howmany_chunks.')</span>';}
            echo '<div class="video_title"><h1>' . htmlspecialchars(($upload_info['title'])) .'</h1>'. $chunk_info . '</div>';
            ?>
        </div>


        <div class="stats_and_actions">
            <div class="stats">
                <?php
                //stats (coming soon...)
                //views
                //upload date
                echo '<span id="upload_info_created"></span><script>document.getElementById("upload_info_created").innerHTML = maketimus('.datetime_to_timestamp($upload_info['created']).');</script>';
                ?>
            </div>
            <div class="actions">
                <?php
                //show options links
                echo '<div style="position:relative;">
                    <a href="' . $divx_file_url . '" download="' . htmlspecialchars(($upload_info['title'])) .'">' . $text[150] . '</a>
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
                //thumbs.. coming soon
                ?>
            </div>
        </div>


        <div class="user_and_subscribe">
            <div class="user">
                <?php
                //user
                //echo '<a href="/user/'.$user_data_array['hash'].'">'.htmlspecialchars($user_data_array['name']).'</a>';
                //just show channel now..
                echo '<a href="/channel/'.$channel_data_array['hash'].'">'.htmlspecialchars($channel_data_array['name']).'</a>';
                ?>
            </div>
            <div class="subscribe">
                <?php
                //subscribe (coming soon....)
                ?>
            </div>
        </div>


        <div class="description">
            <?php
            //description
            if(!isset($_GET['embed']))
            {echo '<div><p>' . nl2br(makeClickableLinks(htmlspecialchars(($upload_info['description'])))) . '</p></div>';}

            //tags coming soon

            ?>
        </div>

    <!-- close content container -->
    </div>

    <?php
    
    //if channel, include channel navigation!
        //$previousvideo_url, $nextvideo_url
        
        if(isset($_GET['channel'])){
            echo '<div class="channel_next">';
            if($previousvideo_url){
                echo '<a href="'.$previousvideo_url.'">'.__('previous').'</a>';
            }
            if($nextvideo_url){
                if($previousvideo_url){
                   echo ' | ';
                }
                echo '<a href="'.$nextvideo_url.'">'.__('next').'</a>';
            }
            echo '</div>';
        }
        
} else {
    echo __('error, unknown video state');
}

if(!isset($_GET['embed'])) {
    echo '<div class="video_edit">'.$edit_form . $delete_form.'</div>';

    //comments.. coming soon...

}



//related videos, and auto play... coming soon...


if(!isset($_GET['embed'])) {
	include(BP.'/include/footer.php');
}
include(BP.'/include/page_end.php');