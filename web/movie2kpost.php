<?php

function postshiz($title, $season, $episode, $link, $imdb) {
    $json['apikey'] = '85b3e69af2dcd36e51376dedb64ed29a';
    $json['action'] = 'addMovie';
    $json['title'] = $title;
    $json['season'] = $season; # add only if movie is a tv show!
    $json['episode'] = $episode; # add only if movie is a tv show!
    $json['xxx'] = 0; # set to 1 if movie is a adult movie
    $json['language'] = 3; # example: 3=english (http://www.movie2k.to/api_data.php?show=language)
    $json['hoster'] = 151; # example: 5=embed flash (http://www.movie2k.to/api_data.php?show=hoster)
    $json['part1'] = $link;
    $json['picturequality'] = 5; # 0=Unknown, 1=Cam, 2=TS, 3=TC, 4=Screener, 55=R5, 5=DVDRip/BDRip
    $json['soundquality'] = 4; # 0=Unknown, 1=Mic dubbed, 3=Line dubbed, 4=Dolby Digital/DVDRip
    $json['imdb'] = $imdb; #ID of the movie @imdb.com (not for xxx movies)

    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_URL, "http://www.movie2k.to/api_json.php");
	curl_setopt($ch, CURLOPT_URL, "http://www.movie4k.to/api_json.php");
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'json=' . rawurlencode(utf8_encode(json_encode($json))));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $res = curl_exec($ch);
    curl_close($ch);

    $json_res = json_decode($res);
    if ($json_res->{'error'})
        die($json_res->{'error'});
    echo $json_res->{'movieid'};
}

function postdc($season, $episode, $barbash) {
    postshiz('Hoarders', $season, $episode, 'http://barbavid.com/video/' . $barbash, '1497563');
}


postdc(2,1,'yv21y6q4abcw');
postdc(2,2,'fmwqw7lc3y0w');
postdc(2,3,'le5swu19m48w');
postdc(2,4,'asel2dki4npc');
postdc(2,5,'uj7f40s9vxfk');
postdc(2,6,'jb76e96gxgjk');
postdc(2,7,'ckatpjjs4qgw');
postdc(2,8,'z97v90hgpiww');
postdc(2,9,'ndsv57l61wcg');
postdc(2,10,'2p717ixg4hpc');
postdc(2,11,'wavjkhkp181s');
postdc(2,12,'z8rgq9gki7sw');
postdc(2,13,'hbahq3wz92ps');
postdc(2,14,'h3ef4xo6noqo');
postdc(2,15,'ftkr9k65u1vk');




?>