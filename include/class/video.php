<?php


class Video{

    public function get_home_videos($page,$perpage){
        $options=array('base_url'=>'');
        return $this->get_videos($page,$perpage,$options);
    }

    public function get_user_videos($user_data_array,$page,$perpage,$include_suspended=false){
        $options=array(
            'user'=>$user_data_array['id'],
            'base_url'=>'/user/'.$user_data_array['hash'],
            'include_suspended'=>$include_suspended
        );
        return $this->get_videos($page,$perpage,$options);
    }

    public function get_channel_videos($channel_data_array,$page,$perpage){
        $options=array('channel'=>$channel_data_array['id'],'base_url'=>'/channel/'.$channel_data_array['hash']);
        return $this->get_videos($page,$perpage,$options);
    }


    public function get_videos($page,$perpage,$options){
        global $db;

        $params=array();
        $user_id_clause='';
        if(isset($options['user'])){
            $user_id_clause=' && user_id = :user_id ';
            $params[':user_id']=$options['user'];
        }
        if(isset($options['channel'])){
            //coming soon... will need to first join channel asso table then add channel_id clause and param
        }

        $suspended_clause=' && suspend = 0 ';
        if(isset($options['include_suspended']) && $options['include_suspended']==true){
            $suspended_clause='';
        }

        $where = 'WHERE 1=1'.$user_id_clause.$suspended_clause;
        $order = 'ORDER BY created DESC';

        $start_at=($page-1)*$perpage;
        $query='SELECT * FROM videos '.$where.' '.$order.' LIMIT '.$start_at.','.$perpage;
        //var_dump($query);
        $videos = $db->query($query,$params);

        $videos['this_page']=$page;
        $query='SELECT COUNT(*) as totalvideos FROM videos '.$where;
        //var_dump($query);
        $count_videos=$db->query($query,$params);
        $count_videos=$count_videos['request_result'][0]['totalvideos'];
        $videos['total_pages']=ceil($count_videos/$perpage);

        $videos['base_url']=$options['base_url'];

        //var_dump($videos);

        return $videos;
    }

    public function show_videos($videos){
        ?>
        <div class="video_thumbs_container">
            <?php
            foreach($videos['request_result'] as $video_data){
                echo $this->show_video_thumb($video_data);
            }
            ?>
            <div class="pagination"><?php echo $this->make_pagination($videos['this_page'],$videos['total_pages'],$videos['base_url']) ?></div>
        </div>
        <?php
    }

    function show_video_thumb($upload_info){
        global $main_domain;

        $datfile_num = find_place_according_to_index($upload_info['file_md5'], 'videos_index.dat');
        $video_info = get_element_info($upload_info['file_md5'], $datfile_num);

        $suspended='';
        if($upload_info['suspend']!=0){
            $suspended='<div class="video_suspend_container">'.__('suspended').'</a></div>';
        }

        $video_not_found='';
        $video_details='';
        if ($video_info === false) {
            $video_not_found = '<div class="video_error_container">'.__('error, video not found').'</a></div>';
        }else{
            $video_info = unserialize($video_info);
            //print_r($video_info);
            $videourl = 'https://' . $main_domain.'/video/' . $upload_info['hash'];
            $thumburl = 'https://' . $video_info['server'] . '.'.$main_domain.'/thumb?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][1];

            $video_details='<div class="video_thumb_container"><a href="'.$videourl.'"><img class="video_thumb" src="'.$thumburl.'"></a></div>
            <div class="video_title_container"><a href="'.$videourl.'">'.htmlspecialchars($upload_info['title']).'</a></div>';
        }



        $html='<div class="video_thumb">
            '.$video_details.$suspended.$video_not_found.'
        </div>';

        return $html;
    }

    private function make_pagination($this_page,$total_pages,$base_url){
        $prev_page_link='';
        if($this_page>1){
            $prev_page_url=$base_url.'/page/'.($this_page-1);
            $prev_page_link='<a href="'.$prev_page_url.'">'.__('previous').'</a> | ';
        }
        $next_page_link='';
        if($this_page<$total_pages){
            $next_page_url=$base_url.'/page/'.($this_page+1);
            $next_page_link=' | <a href="'.$next_page_url.'">'.__('next').'</a>';
        }
        $pagination_string='<div>'.$prev_page_link.__('page %1 of %2',$this_page,$total_pages).$next_page_link.'</div>';
        return $pagination_string;
    }
}