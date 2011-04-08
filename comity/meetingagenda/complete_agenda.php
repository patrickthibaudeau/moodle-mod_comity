<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * **DEPRECATED: Was introduced to allow agenda to be considered 'complete' and become uneditable
 *      *Functionality was removed from final design.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once('../lib_comity.php');

$event_id = optional_param('event_id', 0, PARAM_INT); // event ID, or

global $DB,$PAGE,$USER,$CFG;

$agenda = null;

if ($event_id) {
	$agenda  = $DB->get_record('comity_agenda', array('comity_events_id' => $event_id), '*', $ignoremultiple=false);
	if($agenda){
	$comity_id = $agenda->comity_id;
	$event_id = $agenda->comity_events_id;
	$agenda_id =$agenda->id;
	//$DB->delete_records('comity_agenda', array('comity_events_id' => $event_id));
       //exit();
	} else {
          print_error('Unable to Complete Agenda');
        }
} else {
print_error('Unable to Complete Agenda');
}

comity_check($comity_id);
$cm = get_coursemodule_from_id('comity', $comity_id); //get course module

//Get Credentials for this user
if ($current_user_record = $DB->get_record("comity_members", array("comity_id"=>$comity_id,"user_id"=>$USER->id))){
$user_role = $current_user_record->role_id;
}


//Simple cypher for code clarity
$role_cypher = array('1' => 'president', '2' => 'vice', '3' => "member", "4" => 'admin');

//check if user has a valid user role, otherwise give them the credentials of a guest
if (isset($user_role) && ($user_role == '1' || $user_role == '2' || $user_role == '3' || $user_role == '4')) {
    $credentials = $role_cypher[$user_role];
} else {
    $credentials = "guest";
}

if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin') {

 if ($DB->record_exists('comity_agenda', array('id' => $agenda->id))) {
                $agenda_object = new stdClass();
                $agenda_object->id = $agenda->id;
                $agenda_object->completed = 1;


                $DB->update_record('comity_agenda', $agenda_object, $bulk = false);
            }
redirect($CFG->wwwroot."/mod/comity/meetingagenda/view.php?event_id=".$event_id);
} else {
print_error("Access Restriction");
}




?>
