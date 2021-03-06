//document.domain = "barbavid.com";



var curr_uploading = 0;
var countchecks = 0;
var file_upload_id = null;
var remember_title = null;

function remember_file_upload_id(func_file_upload_id){
    file_upload_id = func_file_upload_id;
}

function startUpload()
{
    curr_uploading = 1;
    countchecks = 0;
    document.getElementById('f1_upload_process').style.display = 'block';
    document.getElementById('result').innerHTML = '';

    if(remember_upload_mode=='file'){
        var id = document.getElementById('UPLOAD_IDENTIFIER').value;
    }else{
        var id = 'new_file_upload';
    }
    remember_file_upload_id(id);
    remember_title = document.getElementById('title').value;

    check_prog();
}
function stopUpload(results,debuginfo)
{
    console.log("stop upload: ",debuginfo,results);
    curr_uploading = 0;
    document.getElementById('result').innerHTML = results;
    document.getElementById('f1_upload_process').style.display = 'none';
}

function check_prog()
{
    if(curr_uploading==1)
    {
        getprog();
        var t=setTimeout("check_prog();",3000);
    }
    else
    {
        document.getElementById("upload_progress").innerHTML = '';
    }
}


function getprog()
{
    countchecks++;
    var andspacer = '';
    if(countchecks == 3)
    {
        andspacer = '&space=1';
    }

    //method 1: document.domain
    //var urlz = "http://" + upload_server + ".barbavid.com/uploadprogress?id=" + id + andspacer;
    //document.getElementById("upload_progress_iframe").src = urlz;
    
    //method 2: jsonp
    var alreadyexistingscript = document.getElementById("upload_progress_jsonp");
    if(alreadyexistingscript != null)
    {
        document.getElementsByTagName("head")[0].removeChild(alreadyexistingscript);
    }

    var urlz = "https://" + upload_server + "." + main_domain + "/uploadprogress?mode=" + remember_upload_mode + "&id=" + file_upload_id + "&jsonp=1&rand=" + Math.random() + andspacer;
    var m = document.createElement('script');
    m.setAttribute("type","text/javascript");
    m.setAttribute("charset", "utf-8");
    m.setAttribute("src", urlz);
    m.setAttribute("id", "upload_progress_jsonp");
    document.getElementsByTagName("head")[0].appendChild(m);

//    var shoscripts = '';
//    var allscripts = document.getElementsByTagName("script");
//    for(x in allscripts)
//    {shoscripts = shoscripts + allscripts[x].id + ' ';}
//    document.getElementById("uploads_log").innerHTML = shoscripts;
}




function interrupt_upload()
{
    document.getElementById("upload_target").src = "";
}
function show_upload_progress(rezu)
{
    document.getElementById("upload_progress").innerHTML = rezu;
}


window.onbeforeunload = askConfirm;
function askConfirm(){
    if(curr_uploading == 1)
    {
        return ununloadtext;
    }
}


function remembersuccess(title,upload_id)
{
	//alert("remembersuccess('"+title+"','"+upload_id+"')");
    if(title=='rememeber_title'){
        title=remember_title;
    }
    title.replace('|','&#124;')
    title.replace('_','&#95;')
    title.replace('<','&#60;')
    var current_mem_cooki = getCookie("uploads_log");
	//alert(current_mem_cooki);
    var splitted_shiz = current_mem_cooki.split('|', 30);
    var remerged_grabba = splitted_shiz.join('|');
	//alert(remerged_grabba);
    var ts = Math.round((new Date()).getTime() / 1000);
    var newdata = upload_id + '_' + ts + '_' + encodeURIComponent(title) + '|';
    var newcookiestr = newdata + remerged_grabba;
	//alert(newcookiestr);
    setCookie("uploads_log",newcookiestr,5*365);
    //showlastsuccess();
	showlastsuccess(newcookiestr);
}

//function showlastsuccess()
function showlastsuccess(current_mem_cooki)
{
    //var current_mem_cooki = getCookie("uploads_log");
	//alert(current_mem_cooki);
    var splitted_shiz = current_mem_cooki.split('|');
    var rezu = '<div>' + uplogtx[1] + ':</div><table class="uplogs">';
    var countem = 0;
    for(x in splitted_shiz)
    {
        if(splitted_shiz[x] != '')
        {
            countem++;
            var splitted_shaz = splitted_shiz[x].split('_');
            if(splitted_shaz[2]=='')
            {
                splitted_shaz[2]='<i>' + uplogtx[0] + '</i>';
            }
            var row = '<tr><td>' + maketimus(splitted_shaz[1]) + '</td><td><a href="http://'+main_domain+'/video/' + splitted_shaz[0] + '" target="_blank">http://'+main_domain+'/video/' + splitted_shaz[0] + '</a></td><td>' + decodeURIComponent(splitted_shaz[2]) + '</td></tr>';
            rezu = rezu + row;
        }
    }
    rezu = rezu + '</table><div style="font-size:80%;">' + uplogtx[2] + '</div>';
    if(countem == 0)
    {
        rezu = '';
    }
	//alert(rezu);
    document.getElementById("uploads_log").innerHTML = rezu;
}






function remember_popup_URL(inputId)
{
	var input=document.getElementById(inputId);
	setCookie('remember_popup_URL',input.value,2*365)
}
function populate_popup_URL(inputId)
{
	var input=document.getElementById(inputId);
	input.value=getCookie('remember_popup_URL');
}

var remember_upload_mode = null;
function select_upload_mode(mode){
    if(mode=='detect'){
        if(document.getElementById('file_or_url_url').checked){
            mode='url';
        }else{
            mode='file';
            document.getElementById('file_or_url_file').checked = true;
        }
    }
    remember_upload_mode = mode;

    if(mode=='file'){
        document.getElementById("file").disabled = false;
        document.getElementById("url").disabled = true;

        //for the 500 error on upload fix, must put back the names if needed
        document.getElementById("UPLOAD_IDENTIFIER").name = "UPLOAD_IDENTIFIER";
        document.getElementById("file").name = "file";
    }else if(mode=='url'){
        document.getElementById("file").disabled = true;
        document.getElementById("url").disabled = false;

        //note: this is an important fix:
        //keeping the UPLOAD_IDENTIFIER input without an uploaded file caused a bug with uploadprogress which gave a 500 error
        //I spent so much time investigating this!!
        document.getElementById("UPLOAD_IDENTIFIER").name = "";
        document.getElementById("file").name = "";
    }
}


