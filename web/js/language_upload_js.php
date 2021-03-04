<?php
if(!@include('language_'.urlencode($_GET['lang']).'.php'))
{die('incorrect language');}

header('Content-type: text/javascript; charset=UTF-8');
$time_to_live = 2*365*24*3600;
header(sprintf('Expires: %s GMT', gmdate("D, d M Y H:i:s", time() + $time_to_live)));
header('Cache-Control: public, max-age=' . $time_to_live);

include('settings.php');
echo 'var ununloadtext = "'.htmlspecialchars($text[13]).'"; ';
echo 'var uplogtx=new Array("'.htmlspecialchars($text[201]).'","'.htmlspecialchars($text[202]).'","'.htmlspecialchars(explain_expiration($upload_exire_after_x_days_of_inactiv)).'"); ';


?>