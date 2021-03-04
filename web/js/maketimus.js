function maketimus(timestampz)
{
    var fomratedtime;
    if(timestampz == 0)
    {
        fomratedtime = '<i style="color:#AAAAAA;">N.A.</i>';
    }
    else
    {
        var time = new Date(timestampz * 1000);
        var day = time.getDate();

        var linkmonthnum = time.getMonth();
        var linkmonth = maketimux_text[linkmonthnum];

        var year = time.getFullYear() + "";
        //	year = year.substring(2);
        var hour = time.getHours();
        var minute = time.getMinutes();
        if (minute < 10)
        {
            minute = "0" + minute;
        }
        var second = time.getSeconds();
        if (second < 10)
        {
            second = "0" + second;
        }
        fomratedtime = day + linkmonth + year + " " + hour + ":" + minute + ":" + second;
    }
    return fomratedtime;
}
