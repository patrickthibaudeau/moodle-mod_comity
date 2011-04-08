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
 * The content for the "Motions By Year" tab of the Agenda/Meeting Extension to Committee Module.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB;

//If agenda doesn't exist, don't load anything
if (!$agenda) {
   print '<center><h3>' . get_string('no_agenda', 'comity') . '</h3></center>';
   return;
}


//------------------SECURITY----------------------------------------------------
//------------------------------------------------------------------------------
//Simple cypher for code clarity
$role_cypher = array('1' => 'president', '2' => 'vice', '3' => "member", "4" => 'admin');

//check if user has a valid user role, otherwise give them the credentials of a guest
if (isset($user_role) && ($user_role == '1' || $user_role == '2' || $user_role == '3' || $user_role == '4')) {
    $credentials = $role_cypher[$user_role];
} else {
    $credentials = "guest";
}


if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin'|| $credentials == 'member'|| is_siteadmin()) {


//indicator to check for list view, or detailed view
$detailed = optional_param('detailed', 0, PARAM_INT); // event ID

print<<<HERE
<style type='text/css'>
.view_type {
float:right;
}
</style>
HERE;

if($detailed){ // detailed view-------------------------------------------------

//---------------DETAILED/LIST VIEW NAVIGATION----------------------------------

//NOTE--DISABLED: NAVIGATION TO LIST VERSION OF PAGE

if(isset($is_viewer)){//In viewer.php general viewer

/*print<<<HERE
/*<div class="view_type">
<a href="$CFG->wwwroot/mod/comity/meetingagenda/viewer.php?comity_id=$comity_id&selected_tab=$selected_tab" ALIGN=RIGHT>List View</a>
</div>
HERE;

    } else { //In Agenda View

print<<<HERE
<div class="view_type">
<a href="$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=$event_id&selected_tab=$selected_tab" ALIGN=RIGHT>List View</a>
</div>
HERE;*/
}
//------------------------------------------------------------------------------

  require_once('motions_by_year_form.php'); //Form for users that can view
$mform = new mod_agenda_motions_by_year_form($event_id, $agenda_id, $comity_id, $cm->instance);

//Get Defaults
$toform = $mform->getDefault_toform();
$toform->event_id = $event_id;
$toform->selected_tab = $selected_tab;

//Set Defaults
$mform->set_data($toform);

//Display Menu
require_once("$CFG->dirroot/mod/comity/meetingagenda/topic_by_year_sidebar.php");

//Display Form
print '<div class="form">';
$mform->display();
print '</div>';



} else { // List View / Default View--------------------------------------------

//---------------DETAILED/LIST VIEW NAVIGATION----------------------------------

//NOTE--DISABLED: NAVIGATION TO DETAILED VERSION OF PAGE
    
    if(isset($is_viewer)){//In viewer.php general viewer


/*print<<<HERE
<div class="view_type">
<a href="$CFG->wwwroot/mod/comity/meetingagenda/viewer.php?comity_id=$comity_id&selected_tab=$selected_tab&detailed=1" ALIGN=RIGHT>Detailed View</a>
</div>
HERE;

}   else { //In Agenda View


print<<<HERE
<div class="view_type">
<a href="$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=$event_id&selected_tab=$selected_tab&detailed=1" ALIGN=RIGHT>Detailed View</a>
</div>
HERE;*/
    }
//------------------------------------------------------------------------------

require_once('motions_by_year_list_form.php'); //Form for users that can view
$mform = new mod_agenda_motions_by_year_list_form($event_id, $agenda_id, $comity_id, $cm->instance);

//Get Defaults
$toform = $mform->getDefault_toform();
$toform->event_id = $event_id;
$toform->selected_tab = $selected_tab;

//Set Defaults
$mform->set_data($toform);

//Display Menu
require_once("$CFG->dirroot/mod/comity/meetingagenda/topic_by_year_sidebar.php");

//Display Form
print '<div class="form">';
$mform->display();
print '</div>';

}
}
?>