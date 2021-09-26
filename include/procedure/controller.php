<?php

$posoffirstquestionmark=stripos($_SERVER['REQUEST_URI'],'?');
if($posoffirstquestionmark===false){
    $request_noparams=$_SERVER['REQUEST_URI'];
}else{
    $request_noparams=substr($_SERVER['REQUEST_URI'],$posoffirstquestionmark);
}
$exploded_request=explode('/',$request_noparams);
if(
    !(//not!
        //mofo its homepage!!
        $request_noparams=='/' //is homepage no page number
            ||
        (count($exploded_request)==3 && $exploded_request[1]=='page') //is homepage with page number
    )
){
    //basically if we are here it means it was not home page... thats were we will be doing some controller work
    $possible_matches=array(
        $customization_folder.'/web/'.$request_noparams,
        $customization_folder.'/web/'.$request_noparams.'.php',
    );
    foreach($possible_matches as $possible_match){
        if(file_exists($possible_match)){
            include $possible_match;
            die();
        }
    }


    http_response_code(404);
    $notfoundfile=$customization_folder.'/404.php';
    if(file_exists($notfoundfile)){
        include $notfoundfile;
    }else{
        echo 'page not found';
    }
    die();
}