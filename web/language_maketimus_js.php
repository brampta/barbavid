<?php
if(!@include('language_'.urlencode($_GET['lang']).'.php'))
{die('incorrect language');}

header('Content-type: text/javascript; charset=UTF-8');
$time_to_live = 2*365*24*3600;
header(sprintf('Expires: %s GMT', gmdate("D, d M Y H:i:s", time() + $time_to_live)));
header('Cache-Control: public, max-age=' . $time_to_live);


echo 'var maketimux_text=new Array("'.$text[300].'","'.$text[301].'","'.$text[302].'","'.$text[303].'","'.$text[304].'","'.$text[305].'","'.$text[306].'","'.$text[307].'","'.$text[308].'","'.$text[309].'","'.$text[310].'","'.$text[311].'"); ';


?>