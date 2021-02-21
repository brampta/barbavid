<?php

$current_page = $_SERVER['REQUEST_URI'];
$lookforquestionmark = stripos($current_page, '?');
if ($lookforquestionmark) {
    $varsinurl = substr($current_page, $lookforquestionmark + 1);
} else {
    $varsinurl = '';
}

parse_str($varsinurl, $array_of_vars_in_url_fr_lang_lnk);


$array_of_vars_in_url_fr_lang_lnk['language'] = 'en';
$rebuilt_varsinurl_fr_lang_lnk = http_build_query($array_of_vars_in_url_fr_lang_lnk);
$en_link = '?' . $rebuilt_varsinurl_fr_lang_lnk;
$array_of_vars_in_url_fr_lang_lnk['language'] = 'fr';
$rebuilt_varsinurl_fr_lang_lnk = http_build_query($array_of_vars_in_url_fr_lang_lnk);
$fr_link = '?' . $rebuilt_varsinurl_fr_lang_lnk;


//echo '<div style="background-color:yellow;color:black;text-align:center;font-weight:bold;">Barbavid is down for maintenance. The estimated duration of the maintenance is 1 hour.</div>';



if (isset($_GET['mobile']) && 1==2) {
    echo '<div style="text-align:right;"><a href="/">' . $text[1000] . '</a> | <a href="' . $en_link . '">english</a> | <a href="' . $fr_link . '">français</a></div>';
} else {

    //echo '<table class="headrr">
			//<tr>
				//<td class="logor_td">
					//<img src="/barbalogo_resized9.png" alt="Barbavid" class="logor" style="max-width:40%;height:auto;" />
				//</td>
				//<td class="right_td">
					//<table class="headrr_sub">
						//<tr><td class="nav1"><a href="/">' . $text[1000] . '</a> | <a href="/make-money-with-your-uploads">' . $text[1001] . '</a></td></tr>
						//<tr><td class="nav2"><a href="' . $en_link . '">english</a> | <a href="' . $fr_link . '">français</a></td></tr>
					//</table>
				//</td>
			//</tr>
		//</table>';
	//redo with divs:
	echo '<div style="float:left;">';
		echo '<img src="/barbalogo_resized9.png" alt="Barbavid" class="logor" style="max-width:25%;height:auto;" />';
	echo '</div>';
	echo '<div style="float:right;">
		<div class="nav1">';
			echo '<a href="/">' . $text[1000] . '</a>';
			//echo ' | <a href="/make-money-with-your-uploads">' . $text[1001] . '</a>';
		echo '</div>
		<div class="nav2">
			<a href="' . $en_link . '">english</a> | <a href="' . $fr_link . '">français</a>
		</div>
		<div style="height:2em;"><!-- --></div>
	</div>
	<div style="clear: both;"></div>';


}
?>
