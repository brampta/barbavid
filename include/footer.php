<?php

$custom_footer_file=BP.'/custom/footer.php';
if(file_exists($custom_footer_file)){
    include $custom_footer_file;
}