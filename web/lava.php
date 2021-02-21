<?php

//$goforadzz[1] = 'http://www.graboid.com/affiliates/scripts/click.php?a_aid=hidodl&amp;a_bid=7d36c4a9';
$goforadzz[1] = 'http://www.affbuzzads.com/affiliate/index?ref=85299';
$goforadzz[2] = 'http://www.affbuzzads.com/affiliate/index?ref=85299';
$goforadzz[3] = 'http://chilispice.net/?ref=66343';
$goforadzz[4] = 'http://cherrykiss.me/?ref=66343';


$goto = $goforadzz[rand(1, 4)];
$location = 'Location: ' . $goto;
header($location);
?>
