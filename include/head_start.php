<?php


$db->disconnect();


echo '<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <script type="text/javascript" src="/js/translations.js?lang='.$language.'"></script>
    <script type="text/javascript" src="/js/nav.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/style.css?v='.filemtime(BP.'/web/css/style.css').'" />';

if(!isset($page_title) || $page_title==''){ 
    $page_title=$site_name;
}
echo '<title>'.htmlspecialchars($page_title).'</title>';