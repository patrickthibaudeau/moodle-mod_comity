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
 * The form for the minutes tab of the Agenda/Meeting Extension to Committee Module.
 * This form has to be required from within view.php - it is not standalone, its a content loading file
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->dirroot/mod/comity/meetingagenda/moodle_user_selector.php");
require_once("$CFG->dirroot/mod/comity/meetingagenda/business.css");
require_once("$CFG->dirroot/mod/comity/meetingagenda/ajax_lib.php");
print '<script type="text/javascript" src="rooms_available.js"></script>';
global $DB;

//Access to the content of this tab is valid only if an agenda is created
//An error is displayed, and loading stops if no agenda is created
if (!$agenda) {

    print '<center><h3>' . get_string('no_agenda', 'comity') . '</h3></center>';
    return;
}

//------------SECURITY----------------------------------------------------------
//------------------------------------------------------------------------------
//Simple role cypher for code clarity
$role_cypher = array('1' => 'president', '2' => 'vice', '3' => "member", "4" => 'admin');

//check if user has a valid user role, otherwise give them the credentials of a guest
if (isset($user_role) && ($user_role == '1' || $user_role == '2' || $user_role == '3' || $user_role == '4' || is_siteadmin())) {
    $credentials = $role_cypher[$user_role];
} else {
    $credentials = "guest";
}


//------------LOADING SCREEN----------------------------------------------------
//------------------------------------------------------------------------------
//Apply loading screen if the parameters are stripped
//this only occurs when the form submits, or partially submit using moodleform
print '<script type="text/javascript">';
print 'if(document.location.href=="';
print $CFG->wwwroot . '/mod/comity/meetingagenda/view.php"){';
print<<<HERE
document.write('<center><img src="loading14.gif" alt="Loading..." /></center>');

}
</script>
HERE;


//Check if user should be able to edit/create the agenda
//Check that the agenda is not completed(completed agendas cannot be edited)
if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin'|| is_siteadmin()) {

    minutes_editable($event_id, $agenda, $agenda_id, $comity_id, $cm, $selected_tab);


//---------VIEW ONLY PERMISSIONS------------------------------------------------
//------------------------------------------------------------------------------
} elseif ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin' || $credentials == 'member' || is_siteadmin()) {

    minutes_viewonly($event_id, $agenda, $agenda_id, $comity_id, $cm, $selected_tab);

    
}



//----------------------------------------------------------------------------

/*
 * The function converts the information subbmitted for attendance and creates
 * an object that is used to create a database entry.
 *
 * @param int $key The index signifying which attendance record we want to convert within the submitted arrays
 * @param int $attendance An int signifying if the person was present(0), absent(1), or unexcused absent(2)
 */
function attendance_toobject_conversion($key, $attendance) {
    $dataobject = new stdClass();


    $note = null;
    if (isset($_REQUEST["participant_status_notes"])) {
        $notes = $_REQUEST["participant_status_notes"];


        if (isset($notes[$key])) {
            $note = $notes[$key];
        }
    }

    switch ($attendance) {
        case 0://Present
            $dataobject->absent = 0;
            $dataobject->unexcused_absence = NULL;
            $dataobject->notes = NULL;

            break;

        case 1://absent
            $dataobject->absent = 1;
            $dataobject->unexcused_absence = NULL;
            $dataobject->notes = $note;

            if ($dataobject->notes == "") {
                $dataobject->notes = NULL;
            }

            break;

        case 2://unexcused absent
            $dataobject->absent = NULL;
            $dataobject->unexcused_absence = 1;
            $dataobject->notes = $note;

            if ($dataobject->notes == "") {
                $dataobject->notes = NULL;
            }

            break;

        default://Should never happen
            $dataobject->absent = NULL;
            $dataobject->unexcused_absence = NULL;
            $dataobject->notes = NULL;

            break;
    }
    return $dataobject;
}


/*
 * This function updates all instances of attendance for committee members
 *
 * @param int $agenda_id The unique agenda ID for this Agenda.
 *
 */
