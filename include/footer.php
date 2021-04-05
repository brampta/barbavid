<?php

$custom_footer_file=$customization_folder.'/footer.php';
if(file_exists($custom_footer_file)){
    include $custom_footer_file;
}