<?php
if(!@include('language_'.urlencode($_GET['lang']).'.php'))
{die('incorrect language');}

header('Content-type: text/javascript; charset=UTF-8');
$time_to_live = 2*365*24*3600;
header(sprintf('Expires: %s GMT', gmdate("D, d M Y H:i:s", time() + $time_to_live)));
header('Cache-Control: public, max-age=' . $time_to_live);


echo 'var closeadtext = "'.htmlspecialchars($text[111]).'";
';
echo 'var counttxt=new Array();
';
foreach($text[112] as $key => $value)
{echo 'counttxt['.$key.']="'.htmlspecialchars($value).'";
';}


?>