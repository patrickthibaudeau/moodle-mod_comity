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
 * The view controller for the agenda side of Agenda/Meeting Extension to Committee Module.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once('../lib_comity.php');


$event_id = optional_param('event_id', 0, PARAM_INT); // event ID

global $DB,$PAGE,$USER,$COURSE;

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
        $agenda_id =null;

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


//DEBUGGING
//print "CommiteeID/Instance?: ".$cm->instance . '</br>';
//print "EventID: ".$event_id . '</br>';
//print "CommiteeID as in tables: ".$comity_id . '</br>';

//if(isset($agenda_id)){
//print "Agenda: " . $agenda_id .  '</br>';
//}

//if($current_user_record){
//print "Role: " . $user_role .  '</br>';
//}

$PAGE->set_url('/mod/comity/meetingagenda/view.php?event_id='.$event_id);
    $PAGE->set_title("$comity->name");
    $PAGE->set_heading("$comity->name");

 $navlinks = array(
     array('name' => get_string('modulename','comity'), 'link' => $CFG->wwwroot.'/mod/comity/view.php?id='.$comity_id, 'type' => 'misc'),
     array('name' => get_string('event','comity'), 'link' => $CFG->wwwroot.'/mod/comity/events.php?id='.$comity_id, 'type' => 'misc')
    );
    $navigation = build_navigation($navlinks);

// Output starts here
echo $OUTPUT->header();

$thispageurl = $CFG->wwwroot;

//Attempt to get current tab param
$selected_tab = optional_param('selected_tab', -1, PARAM_INT);


// Print the tabs to switch modes.----------------------------------------------
//------------------------------------------------------------------------------
//Minutes for the meeting
if ($selected_tab==3) {
	$currenttab = 'arising_issues';
	$thispageurl = $PAGE->url;
	$contents = 'business.php';


//Detailed Buisness Arising List (Disabled -- Not Used)
/*
 } elseif ($selected_tab==2) {
    $currenttab = 'open_topics';
	$thispageurl = $PAGE->url;
	$contents = 'open_topics.php';
*/

//Detailed Topic by year (Disabled -- Not Used)
/*
} elseif ($selected_tab==4) {
	$currenttab = 'topics_by_year';
	$thispageurl = $PAGE->url;
	$contents = 'topics_by_year.php';

*/

//Topics by year list
} elseif ($selected_tab==7) {
	$currenttab = 'topics_by_year_list';
	$thispageurl = $PAGE->url;
	$contents = 'topics_by_year_list.php';


//Motions By Year
} elseif ($selected_tab==5) {
        $currenttab = 'motions_by_year';
        $thispageurl = $PAGE->url;
        $contents = 'motions_by_year.php';

//Business Arising List
} elseif ($selected_tab==6) {
        $currenttab = 'open_topic_list';
        $thispageurl = $PAGE->url;
        $contents = 'open_topic_list.php';        

//Agenda
} else {

    $currenttab = 'agenda';
	$thispageurl = $PAGE->url;
	$contents = 'agenda.php';
	$selected_tab = 1;
}

//Create Tab Objects
$tabs = array(array(
    new tabobject('agenda', new moodle_url($thispageurl, array('selected_tab' => 1)), get_string('agenda_tab', 'comity')),
    new tabobject('arising_issues', new moodle_url($thispageurl, array('selected_tab' => 3)), get_string('minutes_tab', 'comity')),
   // new tabobject('open_topics', new moodle_url($thispageurl, array('selected_tab' => 2)), get_string('open_topics_tab', 'comity')),
   new tabobject('open_topic_list', new moodle_url($thispageurl, array('selected_tab' => 6)), get_string('open_topic_list_tab', 'comity')),
   //new tabobject('topics_by_year', new moodle_url($thispageurl, array('selected_tab' => 4)), get_string('topics_by_year_tab', 'comity')),
    new tabobject('topics_by_year_list', new moodle_url($thispageurl, array('selected_tab' => 7)), get_string('topics_by_year_list_tab', 'comity')),

    new tabobject('motions_by_year', new moodle_url($thispageurl, array('selected_tab' => 5)), get_string('motions_by_year_tab', 'comity')),

));

//Print Tabs
print_tabs($tabs, $currenttab);

//Include tab content
require($contents);

echo $OUTPUT->footer();