function update_attendance($agenda_id) {

    global $DB; //Global Database Variable

    //Arrays of submitted data from form
    $comity_ids = $_REQUEST['participant_id'];
    $attendance = $_REQUEST["participant_status"];
    //$comity_member_id = $_REQUEST["participant_id"];

    //Foreach of the committtee ID(committee members)
    foreach ($comity_ids as $key => $comity_member_id) {

        //If member's attendance exists for this agenda, update record

        //No attendance
            if($attendance[$key]==-1){
              $DB->delete_records('comity_agenda_attendance', array('comity_agenda'=>$agenda_id, 'comity_members'=>$comity_member_id));
            continue;

            }

        if ($DB->record_exists('comity_agenda_attendance', array('comity_agenda' => $agenda_id, 'comity_members' => $comity_member_id))) {
            //update record

            $old_record = $DB->get_record('comity_agenda_attendance', array('comity_agenda' => $agenda_id, 'comity_members' => $comity_member_id));

            $dataobject = attendance_toobject_conversion($key, $attendance[$key]);
            $dataobject->id = $old_record->id;


            $DB->update_record('comity_agenda_attendance', $dataobject, $bulk = false);


        } else {//Attendance record is new

            

            //create attendance record
            $dataobject = attendance_toobject_conversion($key, $attendance[$key]);
            $dataobject->comity_agenda = $agenda_id;
            $dataobject->comity_members = $comity_member_id;

            $DB->insert_record('comity_agenda_attendance', $dataobject, $returnid = false, $bulk = false);

        }
    }//end for loop for committee members


}

/*
 * This function updates all instances of topics.
 *
 * @param object $cm An object representing the course module. We need this object to determine the instance of this course module.
 */
function updatetopics($cm) {
    global $USER, $DB;
    
    //Get submitted form information
    $topics = $_REQUEST['topic_ids'];
    $topics_notes = $_REQUEST['topic_notes'];
    $topics_statuses = $_REQUEST['topic_status'];
   // $topics_followup = $_REQUEST['follow_up'];
    $attachments = $_REQUEST['attachments'];
    $filearea_ids = $_REQUEST['topic_fileareaid'];

    //If at least one topic exists
    if ($topics) {

        //Itterate every topic that has an ID(ie. is already created)-- do an update
        foreach ($topics as $index => $topicid) {
            $note = $topics_notes[$index];
            $status = $topics_statuses[$index];
            //$followup = $topics_followup[$index];
            $modifiedtime = time();
            $modifiedby = $USER->id;

            $dataobject = new stdClass();
            $dataobject->id = $topicid;
            $dataobject->notes = $note;
            //$dataobject->follow_up = $followup;
            $dataobject->status = $status;
            $dataobject->modifiedby = $modifiedby;
            $dataobject->timemodified = $modifiedtime;

            //Double check topic exists in database
            if ($DB->record_exists('comity_agenda_topics', array('id' => $topicid))) {
                $DB->update_record('comity_agenda_topics', $dataobject, $bulk = false);
                file_save_draft_area_files($attachments[$index], $cm->instance, 'mod_comity', 'attachment', $filearea_ids[$index], array('subdirs' => 0, 'maxfiles' => 50));
            }
        }
    }
}


/*
 * This function adds/updates a SINGULAR motion depending on which update button was pressed.
 * Also adds new motion from that topic if exists.
 *
 * @param int $event_id The unique ID that uniquely identifies which event this agenda is created within.
 * @param int $selected_tab The ID for which tab we are currently on.
 *@param int $agenda_id The unique ID signifiying which Agenda we are currently in.
 */
