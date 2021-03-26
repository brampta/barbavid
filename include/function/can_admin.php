<?php

function can_admin($user_id,$mod_level){
    if(!is_array($user_id)){
        $user_id=array($user_id);
    }
    if(isset($_SESSION['mod_level']) && $_SESSION['mod_level']>=$mod_level){
        return true;
    }
    if(isset($_SESSION['user_id']) && in_array($_SESSION['user_id'],$user_id)){
        return true;
    }

    return false;
}