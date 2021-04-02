<?php

$custom_footer_file=BP.'/include/custom/footer.php';
if(file_exists($custom_footer_file)){
    include $custom_footer_file;
}