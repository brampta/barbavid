<?php

function can_admin($user_id,$mod_level){
    global $upload_info;
    if(isset($_SESSION['mod_level']) && $_SESSION['mod_level']>=$mod_level){
        return true;
    }
    if(isset($_SESSION['user_id']) && $_SESSION['user_id']==$user_id){
        return true;
    }

    return false;
}