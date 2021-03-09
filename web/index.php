<?php
include(dirname(dirname(__FILE__)).'/include/init.php');
include(BP.'/include/dat_system/dat_system_functions.php');
include(BP.'/include/function/video.php');

$videos = $db->query('SELECT * FROM videos');
//var_dump($videos);

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');

?>
<p class="welcome"><?php echo __('Welcome to %1',$site_name) ?></p>
<?php

?><div class="video_thumbs_container"><?php
foreach($videos['request_result'] as $video_data){
    echo show_video_thumb($video_data);
}
?></div><?php

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');