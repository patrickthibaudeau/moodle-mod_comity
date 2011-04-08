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

$id = required_param('planner',PARAM_INT);    // Planner ID
$responses = optional_param('responses','',PARAM_RAW);

$planner = $DB->get_record('comity_planner', array('id'=>$id));

$memberobj = $DB->get_record('comity_members', array('user_id'=>$USER->id,'comity_id'=>$planner->comity_id));
$planneruserobj = $DB->get_record('comity_planner_users', array('comity_member_id'=>$memberobj->id,'planner_id'=>$id));

$dates = $DB->get_records('comity_planner_dates', array('planner_id'=>$id));
foreach($dates as $date){
    $DB->delete_records('comity_planner_response', array('planner_user_id'=>$planneruserobj->id,'planner_date_id'=>$date->id));
}

if(is_array($responses)){

foreach($responses as $key => $value){
    $responseobj = new stdClass;
    $responseobj->planner_user_id = $planneruserobj->id;
    $responseobj->planner_date_id = $key;
    $responseobj->response = 1;
    $DB->insert_record('comity_planner_response', $responseobj);
}
}
echo '<script type="text/javascript">';
echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/planner.php?id='.$planner->comity_id.'";';
echo '</script>';

?>
