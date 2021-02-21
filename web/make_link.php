<?php

//function makeClickableLinks($text)
//{
//    return preg_replace_callback('#((http://)[-a-zA-Z0-9@:%_\+.~,\#?&//=!]+)#',
//        create_function('$matches',
//            'return \'<a class="gabba_link" href="http://anolink.com/?link=\'
//            . urlencode($matches[1])
//	    . \'" target="_blank"> \'
//	    . $matches[1]
//	    . \' </a>\';'),
//        $text);
//}


function makeClickableLinks($text)
{
    return preg_replace_callback('#((http://)[-a-zA-Z0-9@:%_\+.~,\#?&//=!]+)#',
        @create_function('$matches',
            'return \'<a class="gabba_link" rel="nofollow" href="\'
            . $matches[1]
	    . \'" target="_blank"> \'
	    . $matches[1]
	    . \' </a>\';'),
        $text);
}







?>
