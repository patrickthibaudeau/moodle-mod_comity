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
 * Sidebar for the topics by year.
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/mod/comity/meetingagenda/business_menu_style.css");
require_once("$CFG->dirroot/mod/comity/meetingagenda/lib.php");

//-----TOPIC NAMES--------------------------------------------------------------

print '<div class="buisness_side_menu">';
print '<h3 class="headerbar">'.get_string('menu_topics','comity').'</h3><ul>';

//Get minimum and Max Year for events
$sql = "SELECT min(e.year) as minyear, max(e.year) as maxyear from {comity_events} e WHERE e.comity_id = $comity_id";
$record =  $DB->get_record_sql($sql, array());
$start_year = $record->minyear;
$end_year = $record->maxyear;

//make a achor link for each year inbetween the max and min
for($year=$start_year;$year<=$end_year;$year++){
 print '<li><a href="'."#year_$year".'">'."$year".'</a></li>';
}

print '</ul>'; //end list


print '</div>'; //end div

?>