function addAndUpdate_Motion($event_id, $selected_tab,$agenda_id) {
    global $USER, $DB, $CFG;

    //These are arrays -- each [index] is a different topic(based on submitted index), [subindex] is a different motion of that topic
    //Used parallel arrays to submit information from form, which isn't always the best way, but it works

    $topic_return = 1;

    $buttonPressed = $_REQUEST['add_motion']; //get button pressed
    if ($buttonPressed) {//double check if button was pressed
        if (isset($_REQUEST['motion_ids'])) {//check if there are any motions to update
            $motion_ids = $_REQUEST['motion_ids']; //motion id in table: comity_agenda_motions
            $proposal_array = $_REQUEST['proposition']; //text detailing proposal
            $proposalby_array = $_REQUEST['proposed']; //member id (id in comity_members table) of who gave motion
            $supportedby_array = $_REQUEST['supported']; //member id (id in comity_members table) of who supported motion
            $result_array = $_REQUEST['motion_result']; //result of voting on motion
            $yesCount_array = $_REQUEST['aye']; //count of yes votes
            $noCount_array = $_REQUEST['nay']; //count of no votes
            $abstainCount_array = $_REQUEST['abs']; //count of abstains

            
            if (isset($_REQUEST['unanimous'])) { //checkboxes disappear if not checked
                $unanimous = $_REQUEST['unanimous'];
            }

        }
            //There will only be one item in array(one button pressed), but
            //nice way to get the array key, which represents which topic we are looking at ($index in buisness_mod_form.php)

            foreach ($buttonPressed as $index => $garbage) { //topic level
                $topic_return = $index; //used to get to <a> tag anchor(html) on page redirect

//------------UPDATING PREVIOUS MOTIONS-----------------------------------------

                if (isset($motion_ids[$index])) {
                    foreach ($motion_ids[$index] as $sub_index => $motionid) {//itterate through motions
                        if ($DB->record_exists('comity_agenda_motions', array('id' => $motionid))) {

                            $dataobject = new stdClass();
                            $dataobject->id = $motionid;
                            $dataobject->motion = $proposal_array[$index][$sub_index];

                            //SELECTOR FOR MOTION BY
                            //The default value for empty is '-1', we replace this value with NULL
                            if ($proposalby_array[$index][$sub_index] == "-1") {
                                $dataobject->motionby = NULL;
                            } else {
                                $dataobject->motionby = $proposalby_array[$index][$sub_index];
                            }

                            //SELECTOR FOR SECONDED BY
                            //The default value for empty is '-1', we replace this value with NULL
                            if ($supportedby_array[$index][$sub_index] == "-1") {
                                $dataobject->secondedby = NULL;
                            } else {
                                $dataobject->secondedby = $supportedby_array[$index][$sub_index];
                            }

                            //RESULT SELECTOR
                            //The default value for empty is '-1', we replace this value with NULL
                            if ($result_array[$index][$sub_index] == "-1") {
                                $dataobject->carried = NULL;
                            } else {
                                $dataobject->carried = $result_array[$index][$sub_index];
                            }

                            //VOTE COUNTS
                            $dataobject->yea = $yesCount_array[$index][$sub_index];
                            $dataobject->nay = $noCount_array[$index][$sub_index];
                            $dataobject->abstained = $abstainCount_array[$index][$sub_index];
                            $dataobject->timemodified = time();
                            $dataobject->unanimous = NULL;

                            //check if checkbox is checked
                            if (isset($unanimous)) {
                                if (isset($unanimous[$index][$sub_index])) {

                                    $dataobject->unanimous = $unanimous[$index][$sub_index];
                                } else {
                                    $dataobject->unanimous = null;
                                }
                            } else {
                                $dataobject->unanimous = null;
                            }

                            //print_object($dataobject);
                            $DB->update_record('comity_agenda_motions', $dataobject, $bulk = false);
                        }
                    }//end motion itterations
                }//--------------END UPDATES----------------
                //



//-------------------ADDING NEW MOTION FROM TOPIC-------------------------------
                //Get subbmitted information
                $proposal_array = $_REQUEST['proposition_new']; //text detailing proposal
                $proposalby_array = $_REQUEST['proposed_new']; //member id (id in comity_members table) of who gave motion
                $supportedby_array = $_REQUEST['supported_new']; //member id (id in comity_members table) of who supported motion
                $result_array = $_REQUEST['motion_result_new']; //result of voting on motion
                $yesCount_array = $_REQUEST['aye_new']; //count of yes votes
                $noCount_array = $_REQUEST['nay_new']; //count of no votes
                $abstainCount_array = $_REQUEST['abs_new']; //count of abstains
                $topicids_array = $_REQUEST['topic_ids'];

                if (isset($_REQUEST['unanimous_new'])) { //checkboxes disappear if not checked
                    $unanimous_array = $_REQUEST['unanimous_new'];
                }

                //$index specifies which topic we are currently adding this motion for
                $proposal = $proposal_array[$index];
                $proposalby = $proposalby_array[$index];
                $supportedby = $supportedby_array[$index];
                $result = $result_array[$index];
                $yesCount = $yesCount_array[$index];
                $noCount = $noCount_array[$index];
                $abstainCount = $abstainCount_array[$index];
                $topicid = $topicids_array[$index];

                //Checkbox for unanimous
                $unanimous = null;
                if (isset($unanimous_array[$index])) {
                   $unanimous = $unanimous_array[$index];
                }

                $proposal = trim("$proposal");

                //If proposal's name is nothinig, do not create record
                if($proposal!=""){

                   $dataobject = new stdClass();
                   $dataobject->comity_agenda = $agenda_id;
                   $dataobject->comity_agenda_topics = $topicid;
                   $dataobject->motion = $proposal;
                   $dataobject->motionby = replaceNegativeOneWithNull($proposalby);
                   $dataobject->secondedby = replaceNegativeOneWithNull($supportedby);
                   $dataobject->carried = replaceNegativeOneWithNull($result);
                   $dataobject->unanimous = $unanimous;
                   $dataobject->yea = $yesCount;
                   $dataobject->nay = $noCount;
                   $dataobject->abstained = $abstainCount;
                   $dataobject->timemodified= time();

                  $DB->insert_record('comity_agenda_motions', $dataobject, $returnid=false, $bulk=false);

                }
            }//End topics
        
    }
   //Redirect back to topic achor point on page("#topic_" . $topic_return)
   redirect("$CFG->wwwroot/mod/comity/meetingagenda/view.php?event_id=" . $event_id . "&selected_tab=" . $selected_tab . "#topic_" . $topic_return);
}

 /*
 * A function to replace our default value of '-1' with NULL for database input
 *
 * @param int $value If value is -1 return null, else return $value.
 *
 */
