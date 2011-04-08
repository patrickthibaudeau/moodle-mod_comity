<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

function save_reservation_id($agenda_id, $reservationid){
global $DB;

$dataobject = new stdClass();

$dataobject->id = $agenda_id;
$dataobject->room_reservation_id = $reservationid;

if($DB->update_record('comity_events', $dataobject, $bulk=false)){
 print true;
} else {
 print false;
}


}

function reset_reservation_id($event_id){
global $DB;

$dataobject = new stdClass();

$dataobject->id = $event_id;
$dataobject->room_reservation_id = 0;

$DB->update_record('comity_events', $dataobject, $bulk=false);

print $agenda_id . " " . $reservationid;

}

function get_room_by_reservation_id($reservation_id, $printresult=0){
global $DB;

    $sql = 'SELECT room.id,room.name,res.startdate,res.enddate FROM {roomscheduler_reservations} res, {roomscheduler_rooms} room '.
    "WHERE res.location = room.id AND room.active=1 AND res.active=1 AND res.id = $reservation_id";

   $record = $DB->get_record_sql($sql, array());

   $room;
   if($record){
  $room = $record->id . ",";
  $room .= $record->name . ",";
  $room .= $record->startdate . ",";
  $room .= $record->enddate;

    if($printresult){
      print $room;
    }

     return $room;

   } else {


   if($printresult){
    print '';
   }
   return NULL;
   }



}

?>
