<?

echo '<html>
<head>
<title>Barbavid</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>';



$cookiefile = '/var/www/nab/curl_cookies/barbafun.txt';
if (isset($_GET['clearcook'])) {
    unlink($cookiefile);
}

function get_http_header($url, $post_data) {
    global $cookiefile;
    $uh = curl_init();

    curl_setopt($uh, CURLOPT_COOKIEFILE, $cookiefile);
    curl_setopt($uh, CURLOPT_COOKIEJAR, $cookiefile);



    curl_setopt($uh, CURLOPT_URL, $url);
    curl_setopt($uh, CURLOPT_HEADER, 1);
    //curl_setopt($uh, CURLOPT_NOBODY, TRUE);
    curl_setopt($uh, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($uh, CURLOPT_FOLLOWLOCATION, 0);
    //curl_setopt($uh, CURLOPT_MAXREDIRS, 2);
    curl_setopt($uh, CURLOPT_COOKIEFILE, "cookie/cookie.txt");
    curl_setopt($uh, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");

    if ($post_data != '') {

        echo('$post_data ' . $post_data . '<br />');
        curl_setopt($uh, CURLOPT_POST, TRUE);
        curl_setopt($uh, CURLOPT_POSTFIELDS, $post_data);
    }

    $res = curl_exec($uh);
    curl_close($uh);
    return $res;
}

$header = get_http_header($_GET['url'], $_GET['pd']);









echo '<center><br /><form method="get">see the code of a webpage:<br />url: <input type="text" name="url" style="width:400px;" value="' . htmlentities($_GET['url']) . '" /><br />
    post data:<input type="text" name="pd" value="' . htmlentities($_GET['pd']) . '" style="width:400px;" /><br />
    <input type="checkbox" name="clearcook" />clear cookies<br />
    <input type="submit" value="see header"  /></form></center><br />';




if (isset($_GET['url'])) {
    echo '<h4>header:</h4>';
    echo htmlentities($header);
}



echo '</body></html>';
?>
