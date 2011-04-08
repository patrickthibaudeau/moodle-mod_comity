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

global $CFG;

$event_id = optional_param('event_id',null, PARAM_INT);

if(isset($event_id)){

$event = $DB->get_record('comity_events', array('id'=>$event_id));

$eventname = $event->summary;
$eventname = str_replace(' ', '_', $eventname);
$eventname = str_replace('\'', '_', $eventname);
$eventname = str_replace('.','_' ,$eventname);


//$Filename = "ComityEvent" . $event_id . ".ics";
$Filename = $eventname.".ics";
header("Content-Type: text/x-vCalendar");
header("Content-Disposition: inline; filename=$Filename");

//$event = $DB->get_record('comity_events', array('id'=>$event_id));

$DescDump = str_replace("\r", "=0D=0A=", $event->description);

/*
     BEGIN:VCALENDAR
VERSION:1.0
PRODID:SSPCA Web Calendar

    TZ:-07
    END:VCALENDAR
*/

?>
BEGIN:VCALENDAR
BEGIN:VEVENT
TZ:-0700
DTSTART;TZID="Mountain Standard Time":<?php echo $event->stamp_t_start . "\n"; ?>
DTEND;TZID="Mountain Standard Time":<?php echo $event->stamp_t_end . "\n"; ?>
SUMMARY:<?php echo $event->summary . "\n"; ?>
DESCRIPTION;ENCODING=QUOTED-PRINTABLE: <?php echo $DescDump . "\n"; ?>
END:VEVENT
END:VCALENDAR

<?php } ?>
