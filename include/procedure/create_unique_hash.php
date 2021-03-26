<?php


/* now this will be done on db instead..
//create unique hash to represent this upload
//assumes that dat_system_functions.php is already included
$gotafreeone=0;
$maxturns=5000;
$counturns=0;
while($gotafreeone==0 && $counturns<$maxturns)
{
    $counturns++;
    $arandomhash=base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);

    $datfile_num=find_place_according_to_index($arandomhash,'uploads_index.dat');
    if(!get_element_info($arandomhash,$datfile_num))
    {$gotafreeone=1;}
}
*/

//that bit is for backwards support..
$table_for_hash=isset($table_for_hash)?$table_for_hash:'videos';

//now the procedure:
$found_hash='';
$countturns=0; $maxturns=100;
while($found_hash=='' && $countturns<$maxturns){
    $countturns++;
    $arandomhash=base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);
    $upload_info_for_hashgive = $db->load_by($table_for_hash,'hash',$arandomhash);
    if(!$upload_info_for_hashgive){
        $found_hash = $arandomhash;
    }
}