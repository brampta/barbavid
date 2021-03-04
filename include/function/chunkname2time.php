<?php

function chunkname2time($chunkname)
{
    $extensionless_chunkname=explode('.',$chunkname);
    $split_chunkname=explode('_',$extensionless_chunkname[0]);
    $start_min=abs($split_chunkname[0]);
    $start_hours=floor($start_min/60);
    if($start_hours<10)
    {$start_hours='0'.$start_hours;}
    $start_mins=$start_min%60;
    if($start_mins<10)
    {$start_mins='0'.$start_mins;}
    $end_min=abs($split_chunkname[1]);
    $end_hours=floor($end_min/60);
    if($end_hours<10)
    {$end_hours='0'.$end_hours;}
    $end_mins=$end_min%60;
    if($end_mins<10)
    {$end_mins='0'.$end_mins;}

    $rezu=$start_hours.':'.$start_mins.':00 - '.$end_hours.':'.$end_mins.':00';
    return $rezu;
}

?>