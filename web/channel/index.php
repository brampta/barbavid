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
include(BP.'/include/function/get_path_var.php');
$page=get_path_var('page',$_GET['path'],1);
$channel_data_array=$channel->load_by_hash($channel_hash);
$include_suspended=false;
include(BP.'/include/function/can_admin.php');
if(can_admin($channel_data_array['admin_ids'],1)){
    $include_suspended=true;
}
$videos = $video->get_channel_videos($channel_data_array,$page,50,$include_suspended);

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

?>
<h1 class="page_title"><?php echo $channel_data_array['name'] ?></h1>
<?php

$video->show_videos($videos);

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');