<?php $nodb=true; include(dirname(dirname(dirname(__FILE__))).'/include/init.php'); ?>

var translations = <?php echo json_encode($translations); ?>;


//translate!!
function __(string){
    for (let i = 0; i < translations.length; i++) {
        if(translations[i][0]==string){
            string=translations[i][1];
            break;
        }
    }

    if(arguments.length>1){
        string = translation_process_variables(arguments);
    }
    return string;
}
function translation_process_variables(arguments){
    //console.log(arguments);
    var string = arguments[0];

    for(let i = 1; i < arguments.length; i++){
        //console.log('will replace %'+i+' with '+arguments[i])
        string = string.replace('%'+i,arguments[i]);
    }

    return string;
}