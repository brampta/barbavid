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

$en_class='';
$fr_class='';
if($language=='en'){
    $en_class='selected_lang';
}else if($language=='fr'){
    $fr_class='selected_lang';
}

?>

<div class="mobinav_toggle like_a" onclick="toggle_mobinav()"><i class="fa fa-bars" aria-hidden="true"></i></div>

<div class="header_container nav1">
	<ul id="nav">
		<li><a href="/"><?php echo __('home') ?></a></li>
		<li><a href="/upload"><?php echo $text[1000] ?></a></li>
        <li><a href="/channel/list"><?php echo __('channels') ?></a></li>
		<?php if(isset($_SESSION['user_id'])){ ?>
			<li><a href="/user/edit"><?php echo __('logged in as %1',$_SESSION['name']) ?></a></li>
			<li><a href="/user/logout"><?php echo __('logout') ?></a></li>
		<?php }else{ ?>
			<li><a href="/user/login"><?php echo __('login') ?></a></li>
			<li><a href="/user/register"><?php echo __('register') ?></a></li>
		<?php } ?>
		<li><a href="<?php echo $en_link ?>" class="<?php echo $en_class ?>">en</a> <span class="lang_separ">|</span> <a href="<?php echo $fr_link ?>" class="<?php echo $fr_class ?>">fr</a></li>
	</ul>
</div>

<div class="logo_container">
    <?php if($logofile){?><img class="logo" src="<?php echo  $logofile ?>" alt="<?php echo  $site_name ?>" /><?php } ?>
</div>




