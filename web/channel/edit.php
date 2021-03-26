<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');
include(BP.'/include/function/forms.php');

//get channel data (new or existing...)
$can_admin_channel=false;
$channel_hash=false;
if(isset($_GET['hash'])){
    $channel_hash=$_GET['hash'];
}else if(isset($_POST['hash'])){
    $channel_hash=$_POST['hash'];
}
if($channel_hash){
    $channel_data_array = $channel->load_by_hash($channel_hash);
    //var_dump($channel_data_array);
    include(BP.'/include/function/can_admin.php');
    $can_admin_channel=can_admin($channel_data_array['admin_ids'],1);
    //.;^var_dump('$can_admin_channel',$can_admin_channel);
}else{
    if(isset($_SESSION['user_id'])){
        $can_admin_channel=true;
    }
}


if($can_admin_channel && isset($_POST['name'])){
    $channel_update = $channel->update($channel_hash,$_POST['name']);
    if($channel_update['success']){
        $channel_data_array = $channel->load($channel_update['channel_id']);
    }
}


if($channel_hash){
    $page_title = __('edit channel %1', $channel_data_array['name']);
}else{
    $page_title = __('create channel');
}
include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');


if(!$can_admin_channel){
    ?>
    <p class="message error"><?php echo __('unauthorized') ?></p>
    <?php
}else{

    ?>
    <div class="back_page"><a href="/channel/list"><?php echo __('return to channels list') ?></a></div>
    <?php

    $message->show_messages();

    ?>
    <div class="user_form">
        <h1><?php echo $page_title ?></h1>
        <form method="post">
            <div class="user_form_input"><label for="name"><?php echo __('name:') ?></label><input id="name" name="name" type="text" value="<?php echo reget_post('name',isset($channel_data_array['name'])?$channel_data_array['name']:'') ?>"></div>
            <?php if($channel_hash){echo '<input type="hidden" name="hash" value="'.$channel_hash.'">';} ?>
            <div class="user_form_submit"><input type="submit" value="<?php echo __('update') ?>"></div>
        </form>
        <div class="user_form_link"><a href="change_password"><?php echo __('change password') ?></a></div>
    </div>
    <?php
}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');