<?php


class Video{

    public function get_home_videos($page,$perpage){
        $options=array(
                'base_url'=>'',
        );
        return $this->get_videos($page,$perpage,$options);
    }

    public function get_user_videos($user_data_array,$page,$perpage,$include_suspended=false){
        $options=array(
            'user'=>$user_data_array['id'],
            'user_hash'=>$user_data_array['hash'],
            'base_url'=>'/user/'.$user_data_array['hash'],
            'include_suspended'=>$include_suspended,
        );
        return $this->get_videos($page,$perpage,$options);
    }

    public function get_channel_videos($channel_data_array,$page=1,$perpage=50,$include_suspended=false,$channel_embed_video=null){
        $options=array(
            'channel'=>$channel_data_array['id'],//well ok, later I needed the whole array, maybe remove those later and just use the array
            'channel_hash'=>$channel_data_array['hash'],//just use the array..
            'channel_data_array'=>$channel_data_array,
            'base_url'=>'/channel/'.$channel_data_array['hash'],
            'include_suspended'=>$include_suspended,
            'channel_embed_video'=>$channel_embed_video,
        );
        return $this->get_videos($page,$perpage,$options);
    }


    public function get_videos($page,$perpage,$options){
        global $db;
        $order = 'ORDER BY created DESC'; //thats the default order

        $params=array();
        $user_id_clause='';
        if(isset($options['user'])){
            $user_id_clause=' && user_id = :user_id ';
            $params[':user_id']=$options['user'];
        }
        $channel_id_clause='';
        $channel_embed_video_clause='';
        if(isset($options['channel'])){
            //coming soon... will need to first join channel asso table then add channel_id clause and param
            $channel_id_clause=' && channel_id = :channel_id ';
            $params[':channel_id']=$options['channel'];
            if(isset($options['channel_embed_video'])){
                
                //get next video in channel!
                $exploded_channel_embed_video = explode(':',$options['channel_embed_video']);
                if($exploded_channel_embed_video[0]=='after'){
                    //var_dump('is after embed video '.$exploded_channel_embed_video[1]);
                    //search for video after
                    $looking_for_hash = $exploded_channel_embed_video[1];
                    $per_page = 2; //low for testing,increase a lot later..
                    $maxturns = 1000;
                    $countturns = 0;
                    $found_video = false;
                    $next_video = null;
                    while($countturns < $maxturns){
                        $countturns++;
                        
                        $videos = $this->get_channel_videos($options['channel_data_array'],$countturns,$per_page,false);
                        //echo '<pre>'.print_r($videos,true).'</pre>';
                        $count_turn_results = 0;
                        foreach($videos['request_result'] as $video_data){
                            //echo $video_data['hash'].'<br>';
                            $count_turn_results++;
                            if($found_video){
                                $next_video = $video_data['hash'];
                                //echo 'next video<br>';
                                break 2;
                            }
                            
                            if($video_data['hash']==$looking_for_hash){
                                $found_video = true;
                                //echo 'matching video<br>';
                            }
                        }
                        if($count_turn_results == 0){
                            break;
                        }
                    }
                    if($next_video){
                        $options['channel_embed_video'] = 'hash:'.$next_video;
                    }else{
                        $options['channel_embed_video'] = 'last';
                    }
                    
                }
                
                //get either latest or specific video from channel
                //echo $options['channel_embed_video'].'<br>';
                $exploded_channel_embed_video = explode(':',$options['channel_embed_video']);
                if($exploded_channel_embed_video[0] == 'hash'){
                    $channel_embed_video_clause=' && hash = :hash ';
                    $params[':hash']=$exploded_channel_embed_video[1] ;
                }else /*if($exploded_channel_embed_video[0] == 'last')*/{ //no if, so show latest is default
                    $order = 'ORDER BY created DESC'; //just make sure last one's on top
                }
            }
        }

        $suspended_clause=' && ready = 1 && suspend = 0 ';
        if(isset($options['include_suspended']) && $options['include_suspended']==true){
            $suspended_clause='';
        }

        $where = 'WHERE 1=1'
                .$user_id_clause
                .$channel_id_clause
                .$channel_embed_video_clause
                .$suspended_clause;
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
        $videos['total_videos']=$count_videos;
        $videos['total_pages']=ceil($count_videos/$perpage);

        //$videos['base_url']=$options['base_url'];
        $videos['options']=$options;

        //var_dump($videos);

        return $videos;
    }

    public function show_videos($videos){
        ?>
        <div class="video_thumbs_container">
            <div class="video_thumbs_container_inner">
                <?php
                foreach($videos['request_result'] as $video_data){
                    echo $this->show_video_thumb($video_data);
                }
                ?>
                <div class="pagination"><?php echo $this->make_pagination($videos['this_page'],$videos['total_pages'],/*$videos['base_url']*/$videos['options']['base_url']) ?></div>
            </div>
        </div>
        <?php
    }

    function show_video_thumb($upload_info){
        global $main_domain;

        $datfile_num = find_place_according_to_index($upload_info['file_md5'], 'videos_index.dat');
        $video_info = get_element_info($upload_info['file_md5'], $datfile_num);

        $videourl = 'https://' . $main_domain.'/video/' . $upload_info['hash'];

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
            //var_dump($video_info);

            $ready='';
            $thumb_img='';
            if($upload_info['ready']==1){
                $thumburl = 'https://' . $video_info['server'] . '.'.$main_domain.'/thumb?video=' . $upload_info['file_md5'] . '&chunk=' . $video_info['chunks'][1];
                $thumb_img='<div class="video_thumb_container"><a href="'.$videourl.'"><img class="video_thumb_img" src="'.$thumburl.'"></a></div>';
            }else{
                if(isset($video_info['server']) && substr($video_info['server'], 0, 6) == 'upload'){
                    $reason=__('still encoding');
                }else if(isset($video_info['server']) && substr($video_info['server'], 0, 15) == 'failedencoding_'){
                    $reason=__('encoding failed');
                }else{
                    $reason=__('unknown error');
                }
                $ready='<div class="video_suspend_container">'.$reason.'</a></div>';
            }

            //print_r($video_info);

            $video_details='
            <div class="video_title_container"><a href="'.$videourl.'">'.htmlspecialchars($upload_info['title']).'</a></div>';
        }



        $html='<div class="video_thumb">
            '.$thumb_img.$video_details.$suspended.$video_not_found.$ready.'
        </div>';

        return $html;
    }

    private function make_pagination($this_page,$total_pages,$base_url){
        if($total_pages==1){
            return '';
        }
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
    
    public function channel_embed_redirect_url($videos,$autoplay){
        $video_data = $videos['request_result'][0];
        $video_hash = $video_data['hash'];
        $channel_hash = $videos['options']['channel_hash'];
        $autoplay_query = '';
        if($autoplay){
            $autoplay_query = '&autoplay=1';
        }
        return '/video/'.$video_hash.'?embed=1&channel='.$channel_hash.$autoplay_query;
       
    }
}