function replaceNegativeOneWithNull($value){

    if($value=='-1'){
        return NULL;
    } else {
        return $value;
    }

}
/*
 * This function adds/updates a ALL motions.
 * Also adds new motion from topics if applicable.
 *
 * @param int $event_id The unique ID that uniquely identifies which event this agenda is created within.
 * @param int $selected_tab The ID for which tab we are currently on.
 *@param int $agenda_id The unique ID signifiying which Agenda we are currently in.
 */
function addAndUpdate_Motions($event_id, $selected_tab,$agenda_id) {
    global $USER, $DB, $CFG;

    //These are arrays -- each [index] is a different topic(based on submitted index), [subindex] is a different motion of that topic
    //Used parallel arrays to submit information from form, which isn't always the best way, but it works

    $topic_return = 1;

            if (isset($_REQUEST['topic_ids'])) {//check if there are any motions to update
            $topic_ids = $_REQUEST['topic_ids'];


            if (isset($_REQUEST['motion_ids'])){
         $motion_ids = $_REQUEST['motion_ids']; //motion id in table: comity_agenda_motions
        }

            //There will only be one item in array(one button pressed), but
            //nice way to get the array key, which represents which topic we are looking at ($index in buisness_mod_form.php)

            //print_object($_REQUEST);exit();

            foreach ($topic_ids as $index => $topic_id) { //topic level

             if (isset($motion_ids)){
            $topic_ids = $_REQUEST['topic_ids'];
            $proposal_array = $_REQUEST['proposition']; //text detailing proposal
            $proposalby_array = $_REQUEST['proposed']; //member id (id in comity_members table) of who gave motion
            $supportedby_array = $_REQUEST['supported']; //member id (id in comity_members table) of who supported motion
            $result_array = $_REQUEST['motion_result']; //result of voting on motion
            $yesCount_array = $_REQUEST['aye']; //count of yes votes
            $noCount_array = $_REQUEST['nay']; //count of no votes
            $abstainCount_array = $_REQUEST['abs']; //count of abstains


            if (isset($_REQUEST['unanimous'])) { //checkboxes disappear if not checked
                $unanimous = $_REQUEST['unanimous'];
            }

        

        


//------------UPDATING PREVIOUS MOTIONS-----------------------------------------

                if (isset($motion_ids[$index])) {
                    foreach ($motion_ids[$index] as $sub_index => $motionid) {//itterate through motions
                        if ($DB->record_exists('comity_agenda_motions', array('id' => $motionid))) {

                           //print "[$index][$sub_index]-!".$result_array[2]."</br>";
                           

                           
                            $dataobject = new stdClass();
                            $dataobject->id = $motionid;
                            $dataobject->motion = $proposal_array[$index][$sub_index];

                            //SELECTOR FOR MOTION BY
                            //The default value for empty is '-1', we replace this value with NULL
                            if ($proposalby_array[$index][$sub_index] == "-1") {
                                $dataobject->motionby = NULL;
                            } else {
                                $dataobject->motionby = $proposalby_array[$index][$sub_index];
                            }

                            //SELECTOR FOR SECONDED BY
                            //The default value for empty is '-1', we replace this value with NULL
                            if ($supportedby_array[$index][$sub_index] == "-1") {
                                $dataobject->secondedby = NULL;
                            } else {
                                $dataobject->secondedby = $supportedby_array[$index][$sub_index];
                            }

                            //RESULT SELECTOR
                            //The default value for empty is '-1', we replace this value with NULL
                            if ($result_array[$index][$sub_index] == "-1") {
                                $dataobject->carried = NULL;
                            } else {
                                $dataobject->carried = $result_array[$index][$sub_index];
                            }

                            //VOTE COUNTS
                            $dataobject->yea = $yesCount_array[$index][$sub_index];
                            $dataobject->nay = $noCount_array[$index][$sub_index];
                            $dataobject->abstained = $abstainCount_array[$index][$sub_index];
                            $dataobject->timemodified = time();
                            $dataobject->unanimous = NULL;

                            //check if checkbox is checked
                            if (isset($unanimous)) {
                                if (isset($unanimous[$index][$sub_index])) {

                                    $dataobject->unanimous = $unanimous[$index][$sub_index];
                                } else {
                                    $dataobject->unanimous = null;
                                }
                            } else {
                                $dataobject->unanimous = null;
                            }

                            //print_object($dataobject);
                            $DB->update_record('comity_agenda_motions', $dataobject, $bulk = false);
                        }
                    }//end motion itterations
                }//--------------END UPDATES----------------
                //
}


//-------------------ADDING NEW MOTION FROM TOPIC-------------------------------
                //Get subbmitted information
                $proposal_array = $_REQUEST['proposition_new']; //text detailing proposal
                $proposalby_array = $_REQUEST['proposed_new']; //member id (id in comity_members table) of who gave motion
                $supportedby_array = $_REQUEST['supported_new']; //member id (id in comity_members table) of who supported motion
                $result_array = $_REQUEST['motion_result_new']; //result of voting on motion
                $yesCount_array = $_REQUEST['aye_new']; //count of yes votes
                $noCount_array = $_REQUEST['nay_new']; //count of no votes
                $abstainCount_array = $_REQUEST['abs_new']; //count of abstains
                $topicids_array = $_REQUEST['topic_ids'];

                if (isset($_REQUEST['unanimous_new'])) { //checkboxes disappear if not checked
                    $unanimous_array = $_REQUEST['unanimous_new'];
                }

                //$index specifies which topic we are currently adding this motion for
                $proposal = $proposal_array[$index];
                $proposalby = $proposalby_array[$index];
                $supportedby = $supportedby_array[$index];
                $result = $result_array[$index];
                $yesCount = $yesCount_array[$index];
                $noCount = $noCount_array[$index];
                $abstainCount = $abstainCount_array[$index];
                $topicid = $topicids_array[$index];

                //Checkbox for unanimous
                $unanimous = null;
                if (isset($unanimous_array[$index])) {
                   $unanimous = $unanimous_array[$index];
                }

                $proposal = trim("$proposal");

                //If proposal's name is nothinig, do not create record
                if($proposal!=""){

                   $dataobject = new stdClass();
                   $dataobject->comity_agenda = $agenda_id;
                   $dataobject->comity_agenda_topics = $topic_id;
                   $dataobject->motion = $proposal;
                   $dataobject->motionby = replaceNegativeOneWithNull($proposalby);
                   $dataobject->secondedby = replaceNegativeOneWithNull($supportedby);
                   $dataobject->carried = replaceNegativeOneWithNull($result);
                   $dataobject->unanimous = $unanimous;
                   $dataobject->yea = $yesCount;
                   $dataobject->nay = $noCount;
                   $dataobject->abstained = $abstainCount;
                   $dataobject->timemodified= time();

                  $DB->insert_record('comity_agenda_motions', $dataobject, $returnid=false, $bulk=false);

                }
            }//End topics
            }

  }

  /*
 * Prints a link to a script that will create agenda minutes for the given event.
 * This pdf is more detailed than the plain agenda PDF.
 *
 * @param int $event_id The ID for the event.
 *
 */
