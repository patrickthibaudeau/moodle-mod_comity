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
 * The content for the Buisness Arising tab of the Agenda/Meeting Extension to Committee Module.
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB;


//If agenda doesn't exist, don't load content
if (!$agenda) {
    print '<center><h3>' . get_string('no_agenda', 'comity') . '</h3></center>';
    return;
}


//Simple cypher for code clarity
$role_cypher = array('1' => 'president', '2' => 'vice', '3' => "member", "4" => 'admin');

//check if user has a valid user role, otherwise give them the credentials of a guest
if (isset($user_role) && ($user_role == '1' || $user_role == '2' || $user_role == '3' || $user_role == '4')) {
    $credentials = $role_cypher[$user_role];
} else {
    $credentials = "guest";
}

//----------SECURITY------------------------------------------------------------
//------------------------------------------------------------------------------
//Check if they are a memeber
if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin'|| $credentials == 'member'|| is_siteadmin()) {

    //Load Form
    require_once('open_topic_form.php'); //Form for users that can view
    $mform = new mod_agenda_open_topics_form($event_id, $agenda_id, $comity_id, $cm->instance);

    //Get default values
    $toform = $mform->getDefault_toform();
    $toform->event_id = $event_id;
    $toform->selected_tab = $selected_tab;
    //Set data
    $mform->set_data($toform);

    //Display Menu
    require_once("$CFG->dirroot/mod/comity/meetingagenda/buisness_sidebar.php");

    //Display Form
    print '<div class="form">';
    $mform->display();
    print '</div>';

}

?>