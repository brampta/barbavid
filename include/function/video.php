<?php

function show_video_thumb($upload_info){
    global $main_domain;

    $datfile_num = find_place_according_to_index($upload_info['file_md5'], 'videos_index.dat');
    $video_info = get_element_info($upload_info['file_md5'], $datfile_num);
    if ($video_info === false) {
        return 'error, video not found';
    }
    $video_info = unserialize($video_info);
    //print_r($video_info);

    $videourl = 'https://' . $main_domain.'/video/' . $upload_info['hash'];
    $thumburl = 'https://' . $video_info['server'] . '.'.$main_domain.'/thumb?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][1];
    $html='<div class="video_thumb">
    <div class="video_thumb_container"><a href="'.$videourl.'"><img class="video_thumb" src="'.$thumburl.'"></a></div>
    <div class="video_title_container"><a href="'.$videourl.'">'.htmlspecialchars($upload_info['title']).'</a></div>
</div>';

    return $html;
}