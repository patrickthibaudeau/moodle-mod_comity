<?php

/// This file is part of Moodle - http://moodle.org/
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
 * The form for the "Motions By Year" tab for the Agenda/Meeting Extension to Committee Module.
 *
 *          **List View
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->dirroot/mod/comity/meetingagenda/lib.php");
require_once("$CFG->dirroot/mod/comity/meetingagenda/topic_by_year_sidebar.php");

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
if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin' ||$credentials == 'member') {

create_topics_table('',$comity_id,$event_id,$selected_tab);

}

/*
 * A function to check if the current status should be selected in the dropdown menu
 *
 * @param string $mode The dropdown value.
 * @param string $status The database value for the topic status.
 *
 * @return string Returns selected if the $mode matches the $status
 */

function checkSelected($mode,$status) {

    if($mode == $status){
        return "selected";
    } else {
        return "";
    }


}


/*
 * A function to create the table for open topics.
 * No. | Date | Topic Title | Status | Save Image(if editable)
 *
 * @param string $control If equals edit, submit button is added for submission
 *
 */
function create_topics_table($control,$comity_id,$event_id,$selected_tab){

    global $DB,$CFG,$is_viewer;

   
$sql = "SELECT min(e.year) as minyear, max(e.year) as maxyear from {comity_events} e WHERE e.comity_id = $comity_id";
$record =  $DB->get_record_sql($sql, array());
$start_year = $record->minyear;
$end_year = $record->maxyear;

if(!$start_year){
    $start_year = 99999;
}
if(!$end_year){
    $start_year = -1;
}

print '<center><table>';

//from min year to max year print topics
for($year=$start_year;$year<=$end_year;$year++){

$sql = "SELECT DISTINCT t.*, e.day, e.month, e.year, e.id as EID FROM {comity_agenda} a, {comity_agenda_topics} t, {comity_events} e ".
        "WHERE t.comity_agenda = a.id AND e.id = a.comity_events_id AND e.comity_id = a.comity_id ".
        "AND a.comity_id = $comity_id AND e.year = $year AND t.hidden <> 1 ".
        "ORDER BY year ASC, month DESC, day DESC";

//Get topics from current $YEAR
$topics =  $DB->get_records_sql($sql, array(), $limitfrom=0, $limitnum=0);


print '<tr><td><h3>'.$year."</h3></td><td> <a name=\"year_$year\"></a></td><td></td></tr>";

if($topics){ //check if any topics actually exist

    //possible topic status:
    $topic_statuses = array('open'=>get_string('topic_open', 'comity'),
                            'in_progress'=>get_string('topic_inprogress', 'comity'),
                            'closed'=>get_string('topic_closed', 'comity'));





$index=1;
foreach($topics as $key=>$topic){

//$this->topicNames[$index] = $topic->title;


//-----LINK TO AGENDA-----------------------------------------------------------
$url = "$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=" . $topic->eid . "&selected_tab=" . 3;
//$mform->addElement('html','<div class="agenda_link_topic"><li><a href="'.$url.'" >'.toMonth($topic->month) ." ".$topic->day.", ".$topic->year.'</a></li></div>');


print "<tr><td>$index. </td>";

print '<td><a href="'.$url.'" >'.toMonth($topic->month) ." ".$topic->day.", ".$topic->year.'</a>';
print '</td><td>';

print $topic->title."</td>";

$status = $topic->status;


 print "<td>".$topic_statuses[$topic->status]."</td></tr>";




$index++;

}//end foreach topic



}//end topics

}
print '</table></center>';
}