function pdf_version($event_id){
    global $CFG;

$url = "$CFG->wwwroot/mod/comity/meetingagenda/pdf_script.php?event_id=" . $event_id;


print<<<HERE
<style type='text/css'>
#comity_save_pdf {
float: right;
vertical-align:top;
}
</style>
HERE;

//-------------------DOWNLOAD PDF VERSION OF AGENDA-----------------------------
print '<div id="comity_save_pdf">';
print '<form action="'.$url.'">';
print '<span style="display:inline-block; vertical-align:top">'.get_string('save_pdf','comity').":  </span>";
print ' <input type="image" id="save_image" SRC="../pix/pdf_icon.gif" VALUE="Submit now"/>';
print '<input type="hidden" name="event_id" value="'.$event_id.'"/>';
print '<input type="hidden" name="plain_pdf" value="0"/>';
print '</form>';
print '</div>';
//------------------------------------------------------------------------------
}


/*
 * Prints contents of the minutes tab, with the ability to edit content
 *
 * @param int $event_id The ID for the current event of the agenda.
 * @param object $agenda The object representing the database entry for the current agenda.
 * @param int $agenda_id The ID for the current agenda.
 * @param int $comity_id The ID for the current committee.
 * @param object $cm The course module object.
 * @param int $selected_tab The current tab for the minutes.
 *
 * 
 */
