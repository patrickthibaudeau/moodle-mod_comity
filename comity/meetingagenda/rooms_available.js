/*
 * Global Array For Months based on index 0:jan -> 11:Dec
 *
 */
var m_names = new Array("January", "February", "March",
"April", "May", "June", "July", "August", "September",
"October", "November", "December");





/*
* Initalizes booked room popup with provided values.
*
*  @param int eventid The unique id for the current event
*  @param string form The name of the form for the book room popup
*  @param string starttime "year,month,day,starthour,startminutes"
*  @param string starttime "year,month,day,endhour,endminutes"
*  @param int courseid Id of the current course
*  @param string committeeName Name of the current committee
*
*   @return void
*/
function initalize_popup(eventid,form,starttime,enddtime,courseid,committeeName){

//Split starttime into an array of date components
var start_date_components = starttime.split(',');

//Split endtime into an array of date components
var end_date_components = enddtime.split(',');

//month needs to be indexed starting at 0, therefore month -1
start_date_components[1] = start_date_components[1] - 1;
end_date_components[1] = end_date_components[1] - 1;

//Plug values into form
    var start_time = document.getElementsByName(form+'_startTime');
    var end_time = document.getElementsByName(form+'_endTime');
    var start_date = document.getElementsByName(form+'_startTime_date');
    var end_date = document.getElementsByName(form+'_endTime_date');
    
    var course_id = document.getElementsByName('courseid');
    var committee_Name = document.getElementsByName('committee_Name');
    var event_id = document.getElementsByName('eventid');

    course_id[0].value = courseid;
    committee_Name[0].value = committeeName;
    event_id[0].value = eventid;


    //Start time
    //var start_time_date = new Date(start*1000);
    var start_time_date = new Date(start_date_components[0],start_date_components[1],start_date_components[2],start_date_components[3],start_date_components[4]);
    var start_time_minutes = start_time_date.getMinutes().toString();

   
//round down starttime
start_time_minutes = round_down(start_time_minutes,start_time_date);

    //start time
    start_time[0].value = start_time_date.getHours().toString()+start_time_minutes;
    //End time
    var end_time_date = new Date(end_date_components[0],end_date_components[1],end_date_components[2],end_date_components[3],end_date_components[4]);
    var end_time_minutes = end_time_date.getMinutes().toString();


//round up end time
end_time_minutes = round_up(end_time_minutes, end_time_date);

//Load Popup header
time_header(start_time_date, end_time_date);


    end_time[0].value = end_time_date.getHours().toString()+end_time_minutes;

    //Start date
    start_date[0].value = (start_time_date.getMonth()+1).toString()+'/'+start_time_date.getDate().toString()+'/'+start_time_date.getFullYear().toString();
    //End date
    end_date[0].value = (end_time_date.getMonth()+1).toString()+'/'+end_time_date.getDate().toString()+'/'+end_time_date.getFullYear().toString();

}


/*
 * Loads data from form into book room popup
 *
 * @param string form Form name of book room popup
 * @param int courseid The id for the current course
 * @param string committeeName The name of the Committee.
 *
 */
