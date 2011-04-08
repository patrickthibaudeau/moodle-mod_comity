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
 * The sidebar of the Agenda/Meeting Extension to Committee Module.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/mod/comity/meetingagenda/business_menu_style.css");
require_once("$CFG->dirroot/mod/comity/meetingagenda/lib.php");

//START DIV FOR CSS
print '<div class="buisness_side_menu">';



//-------AGENDA MENU FOR VIEWER-------------------------------------------------
//------------------------------------------------------------------------------

if(isset($is_viewer)){ //set from viewer.php

$sql = "SELECT ce.id, ce.year, ce.year, ce.month, ce.day ".
"FROM {comity_agenda} ca, {comity_events} ce ".
"WHERE ce.comity_id = ? AND ce.id=ca.comity_events_id ".
"AND ce.comity_id=ca.comity_id ".
"ORDER BY year DESC, month DESC, day DESC";

$agendas = $DB->get_records_sql($sql, array($comity_id,$event_id), $limitfrom=0, $limitnum=0);

print '<h3 class="headerbar">'.get_string('agendas','comity').'</h3><ul>';

foreach($agendas as $agenda){
$url = "$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=" . $agenda->id . "&selected_tab=" . 3;
 print '<li><a href="'.$url.'" >'.toMonth($agenda->month) ." ".$agenda->day.", ".$agenda->year.'</a></li>';
}
}


//-----TOPIC NAMES--------------------------------------------------------------
$topics = $mform->getIndexToNamesArray(); //Get names of topics from data stored in form
//------------------------------------------------------------------------------

//Start html DIV

if(!$topics){
   $topics = array();
}

//-----------TOPIC MENU---------------------------------------------------------
//------------------------------------------------------------------------------

print '<h3 class="headerbar">'.get_string('menu_topics','comity').'</h3><ul>';

//Create a list of <a> tags that link to anchor points
foreach($topics as $index=>$topic){

    print '<li><div class="comity_side_li"><a href="'."#topic_$index".'">'."$index. $topic".'</a></div></li>';
    }

//end list
print '</ul>';


//------------PREVIOUS AGENDAS MENU---------------------------------------------
//------------------------------------------------------------------------------

//Check if event is set: viewer.php does not use event_id and therefore we ignore
//this section of menu if its not set
if(isset($event_id)){

$current_event = $DB->get_record('comity_events', array('id'=>$event_id));



//DOWNLOAD AGENDA LINK -- Replaced with button
//print '<h3 class="headerbar">'."PDF".'</h3><ul>';
//$url = "$CFG->wwwroot/mod/comity/meetingagenda/pdf_script.php?event_id=" . $event_id;
//print '<li><a href="'.$url.'" target="_blank">'."Download".'</a></li>';


$day = $current_event->day;
$month = $current_event->month;
$year = $current_event->year;
$start_hour = $current_event->starthour;

//PREVIOUS MINUTES UP TO, but not including current agenda (ordered by most recent to oldest)
$sql = "SELECT ce.id, ce.year, ce.year, ce.month, ce.day ".
"FROM {comity_agenda} ca, {comity_events} ce ".
"WHERE ce.comity_id = ? AND ce.id=ca.comity_events_id AND ca.id IS NOT NULL ".
"AND ca.comity_events_id <> ? AND ce.comity_id=ca.comity_id ".
"AND ((ce.year < $year) OR (ce.year = $year AND ce.month < $month) ".
"OR (ce.year = $year AND ce.month = $month AND ce.day < $day ) ".
"OR (ce.year = $year AND ce.month = $month AND ce.day = $day AND ce.endhour < $start_hour )) ".
"ORDER BY year DESC, month DESC, day DESC";


$agendas = $DB->get_records_sql($sql, array($comity_id,$event_id), $limitfrom=0, $limitnum=0);

print '<h3 class="headerbar">'.get_string('other_agendas','comity').'</h3><ul>';

foreach($agendas as $agenda){
$url = "$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=" . $agenda->id . "&selected_tab=" . $selected_tab;
 print '<li><a href="'.$url.'">'.toMonth($agenda->month) ." ".$agenda->day.", ".$agenda->year.'</a></li>';
}




}
print '</div>';

?>
