<?php

function reget_post($postname,$default_value=''){
    if(isset($_POST[$postname])){
        $value = $_POST[$postname];
    }else{
        $value = $default_value;
    }
    return htmlspecialchars($value);
}

function reget_checkbox($postname){
    if(isset($_POST[$postname])){
        $value = 'checked="checked"';
    }else{
        $value = '';
    }
    return $value;
}