<?php

//===========get language
if(isset($_GET['language']))
{$language=$_GET['language'];}
else if(isset($_COOKIE['language']))
{$language=$_COOKIE['language'];}
else
{
    if(1==2 /*need to reimplement geoip, maybe..*/)
    {$language='fr';}
    else
    {$language='en';}
}
setcookie("language", $language, time()+(4*365*24*3600),'/','.'.$main_domain);
if(!@include(BP.'/language/language_'.urlencode($language).'.php'))
{
    $language='en';
    include(BP.'/language/language_'.urlencode($language).'.php');
    setcookie("language", $language, time()+(4*365*24*3600),'/','.'.$main_domain);
}
//===========get language

?>