function  initalize_popup_newEvent(form,courseid,committeeName){

var start_date_components = new Array();
var end_date_components= new Array();

//Get date selection form elements(start)
start_date_components[0] = document.getElementsByName('year')[0].value;
start_date_components[1] = document.getElementsByName('month')[0].value-1;
start_date_components[2] = document.getElementsByName('day')[0].value;
start_date_components[3] = document.getElementsByName('starthour')[0].value;
start_date_components[4] = document.getElementsByName('startminutes')[0].value;

//Get date selection form elements(end)
end_date_components[0] = document.getElementsByName('year')[0].value;
end_date_components[1] = document.getElementsByName('month')[0].value-1;
end_date_components[2] = document.getElementsByName('day')[0].value;
end_date_components[3] = document.getElementsByName('endhour')[0].value;
end_date_components[4] = document.getElementsByName('endminutes')[0].value;

//Plug values into form
    var start_time = document.getElementsByName(form+'_startTime');
    var end_time = document.getElementsByName(form+'_endTime');
    var start_date = document.getElementsByName(form+'_startTime_date');
    var end_date = document.getElementsByName(form+'_endTime_date');

    var course_id = document.getElementsByName('courseid');
    var committee_Name = document.getElementsByName('committee_Name');

    course_id[0].value = courseid;

    committee_Name[0].value = committeeName;
   


    //Start time
    //var start_time_date = new Date(start*1000);
    var start_time_date = new Date(start_date_components[0],start_date_components[1],start_date_components[2],start_date_components[3],start_date_components[4]);
    var start_time_minutes = start_time_date.getMinutes().toString();

//round down starttime
start_time_minutes = round_down(start_time_minutes,start_time_date);
start_time[0].value = start_time_date.getHours().toString()+start_time_minutes;

    //End time
   // var end_time_date = new Date(end*1000);

    var end_time_date = new Date(end_date_components[0],end_date_components[1],end_date_components[2],end_date_components[3],end_date_components[4]);
var end_time_minutes = end_time_date.getMinutes().toString();
//round up end time
end_time_minutes = round_up(end_time_minutes, end_time_date);

//set popup time header
time_header(start_time_date, end_time_date);


end_time[0].value = end_time_date.getHours().toString()+end_time_minutes;

//load date

//alert(end_time[0].value + " " + start_time[0].value + " " + start_date[0].value + " " + start_date[0].value);
start_date[0].value = (start_time_date.getMonth()+1).toString()+'/'+start_time_date.getDate().toString()+'/'+start_time_date.getFullYear().toString();
end_date[0].value = (end_time_date.getMonth()+1).toString()+'/'+end_time_date.getDate().toString()+'/'+end_time_date.getFullYear().toString();

}


/*
 * Calls functions to initalize popups from initalized data, and do an ajax call to get avaliable rooms.
*
*  @param int eventid The unique id for the current event
*  @param string form The name of the form for the book room popup
*  @param string starttime "year,month,day,starthour,startminutes"
*  @param string starttime "year,month,day,endhour,endminutes"
*  @param int courseid Id of the current course
*  @param string committeeName Name of the current committee
*
*   @return void
 */
function rooms_avaliable_popup(eventid,form,starttime,enddtime,courseid,committeeName){
initalize_popup(eventid,form,starttime,enddtime,courseid,committeeName);
get_avaliable_rooms(form);
}


/*
 * Calls functions to update form from form selection elements, and
 * then do an ajax call to get avaliable rooms.
 *
 * @param string form Form name of book room popup
 * @param int courseid The id for the current course
 * @param string committeeName The name of the Committee.
 *
 */
function rooms_avaliable_popup_newEvent(form,courseid,committeeName){
initalize_popup_newEvent(form,courseid,committeeName);
get_avaliable_rooms(form);
}

/*
 * Loads the rooms_available DIV with a table of avaliable rooms for the date/time
 * currently loaded into the book room popup
 *
 * @param String formname The name of the form within the room booking popup
 *
 */
