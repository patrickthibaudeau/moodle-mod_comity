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
 * A page displaying all events for the events tab of the overall agenda viewer. (viewer.php)
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/mod/comity/meetingagenda/lib.php");
require_once("$CFG->dirroot/mod/comity/meetingagenda/agenda_viewer.css");

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
if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin'|| $credentials == 'member' || is_siteadmin()) {

print '<div class="viwer_menu">';
$sql = "SELECT ce.id, ce.year, ce.year, ce.month, ce.day, ce.summary ".
"FROM {comity_events} ce ".
"WHERE ce.comity_id = ? ".
"ORDER BY year DESC, month DESC, day DESC";

$agendas = $DB->get_records_sql($sql, array($comity_id,$event_id), $limitfrom=0, $limitnum=0);

print '<h3 class="headerbar">'.get_string('events','comity').'</h3><ul>';

foreach($agendas as $agenda){
$url = "$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=" . $agenda->id . "&selected_tab=" . 1;
 print '<li><a href="'.$url.'" >'.toMonth($agenda->month) ." ".$agenda->day.", ".$agenda->year.'</a></li>';
}

print '</div>';
}

?>