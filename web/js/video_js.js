//I think none of that is still being used!!
//var count6;
//function runcloser()
//{
//    var arraylen = counttxt.length;
//    var closer = document.getElementById('closer');
//    if(arraylen == 0)
//    {
//        closer.innerHTML = closeadtext;
//        closer.style.cursor = 'pointer';
//        closer.onclick = close_ad;
//    }
//    else
//    {
//        var lastelem = counttxt[arraylen - 1];
//        closer.innerHTML = lastelem;
//        counttxt.pop();
//        var t=setTimeout("runcloser()",1000);
//    }
//}
//function close_ad()
//{
//    var adbox = document.getElementById('rabbax');
//    var container = document.getElementById("player");
//    container.removeChild(adbox);
//}
//function play()
//{
//    clearTimeout(count6);
//    var playercode = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="' + player_w + '" height="' + player_h + '" id="flvplayer" align="middle">' +
//    '<param name="allowScriptAccess" value="sameDomain" />' +
//    '<param name="allowFullScreen" value="true" />' +
//    '<param name="movie" value="http://freeflashplayer.net/flvplayer.swf" />' +
//    '<param name="flashvars" value="url=' + playlink_ue + '&linktext=none&linkurl=none&autoplay=1&loop=0&dwn=no&v=75&preloader=0" />' +
//    '<param name="loop" value="true" />' +
//    '<param name="quality" value="high" />' +
//    '<param name="bgcolor" value="#000000" />' +
//    '<param name="wmode" value="opaque" />' +
//    '<embed src="http://freeflashplayer.net/flvplayer.swf" wmode="opaque" flashvars="url=' + playlink_ue + '&linktext=none&linkurl=none&autoplay=1&loop=0&dwn=no&v=75&preloader=0" loop="true" quality="high" bgcolor="#000000" width="' + player_w + '" height="' + player_h + '" name="flvplayer" align="middle" allowScriptAccess="sameDomain" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object>';
//    document.getElementById("player").innerHTML = playercode;
//}
//function download()
//{
//    document.location.href=playlink;
//}


