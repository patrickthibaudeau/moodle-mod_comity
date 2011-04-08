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
 * The form for the agenda tab of the Agenda/Meeting Extension to Committee Module.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

*/
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once("$CFG->dirroot/mod/comity/meetingagenda/lib.php");
require_once("$CFG->dirroot/mod/comity/meetingagenda/rooms_avaliable_form.php");

class mod_comity_agenda_form extends moodleform {

private $_instance;
public $repeat_count;
private $agenda_id;

function __construct($itterations,$agenda_id = null){
$this->repeat_count = $itterations;
$this->agenda_id = $agenda_id;
parent::__construct();
}


function definition() {
$mform =& $this->_form;

global $DB;

$agenda_id = $this->agenda_id;
		
		

//---------GENERAL AGENDA INFORMATION-------------------------------------------
//------------------------------------------------------------------------------
$mform->addElement('header', 'general', get_string('general', 'form'));
$mform->addElement('static', 'committee', get_string('committee_agenda', 'comity'),"");
$mform->addElement('static', 'date', get_string('date_agenda', 'comity'),"");
$mform->addElement('static', 'time', get_string('time_agenda', 'comity'),"");
$mform->addElement('static', 'duration', get_string('duration_agenda', 'comity'),"");


$mform->addElement('text', 'location', get_string('location_agenda', 'comity'),"");

$agenda = $DB->get_record('comity_agenda', array('id' => $agenda_id), '*', $ignoremultiple = false);

if($agenda){
$event_record = $DB->get_record('comity_events', array('id' => $agenda->comity_events_id), '*', $ignoremultiple = false);

//----CHECK FOR SCHEDULER PLUGIN -----------------------------------------------
 $dbman = $DB->get_manager();
$table = new xmldb_table('roomscheduler_reservations');
$scheduler_plugin_installed = $dbman->table_exists($table);

if ($scheduler_plugin_installed && $event_record->room_reservation_id > 0) {   //plugin exists
 $mform->addElement('static','scheduler_element',get_string('room_scheduler','comity'), '<div id="booked_location"></div>');
}
 //----------------------------------------------------------------------------



 
if(isset($event_record)){
 conditionally_add_static($mform, $event_record->summary, 'summary', get_string('summary_agenda', 'comity'));
conditionally_add_static($mform, $event_record->description, 'description', get_string('desc_agenda', 'comity'));

}

}


//---------AGENDA Description And Summary---------------------------------------
//------------------------------------------------------------------------------
$mform->addElement('static', 'edit_url','');

//---------Agenda Topics--------------------------------------------------------
//------------------------------------------------------------------------------
$mform->addElement('header', 'create_topics', get_string('create_topics', 'comity'));
$repeatno = $this->repeat_count;

//-----Repeating Topic Elements-------------------------------------------------
$repeatarray=array();
    $repeatarray[] = $mform->createElement('text', 'topic_title', get_string('title_agenda', 'comity'),array('size'=>'80'));
    $repeatarray[] = $mform->createElement('text', 'duration_topic', get_string('duration_agenda', 'comity'),"");
    $repeatarray[] = $mform->createElement('textarea', 'topic_description', get_string('desc_agenda', 'comity'), 'wrap="virtual" rows="5" cols="80"');
    $repeatarray[] = $mform->createElement('filemanager', 'attachments', get_string('attachments', 'comity'), null,array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 10, 'accepted_types' => array('*')) );
    $repeatarray[]= $mform->createElement('submit', 'remove_topic', get_string('remove_topic','comity'));
    $repeatarray[] = $mform->createElement('hidden', 'topic_id', '');
    //$repeatarray[] = $mform->createElement('static', 'spacer_', '',"----------------------------------------------------------------------------");
    $repeateloptions = array();
    $this->repeat_elements($repeatarray, $repeatno, $repeateloptions, 'option_repeats','option_add_fields', 1, get_string('add_topic','comity'));

//-----Conditional Rules--------------------------------------------------------
 $mform->disabledIf('option_add_fields', 'created', 'eq', 'no');
 $mform->disabledIf('submitbutton', 'is_editable', 'eq', 'no');
 $mform->disabledIf('option_add_fields', 'is_editable', 'eq', 'no');
 
 for($i=0;$i<$repeatno;$i++){
 $mform->disabledIf('topic_title['.$i.']', 'is_editable', 'eq', 'no');
 $mform->disabledIf('duration_topic['.$i.']', 'is_editable', 'eq', 'no');
 $mform->disabledIf('topic_description['.$i.']', 'is_editable', 'eq', 'no');
 $mform->disabledIf('userfile['.$i.']', 'is_editable', 'eq', 'no');
 $mform->registerNoSubmitButton('remove_topic['.$i.']');
 $mform->addRule('topic_title['.$i.']', null, 'required', null, 'client');

 }

 //----Remove Buttons-----------------------------------------------------------
$mform->registerNoSubmitButton('remove_topic');
$mform->registerNoSubmitButton('remove_agenda');
$mform->registerNoSubmitButton('remove_agenda');


//Hidden Elements for information transfer between pages------------------------
//------------------------------------------------------------------------------
$mform->addElement('hidden', 'is_editable', 'no');
$mform->addElement('hidden', 'created', 'no');
$mform->addElement('hidden', 'event_id', '');
$mform->addElement('hidden', 'selected_tab', '');
$mform->addElement('hidden', 'delete_requested', 'no');


//$this->add_action_buttons();

$mform->addElement('html', '<br>');

$buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] =& $mform->createElement('submit', 'complete_agenda', get_string('complete_agenda','comity'));

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);


//-----Conditional Rules--------------------------------------------------------
$mform->disabledIf('remove_agenda', 'is_editable', 'eq', 'no');
$mform->disabledIf('remove_agenda', 'created', 'eq', 'no');
$mform->registerNoSubmitButton('remove_agenda');

$mform->disabledIf('complete_agenda', 'is_editable', 'eq', 'no');
$mform->disabledIf('complete_agenda', 'created', 'eq', 'no');

//------------Remove Agenda-----------------------------------------------------

$mform->addElement('html', '<br><br><br><br>');
$buttonarray=array();
$buttonarray[] =& $mform->createElement('submit', 'remove_agenda', get_string('remove_agenda','comity'));

$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);


}


}
