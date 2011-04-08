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
 * The form for the "Motions By Year", but only with viewing permissions, for the Agenda/Meeting Extension to Committee Module.
 *
 * **DEPRECATED: Detailed View
 *              -Detailed View functionality was removed from release version
 *              -Replaced with list view only (Can be re-enabled by removing commented sections from view.php & viewer.php)
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once("$CFG->dirroot/mod/comity/meetingagenda/moodle_user_selector.php");
require_once("$CFG->dirroot/mod/comity/meetingagenda/lib.php");
require_once("$CFG->dirroot/mod/comity/meetingagenda/agenda_link.css");

class mod_agenda_motions_by_year_form extends moodleform {

    private $instance;
    private $event_id;
    private $agenda_id;
    private $comity_id;
    private $default_toform;

    private $topicNames; //Used by menu to determine which html anchor points are to what name

function __construct($event_id, $agenda_id, $comity_id, $cm) {
$this->event_id = $event_id;
$this->agenda_id = $agenda_id;
$this->comity_id = $comity_id;
$this->instance = $cm;
parent::__construct();
    }

function definition() {

global $DB,$CFG;

$mform = & $this->_form;

//object to contain default values
$toform = new stdClass();
$exclusion_id = array();

//--variable convience--
$event_id = $this->event_id;
$agenda_id = $this->agenda_id;
$comity_id = $this->comity_id;
$instance = $this->instance;

$commityRecords = $DB->get_records('comity_agenda_members', array('comity_id' => $comity_id), '', '*', $ignoremultiple = false);

//print_object($commityRecords);

//--------Comittee Members------------------------------------------------------
$comitymembers = array();//Used to store commitee members in an array
if ($commityRecords) {

    $index = 0;
    foreach ($commityRecords as $member) {
        $name = $this->getUserName($member->user_id);
        $toform->participant_name[$index] = $name.": ";
        $comitymembers[$member->id] = $name;
    }
}

     //possible motion status
    $motion_result = array( '-1'=>'-----',
                            '1'=>'<font color="#4AA02C">'.get_string('motion_accepted', 'comity').'</font>',
                           '0'=>get_string('motion_rejected', 'comity'));

//Max/Min Years for all motions of the committee
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

$index = 1;
//For each $year get motions for the committee
for($year=$start_year;$year<=$end_year;$year++){


//$sql = "SELECT DISTINCT m.*, e.day, e.month, e.year, e.id as EID ".
   //     "FROM {comity_agenda} a, {comity_agenda_motions} m, {comity_events} e ".
    //    "WHERE m.comity_agenda = a.id AND a.comity_id = e.comity_id AND a.comity_events_id = e.id ".
      //  "AND a.comity_id = $comity_id AND e.year = $year ".
        //"ORDER BY year ASC, month ASC, day ASC";

$sql = "SELECT DISTINCT m.*, e.day, e.month, e.year, e.id as EID ".
        "FROM {comity_agenda} a, {comity_agenda_motions} m, {comity_events} e, {comity_agenda_topics} t ".
        "WHERE m.comity_agenda = a.id AND a.comity_id = e.comity_id AND a.comity_events_id = e.id ".
        "AND a.comity_id = $comity_id AND e.year = $year AND t.id=m.comity_agenda_topics AND ".
        "t.comity_agenda = m.comity_agenda AND t.comity_agenda=a.id AND t.hidden <> 1 ".
        "ORDER BY year ASC, month ASC, day ASC";

$motions =  $DB->get_records_sql($sql, array(), $limitfrom=0, $limitnum=0);


//-----MOTIONS------------------------------------------------------------------
//------------------------------------------------------------------------------
if($motions){

    $mform->closeHeaderBefore('YEAR');
    $mform->addElement('html', "<a name=\"year_$year\"></a>");
    $mform->addElement('header', "YEAR","$year");
    $motion_index=1;

    foreach($motions as $key=>$motion){

        $proposing_choices = $comitymembers;
        $supporting_choices = $comitymembers;

        $proposing_choices['-1']=get_string('proposedby', 'comity');
        $supporting_choices['-1']=get_string('supportedby', 'comity');

        $mform->addElement('static', "", "", "<h6>".get_string('motion', 'comity')." $motion_index:</h6>");

//-----LINK TO TOPIC'S AGENDA-----------------------------------------------------------
$url = "$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=" . $motion->eid . "&selected_tab=" . 3;
$mform->addElement('html','<div class="agenda_link_motion"><li><a href="'.$url.'">'.toMonth($motion->month) ." ".$motion->day.", ".$motion->year.'</a></li></div>');

        //$mform->addElement('static', "proposition[$index][$motion_index]", get_string('motion_proposal', 'comity'), array('size'=>'50'));

        $notes = print_collapsible_region($motion->motion, 'motion_proposal', "proposition_".$index."_".$motion_index, get_string('motion_proposal', 'comity'), $userpref = false, $default = false, $return = true);
$mform->addElement('html', $notes);

        $mform->addElement('static', "proposed[$index][$motion_index]", get_string('motion_by', 'comity') , $proposing_choices, $attributes=null);
        $mform->addElement('static', "supported[$index][$motion_index]", get_string('motion_second', 'comity') , $supporting_choices, $attributes=null);





$votes = array();
    $votes[] =$mform->createElement('static', "aye_label[$index][$motion_index]", "", "Aye: ");
    $votes[] =$mform->createElement('static', "aye[$index][$motion_index]", '', array('size'=>'1 em','maxlength'=>"3"));
    $votes[] =$mform->createElement('static', "nay_label[$index][$motion_index]", "", "Nay: ");
    $votes[] =$mform->createElement('static', "nay[$index][$motion_index]", '', array('size'=>'1 em','maxlength'=>"3"));
    $votes[] =$mform->createElement('static', "abs_label[$index][$motion_index]", "", "Abs: ");
    $votes[] =$mform->createElement('static', "abs[$index][$motion_index]", '', array('size'=>'1 em','maxlength'=>"3"));
    $votes[] =$mform->createElement('static', "unanimous[$index][$motion_index]", '', "");

    $mform->addGroup($votes, "motion_votes[$index][$motion_index]", get_string('motion_votes', 'comity') , array(' '), false);

  $results = array();
    $results[] = $mform->createElement('static', "motion_result[$index][$motion_index]", '', $motion_result, $attributes=null);
    $mform->addGroup($results, "result[$index][$motion_index]", get_string('motion_outcome', 'comity') , array(' '), false);


$mform->addElement('hidden', "motion_ids[$index][$motion_index]", $motion->id);
$mform->addElement('html', "</br>");


//-------DEFAULT VALUEs FOR MOTIONS---------------------------------------------

$toform->proposition[$index][$motion_index] = $motion->motion;

if(isset($motion->motionby)){
$mform->setDefault("proposed[$index][$motion_index]", $proposing_choices[$motion->motionby]);
} else {
$mform->setDefault("proposed[$index][$motion_index]", "");
}

if(isset($motion->secondedby)){
 $mform->setDefault("supported[$index][$motion_index]", $supporting_choices[$motion->secondedby]);
} else {
$mform->setDefault("supported[$index][$motion_index]", "");
}

if(isset($motion->carried)){
 $mform->setDefault("motion_result[$index][$motion_index]", $motion_result[$motion->carried]);
} else {
$mform->setDefault("motion_result[$index][$motion_index]", "");
}


if(isset($motion->unanimous)){

$toform->unanimous[$index][$motion_index] = "(".get_string('unanimous','comity').")";
}

$toform->aye[$index][$motion_index] = $motion->yea;
$toform->nay[$index][$motion_index] = $motion->nay;
$toform->abs[$index][$motion_index] = $motion->abstained;


//-------------SET Highest vote as bolded---------------------------------------
if($motion->yea > $motion->nay){
    if($motion->yea > $motion->abstained){
        $toform->aye[$index][$motion_index] = "<b>".$toform->aye[$index][$motion_index]."</b>";
        $toform->aye_label[$index][$motion_index] = "<b>Aye: </b>";
    } elseif($motion->abstained!=$motion->yea) {
$toform->abs[$index][$motion_index] = "<b>".$toform->abs[$index][$motion_index]."</b>";
$toform->abs_label[$index][$motion_index] = "<b>Abs: </b>";
    }
} else{
    if($motion->nay > $motion->abstained){
$toform->nay[$index][$motion_index] = "<b>".$toform->nay[$index][$motion_index]."</b>";
$toform->nay_label[$index][$motion_index] = "<b>Nay: </b>";
    } elseif($motion->abstained!=$motion->nay) {
$toform->abs[$index][$motion_index] = "<b>".$toform->abs[$index][$motion_index]."</b>";
$toform->abs_label[$index][$motion_index] = "<b>Abs: </b>";
    }

}

 $mform->addGroupRule("motion_votes[$index][$motion_index]",
 array("aye[$index][$motion_index]" => array(array(get_string('numeric_only','comity'), 'numeric', null, 'client', false, true)),
       "nay[$index][$motion_index]" => array(array(get_string('numeric_only','comity'), 'numeric', null, 'client', false, true)),
       "abs[$index][$motion_index]" => array(array(get_string('numeric_only','comity'), 'numeric', null, 'client', false, true))
     ));

$mform->addElement('static', "</br></br>");
//---------END DEFAULTS---------------------------------------------------------

$motion_index++;
    }

$index++;
}



    }

//Hidden Variables
$mform->addElement('hidden', 'event_id', '');
$mform->addElement('hidden', 'selected_tab', '');

//Set defaults to private variable
$this->default_toform = $toform;
    }

/*
 * Returns the default values for the form.
 */
    function getDefault_toform() {
        return $this->default_toform;
    }

/*
 * Converts a given moodle ID into a FirstName LastName String.
 *
 *  @param $int $userID An unique moodle ID for a moodle user.
 */
    function getUserName($userID){
    Global $DB;

    $user = $DB->get_record('user', array('id' => $userID), '*', $ignoremultiple = false);
    $name = null;
    if($user){
    $name = $user->firstname . " " . $user->lastname;
    }
    return $name;
    }

/*
 * Returns An array of topic names with array keys being the index that the topic
 * is on the page. Used for menu sidebar creation.
 */
    function getIndexToNamesArray(){
        return $this->topicNames;
    }


}
