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
 * The form for the business arising tab, but only with viewing permissions, for the Agenda/Meeting Extension to Committee Module.
 *
 *      **LIST VIEW
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->dirroot/mod/comity/meetingagenda/lib.php");


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
if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin') {

create_topics_table('edit',$comity_id,$event_id,$selected_tab);

//---------TOPICS---------------------------------------------------------------



} elseif($credentials == 'member') {


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

    if($control=='edit'){
    $post_url = "update_topic_status.php";
    } else {
    $post_url = "";
    }

    $sql = "SELECT DISTINCT t.*, e.day, e.month, e.year, e.id as EID FROM {comity_agenda} a, {comity_agenda_topics} t, {comity_events} e ".
        "WHERE t.comity_agenda = a.id AND e.id = a.comity_events_id AND e.comity_id = a.comity_id ".
        "AND a.comity_id = $comity_id AND t.status <> 'closed' ".
        "ORDER BY year ASC, month ASC, day ASC";

$topics =  $DB->get_records_sql($sql, array(), $limitfrom=0, $limitnum=0);




if($topics){ //check if any topics actually exist

    //possible topic status:
    $topic_statuses = array('open'=>get_string('topic_open', 'comity'),
                            'in_progress'=>get_string('topic_inprogress', 'comity'),
                            'closed'=>get_string('topic_closed', 'comity'));


print '<center><table>';


$index=1;
foreach($topics as $key=>$topic){

//$this->topicNames[$index] = $topic->title;


//-----LINK TO AGENDA-----------------------------------------------------------
$url = "$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=" . $topic->eid . "&selected_tab=" . 3;
//$mform->addElement('html','<div class="agenda_link_topic"><li><a href="'.$url.'" >'.toMonth($topic->month) ." ".$topic->day.", ".$topic->year.'</a></li></div>');


print '<tr><form method="post" action="'.$post_url.'">';


print "<td>$index. </td>";

print '<td><a href="'.$url.'" >'.toMonth($topic->month) ." ".$topic->day.", ".$topic->year.'</a>';
print '</td><td>';

print $topic->title."</td>";

$status = $topic->status;


if($control=='edit'){
print '<td><select name="status" id="comity_status_selector_'.$index.'" onchange=\'change("'.$topic->status.'",'."$index".')\'>';
print '<option value="open" '.checkSelected("open",$topic->status).'>'.$topic_statuses['open'].'</option>';
print '<option value="in_progress" '.checkSelected("in_progress",$topic->status).'>'.$topic_statuses['in_progress'].'</option>';
print '<option value="closed" '.checkSelected("closed",$topic->status).'>'.$topic_statuses['closed'].'</option>';

print '</select></td><td>';

print '<input type="image" id="save_image_'.$index.'" SRC="../pix/save.png" VALUE="Submit now"/></td>';


print<<<HERE
<script type='text/javascript'>
document.getElementById('save_image_$index').style.visibility = "hidden";




</script>
HERE;




} else {

 print "<td>".$topic_statuses[$topic->status]."</td>";

}

if(isset($is_viewer)){
$return_url = $CFG->wwwroot."/mod/comity/meetingagenda/viewer.php?comity_id=".$comity_id."&selected_tab=".$selected_tab;
} else {
$return_url = $CFG->wwwroot."/mod/comity/meetingagenda/view.php?event_id=".$event_id."&selected_tab=".$selected_tab;
}


print '<input type="hidden" name="return_url" value="'.$return_url.'"/> ';
print '<input type="hidden" name="topic_id" value="'.$topic->id.'"/> ';
print '</form></tr>';


$index++;

}//end foreach topic

print '</table></center>';

}//end topics

}

print<<<HERE
<script type='text/javascript'>

//Function to change current visibility state of an src submit image.
//The image is hidden if the current selected value is not the same as the old_value
//
//@param string old_value The original/default value of the html dropdown menu
//@param int index The current instance of the image.

function change(old_value,index){

var select = document.getElementById('comity_status_selector_'+index);

var selected_value = select.options[select.selectedIndex].value;

if(selected_value != old_value){
document.getElementById('save_image_'+index).style.visibility = "visible";
 } else {
document.getElementById('save_image_'+index).style.visibility = "hidden";
}
}
</script>
HERE;




