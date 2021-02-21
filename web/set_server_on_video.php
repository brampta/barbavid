<?php
include('allowed_ips.php');
//echo $_SERVER['REMOTE_ADDR'].'<br />';
if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}

if($_GET['server']!='' && $_GET['video']!='')
{
    include('dat_system_functions.php');
    $keyvalue_toset_array['server']=$_GET['server'];
    if($_GET['chunks']!='')
    {$keyvalue_toset_array['chunks']=unserialize($_GET['chunks']);}
    if($_GET['reso']!='')
    {$keyvalue_toset_array['reso']=$_GET['reso'];}
    if(isset($_GET['time']) && $_GET['time']!='')
    {$keyvalue_toset_array['time']=$_GET['time'];}

    set_elements('videos_index.dat',$_GET['video'],$keyvalue_toset_array);
    echo 'ok';
}

?>