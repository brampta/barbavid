<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
//include(BP.'/include/dat_system/dat_system_functions.php');
//include(BP.'/include/function/video.php');

if(!isset($_GET['path'])){
    header("Location: /");
    exit;
}
$exploded_path = explode('/', $_GET['path']);
$user_hash = $exploded_path[0];
include(BP.'/include/function/get_path_var.php');
$page=get_path_var('page',$_GET['path'],1);
$user_data_array=$db->load_by('users','hash',$user_hash);
$include_suspended=false;
include(BP.'/include/function/can_admin.php');
if(can_admin($user_data_array['id'],1)){
    $include_suspended=true;
}
$videos = $video->get_user_videos($user_data_array,$page,5,$include_suspended);

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

?>
<h1 class="username"><?php echo $user_data_array['name'] ?></h1>
<?php

$video->show_videos($videos);

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');