<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

$rezu='false';
if($_GET['hash']!='' && $_GET['index_file']!='')
{
    include(BP.'/include/dat_system/dat_system_functions.php');
    $datfile_num=find_place_according_to_index($_GET['hash'],$_GET['index_file']);
    if(get_element_info($_GET['hash'],$datfile_num)!==false)
    {$rezu='true';}
}
echo $rezu;

$db->disconnect();