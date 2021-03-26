<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(isset($_POST['delete_channel_id'])){
    $channel->delete($_POST['delete_channel_id']);
}

include(BP.'/include/function/get_path_var.php');
$page=get_path_var('page',$_SERVER['REQUEST_URI'],1);
$channels = $channel->get_channels($page,20);
//var_dump($channels);

$page_title = __('channels list');
include(BP.'/include/head_start.php');
?>
<script type="text/javascript" src="/js/post.js"></script>
<?php
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');
$message->show_messages();

if(!$_SESSION['user_id']){
    ?>
    <p class="message error"><?php echo __('unauthorized') ?></p>
    <?php
}else{
    ?>
    <h1 class="page_title"><?php echo $page_title ?></h1>
    <div class="actions"><a href="/channel/edit"><?php echo __('create new channel') ?></a></div>
    <?php
    $channel->show_channels($channels);
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');