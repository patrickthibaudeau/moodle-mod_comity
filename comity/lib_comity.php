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

/**
 * Gets Firstname Lastname and email of comity members.
 *
 * @global object
 * @param id The comity id
 * @return array of users
 */
function get_comity_members($id){
    global $DB;
    $membersql = "SELECT {user}.id, {user}.firstname, {user}.lastname, {user}.email
                  FROM {user} INNER JOIN {comity_members} ON {user}.id = {comity_members}.user_id
                  WHERE {comity_members}.comity_id = $id";

    $members = $DB->get_records_sql($membersql);

    return $members;

}

function print_content_header($style) {
    $styles = 'style="'.$style.'"';
    echo '<div class="cornerBox" '.$styles.'>'."\n";
    echo '<div class="corner TL"></div>'."\n";
    echo '<div class="corner TR"></div>'."\n";
    echo '<div class="corner BL"></div>'."\n";
    echo '<div class="corner BR"></div>'."\n";
    echo '	<div class="cornerBoxInner">'."\n";

    return;
}

function print_content_footer() {
    echo ' </div>'."\n"; //cornerbox inner 1
    echo '</div>'."\n"; //cornerbox

    return;
}

function print_inner_content_header($style) {

    $styles = 'style="'.$style.'"';
    echo '<div class="lightcornerBox" '.$styles.'">'."\n";
    echo '<div class="lightcorner TL"></div>'."\n";
    echo '<div class="lightcorner TR"></div>'."\n";
    echo '<div class="lightcorner BL"></div>'."\n";
    echo '<div class="lightcorner BR"></div>'."\n";
    echo '	<div class="cornerBoxInner">'."\n";

    return;
}

function print_inner_content_footer() {
    echo ' </div>'."\n"; //lightcornerbox inner
    echo '</div>'."\n"; //lightcornerbox

    return;
}

function comity_printnavbar($id) {
    global $CFG;

    echo '<table width=100% border=0>';
    echo '<tr><td>';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/view.php?id='.$id.'">'.get_string('members', 'comity').'</a> ';
    echo '</td></tr>';
    echo '<tr><td>';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/events.php?id='.$id.'">'.get_string('events', 'comity').'</a>';
    echo '</td></tr>';
    echo '<tr><td>';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'">'.get_string('files', 'comity').'</a>';
    echo '</td></tr>';
    echo '<tr><td>';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/planner.php?id='.$id.'">'.get_string('planner', 'comity').'</a>';
    echo '</td></tr>';
    echo '<tr><td>';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/meetingagenda/viewer.php?comity_id='.$id.'">'.get_string('agendas', 'comity').'</a>';
    echo '</td></tr>';
    echo '</table>';
}

function comity_header($comity,$pagename,$pagelink) {
    global $PAGE,$OUTPUT,$CFG,$DB,$style_nav,$style_content;

    $course_mod = $DB->get_record('course_modules', array('id'=>$comity));
    $comity_instance = $DB->get_record('comity', array('id'=>$course_mod->instance));

    $comity_name = $comity_instance->name;

    $context = get_context_instance(CONTEXT_MODULE, $comity);
    //print_r($context);

    //Print header
    $page = get_string($pagename, 'comity');
    $title = $comity_name . ': ' . $page;

    $navlinks = array(
            array('name' => get_string($pagename,'comity'), 'link' => $CFG->wwwroot.'/mod/comity/'.$pagelink, 'type' => 'misc')
    );
    $navigation = build_navigation($navlinks);

    //$PAGE->set_context();
    $PAGE->set_url('/mod/comity/'.$pagelink);
    $PAGE->set_title($comity_name);
    $PAGE->set_heading($title);
    echo $OUTPUT->header();

//---------------CONTENT---------------//

    echo '<link rel="stylesheet" type="text/css" href="style.php">';

    print_content_header('');

    print_inner_content_header($style_nav);

    comity_printnavbar($comity);

    print_inner_content_footer();

    print_inner_content_header($style_content);
}

function comity_check($id) {
    global $USER,$DB;

    // checks
    if ($id) {
        if (! $cm = get_coursemodule_from_id('comity', $id)) {
            print_error("Course Module ID was incorrect");
        }

        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error("Course is misconfigured");
        }

        if (! $comity = $DB->get_record("comity", array("id"=>$cm->instance))) {
            print_error("Course module is incorrect");
        }

    } else {
        if (! $comity = $DB->get_record("comity", array("id"=>$l))) {
            print_error("Course module is incorrect");
        }
        if (! $course = $DB->get_record("course", array("id"=>$comity->course))) {
            print_error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("comity", $comity->id, $course->id)) {
            print_error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    //context needed for access rights
    $context = get_context_instance(CONTEXT_USER, $USER->id);

    global $cm;
}

function comity_isadmin($id) {
    global $DB,$USER;
    
    if($DB->get_record('comity_members', array('comity_id'=>$id,'role_id'=>2,'user_id'=>$USER->id))
            || $DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>1, 'user_id'=>$USER->id))
            || $DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>4, 'user_id'=>$USER->id))
            || is_siteadmin()) {
        return true;
    }
    else {
        return false;
    }
}

