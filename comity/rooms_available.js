$(document).ready(function() {

    $("a#avaliable_rooms_link").fancybox({
        'transitionIn'		: 'none',
        'transitionOut'		: 'none',
        'modal'                 : true,
        'autoDimensions'        : false,
        'width'                 : 600,
        'height'                : 320


    });


});

function initalize_popup(form){



//Plug values into form
    var start_time = document.getElementsByName(form+'_startTime');
    var end_time = document.getElementsByName(form+'_endTime');
    var start_date = document.getElementsByName(form+'_startTime_date');
    var end_date = document.getElementsByName(form+'_endTime_date');

    //Start time
    //var start_time_date = new Date(start*1000);
    var start_time_date = new Date();
    var start_time_minutes = start_time_date.getMinutes().toString();

   

start_time_minutes = String(Math.round(start_time_minutes/10)*10);

//SPECIAL CASES---------------------
     if(start_time_minutes.length==1){
        start_time_minutes = '0'+start_time_minutes;
    }

    if(start_time_minutes == '60')
  {
    start_time_minutes = '00';
    start_time_date.setTime(start_time_date.getTime() + (60*60*1000));
  }
//---------------------------------


    start_time[0].value = start_time_date.getHours().toString()+start_time_minutes;

    //End time
   // var end_time_date = new Date(end*1000);

    var end_time_date = new Date();
end_time_date.setTime(end_time_date.getTime() + (60*60*1000));




    var end_time_minutes = end_time_date.getMinutes().toString();


    end_time_minutes = String(Math.round(end_time_minutes/10)*10);


//SPECIAL CASES---------------------
     if(end_time_minutes.length==1){
        end_time_minutes = '0'+start_time_minutes;
    }

if(end_time_minutes == '60')
  {
    end_time_minutes = '00';
    end_time_date.setTime(end_time_date.getTime() + (60*60*1000));
  }
//------------------------------

    end_time[0].value = end_time_date.getHours().toString()+end_time_minutes;

    //Start date
    start_date[0].value = (start_time_date.getMonth()+1).toString()+'/'+start_time_date.getDate().toString()+'/'+start_time_date.getFullYear().toString();
    //End date
    end_date[0].value = (end_time_date.getMonth()+1).toString()+'/'+end_time_date.getDate().toString()+'/'+end_time_date.getFullYear().toString();


}

function rooms_avaliable_popup(formname){
initalize_popup(formname);
$('a#avaliable_rooms_link').trigger('click');
get_avaliable_rooms(formname);
}

function get_avaliable_rooms(formname){

 var startDate = document.getElementsByName(formname+'_startTime_date')[0].value.split('/');
    var startTime;
        startTime = document.getElementsByName(formname+'_startTime')[0].value;

    var startTimeMinutes = startTime.substring(startTime.length-2,startTime.length);
    var startTimeHour = startTime.substring(0,startTime.length-2);
    //End
    var endDate = document.getElementsByName(formname+'_endTime_date')[0].value.split('/');
    var endTime;

        endTime = document.getElementsByName(formname+'_endTime')[0].value;

    var endTimeMinutes = endTime.substring(endTime.length-2,endTime.length);
    var endTimeHour = endTime.substring(0,endTime.length-2);
    //Date objects
    var start = new Date(startDate[2],startDate[0]-1,startDate[1],startTimeHour,startTimeMinutes,0,0);
    var end = new Date(endDate[2],endDate[0]-1,endDate[1],endTimeHour,endTimeMinutes,0,0);

    var params = [start.valueOf()/1000, end.valueOf()/1000];


    var script = 'reservation_controller.php?function=get_avaliable_rooms&params='+params;

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
        }
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("rooms_available").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("POST",script,true);
    xmlhttp.send();
    document.getElementById("rooms_available").innerHTML='<center><img src="img/ajax-loader.gif" alt="Loading" /></center>'
}

function get_avaliable_rooms(formname){

 var startDate = document.getElementsByName(formname+'_startTime_date')[0].value.split('/');
    var startTime;
        startTime = document.getElementsByName(formname+'_startTime')[0].value;

    var startTimeMinutes = startTime.substring(startTime.length-2,startTime.length);
    var startTimeHour = startTime.substring(0,startTime.length-2);
    //End
    var endDate = document.getElementsByName(formname+'_endTime_date')[0].value.split('/');
    var endTime;

        endTime = document.getElementsByName(formname+'_endTime')[0].value;

    var endTimeMinutes = endTime.substring(endTime.length-2,endTime.length);
    var endTimeHour = endTime.substring(0,endTime.length-2);
    //Date objects
    var start = new Date(startDate[2],startDate[0]-1,startDate[1],startTimeHour,startTimeMinutes,0,0);
    var end = new Date(endDate[2],endDate[0]-1,endDate[1],endTimeHour,endTimeMinutes,0,0);

    var params = [start.valueOf()/1000, end.valueOf()/1000];


    var script = 'reservation_controller.php?function=get_avaliable_rooms&params='+params;

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
        }
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("rooms_available").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("POST",script,true);
    xmlhttp.send();
    document.getElementById("rooms_available").innerHTML='<center><img src="img/ajax-loader.gif" alt="Loading" /></center>';
}


function book_room(room){

var formname = document.getElementsByName('form_name')[0].value;
var baseurl = document.getElementsByName('base_url')[0].value;
var courseid = document.getElementsByName('courseid')[0].value;

   var startDate = document.getElementsByName(formname+'_startTime_date')[0].value.split('/');
    var startTime;
        startTime = document.getElementsByName(formname+'_startTime')[0].value;

    var startTimeMinutes = startTime.substring(startTime.length-2,startTime.length);
    var startTimeHour = startTime.substring(0,startTime.length-2);
    //End
    var endDate = document.getElementsByName(formname+'_endTime_date')[0].value.split('/');
    var endTime;

        endTime = document.getElementsByName(formname+'_endTime')[0].value;

    var endTimeMinutes = endTime.substring(endTime.length-2,endTime.length);
    var endTimeHour = endTime.substring(0,endTime.length-2);
    //Date objects
    var start = new Date(startDate[2],startDate[0]-1,startDate[1],startTimeHour,startTimeMinutes,0,0);
    var end = new Date(endDate[2],endDate[0]-1,endDate[1],endTimeHour,endTimeMinutes,0,0);

   
    var fromTime = start.valueOf()/1000;
    var toTime = end.valueOf()/1000;
    var subject = '';
    var category='default';

    var params = [room, fromTime, toTime, subject, category];

var script = 'reservation_controller.php?function=new_reservation&params='+params;

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
          document.getElementById("rooms_available").innerHTML='<center><img src="img/ajax-loader.gif" alt="Loading..." /></center>';
        }
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            window.location = baseurl+"/blocks/roomscheduler/room.php?course="+courseid+"&room="+room+"&time="+toTime;
        }
    }
    xmlhttp.open("POST",script,true);
    xmlhttp.send();

}