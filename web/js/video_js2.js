function toggle_embed()
{
	var embed_code_div=document.getElementById("embed_code_div");
	if(embed_code_div.style.display=='none')
	{embed_code_div.style.display='block';}
	else
	{embed_code_div.style.display='none';}
}

//function clisqued()
//{alert('video clicked'); var t=setTimeout("close_clisq()",250); window.focus();}
//function close_clisq()
//{document.getElementById("clisq_div").style.display='none'; window.focus();}

//marvellously named function, does not seem to be used anymore
//var rabbazied=0;
//var rabbatime;
//function rabbadoo(garr)
//{
//	if(rabbazied==0)
//	{
//		rabbazied=1;
//		clearTimeout(rabbatime);
//		rabbatime = setTimeout(function(){rabbazied=0;}, 45*60*1000);
//		window.open(garr);
//	}
//}

//not used either..
//function awaa(){ //apply Web Audio API
//	//create audio context, create gainNode
//	var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
//	//var audioCtx = new window.AudioContext();
//	//var audioCtx = new window.webkitAudioContext();
//	var gainNode = audioCtx.createGain();
//	//connect source to gain, connect gain to audio context's destination
//
//	//window.addEventListener('load', function(e) {
//		var HTML5Barbavideo=document.getElementById("HTML5Barbavideo");
//		var source=audioCtx.createMediaElementSource(HTML5Barbavideo);
//		source.connect(gainNode);
//		gainNode.connect(audioCtx.destination);
//		gainNode.gain.value = 2;
//		//source.connect(audioCtx.destination);
//	//}, false);
//}




function attach_video_events(){
    var video_tag=document.getElementById('HTML5Barbavideo');
    video_tag.onended = function() {
        if(nextvideo_url){
            window.location = nextvideo_url;
        }
    };
    if(autoplay){
        if(video_tag.canplay){
            video_tag.play();
        }else{
            video_tag.oncanplay = function() {
                video_tag.play();
            };
        }
    }
}