/*
 * Given an comity id check to determine if the user is a member of the committee.
 * Does not check for just committee role of member, but any role of member, president, Vpresident, and admin.
 *
 */
function comity_isMember($id) {
    global $DB,$USER;

    if($DB->get_record('comity_members', array('comity_id'=>$id,'role_id'=>2,'user_id'=>$USER->id))
            || $DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>1, 'user_id'=>$USER->id))
            || $DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>3, 'user_id'=>$USER->id))
            || $DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>4, 'user_id'=>$USER->id))) {
            
        return true;
    
            } else {
        return false;
    }
}

function comity_footer() {
    global $OUTPUT;
    print_inner_content_footer();

    echo '<div style="clear:both;"></div>';

    print_content_footer();
    
    echo $OUTPUT->footer();
}

function breadcrumb($folderid) {
    global $DB,$id,$CFG;

    $folderobj = $DB->get_record('comity_files',array('id'=>$folderid));

    if($folderid==0) {
        $obj = new stdClass();
        $obj->name = get_string('root','comity');
        $obj->url = $CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file=0';
        $obj->private = 0;
        //Make array (easier to add objects after)
        $returned[] = $obj;
        return $returned;
    }
    else {
        $obj = new stdClass();
        $obj->name = $folderobj->name;
        $obj->url = $CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file='.$folderobj->id;
        $obj->private = $folderobj->private;

        $returned = breadcrumb($folderobj->parent);
        //Add new object to array
        array_push($returned,$obj);

        return $returned;
    }
}

/**
 * Returns markup for dropdown for time selection.
 *
 * @param string $name name and id to give our form element
 * @param int $time timestamp to match pre-selected value
 *
 * return string with HTML markup ready for output
 */
function render_timepicker($name,$time) {
    $hourtime = date('G',$time);
    $minutetime = date('i',$time);
    if($minutetime<10){
        $minutetime = 10;
    }
    else if($minutetime>=50){
        $hourtime += 1;
        $minutetime = 0;
    }
    else{
        $minutetime = ceil($minutetime/10)*10;
    }

    $string = '<select name="'.$name.'_time" id="'.$name.'_time">';
    for($hour=0;$hour<=23;$hour++){
        for($minutes=0;$minutes<60;$minutes+=10){
            if($minutes==0){
                $output = '00';
            }
            else{
                $output = $minutes;
            }
            $string .= '<option value="'.$hour.':'.$output.'" ';
            if($hourtime==$hour && $minutetime==$minutes){
                $string .= 'SELECTED';
            }
            $string .= '>'.$hour.':'.$output.'</option>';
        }
    }
    $string .= '</select>';

    return $string;
}

/*
 * Function to display the planner results as a table
 *
 * @param int $planner_id The database id for the planner.
 */
function display_planner_results($planner_id, $comity_id){

    global $DB, $USER;

    $planner = $DB->get_record('comity_planner', array('id'=>$planner_id));

//content
echo '<table class="generaltable" style="margin-left:0;">';
echo '<tr>';
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

    for($i=0;$i<$count;$i++) {

        if($DB->get_record('comity_planner_response', array('planner_user_id'=>$member->id, 'planner_date_id'=>$date_col[$i]))){
            $date_col_count[$i]++;
        }
        else if($member->rule==1){
            $date_flag[$i] = true;
        }

    }
    echo '</tr>';
}

echo '<tr>';

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
    echo '<td class="cell" style="font-size:10px;height:20px;background-color:'.$background.';">'.
    '<center><form method="post" action="planner_to_event_script.php">'.$percentage.'%';
    

    if(comity_isadmin($comity_id) && $percentage > 0){
    
     echo ' <input type="image" src="pix/accept.png" />';
     echo '<input type="hidden" name="date_id" value ="'.$date_col[$i].'"/>';
     echo '<input type="hidden" name="comity_id" value ="'.$comity_id.'"/>';
     echo '</form>';

    }

    echo '</center></td>';
}
echo '</tr>';



echo '</table>';

}

$style_nav = 'width: 29%;float: left;margin-right: 1%';
$style_content = 'width: 69%;float: left;margin-left: 1%';

?>
