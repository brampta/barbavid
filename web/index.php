<?php
include(dirname(dirname(__FILE__)).'/include/init.php');

include(BP.'/include/procedure/controller.php');

include(BP.'/include/function/get_path_var.php');
$page=get_path_var('page',$_SERVER['REQUEST_URI'],1);
$videos = $video->get_home_videos($page,50);

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

?>
<p class="page_title"><?php echo __('Welcome to %1',$site_name) ?></p>
<?php

$video->show_videos($videos);

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');