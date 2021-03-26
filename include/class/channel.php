<?php


class Channel{

    public function load($id){
        return $this->do_load_channel('id',$id);
    }
    public function load_by_hash($hash){
        return $this->do_load_channel('hash',$hash);
    }
    private function do_load_channel($key,$id){
        global $db;

        if($key=='id'){
            $channel_data_array = $db->load('channels',$id);
        }else{
            $channel_data_array = $db->load_by('channels',$key,$id);
        }
        $channel_data_array['admin_ids']=$this->load_channel_admins($channel_data_array['id']);

        return $channel_data_array;
    }

    private function load_channel_admins($channel_id){
        global $db;

        $channel_admins=array();
        $query="SELECT user_id FROM channel_admins WHERE channel_id = ?";
        $params=array($channel_id);
        $channel_admins_request = $db->query($query,$params);
        foreach($channel_admins_request['request_result'] as $channel_admin_data){
            $channel_admins[]=$channel_admin_data['user_id'];
        }

        return $channel_admins;
    }

    public function update($hash,$name){
        global $db, $message;
        $return_data=array('success'=>false,'errors'=>array(),'channel_id'=>null);

        //prep channel data array
        $channel_data_array = array();
        if($hash) {
            $channel_data_array_orig = $this->load_by_hash($hash);
            $channel_data_array['id']=$channel_data_array_orig['id'];
        }

        //set values to update
        $channel_data_array['name']=$name;

        if($hash){
            //channel info update
            $channel_data_array['hash']=$hash;
            //var_dump('$channel_data_array',$channel_data_array);
            $update_results = $db->update('channels',$channel_data_array['id'],$channel_data_array);
            if($update_results!=1){
                $return_data['errors'][]='update_error';
                $message->add_message('error',__('error updating the database'));
            }else{
                $return_data['success']=true;
                $return_data['channel_id'] = $channel_data_array['id'];
                $message->add_message('success',__('successfully updated channel'));
            }
        }else{
            //creating new channel!
            $table_for_hash='channels';
            include(BP.'/include/procedure/create_unique_hash.php');
            $channel_data_array['hash']=$found_hash;
            $id = $db->insert('channels', $channel_data_array);
            if (!$id) {
                //error creating channel
                $return_data['errors'][] = 'insert_error';
                $message->add_message('error', __('database error'));
            } else {
                //create channel admin for creator!
                $channel_admin_data_array=array();
                $channel_admin_data_array['channel_id']=$id;
                $channel_admin_data_array['user_id']=$_SESSION['user_id'];
                $channel_admin_data_array['level']=1; //for now just level 1 whatever..
                $channel_admin_id = $db->insert('channel_admins', $channel_admin_data_array);
                if (!$channel_admin_id) {
                    $return_data['errors'][] = 'insert_error';
                    $message->add_message('error', __('database error'));
                }else{
                    $return_data['success'] = true;
                    $return_data['channel_id'] = $id;
                    $message->add_message('success', __('successfully created channel'));
                }
            }
        }

        return $return_data;
    }

    public function get_channels($page,$perpage,$options=array()){
        global $db;

        $params=array();
        $params[':user_id']=$_SESSION['user_id'];

        $order = 'ORDER BY channels.created DESC';
        $start_at=($page-1)*$perpage;

        $query='SELECT * FROM channels JOIN channel_admins ON channels.id = channel_admins.channel_id WHERE channel_admins.user_id = :user_id '.$order.' LIMIT '.$start_at.','.$perpage;;
        //var_dump($query,$params);
        $channels = $db->query($query,$params);

        $channels['this_page']=$page;
        $query='SELECT COUNT(*) as totalchannels FROM channels JOIN channel_admins ON channels.id = channel_admins.channel_id WHERE channel_admins.user_id = :user_id';
        $count_channels=$db->query($query,$params);
        //var_dump($count_channels);
        $count_channels=$count_channels['request_result'][0]['totalchannels'];
        $channels['total_channels']=$count_channels;
        $channels['total_pages']=ceil($count_channels/$perpage);

        $channels['base_url']='/channel/list';

        return $channels;
    }

    public function show_channels($channels){
        ?>
        <div class="channels_container">
            <?php
            foreach($channels['request_result'] as $channel_data){
                echo $this->show_channel($channel_data);
            }
            ?>
            <div class="pagination"><?php echo $this->make_pagination($channels['this_page'],$channels['total_pages'],$channels['base_url']) ?></div>
        </div>
        <script>
            function delete_channel(channel_id,channel_name){
                var confirm = window.confirm(__("are you sure you want to delete channel %1?",channel_name));
                if(confirm){
                    post(window.location.href, {delete_channel_id: channel_id});
                }
            }
        </script>
        <?php
    }
    function show_channel($channel_info){
        global $main_domain;

        $channelurl = 'https://' . $main_domain.'/channel/' . $channel_info['hash'];
        $channel_edit_url = 'https://' . $main_domain.'/channel/edit?hash=' . $channel_info['hash'];
        $channel_details='<div class="channel_link_container">
            <a href="'.$channelurl.'">'.htmlspecialchars($channel_info['name']).'</a>
            <span class="channel_actions">
               <a href="'.$channel_edit_url.'">'.__('edit').'</a>
               | <a onclick="delete_channel(\''.$channel_info['hash'].'\',\''.$channel_info['name'].'\')">'.__('delete').'</a>
            </span>
        </div>';

        $html='<div class="channel">
            '.$channel_details.'
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

    public function delete($hash){
        global $db, $message, $video;
        $return_data=array('success'=>false,'errors'=>array(),'channel_id'=>null);

        //get channel data
        $channel_data_array = $this->load_by_hash($hash);
        //first check if user is admin on this channel
        include(BP.'/include/function/can_admin.php');
        $can_admin_channel=can_admin($channel_data_array['admin_ids'],1);
        if(!$can_admin_channel){
            $return_data['errors'][]='unauthorized';
            $message->add_message('error',__('unauthorized'));
        }else{
            //then check how many videos on channel
            $videos = $video->get_channel_videos($channel_data_array);
            if($videos['total_videos']>=1){
                $return_data['errors'][]='channel_not_empty';
                $message->add_message('error',__('you cannot delete a channel that is not empty'));
            }else{
                //then delete all admins on the channel
                $query='DELETE FROM channel_admins WHERE channel_id = :channel_id';
                $params=array(':channel_id'=>$channel_data_array['id']);
                $db->query($query,$params);
                //and finally delete the chann{}else{}
                $query='DELETE FROM channels WHERE id = :channel_id';
                $params=array(':channel_id'=>$channel_data_array['id']);
                $db->query($query,$params);
            }
        }

        return $return_data;
    }

    public function get_channel_select($name,$class,$selected=':auto'){
        $select_html='<select name="'.$name.'" class="'.$class.'">';

        $channels = $this->get_channels(1,1000);
        if($channels['total_channels']<1){
            return false;
        }
        if($selected==':auto'){
            //TODO: find last channel that user uploaded to, set as $selected
        }
        foreach($channels['request_result'] as $channel_data){
            $selected_html='';
            if($channel_data['hash']==$selected){
                $selected_html=' selected="selected"';
            }
            $select_html.='<option value="'.$channel_data['hash'].'"'.$selected_html.'>'.$channel_data['name'].'</option>';
        }

        $select_html.='</select>';
        return $select_html;
    }
}