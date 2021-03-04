<?php
$dat_system_keep_last_x_baks=60;

require(dirname(dirname(dirname(__FILE__))).'/settings.php');
if($_SERVER['REMOTE_ADDR']!=$admin_ip && $_SERVER['REMOTE_ADDR']!='')
{die('unauthorized');}



$allcurrentbaks=glob(DAT_SYSTEM_HOME.'/dat_system_baks/*');
print_r($allcurrentbaks);
$reverse_array=array_reverse($allcurrentbaks);
$countem=0;
foreach($reverse_array as $key => $value)
{
    $countem++;
    if($countem>$dat_system_keep_last_x_baks)
    {
        $delit='rm -R '.$value;
        echo $delit.'
';
        exec($delit);
    }
}

?>