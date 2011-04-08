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
 * The content for the topics by year tab of the Agenda/Meeting Extension to Committee Module.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB;

//No data to display if no agenda has been created
if (!$agenda) {
    print '<center><h3>' . get_string('no_agenda', 'comity') . '</h3></center>';
    return;
}

//-------SECURTIY---------------------------------------------------------------
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

    require_once('topics_by_year_form.php'); //Form for users that can view
    $mform = new mod_agenda_topics_by_year_form($event_id, $agenda_id, $comity_id, $cm->instance);

    //Get default values for form
    $toform = $mform->getDefault_toform();
    $toform->event_id = $event_id;
    $toform->selected_tab = $selected_tab;

     //set forum values
     $mform->set_data($toform);

      //Display Menu
      require_once("$CFG->dirroot/mod/comity/meetingagenda/topic_by_year_sidebar.php");

      //Display form
      print '<div class="form">';
      $mform->display();
      print '</div>';

}

?>