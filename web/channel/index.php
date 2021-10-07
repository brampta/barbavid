<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
//include(BP.'/include/dat_system/dat_system_functions.php');
//include(BP.'/include/function/video.php');

if(!isset($_GET['path'])){
    header("Location: /");
    exit;
}
$exploded_path = explode('/', $_GET['path']);
$channel_hash = $exploded_path[0];
$channel_data_array=$channel->load_by_hash($channel_hash);

//channel embed
if(isset($_GET['embed'])){
    if(isset($_GET['video'])){
        $channel_embed_video = 'hash:'.$_GET['video'];
    }else{
        $channel_embed_video = 'last';
    }
    $videos = $video->get_channel_videos($channel_data_array,1,1,false,$channel_embed_video);
    //echo '<pre>'.print_r($videos,true).'</pre>';die();
    if(isset($_GET['autoplay'])){
        $autoplay = true;
    }else{
        $autoplay = false;
    }
    $redirect_url = $video->channel_embed_redirect_url($videos,$autoplay);
    header('Location: '.$redirect_url);
    exit;
}

//normal channel page
include(BP.'/include/function/get_path_var.php');
$page=get_path_var('page',$_GET['path'],1);
$include_suspended=false;
include(BP.'/include/function/can_admin.php');
if(can_admin($channel_data_array['admin_ids'],1)){
    $include_suspended=true;
}
$videos = $video->get_channel_videos($channel_data_array,$page,50,$include_suspended);

include(BP.'/include/head_start.php');
echo '<script type="text/javascript" src="/js/video_js2.js"></script>';
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

?>
<h1 class="page_title"><?php echo $channel_data_array['name'] ?></h1>
<?php

$video->show_videos($videos);

echo '<div style="text-align:center;">
    <a onclick="toggle_embed()" style="cursor:pointer;text-decoration:underline;">' . __('embed channel') . '</a>
    <div id="embed_code_div" style="display:none;position:absolute;left:0;right:0;margin-left:auto;margin-right:auto;" class="poppy_info">
        <div style="right:0;margin-right:0;text-align:right;"><a style="cursor:pointer;" onclick="toggle_embed()">[x]</a></div>
        ' . __('use this code to embed this channel') . ':<br />
        <textarea style="width:300px;height:90px;">&#60;iframe src="https://'.$main_domain.'/channel/'.$channel_hash.'?embed=1" style="width:640px;height:540px;">&#60;/iframe></textarea>
    </div>
</div>';

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');