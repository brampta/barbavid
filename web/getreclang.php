<?php
ini_set('session.cookie_domain', '.barbavid.com');


//===========get language
if(isset($_GET['language']))
{$language=$_GET['language'];}
else if(isset($_COOKIE['language']))
{$language=$_COOKIE['language'];}
else
{
    //get cuntry
    $visitor_ip=$_SERVER['REMOTE_ADDR'];
    if(isset($_COOKIE['cuntry']) && $_COOKIE['cuntry']!='')
    {$cuntry=$_COOKIE['cuntry'];}
    else
    {
        include('db_conn.php');
        $exploded_ip=explode('.',$visitor_ip);
        $thisipnum=16777216*$exploded_ip[0] + 65536*$exploded_ip[1] + 256*$exploded_ip[2] + $exploded_ip[3];
        $result2 = mysql_query("SELECT country_code FROM ip2country WHERE ipnum_start <= '$thisipnum' && ipnum_end >= '$thisipnum' LIMIT 0, 1");
        while($row2 = mysql_fetch_array($result2))
        {
            $cuntry=$row2['country_code'];
            setcookie("cuntry", $cuntry, time()+(14*24*3600));
        }
        mysql_close($conn);
    }
    if($cuntry=='CA' || $cuntry=='FR')
    {$language='fr';}
    else
    {$language='en';}
}
setcookie("language", $language, time()+(4*365*24*3600),'/','.barbavid.com');
//===========get language



echo 'var1='.$language;
?>