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

$id = required_param('id',PARAM_INT);    // Course Module ID
$edit = optional_param('edit',0,PARAM_INT);   //If editing, this points to the planner id

$name = required_param('name',PARAM_TEXT);  // Planner name
$description = optional_param('description',null,PARAM_TEXT);    // Planner description
$user_rules = optional_param('rule',null,PARAM_RAW);     // Array of member id's + rule
$dates = optional_param('list',null,PARAM_RAW);      // Array of all potential dates


//First add entry to comity_planner table
$planner = new stdClass;
$planner->name = $name;
$planner->description = $description;
if($edit != 0){
    $planner->id = $edit;
    $DB->update_record('comity_planner',$planner);
    $planner_id = $edit;
}
else{
    $planner->active = 1;
    $planner->comity_id = $id;
    $planner_id = $DB->insert_record('comity_planner', $planner);
}


//Next add entries to comity_planner_users table
foreach($user_rules as $member_id => $rule){
    //If updating
    if($currentuser = $DB->get_record('comity_planner_users', array('planner_id'=>$planner_id,'comity_member_id'=>$member_id))){
        $currentuser->rule = $rule;
        $DB->update_record('comity_planner_users', $currentuser);
    }
    //New entry
    else{
        $planner_user = new stdClass;
        $planner_user->planner_id = $planner_id;
        $planner_user->comity_member_id = $member_id;
        $planner_user->rule = $rule;

        $DB->insert_record('comity_planner_users', $planner_user);
    }
}

//Finally add entries to comity_planner_dates table
//If Editing, get all existing entries, remove them later (easier this way)
if($edit != 0){
    $current_dates = $DB->get_records('comity_planner_dates', array('planner_id'=>$planner_id));
}

foreach($dates as $date){
    $date_arr = explode('@',$date);
    
    $date_raw = explode('/',$date_arr[0]);
    $day = $date_raw[0];
    $month = $date_raw[1];
    $year = $date_raw[2];

    $from_raw = $date_arr[1];
    $to_raw = $date_arr[2];

    $from_string = $year.'-'.$month.'-'.$day.' '.$from_raw;
    $to_string = $year.'-'.$month.'-'.$day.' '.$to_raw;

    $from_time = strtotime($from_string);
    $to_time = strtotime($to_string);

    $planner_date = new stdClass;
    $planner_date->planner_id = $planner_id;
    $planner_date->from_time = $from_time;
    $planner_date->to_time = $to_time;

    $newid = $DB->insert_record('comity_planner_dates',$planner_date);
    //echo 'New Date ID: '.$newid.'<br/>';

    if($olddate = $DB->get_record_select('comity_planner_dates', "planner_id='$planner_id' AND from_time='$from_time' AND to_time='$to_time' AND id<>'$newid'")){
        $responseobjects = $DB->get_records('comity_planner_response',array('planner_date_id'=>$olddate->id));
        foreach($responseobjects as $response){
            $response->planner_date_id = $newid;
            //echo 'Response : ';
            //print_object($response);
            $DB->update_record('comity_planner_response',$response);
        }
    }
}

//If editing, remove all old entries
if($edit != 0){
    foreach($current_dates as $current_date){
        $DB->delete_records('comity_planner_dates', array('id'=>$current_date->id));
    }
}

echo '<script type="text/javascript">';
echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/planner.php?id='.$id.'";';
echo '</script>';

?>
