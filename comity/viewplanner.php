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

$id = optional_param('id',0,PARAM_INT);    // Planner ID
$planner = $DB->get_record('comity_planner', array('id'=>$id));

// print header
comity_check($planner->comity_id);
comity_header($planner->comity_id,'planner','viewplanner.php?id='.$id);

//content
echo '<form method="POST" action="responses.php">';
echo '<input type="hidden" name="planner" value="'.$id.'">';
echo '<table class="generaltable" style="margin-left:0;">';
echo '<tr>';
echo '<th class="header"></th>';
$dates = $DB->get_records('comity_planner_dates', array('planner_id'=>$planner->id), 'to_time ASC');
$count = 0;
$date_col = array();        //Keep track of what id is which column
$date_col_count = array();  //Keep track of how many people can make this date
$date_flag = array();       //If a required person cannot make it, flag it here
foreach($dates as $date) {
    echo '<th class="header">'.strftime('%a %d %B, %Y', $date->from_time).'<br/>';
    echo '<span style="font-size:10px;font-weight:normal;">'.date('H:i', $date->from_time).' - '.date('H:i', $date->to_time).'</span>';
    echo '</th>';
    $date_col[$count] = $date->id;
    $date_flag[$count] = false; //Initialise
    $date_col_count[$count] = 0;    //Initialise
    $count++;
}
echo '</tr>';

$members = $DB->get_records('comity_planner_users', array('planner_id'=>$planner->id));
$numberofmembers = $DB->count_records('comity_planner_users', array('planner_id'=>$planner->id));
foreach($members as $member) {
    echo '<tr>';
    $memberobj = $DB->get_record('comity_members', array('id'=>$member->comity_member_id));
    $userobj = $DB->get_record('user', array('id'=>$memberobj->user_id));
    if($member->rule==1) {
        $style = 'font-weight:bold;';
    }
    else {
        $style = '';
    }
    echo '<td class="cell" style="'.$style.'">'.$userobj->firstname.' '.$userobj->lastname.'</th>';
    for($i=0;$i<$count;$i++) {
        echo '<td class="cell" style="text-align:center;"><input type="checkbox" ';
        if($USER->id != $userobj->id || $planner->active==0) {
            echo 'DISABLED ';
        }
        else {
            echo 'name="responses['.$date_col[$i].']" ';
        }
        if($DB->get_record('comity_planner_response', array('planner_user_id'=>$member->id, 'planner_date_id'=>$date_col[$i]))){
            echo 'CHECKED';
            $date_col_count[$i]++;
        }
        else if($member->rule==1){
            $date_flag[$i] = true;
        }
        echo '></td>';
    }
    echo '</tr>';
}

echo '<tr>';
echo '<td></td>';
for($i=0;$i<$count;$i++) {
    if($date_flag[$i]){
        $background = 'red';
        $percentage = '0';
    }
    else{
        $brilliance = ($date_col_count[$i])/($numberofmembers);
        $background = 'rgba(33,204,33,'.$brilliance.')';
        $percentage = number_format($brilliance*100,0);

    }
    echo '<td class="cell" style="font-size:10px;height:20px;background-color:'.$background.';">'.$percentage.'%</td>';
}
echo '</tr>';
echo '</table>';

if($planner->active != 0){
    echo '<input type="submit" value="'.get_string('save','comity').'">';
}
echo '<input type="button" value="'.get_string('back','comity').'" onclick="window.location.href=\''.$CFG->wwwroot.'/mod/comity/planner.php?id='.$planner->comity_id.'\';">';
echo '</form>';

//footer
comity_footer();

?>
