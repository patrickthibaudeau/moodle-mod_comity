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
 * The form for the agenda tab, but only with viewing permissions, for the Agenda/Meeting Extension to Committee Module.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
 
class mod_comity_agenda_form_view extends moodleform {

public $repeat_count; //Count of topics
private $agenda_id;

//Constructor for extended moodleform
function __construct($itterations,$agenda_id = null){
$this->repeat_count = $itterations;
$this->agenda_id = $agenda_id;
parent::__construct();
}


    function definition() {

global $CFG,$DB;
$mform =& $this->_form;

$agenda_id = $this->agenda_id;


//---------GENERAL AGENDA INFORMATION-------------------------------------------
//------------------------------------------------------------------------------
$mform->addElement('header', 'general', get_string('general', 'form'));
$mform->addElement('static', 'committee', get_string('committee_agenda', 'comity'),"");
$mform->addElement('static', 'date', get_string('date_agenda', 'comity'),"");
$mform->addElement('static', 'time', get_string('time_agenda', 'comity'),"");
$mform->addElement('static', 'duration', get_string('duration_agenda', 'comity'),"");
//$mform->addElement('static', 'location', get_string('location_agenda', 'comity'),"");

//---------AGENDA Description And Summary---------------------------------------
//------------------------------------------------------------------------------

$agenda = $DB->get_record('comity_agenda', array('id' => $agenda_id), '*', $ignoremultiple = false);

if($agenda){
 conditionally_add_static($mform, $agenda->location, 'location', get_string('location_agenda', 'comity'));

 //----CHECK FOR SCHEDULER PLUGIN -----------------------------------------------
 $dbman = $DB->get_manager();
$table = new xmldb_table('roomscheduler_reservations');
$scheduler_plugin_installed = $dbman->table_exists($table);

if ($scheduler_plugin_installed) {   //plugin exists
 $mform->addElement('static','scheduler_element',get_string('room_scheduler','comity'), '<div id="booked_location"></div>');
}
 //----------------------------------------------------------------------------


 $event_record = $DB->get_record('comity_events', array('id' => $agenda->comity_events_id), '*', $ignoremultiple = false);

if(isset($event_record)){
 conditionally_add_static($mform, $event_record->summary, 'summary', get_string('summary_agenda', 'comity'));
conditionally_add_static($mform, $event_record->description, 'description', get_string('desc_agenda', 'comity'));

}

}



//---------Agenda Topics--------------------------------------------------------
//------------------------------------------------------------------------------
$mform->addElement('header', 'create_topics', get_string('create_topics', 'comity'));


//-----Repeating Topic Elements-------------------------------------------------
$repeatno = $this->repeat_count;

$repeatarray=array();
    $repeatarray[] = $mform->createElement('static', 'topic_title', get_string('title_agenda', 'comity'),"");
    $repeatarray[] = $mform->createElement('static', 'duration_topic', get_string('duration_agenda', 'comity'),"");
    $repeatarray[] = $mform->createElement('static', 'topic_description', get_string('desc_agenda', 'comity'));
    
$mform->registerElementType('filemanager_view_only', "$CFG->dirroot/mod/comity/meetingagenda/filemanager_view_only.php", 'MoodleQuickForm_Modified_Filemanager');
    $repeatarray[] = $mform->createElement('filemanager_view_only', 'attachments', get_string('attachments', 'comity'), null,array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 10, 'accepted_types' => array('*')) );
   // $repeatarray[] = $mform->createElement('static', 'spacer_', '',"----------------------------------------------------------------------------");
    $repeatarray[] = $mform->createElement('static', 'spacer_', '',"");

$repeateloptions = array();//No options
$this->repeat_elements($repeatarray, $repeatno, $repeateloptions, 'option_repeats','refresh_page', 0, get_string('refresh_page','comity'));

//Hidden Elements for information transfer between pages------------------------
//------------------------------------------------------------------------------
$mform->addElement('hidden', 'is_editable', 'no');
$mform->addElement('hidden', 'created', 'no');
$mform->addElement('hidden', 'event_id', '');
$mform->addElement('hidden', 'selected_tab', '');
$mform->addElement('hidden', 'delete_requested', 'no');




}
}
