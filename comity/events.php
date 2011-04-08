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
 * @package   comity
 * @copyright 2010 Raymond Wainman, Patrick Thibaudeau (Campus St. Jean, University of Alberta)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once('lib.php');
require_once('lib_comity.php');
echo '<link rel="stylesheet" type="text/css" href="style.php">';

$id = optional_param('id',0,PARAM_INT);    // Course Module ID

comity_check($id);
comity_header($id,'events','events.php?id='.$id);

//If a person is not a member(ANY ROLE-pres, member, etc), no content shown.
/*
if(!comity_isMember($id)){
   print "<b>".get_string('not_member','comity')."</b>";
comity_footer();
exit();
}
*/
echo '<div><div class="title">'.get_string('events', 'comity').'</div>';

echo '<span class="content">';

//Current time stamp
$current_year = date('Y');
$current_month = date('m');
$current_day = date('d');
$current_hour = date('H');
$current_minutes = date('i');
$current_time = $current_year.$current_month.$current_day.$current_hour.$current_minutes.'00';

$now = time();

$events = $DB->get_records('comity_events', array('comity_id'=>$id), 'stamp_end ASC');

$nextevent = 0;
foreach($events as $event) {

    //needed to calculate the proper timestamp values for thebackground color
    $eventstart = $event->day.'-'.$event->month.'-'.$event->year.' '.$event->starthour.':'.$event->startminutes;
    $eventtimestamp = strtotime($eventstart);

    //Find out if event is the next one in line. If so change background color
    if (($eventtimestamp >= $now) && ($nextevent==0)) {
        $eventstyle= 'style=background-color:#FFFFC7';
        $nextevent = 1;
    } else {
        $eventstyle='';
    }
    //if($event->stamp_end>$current_time){
    //display event
    echo '<div class="file" '.$eventstyle.'>';
    echo '<table><tr><td>';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/export_event.php?event_id='.$event->id.'"><img id="icon" src="'.$CFG->wwwroot.'/mod/comity/pix/cal.png"></a>';
    echo '</td>';
    echo '<td style="padding:6px;">';
    echo '<b>'.get_string('summary', 'comity').' : </b>';
    echo $event->summary;
    if(comity_isadmin($id)) {
        echo ' - <a href="'.$CFG->wwwroot.'/mod/comity/delete_event_script.php?id='.$id.'&event_id='.$event->id.'" onClick="return confirm(\''.get_string('deleteeventquestion', 'comity').'\');"><img src="'.$CFG->wwwroot.'/mod/comity/pix/delete.gif"></a>';
        echo '<a href="edit_event.php?id='.$id.'&event_id='.$event->id.'"><img src="'.$CFG->wwwroot.'/mod/comity/pix/edit.gif"></a>';
    }
    echo '<br/>';
    echo '<b>'.get_string('date', 'comity').' : </b>';
    echo $event->day.' ';
    if($event->month == 1) {
        echo get_string('january', 'comity');
    }
    if($event->month == 2) {
        echo get_string('february', 'comity');
    }
    if($event->month == 3) {
        echo get_string('march', 'comity');
    }
    if($event->month == 4) {
        echo get_string('april', 'comity');
    }
    if($event->month == 5) {
        echo get_string('may', 'comity');
    }
    if($event->month == 6) {
        echo get_string('june', 'comity');
    }
    if($event->month == 7) {
        echo get_string('july', 'comity');
    }
    if($event->month == 8) {
        echo get_string('august', 'comity');
    }
    if($event->month == 9) {
        echo get_string('september', 'comity');
    }
    if($event->month == 10) {
        echo get_string('october', 'comity');
    }
    if($event->month == 11) {
        echo get_string('november', 'comity');
    }
    if($event->month == 12) {
        echo get_string('december', 'comity');
    }
    echo ', '.$event->year.'<br/>';

    echo '<b>'.get_string('starttime', 'comity').' : </b>';
    echo $event->starthour.':';
    if($event->startminutes <10) {
        echo '0'.$event->startminutes.'<br/>';
    }
    else {
        echo $event->startminutes.'<br/>';
    }

    echo '<b>'.get_string('endtime', 'comity').' : </b>';

    echo $event->endhour.':';
    if($event->endminutes <10) {
        echo '0'.$event->endminutes.'<br/>';
    }
    else {
        echo $event->endminutes.'<br/>';
    }

    echo '<b>'.get_string('description', 'comity').' : </b>';
    echo $event->description.'<br/>';

    echo '<br/>';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/export_event.php?event_id='.$event->id.'">'.get_string('addtocalendar','comity').'</a></br>';
	echo '<a href="'.$CFG->wwwroot.'/mod/comity/meetingagenda/view.php?event_id='.$event->id.'">'.get_string('meeting_agenda','comity').'</a>';
    echo '</td></tr></table>';
    echo '</div>';
    //}
}

echo '</span></div>';

if(comity_isadmin($id)) {
    echo '<div class="add">';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/add_event.php?id='.$id.'"><img src="'.$CFG->wwwroot.'/mod/comity/pix/switch_plus.gif'.'">'.get_string('addevent', 'comity').'</a>';
    echo '</div>';
}

comity_footer();

?>
