function setCookie(c_name,value,expiredays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+expiredays);
    document.cookie=c_name+ "=" +escape(value)+
    ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}


function getCookie(c_name)
{
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=");
        if (c_start!=-1)
        {
            c_start=c_start + c_name.length+1;
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end));
        }
    }
    return "";
}

//new functions from w3schools
// function setCookie(c_name,value,exdays)
// {
	// var exdate=new Date();
	// exdate.setDate(exdate.getDate() + exdays);
	// var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	// document.cookie=c_name + "=" + c_value;
// }
// function getCookie(c_name)
// {
// var c_value = document.cookie;
// var c_start = c_value.indexOf(" " + c_name + "=");
// if (c_start == -1)
  // {
  // c_start = c_value.indexOf(c_name + "=");
  // }
// if (c_start == -1)
  // {
  // c_value = null;
  // }
// else
  // {
  // c_start = c_value.indexOf("=", c_start) + 1;
  // var c_end = c_value.indexOf(";", c_start);
  // if (c_end == -1)
  // {
// c_end = c_value.length;
// }
// c_value = unescape(c_value.substring(c_start,c_end));
// }
// return c_value;
// }