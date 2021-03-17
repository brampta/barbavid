<?php

function datetime_to_timestamp($datetime){
    $d = strtotime($datetime);
    return $d;
}