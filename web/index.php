<?php
include(dirname(dirname(__FILE__)).'/include/init.php');
//include(BP.'/include/dat_system/dat_system_functions.php');
//include(BP.'/include/function/video.php');

include(BP.'/include/function/get_path_var.php');
$page=get_path_var('page',(isset($_GET['path']))?$_GET['path']:'',1);
$videos = $video->get_home_videos($page,5);

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

?>
<p class="page_title"><?php echo __('Welcome to %1',$site_name) ?></p>
<?php

$video->show_videos($videos);

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');