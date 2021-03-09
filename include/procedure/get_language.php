<?php


/* old get lang
if(isset($_GET['language']))
{$language=$_GET['language'];}
else if(isset($_COOKIE['language']))
{$language=$_COOKIE['language'];}
else
{
    if(1==2)
    {$language='fr';}
    else
    {$language='en';}
}
setcookie("language", $language, time()+(4*365*24*3600),'/','.'.$main_domain);
*/

 //new get lang
if(isset($_GET['language'])){
    $language=$_GET['language'];
    setcookie("language", $language, time()+31536000, '/', '.'.$main_domain);
}else if(isset($_COOKIE['language'])){
    $language=$_COOKIE['language'];
}else{
    //$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $language = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ',') : '';
    $language = substr($language, 0,2);
}

//$_GET['lang'] overrides $language but without setting cookie
if(isset($_GET['lang'])){
    $language = $_GET['lang'];
}


$acceptLang = ['fr', 'en'];
$language = in_array($language, $acceptLang) ? $language : 'en';


//old transalte
if(!@include(BP.'/language/language_'.urlencode($language).'.php'))
{
    $language='en';
    include(BP.'/language/language_'.urlencode($language).'.php');
    setcookie("language", $language, time()+(4*365*24*3600),'/','.'.$main_domain);
}




//new translate
$translations=array();
function load_translation($lang){
    global $translations;

    $translation_file=BP.'/language/'.$lang.'.csv';
    if(!file_exists($translation_file))return false;
    $csvData = file_get_contents($translation_file);
    $lines = explode(PHP_EOL, $csvData);
    foreach ($lines as $line) {
        $translations[] = str_getcsv($line);
    }
    //echo '<pre>'.print_r($translations,true).'</pre>';
}
load_translation($language);

function get_translation($string,$args){
    global $translations;

    foreach($translations as $translation){
        if($translation[0]==$string){
            $string = $translation[1];
            break;
        }
    }
    $string=translation_process_varaiables($string,$args);
    return $string;
}
function translation_process_varaiables($string,$args){
    $processed=preg_replace_callback(
        "|(%)(\d)|",
        function ($matches) use($args){
            return $args[$matches[2]];
        },
        $string);
    return $processed;
}

function __($string){
    global $lang;

    $string=get_translation($string,func_get_args());
    return $string;
}