function minutes_editable($event_id, $agenda, $agenda_id, $comity_id, $cm, $selected_tab){
global $DB, $CFG;

pdf_version($event_id);

    $topic_count = 0; //initalize as having zero topics

    if ($agenda) { // agenda already created
        //get actual count of topics of topics
        $topic_count = $DB->count_records('comity_agenda_topics', array('comity_agenda' => $agenda_id), '*', $ignoremultiple = false);
        $topic_count++; //Introducing one empty set of fields to add new topic
        //If no topics exist, then the database returns null, and we replace with zero topic count
        if (!$topic_count) {
            $topic_count = 0; //if no database items(null return) make count zero
        }
    }

//----------FORM OBJECT---------------------------------------------------------
    require_once('buisness_mod_form.php'); //Form for users that can view
    $mform = new mod_buisness_mod_form($event_id, $agenda_id, $comity_id, $cm->instance);



//---------------CANCEL BUTTON PRESSED------------------------------------------
//------------------------------------------------------------------------------
    if ($mform->is_cancelled()) {
        //Do nothing
  redirect($CFG->wwwroot . '/mod/comity/meetingagenda/view.php?event_id=' . $event_id . '&selected_tab=' . $selected_tab);


//---------------PARTIAL SUBMIT-------------------------------------------------
//------------------------------------------------------------------------------
    } elseif ($mform->no_submit_button_pressed()) {

//--------------ADD MOODLE USER BUTTON PRESSED----------------------------------
        if (isset($_REQUEST['new_moodle_member'])) {
            $userselector = new my_user_selector('myuserselector', 10, array('multiselect' => false));
            $user = $userselector->get_selected_user();


            if ($user) { //Check if any user was chosen

                //If person isn't already part of agenda as a moodle user, create record
                //A moodle user will use null firstname/lastname, but include a moodle id
                //We use the moodle id to get information, the firstname/lastname fields are only for
                //non-moodle users
                if (!$DB->record_exists('comity_agenda_guests', array('comity_agenda' => $agenda_id, 'moodleid' => $user->id))) {

                    $dataobject = new stdClass();
                    $dataobject->comity_agenda = $agenda_id;
                    $dataobject->firstname = NULL; //NOT A GUESS, therefore null
                    $dataobject->lastname = NULL;//NOT A GUESS, therefore null
                    $dataobject->moodleid = $user->id;//NOT A GUESS, therefore CANNOT BE NULL

                    $DB->insert_record('comity_agenda_guests', $dataobject, $returnid = false, $bulk = false);
                }
            }
        }

//--------------ADD PREVIOUS GUEST BUTTON PRESSED-------------------------------
//Within form a sql query is made to find all quests ever added in the current
//committee, and the user can select from them in a select menu
        elseif (isset($_REQUEST['add_prev_guest'])) {

            $dataString = $_REQUEST['prev_guests']; // selected previous guest
            $dataArray = explode("{x}", $dataString);//{x} delimites first/last names: firstname{x}lastname

            //Parts of name
            $firstname = $dataArray[0];
            $lastname = $dataArray[1];

            //If they are not already part of this agenda/meeting, add a new record
            if (!$DB->record_exists('comity_agenda_guests', array('comity_agenda' => $agenda_id, 'firstname' => $firstname, 'lastname' => $lastname, 'moodleid' => NULL))) {

                $dataobject = new stdClass();
                $dataobject->comity_agenda = $agenda_id;
                $dataobject->firstname = trim($firstname);
                $dataobject->lastname = trim($lastname);
                $dataobject->moodleid = NULL; //GUESTS have no moodle id

                $DB->insert_record('comity_agenda_guests', $dataobject, $returnid = false, $bulk = false);
            }



//------------ADD NEW GUEST BUTTON PRESSED--------------------------------------
        } elseif (isset($_REQUEST['add_new_guest'])) {

            //Retrieve sent information
            $guest_firstname = trim($_REQUEST['guest_firstname']);
            $guest_lastname = trim($_REQUEST['guest_lastname']);

            //Guest must have a non-empty first/last name
            if ($guest_firstname != "" && $guest_firstname != "") {

                //If they don't exist for this agenda, add them
                if (!$DB->record_exists('comity_agenda_guests', array('comity_agenda' => $agenda_id, 'firstname' => $guest_firstname, 'lastname' => $guest_lastname, 'moodleid' => NULL))) {

                    $dataobject = new stdClass();
                    $dataobject->comity_agenda = $agenda_id;
                    $dataobject->firstname = $guest_firstname;
                    $dataobject->lastname = $guest_lastname;
                    $dataobject->moodleid = NULL; //GUESTS have no moodle id

                    $DB->insert_record('comity_agenda_guests', $dataobject, $returnid = false, $bulk = false);
                }
            }
        }


        //Function to update current status, and status notes of committee members
        update_attendance($agenda_id);

        //Every Submit ultimatley causes a redirection to refresh page
        redirect($CFG->wwwroot . '/mod/comity/meetingagenda/view.php?event_id=' . $event_id . '&selected_tab=' . $selected_tab);
//----------END PARTIAL SUBMIT--------------------------------------------------



//--------------------FULL SUBMIT-----------------------------------------------
//------------------------------------------------------------------------------
    } elseif ($fromform = $mform->get_data()) {


//--------------REMOVE MOODLE USER PRESSED--------------------------------------
        if (isset($_REQUEST['remove_moodle_user'])) {

            $removed_buttons = $_REQUEST['remove_moodle_user']; //array containing pressed remove buttons
            $moodle_user_ids = $_REQUEST['participant_moodle_id']; //array containing corresponding moodle user ids

            //There should only be one button pressed, but its an easy way to get
            //they key, and value of the array
            foreach ($removed_buttons as $key => $moodleuser) {

                $moodle_id = $moodle_user_ids[$key]; //get associated moodle user id
                //if record exists, delete it!

                //If the record exists -- delete it
                if ($DB->record_exists('comity_agenda_guests', array('comity_agenda' => $agenda_id, 'moodleid' => $moodle_id))) {
                    if ($moodle_id) {
                        $DB->delete_records('comity_agenda_guests', array('comity_agenda' => $agenda_id, 'moodleid' => $moodle_id));
                    }
                }
            }

//-------------REMOVE GUEST USER PRESSED----------------------------------------
        } elseif (isset($_REQUEST['remove_guest'])) {

            //Get required information
            $guest_ids = $_REQUEST['participant_guest_id'];
            $guest_remove_buttons = $_REQUEST['remove_guest'];

            //There should only be one button pressed, but its an easy way to get
            //they key, and value of the array
            foreach ($guest_remove_buttons as $key => $guest) {
                $guest_id = $guest_ids[$key]; //row id for instance in comity_agenda_guests table

                //If the record exists delete it
                if ($DB->record_exists('comity_agenda_guests', array('comity_agenda' => $agenda_id, 'id' => $guest_id))) {
                    $DB->delete_records('comity_agenda_guests', array('comity_agenda' => $agenda_id, 'id' => $guest_id));
                }
            }
        }


//-----------General Saving-----------------------------------------------------


        //Save all topic updates
        updatetopics($cm);
        update_attendance($agenda_id); //update attendance

//----ADD/UPADTE MOTION BUTTON PRESSED------------------------------------------
        if (isset($_REQUEST['add_motion'])) { //If specific update button pressed
            addAndUpdate_Motion($event_id, $selected_tab,$agenda_id);
        }

        addAndUpdate_Motions($event_id, $selected_tab,$agenda_id);

//Submit ultimatly ends up redirecting the user back to tab
redirect($CFG->wwwroot . '/mod/comity/meetingagenda/view.php?event_id=' . $event_id . '&selected_tab=' . $selected_tab);


//-----------LOAD FORM----------------------------------------------------------
//------------------------------------------------------------------------------
        } else { //FRESH LOAD OF PAGE

        $toform = $mform->getDefault_toform();//Get Values
        $toform->event_id = $event_id;
        $toform->selected_tab = $selected_tab;


        $mform->set_data($toform); //Set values

        //Display Menu
        require_once("$CFG->dirroot/mod/comity/meetingagenda/buisness_sidebar.php");

        //Display Form
        print '<div class="form">';
        $mform->display(false);
        print '</div>';

//----CHECK FOR SCHEDULER PLUGIN -----------------------------------------------
 $dbman = $DB->get_manager();
$table = new xmldb_table('roomscheduler_reservations');
$scheduler_plugin_installed = $dbman->table_exists($table);

$agenda  = $DB->get_record('comity_agenda', array('comity_events_id' => $event_id), '*', $ignoremultiple=false);

if ($scheduler_plugin_installed) {   //plugin exists
$event = $DB->get_record('comity_events', array('id' => $event_id), '*', $ignoremultiple=false);
if($event->room_reservation_id > 0){
$room = get_room_by_reservation_id($event->room_reservation_id);

    if($room){ //room reservation exists
js_function('parse_room_response',"$room:0:false");
    }
}

}
    }


}

