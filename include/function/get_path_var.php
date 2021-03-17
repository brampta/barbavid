<?php

function get_path_var($varname,$path,$default){
    $exploded_path=explode('/',$path);
    $next_one_would_be_it=false;
    foreach($exploded_path as $path_chunk){
        if($next_one_would_be_it==true){
            return $path_chunk;
        }
        if($path_chunk==$varname){
            $next_one_would_be_it=true;
        }
    }
    return $default;
}