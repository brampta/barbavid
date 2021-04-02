<?php

$text[0] = 'H.264 video hosting';
$text[1000] = 'upload';
$text[1001] = 'Make&#160;$$$!';

//upload
$text[1] = 'File';
$text[2] = 'Title';
$text[3] = 'Description';
$text[4] = 'Upload';
$text[5] = 'Suspend';
$text[6] = 'Save changes';
$text[7] = 'Delete associated video';
$text[8] = 'I\'m sure';
$text[9] = 'Maximum '.$maxmb.' Megabytes<br />
Multiple audio tracks: only the first audio track will be used<br />
Subtitles tracks: will be ignored, please <a href="http://avidemux.sourceforge.net/" target="_blank">hardcode subtitles</a>.';
$text[10] = 'Maximum 100 characters';
$text[11] = 'Maximum 3000 characters';
$text[12] = 'Upload in progress. Do not close this page.';
$text[13] = 'Are you sure that you want to leave this page? There is an upload in progress. It will be lost if you leave the page before it is finished.';
$text[14] = 'Upload a video:';
$text[15] = 'Popup URL';
$text[16] = 'ie http://domain.com/webpage. Maximum 1024 characters';
$text[17] = 'If you specify a Popup URL, the specified webpage will be opened in a new window when a visitor watches your video, if you put advertisement in this webpage, you can make some money with your uploads. If you do not want to have a popup on your video, leave this field blank. Please do not put bad things in your popup. No pornography, no viruses or malware, no excessive popups. Failure to respect this will result in popups being removed from your videos or account suspension.';


//uploads log
$text[201] = 'no title';
$text[202] = 'Your last uploads';

function explain_expiration($days)
{ return 'Note, an upload will expire if not viewed for ' . $days . ' days.'; }

//video
$text[101] = 'This video is not yet finished encoding, come back later.';
$text[102] = 'Sorry, this video has been suspended.';
$text[103] = 'reason of the suspension: ';
$text[104] = 'The video has been deleted.';
$text[105] = 'Video hosting is expensive. Please click the button below to prove that you are alive.';
$text[106] = 'I\'m alive!';
$text[107] = 'Position in the encoding queue: ';

function explain_encode_progress($step, $totaltime, $currenttime)
{
    if ($step == 'startup')
    { $rezu = 'The encoding process is initializing.'; }
	else if ($step == 'pass1')
    {
        $prct = ($currenttime / $totaltime) * 100;
        $prct = floor($prct * 100) / 100;
        $rezu = 'The encoding process is currently running the pass 1 of 2. ' . $currenttime . ' seconds out of ' . $totaltime . ' have been encoded (' . $prct . '%).';
    } else if ($step == 'pass2')
    {
        $prct = ($currenttime / $totaltime) * 100;
        $prct = floor($prct * 100) / 100;
        $rezu = 'The encoding process is currently running the pass 2 of 2. ' . $currenttime . ' seconds out of ' . $totaltime . ' have been encoded (' . $prct . '%).';
    } else if ($step == 'split')
    { $rezu = 'The main part of the encoding process is finished. The system will now split the file if needed and create thumbnails. The video should be ready to watch in a few seconds.'; }
    return $rezu;
}

function showplayingchunk($chunknum, $totchunks)
{
    $rezu = 'currently playing chunk ' . $chunknum . ' of ' . $totchunks;
    return $rezu;
}

$text[108] = 'play previous chunk';
$text[109] = 'play next chunk';
$text[110] = 'return to chunks list';
$text[112] = array('1', '2', '3', '4', '5');
$text[111] = 'close this ad';

$text[113] = 'Play';
$text[114] = 'Download';

$text[115] = 'The encoding process of this video was not successful. The video has been temporarily moved aside and the failed encoding will be investigated as soon as possible. Sorry for the inconvenient.';

