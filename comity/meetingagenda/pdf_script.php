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
 * A script to envoke the creation of the pdf version of the agenda.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once('../lib_comity.php');




$event_id = optional_param('event_id', 0, PARAM_INT); // event ID
$plain_pdf = optional_param('plain_pdf', 0, PARAM_INT); // event ID

global $DB,$PAGE,$USER;

$agenda = null;

//If Event ID provided
if ($event_id) {
        //Check if agenda created
	$agenda  = $DB->get_record('comity_agenda', array('comity_events_id' => $event_id), '*', $ignoremultiple=false);
	if($agenda){
	$comity_id = $agenda->comity_id;
	$event_id = $agenda->comity_events_id;
	$agenda_id =$agenda->id;

        //No Agenda Created
	} else {

	$comity_event = $DB->get_record('comity_events', array('id' => $event_id), '*', $ignoremultiple=false);

	if($comity_event){
	$comity_id = $comity_event->comity_id;
	$event_id = $comity_event->id;

        //NO EVENT
	} else {
	error('You must create an meeting agenda from an event.');
	}
	}

//No EVENT ID
} else {
    error('You must create an meeting agenda from an event.');
}



comity_check($comity_id);

//Get Course Module Object
$cm = get_coursemodule_from_id('comity', $comity_id); //get course module

//Get Comity Object
$comity = $DB->get_record("comity", array("id"=>$cm->instance));

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

if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin'|| $credentials == 'member') {


require_once('pdf.php');
$pdf = new pdf_creator($event_id, $agenda_id, $comity_id, $cm->instance);
$pdf->create_pdf($plain_pdf);

} else {

    print_error("Access Restricted");
}
?>
