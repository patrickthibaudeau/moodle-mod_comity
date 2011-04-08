/*
 * Call a server-side script using AJAX (POST method)
 *
 * @param script the location and name of the script on the server (eg. /scripts/script.php?id=0)
 * @param div the div that needs to be 'refreshed' once the script is complete - is actually refreshed
 *          with data that is output by the script page
 */
function ajax_post(script,div){
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==1)
        {
            document.getElementById(div).innerHTML='<img src="ajax-loader.gif">';
        }
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById(div).innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("POST",script,true);
    xmlhttp.send();
}


/*
 * Call a server-side script using AJAX (GET method)
 *
 * @param script the location and name of the script on the server (eg. /scripts/script.php?id=0)
 * @param div the div that needs to be 'refreshed' once the script is complete - is actually refreshed
 *          with data that is output by the script page
 */
function ajax_get(script,div){
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==3)
        {
            document.getElementById(div).innerHTML='<img src="ajax-loader.gif">';
        }
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById(div).innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET",script,true);
    xmlhttp.send();
}