/*
 * Prints contents of the minutes tab, with only viewing capabilities
 *
 * @param int $event_id The ID for the current event of the agenda.
 * @param object $agenda The object representing the database entry for the current agenda.
 * @param int $agenda_id The ID for the current agenda.
 * @param int $comity_id The ID for the current committee.
 * @param object $cm The course module object.
 * @param int $selected_tab The current tab for the minutes.
 *
 *
 */
function minutes_viewonly($event_id, $agenda, $agenda_id, $comity_id, $cm, $selected_tab){
global $DB, $CFG;

pdf_version($event_id);
require_once('business_mod_form_view.php'); //Form for users that can view
$mform = new mod_buisness_mod_form($event_id, $agenda_id, $comity_id, $cm->instance);

$toform = $mform->getDefault_toform();
$toform->event_id = $event_id;
$toform->selected_tab = $selected_tab;
$mform->set_data($toform);

//Display Menu
require_once("$CFG->dirroot/mod/comity/meetingagenda/buisness_sidebar.php");
print '<div class="form">';
$mform->display(false);
print '</div>';

//----CHECK FOR SCHEDULER PLUGIN -----------------------------------------------
 $dbman = $DB->get_manager();
$table = new xmldb_table('roomscheduler_reservations');
$scheduler_plugin_installed = $dbman->table_exists($table);

if ($scheduler_plugin_installed) {   //plugin exists
$event = $DB->get_record('comity_events', array('id' => $event_id), '*', $ignoremultiple=false);
    if($event->room_reservation_id > 0){

$room = get_room_by_reservation_id($event->room_reservation_id);

    if($room){ //room reservation exists
js_function('parse_room_response',"$room:0:false");
    }
}

}

}
?>