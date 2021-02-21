<?php

proc_nice(19);

$nowtime = time();
echo '<html><body>';

function get_content_of_url($url) {
    //echo 'i curl url '.$url.'<br />';
    $ohyeah = curl_init();
    curl_setopt($ohyeah, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ohyeah, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ohyeah, CURLOPT_TIMEOUT, 30);
    curl_setopt($ohyeah, CURLOPT_URL, $url);
    curl_setopt($ohyeah, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");

    $dataz = curl_exec($ohyeah);
    //echo 'rezu: '.htmlspecialchars($dataz).'<br />';
    curl_close($ohyeah);
    return $dataz;
}

$remember_file = '/home/goblet/grabmu_temp/remember.log';



if (isset($_POST['mulink'])) {
    $formerrors = 0;
    if ($_POST['mulink'] == '') {
        $formerrors = 1;
        echo 'mulink is empty';
    }
    if ($_POST['title'] == '') {
        $formerrors = 1;
        echo 'title is empty';
    }

    if ($formerrors == 0) {
        //$mupagecode = get_content_of_url($_POST['mulink']);
        //$finddownloadlink=stripos($mupagecode, 'id="downloadlink"');
        //$finddownloadlink = stripos($mupagecode, 'download_regular_disabled');
        //$finddownloadlink = stripos($mupagecode, 'class="download_premium_but"');
//        if ($finddownloadlink) {
//            $hrefafter = stripos($mupagecode, 'href="', $finddownloadlink);
//            $startoflink = $hrefafter + 6;
//            $endoflink = stripos($mupagecode, '"', $startoflink);
//            $lenoflink = $endoflink - $startoflink;
//            $downloadlink = substr($mupagecode, $startoflink, $lenoflink);
        $downloadlink = $_POST['mulink'];
//        echo '$downloadlink: ' . $downloadlink . '<br />';
//        echo 'waiting 60 seconds...<br />';
//        sleep(60);
        echo 'download file<br />';
        unset($outputz);
        $dostring = 'cd home/goblet/grabmu_temp; nice -n 19 wget -O /home/goblet/grabmu_temp/' . $nowtime . ' ' . escapeshellarg($downloadlink) . ' 2>&1';
        echo '<b>' . htmlspecialchars($dostring) . '</b>';
        exec($dostring, $outputz);
        foreach ($outputz as $key => $value) {
            echo htmlspecialchars($value) . '<br />';
        }
        echo 'upload to barb<br />';
        unset($outputz);
        $dostring = 'nice -n 19 php /var/www/barbavid/uploadserver2/upload.php /home/goblet/grabmu_temp/' . $nowtime . ' ' . escapeshellarg($_POST['title']) . ' ' . escapeshellarg($_POST['description']) . ' 2>&1';
        echo '<b>' . htmlspecialchars($dostring) . '</b>';
        exec($dostring, $outputz);
        foreach ($outputz as $key => $value) {
            echo htmlspecialchars($value) . '<br />';

            $isitsucces = stripos($value, 'window.top.window.remembersuccess(');
            if ($isitsucces !== false) {
                echo 'is success string, will try to extract title and upload_hash<br />';
                $startofparams = $isitsucces + strlen('window.top.window.remembersuccess(');
                $endofparams = stripos($value, ');', $startofparams);
                $params_len = $endofparams - $startofparams;
                $elparams = substr($value, $startofparams, $params_len);
                echo '$elparams: ' . htmlspecialchars($elparams) . '<br />';
                $exploded_params = explode('"', $elparams);
                $eltitle = $exploded_params[1];
                echo '$eltitle: ' . htmlspecialchars($eltitle) . '<br />';
                $elhash = $exploded_params[3];
                echo '$elhash: ' . htmlspecialchars($elhash) . '<br />';

                file_put_contents($remember_file, base64_encode($_POST['title']) . ' ' . $elhash . ' ' . $_POST['mulink'] . '
', FILE_APPEND);

                echo 'so file upload was successful, now lets delete that file to cleanup after ourselves<br />';
                unlink('/home/goblet/grabmu_temp/' . $nowtime);
            }
        }
//        } else {
//            echo 'id="downloadlink" not found<br />';
//            echo '<textarea style="width:100%;height:400px;">' . htmlspecialchars($mupagecode) . '</textarea><br />';
//        }
    }
}







echo '<a name="formzzz"></a><form method="post" action="#formzzz">
video URL:<br />
<input type="text" name="mulink" style="width:600px;" value="' . htmlspecialchars($_POST['mulink']) . '" /><br />
Title:<br />
<input type="text" name="title" style="width:600px;" value="' . htmlspecialchars($_POST['title']) . '" /><br />
Description:<br />
<textarea name="description" style="width:600px;height:120px;">' . htmlspecialchars($_POST['description']) . '</textarea><br />
<input type="submit" value="grab MU" />
</form>';


$success_array = file($remember_file);
$reversed_success_array = array_reverse($success_array);
echo '<table>';
foreach ($reversed_success_array as $key => $value) {
    $exploded_value = explode(' ', $value);
    echo '<tr><td>' . htmlspecialchars(base64_decode($exploded_value[0])) . '</td><td><a href="http://barbavid.com/video/' . $exploded_value[1] . '" target="_blank">' . $exploded_value[1] . '</a></td><td><a href="' . $exploded_value[2] . '" target="_blank">' . $exploded_value[2] . '</a></td></tr>';
}
echo '</table>';

echo '</body></html>';
?>