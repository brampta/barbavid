<?php
$videokey['k']='error';

//$_POST['video']="1800f39f21ab69a37637c8f56031c4a7";
//$_POST['chunk']="0000000000_0000000030.mp4";
//$_POST['yarbamonkst']='yunburangeoo';


//is referer bad?
$referer_isbad=0;
if(trim($_SERVER['HTTP_REFERER'])!='' && preg_match('/http:\/\/([^\/]*\.)?barbavid\.com\/(.)*/is',$_SERVER['HTTP_REFERER'])===false)
{
    $referer_isbad=1;
    file_put_contents('showbadrefs.txt',$referer_isbad.' '.$_SERVER['HTTP_REFERER'].'
',FILE_APPEND);
}


$static_key_isbad=0;
if($_POST['yarbamonkst']!='yunburangeoo')
{$static_key_isbad=1;}


if(isset($_POST['video']) && isset($_POST['chunk']) && $referer_isbad==0 && $static_key_isbad==0)
{
    include('dat_system_functions.php');
    $nowtime=time();

    $keyname=md5($_SERVER['REMOTE_ADDR'].'_'.$_POST['video'].'_'.$_POST['chunk']);

//check if already key for this IP/video/chunk
    $datfile_num=find_place_according_to_index($keyname,'videokeys_index.dat');
    $current_key_data=get_element_info($keyname,$datfile_num);

//if no key already, create key
    if($current_key_data===false)
    {
        //echo 'i create new key<br />';
        $arandomhash=base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);
        $videokey['k']=$arandomhash;
        $videokey['t']=$nowtime;
        $data=serialize($videokey);

        $datfile_num=find_place_according_to_index($keyname,'videokeys_index.dat');
        add_or_update_element($keyname,$data,$datfile_num,'videokeys_index.dat');
    }
//else if already key, update time
    else
    {
        //echo 'already have key<br />';
        $keyvalue_toset_array2['t']=$nowtime;
        set_elements('videokeys_index.dat',$keyname,$keyvalue_toset_array2);
        $videokey=unserialize($current_key_data);
        //print_r($videokey); echo '<br />';
    }
}

echo 'var1='.$videokey['k'].'&var2='.$referer_isbad.'&var3='.$static_key_isbad;

if(isset($_POST['return_chunknum']))
{echo '&var4='.$_POST['return_chunknum'];}
?>