function get_avaliable_rooms(formname){

//Get starttime/end time from popup elements

//get start date/time
 var startDate = document.getElementsByName(formname+'_startTime_date')[0].value.split('/');
    var startTime;
        startTime = document.getElementsByName(formname+'_startTime')[0].value;

    var startTimeMinutes = startTime.substring(startTime.length-2,startTime.length);
    var startTimeHour = startTime.substring(0,startTime.length-2);
    
    //Get end date/time
    var endDate = document.getElementsByName(formname+'_endTime_date')[0].value.split('/');
    var endTime;

        endTime = document.getElementsByName(formname+'_endTime')[0].value;

    var endTimeMinutes = endTime.substring(endTime.length-2,endTime.length);
    var endTimeHour = endTime.substring(0,endTime.length-2);
    
    //Create Date objects
    var start = new Date(startDate[2],startDate[0]-1,startDate[1],startTimeHour,startTimeMinutes,0,0);
    var end = new Date(endDate[2],endDate[0]-1,endDate[1],endTimeHour,endTimeMinutes,0,0);

    var params = [start.valueOf()/1000, end.valueOf()/1000];

//Ajax call to get table from php function get_avaliable_rooms(starttime, endtime)
   var baseurl = document.getElementsByName('base_url')[0].value;
   var script = baseurl + '/blocks/roomscheduler/reservation_controller.php?function=get_avaliable_rooms&params='+params;

    try {  xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');   }
    catch (e)
    {
        try {   xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');    }
        catch (e2)
        {
          try {  xmlhttp = new XMLHttpRequest();     }
          catch (e3) {  xmlhttp = false;   }
        }
     }

    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==3)
        {
        }
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            //Since we are returning a table -- .innerhtml craters on IE 8/9
            //Therefore we play the bait and switch with a new DOM element
            var oldDiv = document.getElementById("rooms_available");
            var replacement = document.createElement("div");
            replacement.id = oldDiv.id;
            replacement.innerHTML=xmlhttp.responseText;
            oldDiv.parentNode.replaceChild(replacement, oldDiv);

            //document.getElementById("rooms_available").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("POST",script,true); // good all browsers
    xmlhttp.send();

    //Change DIV to loading image
    document.getElementById("rooms_available").innerHTML='<center><img src="pix/ajax-loader.gif" alt="Loading" /></center>';
}

/*
 * Books a room based on the information within the bookroom popup using ajax.
 *
 * @param int room The room ID for selected room to be booked.
 *
 */
function book_room(room){

//Get general data from popup
var formname = document.getElementsByName('form_name')[0].value;
var baseurl = document.getElementsByName('base_url')[0].value;
var courseid = document.getElementsByName('courseid')[0].value;
var committee_Name = document.getElementsByName('committee_Name')[0].value;


//Retrieve start date/time from popup
   var startDate = document.getElementsByName(formname+'_startTime_date')[0].value.split('/');
    var startTime;
        startTime = document.getElementsByName(formname+'_startTime')[0].value;

// last two digits must be minutes, the rest of the begining is hours:: HHMM or HMM
    var startTimeMinutes = startTime.substring(startTime.length-2,startTime.length);
    var startTimeHour = startTime.substring(0,startTime.length-2);

//Retrieve end date/time from popup
    var endDate = document.getElementsByName(formname+'_endTime_date')[0].value.split('/');
    var endTime;

        endTime = document.getElementsByName(formname+'_endTime')[0].value;

// last two digits must be minutes, the rest of the begining is hours:: HHMM or HMM
    var endTimeMinutes = endTime.substring(endTime.length-2,endTime.length);
    var endTimeHour = endTime.substring(0,endTime.length-2);


    //Date objects
    var start = new Date(startDate[2],startDate[0]-1,startDate[1],startTimeHour,startTimeMinutes,0,0);
    var end = new Date(endDate[2],endDate[0]-1,endDate[1],endTimeHour,endTimeMinutes,0,0);

   //Must be in seconds, so we convert from miliseconds that is given by the time object
    var fromTime = start.valueOf()/1000;
    var toTime = end.valueOf()/1000;
    var subject = committee_Name;
    var category='meeting';


//Do an ajax call in order to book the reservation
 //PHP FUNCTION: new_reservation($room, $fromTime, $toTime, $subject, $category, $description='', $allDay=0, $recurrence_id=0, $printresult=0) {
    var params = [room, fromTime, toTime, subject,category,'', 0, 0,1];

var baseurl = document.getElementsByName('base_url')[0].value;
var script = baseurl + '/blocks/roomscheduler/reservation_controller.php?function=new_reservation&params='+params;

    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp1=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp1=new ActiveXObject("Microsoft.XMLHTTP");
    }


    xmlhttp1.onreadystatechange=function(){

        if (xmlhttp1.readyState==3)
        {
            //put loading image into div
          document.getElementById("rooms_available").innerHTML='<center><img src="pix/ajax-loader.gif" alt="Loading..." /></center>';
        }
        if (xmlhttp1.readyState==4 && xmlhttp1.status==200)
        {
        //process response
        process_book_room_response(xmlhttp1.responseText);
        
        get_avaliable_rooms(formname);
        
    }
    }


    xmlhttp1.open("POST",script,true);// FF-B,IE,Chrome
    xmlhttp1.send();

}

/*
 * Processes the response that is returned from booking a room ajax call
 *
 * @param string responseText This will be the reservation id, if successful, otherwise it will be "".
 *
 */
