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

    public function channel_get_around_videos($video_id,$channel_data_array,$quantity){
        //loop requests to get_channel_videos() until finding the required video
        //return page were video was found plus pages before and after, combined in 1 page
        $page = 1;
        $perpage = ceil($quantity/3);
        $count_turns = 0;
        $max_turns = 1000;
        $matching_page_was_first_page = false;
        
        $prev_page = null;
        $page_with_vid = null;
        $page_after = null;
        
        $videos = null;
        while(
                (
                    !$prev_page
                    || !$page_with_vid
                    || !$page_after
                )
                && $count_turns<$max_turns
                ){//as long as you dont have your 3 pages
            $count_turns++;
            //echo '==============page '.$page.'<br>';
            
            if($videos && !$page_with_vid){
                $prev_page = $videos;
            }
            $videos = $this->get_channel_videos($channel_data_array,$page,$perpage,false);
            //echo '<pre>'.print_r($videos,true).'</pre>';
            if($page_with_vid){
                $page_after = $videos;
            }else{
                foreach($videos['request_result'] as $video_data){
                    if($video_data['id']==$video_id || $matching_page_was_first_page){
                        //echo '==============page has the vid! (or is page after if page with vid was first page)<br>';
                        if($page == 1){
                            //echo '==============page with vid was first page! need to delay pages<br>';
                            $matching_page_was_first_page = true;
                        }else{
                            $page_with_vid = $videos;
                        }
                        break;
                    }
                }
            }
            
            if(count($videos['request_result'])<$perpage){
                break;
            }
            $page++;
        }
        
        $combined_videos = array();
        
        // in case there were not enough results
        if(!$page_with_vid){
            $page_with_vid = array('request_result' => array());
        }
        if(!$page_after){
            $page_after = array('request_result' => array());
        }
        
        $combined_videos['request_result'] = array_merge(
            $prev_page['request_result'],
            $page_with_vid['request_result'],
            $page_after['request_result']
        );
        
        //also find page after and page before... for next and prev buttons
        $vid_before = null;
        $vid_after = null;
        $found_vid = false;
        foreach($combined_videos['request_result'] as $video_data){
            //echo $video_data['hash'].'<br>';
            
            if($found_vid){
                $vid_after = $video_data;
                break;
            }
            if($video_data['id']==$video_id){
                $found_vid = true;
                //echo 'is vid<br>';
            }
            if(!$found_vid){
                $vid_before = $video_data;
            }
        }
        if($found_vid){
            if($vid_before){
                //echo 'vid before '.$vid_before['hash'].'<br>';
                $combined_videos['vid_before'] = $vid_before;
            }
            if($vid_after){
                //echo 'vid after '.$vid_after['hash'].'<br>';
                $combined_videos['vid_after'] = $vid_after;
            }
        }
        
        return $combined_videos;
    }
    
    public function get_channel_videos($channel_data_array,$page=1,$perpage=50,$include_suspended=false){
        $options=array(
            'channel'=>$channel_data_array['id'],//well ok, later I needed the whole array, maybe remove those later and just use the array
            'channel_hash'=>$channel_data_array['hash'],//just use the array..
            'channel_data_array'=>$channel_data_array,
            'base_url'=>'/channel/'.$channel_data_array['hash'],
            'include_suspended'=>$include_suspended,
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
        $videos = $db->query($query,$params);

        $videos['this_page']=$page;
        $query='SELECT COUNT(*) as totalvideos FROM videos '.$where;
        $count_videos=$db->query($query,$params);
        $count_videos=$count_videos['request_result'][0]['totalvideos'];
        $videos['total_videos']=$count_videos;
        $videos['total_pages']=ceil($count_videos/$perpage);

        //$videos['base_url']=$options['base_url'];
        $videos['options']=$options;

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
    
    public function get_channel_embed_redirect_url($video_hash,$channel_hash,$autoplay){
        $autoplay_query = '';
        if($autoplay){
            $autoplay_query = '&autoplay=1';
        }
        return '/video/'.$video_hash.'?embed=1&channel='.$channel_hash.$autoplay_query;
    }
}