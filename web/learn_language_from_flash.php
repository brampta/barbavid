<?php
if(isset($_GET['lang']))
{
    ini_set('session.cookie_domain', '.barbavid.com');
    setcookie("language", $_GET['lang'], time()+(4*365*24*3600),'/','.barbavid.com');
}
echo 'var1='.$_GET['lang'];
?>