function process_book_room_response(responseText){

//If the event_id id not "" then we are editing an existing event, otherwise we are
//creating a new event
var event_id = document.getElementsByName('eventid')[0].value;

//If no responseText: Nothing to process
if(responseText==""){
     return;
   }

if(event_id==""){ //no event id means that it is a NEW EVENT
  

var element = document.getElementsByName('room_reservation_id');

if(element){
  element[0].value = responseText; // Assign to an element to be used in creation of event
  $.fancybox.close(); //close Book Room popup
 hideElementByID('avaliable_rooms_link', 'hide'); //Hide the Book Room PopUp Link
display_reservation_details(responseText); //Display the new reservations details onto page
}

} else { // EDITING an existing event

var reservationid = responseText;

var params = [event_id, reservationid];

//Do an ajax call in order to assign the new reservation id to the current event
var script = 'meetingagenda/ajax_controller.php?function=save_reservation_id&params='+params;

    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp2.onreadystatechange=function()
    {

        if (xmlhttp2.readyState==3)
        {
          //loading image
          document.getElementById("rooms_available").innerHTML='<center><img src="pix/ajax-loader.gif" alt="Loading..." /></center>';
        }
        if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
        {

        if(xmlhttp2.responseText=='1'){

           $.fancybox.close();
            hideElementByID('avaliable_rooms_link', 'hide');
            display_reservation_details(reservationid);
        }
    }
    }
    xmlhttp2.open("POST",script,true);// FF-B,IE,Chrome
    xmlhttp2.send();

}
}

/*
* Hide/show HTML Element
*
* @param int ID The identifier(id) of the html element
* @param action string 'show' shows the item, 'hide' hide the object.
*
*/
function hideElementByID(ID, action){

if(action =='hide'){
document.getElementById(ID).style.display = 'none';
} else if(action =='show'){
document.getElementById(ID).style.display = '';
}

}

/*
 * Does an ajax call in order to get the details of a reservation from its provided id.
 *
 * @param int reservationid The id of desired reservations details.
 *
 */
function display_reservation_details(reservationid){


var params = [reservationid, 1];


var script = 'meetingagenda/ajax_controller.php?function=get_room_by_reservation_id&params='+params;

    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp3=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp3.onreadystatechange=function()
    {
        if (xmlhttp3.readyState==3)
        {
          document.getElementById("rooms_available").innerHTML='<center><img src="pix/ajax-loader.gif" alt="Loading..." /></center>';
        }
        if (xmlhttp3.readyState==4 && xmlhttp3.status==200)
        {

          parse_room_response(xmlhttp3.responseText,reservationid, 'true');
    }
    }
    xmlhttp3.open("POST",script,true);
    xmlhttp3.send();


}

/*
 * Parses the ajax reponse from a display_reservation_details function call, and outputs it to the "booked_location" DIV.
 * .
 *
 * @param string responseText The string returned by the ajax call to get reservation details.
 * @param int reservationid The id of the reservation.
 *  @param string enable_delete 'true' includes a delete button in the output, 'false' disregards delete button.
 */
function parse_room_response(responseText,reservationid, enable_delete) {
var content;

//If no response simply empty DIV.
if(responseText == ""){

 content = "";
 document.getElementById("booked_location").innerHTML=content;
 return;
}

//split reponse text into array of components
var reservation = responseText.split(',');

//reservation[2] is reservation start time in seconds, but need in miliseconds for date constructor
//reservation[3] is reservation end time in seconds, but need in miliseconds for date constructor
var start_time_date = new Date(reservation[2]*1000);
var end_time_date = new Date(reservation[3]*1000);

var start_min = start_time_date.getMinutes().toString();
var end_min = end_time_date.getMinutes().toString();


//reservation[1] is the name of the room
content = reservation[1]+" "+m_names[end_time_date.getMonth()] +' '+end_time_date.getDate()+', '+end_time_date.getFullYear();
content += " " + start_time_date.getHours()+":"+append_zero(start_min)+"-"+end_time_date.getHours()+":"+append_zero(end_min);
content += '<img src="img/success.gif" onclick="room_scheduler_redirect(\''+reservation[2]+'\',\''+reservation[0]+'\');" />';

//Conditional delete button
if(enable_delete == 'true'){
content += '<img src="pix/delete.gif" onclick="room_scheduler_delete(\''+reservationid+'\');" />';
}

//Load content into DIV
document.getElementById("booked_location").innerHTML=content;
}


/*
 * Redirect to room schduler on the date and room specified.
 *
 * @param int time The time in seconds since 1970.
 * @param int room The id of the room that will be redirected to.
 */
function room_scheduler_redirect(time,room){
var baseurl = document.getElementsByName('base_url')[0].value;
var courseid = document.getElementsByName('courseid')[0].value;

 var url = baseurl + '/blocks/roomscheduler/room.php?course='+courseid+'&room='+room+'&time='+time;

 window.location = url;
}

