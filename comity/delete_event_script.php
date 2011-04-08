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
 * @copyright 2011 Raymond Wainman, Dustin Durand, Patrick Thibaudeau (Campus St. Jean, University of Alberta)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once('lib.php');
require_once('meetingagenda/lib.php');
require_once('lib_comity.php');
echo '<link rel="stylesheet" type="text/css" href="style.php">';
print '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/comity/meetingagenda/rooms_available.js"></script>';


$id = optional_param('id', 0, PARAM_INT);    // Course Module ID

$event_id = optional_param('event_id', 0, PARAM_INT);

comity_check($id);
comity_header($id, 'deleteevent', 'events.php?id=' . $id);

echo '<div><div class="title">' . get_string('deleteevent', 'comity') . '</div>';

echo get_string('deletingevent', 'comity');

echo '<span class="content">';

echo '</span></div>';

comity_footer();


//---DELETE AGENDA FOR EVENT----------------------------------------------------
if ($event_id) {
    $agenda = $DB->get_record('comity_agenda', array('comity_events_id' => $event_id), '*', $ignoremultiple = false);
    if ($agenda) {
        $comity_id = $agenda->comity_id;
        $event_id = $agenda->comity_events_id;
        $agenda_id = $agenda->id;

        $cm = get_coursemodule_from_id('comity', $comity_id); //get course module
//Delete all files within the instace of this module for agenda
        $fs = get_file_storage();
        $files = $fs->get_area_files($cm->instance, 'mod_comity', 'attachment');
        foreach ($files as $f) {
            $f->delete();
        }

        $DB->delete_records('comity_agenda_topics', array('comity_agenda' => $agenda_id));
        $DB->delete_records('comity_agenda_guests', array('comity_agenda' => $agenda_id));
        $DB->delete_records('comity_agenda_motions', array('comity_agenda' => $agenda_id));
        $DB->delete_records('comity_agenda_attendance', array('comity_agenda' => $agenda_id));
        $DB->delete_records('comity_agenda_members', array('agenda_id' => $agenda_id));
        $DB->delete_records('comity_agenda', array('id' => $agenda_id));


    }
    $event = $DB->get_record('comity_events', array('id' => $event_id), '*', $ignoremultiple = false);
    if($event && $event->room_reservation_id > 0){
        $dbman = $DB->get_manager();
$table = new xmldb_table('roomscheduler_reservations');
$scheduler_plugin_installed = $dbman->table_exists($table);

if ($scheduler_plugin_installed) {   //plugin exists
    js_function('room_scheduler_delete',$event->room_reservation_id);
}
}

}
//-----------------------------------------------------------------------------
//Delete Event itself
$DB->delete_records('comity_events', array('id' => $event_id));

echo '<script type="text/javascript">';
echo 'window.location.href="' . $CFG->wwwroot . '/mod/comity/events.php?id=' . $id . '";';
echo '</script>';
?>
