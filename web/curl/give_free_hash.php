<?php
/*
include('dat_system_functions.php');
include('create_unique_hash.php');
echo $arandomhash;
*/
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

/* that was the right way but we will centralize the procedure in the create_unique_hash.php include file
$found_hash='';
$countturns=0; $maxturns=4;
while($found_hash=='' && $countturns<$maxturns){
    $countturns++;
    $arandomhash=base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);
    $upload_info = $db->load_by('videos','hash',$arandomhash);
    if(!$upload_info){
        $found_hash = $arandomhash;
    }
}
*/

include(BP.'/include/procedure/create_unique_hash.php');
echo $found_hash;

$db->disconnect();
