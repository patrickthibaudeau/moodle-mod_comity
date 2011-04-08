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
 * The class to represent an PDF version of the agenda.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/mod/comity/meetingagenda/lib.php");
require_once("tcpdf/tcpdf.php");

class pdf_creator  {

    private $instance;
    private $event_id;
    private $agenda_id;
    private $comity_id;
    private $default_toform;

    function __construct($event_id, $agenda_id, $comity_id, $cm) {
        $this->event_id = $event_id;
        $this->agenda_id = $agenda_id;
        $this->comity_id = $comity_id;
        $this->instance = $cm;
    }

function create_pdf($plain_pdf) {
global $DB,$CFG;

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//PDF SETTINGS
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(15);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setCellPaddings(1, 1, 1, 1);

//-- variable convience--
$event_id = $this->event_id;
$agenda_id = $this->agenda_id;
$instance = $this->instance;
$comity_id = $this->comity_id;


$event_record = $DB->get_record('comity_events', array('id' => $event_id), '*', $ignoremultiple = false);
$agenda = $DB->get_record('comity_agenda', array('id' => $agenda_id), '*', $ignoremultiple = false);
$comity = $DB->get_record("comity", array("id" => $instance)); // get comity record for our instance



//Add zero to minutes ex: 4 -> 04
$month = toMonth($event_record->month);

$date = $month . " " . $event_record->day . ", " . $event_record->year;
$time = $event_record->starthour . ":" . ZeroPaddingTime($event_record->startminutes) . "-" . $event_record->endhour . ":" . ZeroPaddingTime($event_record->endminutes);

//Find Duration
//Start TimeStamp
$eventstart = $event_record->day . '-' . $event_record->month . '-' . $event_record->year . ' ' . $event_record->starthour . ':' . $event_record->startminutes;
$Start = strtotime($eventstart);

//End TImestamp
$eventstart = $event_record->day . '-' . $event_record->month . '-' . $event_record->year . ' ' . $event_record->endhour . ':' . $event_record->endminutes;
$End = strtotime($eventstart);

//Durations in secs
$durationInSecs = $End - $Start;

//Assign value to duration
$duration = formatTime($durationInSecs);




$header_text = get_string('agenda_tab','comity');
$pdf->SetHeaderData('', '',$header_text, $comity->name."\n".$date);
$pdf->AddPage();

$pdf->ln(2);

$pdf->SetFont('helvetica', 'b', 10);
//$pdf->Cell(0, $h=0, get_string('general', 'form'), $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

$pdf->SetFont('helvetica', '', 10);
$pdf->ln();

//General

$pdf->Cell(0, $h=0, $txt=get_string('time_agenda','comity')." ".$time, $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

if(isset($duration)){
$pdf->Cell(0, $h=0, $txt=get_string('duration_agenda','comity')." ".$duration, $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
}

if(isset($agenda->location) && $agenda->location){
$pdf->Cell(0, $h=0, $txt=get_string('location_agenda','comity')." ".$agenda->location, $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
}


//----Description --------------------------------------------------------------

$toform->committee = $comity->name;
$toform->summary = $event_record->summary;
$toform->description = $event_record->description;
$toform->event_id = $event_id;
$toform->location = $agenda->location;

if(isset($event_record->summary) && $event_record->summary){
$pdf->Cell(0, $h=0, get_string('summary_agenda','comity')." ", $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
$pdf->MultiCell(0, 0, $event_record->summary, $border=0, $align='J', $fill=false, $ln=1, $x='35', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
$pdf->ln(1);}

if(isset($event_record->description) && $event_record->description){
$pdf->Cell(0, $h=0, get_string('desc_agenda_c','comity')." ", $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
$pdf->MultiCell(0, 0, $event_record->description, $border=0, $align='J', $fill=false, $ln=1, $x='35', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
$pdf->ln();}

//------------END DEFAULT VALUES------------------------------------------------




//-----------Participants-------------------------------------------------------
//------------------------------------------------------------------------------



$commityRecords = $DB->get_records('comity_agenda_members', array('comity_id' => $comity_id,'agenda_id'=>$agenda_id), '', '*', $ignoremultiple = false);

//--------Comittee Members------------------------------------------------------
        $comitymembers = array();//Used to store commitee members in an array

        if ($commityRecords && !$plain_pdf) {


if($DB->record_exists('comity_agenda_attendance', array('comity_agenda' => $agenda_id), '*', $ignoremultiple = false)){
$pdf->SetFont('helvetica', 'b', 10);
$pdf->Cell(0, $h=0, get_string('participants','comity')." ", $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
$pdf->SetFont('helvetica', '', 10);
}


            $FORUM_TYPES = array(
                '0' => get_string('agenda_present', 'comity'),
                '1' => get_string('agenda_absent', 'comity'),
                '2' => get_string('agenda_uabsent', 'comity'));

            $index = 0;
            foreach ($commityRecords as $member) {
                $count_label = $index + 1;


//--DEFAULT VALUES--------------------------------------------------------------
//------------------------------------------------------------------------------


                //Get Real name from moodle
                $name = $this->getUserName($member->user_id);
                $comitymembers[$member->id] = $name;


$attendance = $DB->get_record('comity_agenda_attendance', array('comity_agenda' => $agenda_id,'comity_members'=>$member->id), '*', $ignoremultiple = false);



        if($attendance){

            $toform->participant_status_notes[$index] = "";

            if(isset($attendance->absent)){
                if($attendance->absent == 0){ //present
                $attendance_summary = $FORUM_TYPES[0];
                ;
                } elseif($attendance->absent == 1) { //absent
                $attendance_summary = $FORUM_TYPES[1];
               
                if(isset($attendance->notes)){
                   $attendance_summary .= ": ". $attendance->notes;
                }

                }
            }

            if($attendance->unexcused_absence == 1){
              $attendance_summary = $FORUM_TYPES[2];
              if(isset($attendance->notes)){
                   $attendance_summary .= ": ". $attendance->notes;
                }
            }

$pdf->Cell(0, $h=0, $name.": ".$attendance_summary,
$border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');


        }


                $index++;
            }


        }

//------------MOODLE USERS------------------------------------------------------


$sql = "SELECT * FROM {comity_agenda_guests} WHERE comity_agenda = ? AND moodleid IS NOT NULL";

$moodle_members = $DB->get_records_sql($sql, array($agenda_id), $limitfrom=0, $limitnum=0);

            if($moodle_members){


$pdf->ln(1); $pdf->ln(1);$pdf->ln(1);
$pdf->SetFont('helvetica', 'b', 10);
$pdf->Cell(0, $h=0, get_string("moodle_members",'comity')." ", $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
$pdf->SetFont('helvetica', '', 10);

            $index = 0;

            foreach($moodle_members as $moodle_user){
            $count_label = $index + 1;

//----------DEFAULT VALUES------------------------------------------------------
 $name = $this->getUserName($moodle_user->moodleid);
//------------------------------------------------------------------------------


$pdf->Cell(0, $h=0, $name, $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

            $index++;
            }
}

//-----------GUESTS-------------------------------------------------------------

$sql = "SELECT * FROM {comity_agenda_guests} WHERE comity_agenda = ? AND moodleid IS NULL";

$guests = $DB->get_records_sql($sql, array($agenda_id), $limitfrom=0, $limitnum=0);

            if($guests){
$pdf->ln(1); $pdf->ln(1);$pdf->ln(1);
$pdf->SetFont('helvetica', 'b', 10);
$pdf->Cell(0, $h=0, get_string('guest_members','comity')." ", $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
$pdf->SetFont('helvetica', '', 10);

            $index = 0;

            foreach($guests as $guest){
            $count_label = $index + 1;

          

$pdf->Cell(0, $h=0,$guest->firstname . " " . $guest->lastname , $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

            $index++;
            }
        }

//---------TOPICS---------------------------------------------------------------
//------------------------------------------------------------------------------

$pdf->ln(1); $pdf->ln(1);$pdf->ln(1);
$pdf->ln(1); $pdf->ln(1);$pdf->ln(1);
$pdf->SetFont('helvetica', 'b', 10);
$pdf->Cell(0, $h=0, get_string('topics','comity')." ", $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
$pdf->SetFont('helvetica', '', 10);



$topics = $DB->get_records('comity_agenda_topics', array('comity_agenda' => $agenda_id), $sort = 'timecreated ASC', '*', $ignoremultiple = false);

if($topics){ //check if any topics actually exist

    //possible topic status:
    $topic_statuses = array('open'=>get_string('topic_open', 'comity'),
                            'in_progress'=>get_string('topic_inprogress', 'comity'),
                            'closed'=>get_string('topic_closed', 'comity'));
       //possible motion status
    $motion_result = array( '-1'=>'-----',
                            '1'=>get_string('motion_accepted', 'comity'),
                           '0'=>get_string('motion_rejected', 'comity'));
$index=1;
foreach($topics as $key=>$topic){

$this->topicNames[$index] = $topic->title;

$pdf->SetFont('helvetica', 'u', 10);
$pdf->Cell(0, $h=0, "$index. $topic->title", $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
$pdf->SetFont('helvetica', '', 10);

//NOTES FOR TOPIC
//$mform->addElement('htmleditor', "topic_notes[$index]", '', "$topic->title");
//NOTES FOR TOPIC && DEFAULT VALUE FOR NOTES------------------------------------

//$notes_htmlformated = format_text($topic->notes, $format = FORMAT_MOODLE, $options = NULL, $courseid_do_not_use = NULL);
//$notes = print_collapsible_region($notes_htmlformated, 'topic_notes', "topic_status_".$index, get_string('topics_notes', 'comity'), $userpref = false, $default = true, $return = true);

//$pdf->Cell(0, $h=0, "Notes: ", $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

if(isset($topic->duration)&& !$topic->duration==""){
$pdf->Cell(0, $h=0, get_string('duration_agenda','comity')." ".$topic->duration, $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
}



if(isset($topic->description) && !$topic->description==""){
$pdf->MultiCell(0, 0, get_string('desc_agenda_c','comity')." ".$topic->description, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
}

if(!$plain_pdf && isset($topic->notes)&& $topic->notes){
$pdf->MultiCell(0, 0, $topic->notes, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
}
if(!$plain_pdf && isset($topic->status)){
$pdf->Cell(0, $h=0, get_string('topic_status','comity')." ".$topic_statuses[$topic->status], $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
} 

$toform->follow_up[$index] = $topic->follow_up;

//$pdf->Cell(0, $h=0, get_string('topic_followup','comity')." ".$topic->follow_up, $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');


$pdf->ln(1); $pdf->ln(1);$pdf->ln(1);
$pdf->ln(1); $pdf->ln(1);$pdf->ln(1);




//------------------------------------------------------------------------------


$motions = $DB->get_records('comity_agenda_motions', array('comity_agenda' => $agenda_id,'comity_agenda_topics'=>$topic->id), '', '*', $ignoremultiple = false);

//-----MOTIONS------------------------------------------------------------------
//------------------------------------------------------------------------------
if(!$plain_pdf && $motions){

    $sub_index=1;
    foreach($motions as $key=>$motion){

        $proposing_choices = $comitymembers;
        $supporting_choices = $comitymembers;

        $proposing_choices['-1']=get_string('proposedby', 'comity');
        $supporting_choices['-1']=get_string('supportedby', 'comity');

//$pdf->Cell(0, $h=0, "$index.$sub_index: $motion->motion", $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
$pdf->MultiCell(0, 0, "$index.$sub_index: $motion->motion", $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);


//-------DEFAULT VALUEs FOR MOTIONS---------------------------------------------
$toform->proposition[$index][$sub_index] = $motion->motion;

if(isset($motion->motionby)){
//$mform->setDefault("proposed[$index][$sub_index]", $proposing_choices[$motion->motionby]);
$pdf->Cell(0, $h=0, get_string('proposedby','comity')." ".$proposing_choices[$motion->motionby], $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');


} else {
///$mform->setDefault("proposed[$index][$sub_index]", "");
}

if(isset($motion->secondedby)){
 //$mform->setDefault("supported[$index][$sub_index]", $supporting_choices[$motion->secondedby]);
$pdf->Cell(0, $h=0, get_string('supportedby','comity')." ".$supporting_choices[$motion->secondedby], $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

} else {
//$mform->setDefault("supported[$index][$sub_index]", "");
}

$result="";

if(isset($motion->carried)){
$result = $motion_result[$motion->carried];

if(isset($motion->unanimous)){
$result .= "(".get_string('unanimous','comity').")";
}
}

if(isset($motion->carried)){
 //$mform->setDefault("motion_result[$index][$sub_index]", $motion_result[$motion->carried]);
 $pdf->Cell(0, $h=0, get_string('motion_outcome','comity')." ".$result, $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

} else {
//$mform->setDefault("motion_result[$index][$sub_index]", "");
}




$toform->aye[$index][$sub_index] = $motion->yea;
$toform->nay[$index][$sub_index] = $motion->nay;
$toform->abs[$index][$sub_index] = $motion->abstained;

$vote_string = "Aye: $motion->yea ";
$vote_string .= "Nay: $motion->nay ";
$vote_string .= "Abs: $motion->abstained ";

$pdf->Cell(0, $h=0, $vote_string, $border=0, $ln=1, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');



 //---------END DEFAULTS---------------------------------------------------------

$sub_index++;
$pdf->ln(1);$pdf->ln(1);$pdf->ln(1);
    }



}//end if any motions exist

//-------DEFAULT TOPIC VALUEs---------------------------------------------------

$toform->topic_notes[$index] = $topic->notes;

if(isset($topic->status)){
$toform->topic_status[$index] = $topic_statuses[$topic->status];
} else {
 $toform->topic_status[$index] = $topic_statuses['open'];
}

$toform->follow_up[$index] = $topic->follow_up;
//---------END DEFAULTS---------------------------------------------------------


$index++;
$pdf->ln(1);$pdf->ln(1);$pdf->ln(1);$pdf->ln(1);$pdf->ln(1);
}//end foreach topic
$pdf->ln(1);$pdf->ln(1);
}//end topics

     
        //Set default values to private variable
        $this->default_toform = $toform;

       $pdf->Output('agenda.pdf', 'd');
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