$text[116]='Video hosting is expensive,<br />
you need to prove that you are a human<br />
by pressing the button below.';
$text[117]='Human Verification';
$text[118]='Continue to Video';
function showpleasewaitxseconds($seconds)
{
    $rezu = 'Please wait <span id="waittimezz">' . $seconds . '</span> seconds';
    return $rezu;
}
$text[119]='Use the numbered buttons that are at the bottom of the player when it is on stop to navigate between the parts.';

$text[120]='mp4 &#60;video> tag:';
$text[121]='Your browser does not support the video tag.';
$text[122]='mp4 direct download:';
$text[123]='download';
$text[124]='return to chunks selection';
$text[125]='click here for the flash version';
$text[126]='click here for the mobile version';



//new HTML5/Flash player texts
$text[150]='download';
$text[151]='embed';
$text[152]='duplicate';
$text[153]='use this code to embed this video to your page';
$text[154]='playing';


//maketimus
$text[300] = "jan";
$text[301] = "feb";
$text[302] = "mar";
$text[303] = "apr";
$text[304] = "may";
$text[305] = "jun";
$text[306] = "jul";
$text[307] = "aug";
$text[308] = "sep";
$text[309] = "oct";
$text[310] = "nov";
$text[311] = "dec";





//info page
$text[2000] = 'information';
$text[2001] = 'Barbavid H.264 Video Hosting';
$text[2002] = 'Revolutionary system, Barbavid uses the new H.264 video codec in Flash to serve high quality videos using little bandwidth.';
$text[2003] = 'Simple, anonymous, free, unlimited. Conquer the videoworld with Barbavid.';
$text[2004] = 'System Requirements:';
//$text[2005] = 'Latest version of Flash needed.';
$text[2005] = 'get Flash:';
$text[2006] = 'get flash';
$text[2007] = 'Contact:';
$text[2008] = 'anonymity';
$text[2009] = 'About Barbavid';
$text[2010] = 'Anonymity at Barbavid';
$text[2011] = 'Barbavid does not record the IP address of users who upload or download. Barbavid does not record any personal information.';

function make_writeto_string($email_link)
{ return $email_link; }

//duplicate upload
$text[3000] = 'Duplicate Upload';
$text[3001] = 'no title';
$text[3002] = 'The duplicate was successful';
$text[3003] = 'original';
$text[3004] = 'duplicate';

function explain_expiration2($days)
{ return 'Note, an upload (original or duplicate) will expire if not viewed for ' . $days . ' days.'; }

function explain_badvid($upload, $nameforlink)
{ return 'The upload <a href="/video/' . $upload . '" target="_blank">' . $nameforlink . '</a> cannot be duplicated because it is either deleted or suspended.'; }

$text[3005] = 'Error, the title cannot be blank.';
$text[3006] = 'Error, the number that you have typed did not match the number that was shown, try again.';

function explain_dupvid($upload, $nameforlink)
{ return 'Create a duplicate copy of <a href="/video/' . $upload . '" target="_blank">' . $nameforlink . '</a>.'; }

$text[3007] = 'new title';
$text[3008] = 'new description';
$text[3011] = 'new popup';
$text[3009] = 'retype this number';
$text[3010] = 'create duplicate';



//make money page
$text[4000] = 'Make money with your uploads!';
$text[4001] = 'Barbavid offers you a way to make some money online by uploading popular videos and showing your own popups to our visitors who watch your video!';
$text[4002] = 'To do this, simply place the URL of a page under your control in the Popup URL field of the upload form.
You can then put some advertisement and counters on this page in order to make money with the traffic that your uploads get.';

//mobile first page
$text[5002] = 'mobile-first'; //link to this page
$text[5000] = 'Made for mobile first!'; //page title and h1
$text[5001] = 'For already many years, at Barbavid we have been one of the few video hosts that made sure that our content could be viewed on your desktop computers as well as on your mobile devices.
However ever since 2014 Barbavid has made it\'s priority to be usable on mobile, while becoming even better for desktop computers as well.
Our 2014 redesign aims to make sure that every page looks great on mobile, adapts and fully takes advantage of all the different sizes of browsers.'; //page text


?>