/*
 * Appends '0' to the front of any value that is only 1 in length.
 *
 * @param string value  Any string value.
 *
 * @return string value New string value.
 */
function append_zero(value){
    if(value.length ==1){
        value = '0'+value;
    }
    return value;

}


/*
 * Displays the date header for the book room popup.
 * @param object start_time_date A date object representing the start time.
 * @param object end_time_date A date object representing the end time.
 *
 */
function time_header(start_time_date, end_time_date){
var popup_header = '<center>'+m_names[end_time_date.getMonth()] +' '+end_time_date.getDate()+', '+end_time_date.getFullYear();
popup_header += " " + start_time_date.getHours()+":"+append_zero(start_time_date.getMinutes().toString())+"-"+end_time_date.getHours()+":"+append_zero(end_time_date.getMinutes().toString())+'</center>';

//Load rooms_available_header DIV with header data
document.getElementById("rooms_available_header").innerHTML=popup_header;
}


/*
 * Sets a char or string into a string at the specified postion
 *
 * @param string str The string to be modified.
 * @param string/char chr The char or string to be put in.
 * @param int pos The position in the string.
 *
 */
function setCharAt(str, chr, pos){
    var length = str.length;
     var before;
    var after;

    if(pos == 0){//case 1: first positon
   after = str.substring(1, length);
   return chr+after;
    } else if(pos == length-1){ //case 2: last position
   before = str.substring(0, length-1);


   return before+chr;
    } else if(pos > 0 && pos < length){ // interior positions
     before = str.substring(0, pos);
    after = str.substring((pos + 1), length);

    return before + chr + after;
    
    } else { //outside limits
        return str;
    }



    
}


/*
 * Rounds down the time to a division of 10.
 *
 * @param int start_time_minutes The minutes of the start time.
 * @param object start_time_date A date object.
 *
 */
function round_down(start_time_minutes,start_time_date){
    //Round down to a division of 10.
start_time_minutes = append_zero(start_time_minutes);
var length = start_time_minutes.length;


//ROUND DOWN
start_time_minutes = setCharAt(start_time_minutes, '0', length-1);
start_time_date.setMinutes(start_time_minutes);

start_time_minutes = append_zero(start_time_minutes);

return start_time_minutes; //return new minutes (date object changes persist)
}


/*
 * Round up minutes to next division of 10.
 *
 * Replaces the last digit with 0, and increments the tenth postion by 1.
 * If new minutes == 60, change to 00.
 *
 * @param int end_time_minutes The minutes of the end time.
 * @param object end_time_date A date object.
 *
 */
function round_up(end_time_minutes, end_time_date){

end_time_minutes = append_zero(end_time_minutes);
var length = end_time_minutes.length;


//ROUND UP
//get value of tenth
var tenth = end_time_minutes.charAt(length-2);


if(end_time_minutes[length-1]!='0'){ //check if last digit is already 0
var rounded_tenth = parseFloat(tenth)+1;



//Replaces the last digit with 0, and increments the tenth postion by 1.
end_time_minutes = setCharAt(end_time_minutes, rounded_tenth.toString(), length-2);

end_time_minutes = setCharAt(end_time_minutes, '0', length-1);
end_time_date.setMinutes(end_time_minutes);

}

end_time_minutes = append_zero(end_time_minutes);

if(end_time_minutes == '60'){
    end_time_minutes = '00';
}


return end_time_minutes;
}

/*
 * Uses an ajax call in order to delete a reservation of a given id.
 *
 * @param int reservation_id The id of the reservation to be deleted.
 *
 */
function room_scheduler_delete(reservation_id){

var baseurl = document.getElementsByName('base_url')[0].value;
var script = baseurl+'/blocks/roomscheduler/reservation_controller.php?function=delete_reservation&params='+reservation_id;

    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp5=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp5=new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp5.onreadystatechange=function()
    {
        if (xmlhttp5.readyState==3)
        {
          
        }
        if (xmlhttp5.readyState==4 && xmlhttp5.status==200)
        {
            var div = document.getElementById("booked_location");
            var link = document.getElementById("avaliable_rooms_link");
                
                if(div){
                    div.innerHTML='';
                }
                
                if(link){
                    link.style.display='';
                    
                }

            var element = document.getElementsByName('room_reservation_id');

if(element && element[0]){
  element[0].value = 0;
}

    }
    }
    xmlhttp5.open("POST",script,true);
    xmlhttp5.send();


}