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
 * The view controller for the general view of Agenda/Meeting Extension to Committee Module.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once('../lib_comity.php');


$comity_id = optional_param('comity_id', -1, PARAM_INT); // event ID

global $DB,$PAGE,$USER;

$is_viewer = true; //Used in sidebar to activate agenda menu

//If Event ID provided
if ($comity_id) {
  $agenda_id = null;
  $event_id = null;
  $agenda = new stdClass();

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


$PAGE->set_url('/mod/comity/meetingagenda/viewer.php?comity_id='.$comity_id);
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


// Print the tabs to switch modes.
if ($selected_tab==6) {
	$currenttab = 'topics_by_year_list';
	$thispageurl = $PAGE->url;
	$contents = 'topics_by_year_list.php';
/*
  DISABLED--TOPICS BE YEAR
} elseif ($selected_tab==4) {
      $currenttab = 'topics_by_year';
	$thispageurl = $PAGE->url;
	$contents = 'topics_by_year.php';
 */


} elseif ($selected_tab==5) {

        $currenttab = 'motions_by_year';
        $thispageurl = $PAGE->url;
        $contents = 'motions_by_year.php';
} elseif($selected_tab==3) {
    $currenttab = 'open_topics';
	$thispageurl = $PAGE->url;
	$contents = 'open_topics.php';
} elseif($selected_tab==2) {
    $currenttab = 'open_topics_list';
	$thispageurl = $PAGE->url;
	$contents = 'open_topic_list.php';
} else {
    $currenttab = 'viewer_events';
	$thispageurl = $PAGE->url;
	$contents = 'viewer_events.php';

}

//Create Tab Objects
$tabs = array(array(
    new tabobject('viewer_events', new moodle_url($thispageurl, array('selected_tab' => 1)), get_string('events', 'comity')),
    //new tabobject('open_topics', new moodle_url($thispageurl, array('selected_tab' => 3)), get_string('open_topics_tab', 'comity')),
    new tabobject('open_topics_list', new moodle_url($thispageurl, array('selected_tab' => 2)), get_string('open_topic_list_tab', 'comity')),
   // new tabobject('topics_by_year', new moodle_url($thispageurl, array('selected_tab' => 4)), get_string('topics_by_year_tab', 'comity')),
    new tabobject('topics_by_year_list', new moodle_url($thispageurl, array('selected_tab' => 6)), get_string('topics_by_year_list_tab', 'comity')),
    new tabobject('motions_by_year', new moodle_url($thispageurl, array('selected_tab' => 5)), get_string('motions_by_year_tab', 'comity'))
    


));

//Print Tabs
print_tabs($tabs, $currenttab);

//Include tab content
require($contents);

echo $OUTPUT->footer();