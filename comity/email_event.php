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
require_once('lib_comity.php');

global $CFG, $USER;

$event_id = optional_param('event_id',null, PARAM_INT);

if(isset($event_id)){

$event = $DB->get_record('comity_events', array('id'=>$event_id));

$eventname = $event->summary;
$eventname = str_replace(' ', '_', $eventname);
$eventname = str_replace('\'', '_', $eventname);
$eventname = str_replace('.','_' ,$eventname);

//get sender email
$sender = $DB->get_record('user', array('id' => $USER->id));
$senderemail = $sender->email;

//$Filename = $eventname.".ics";

//Get committee members
$members = get_comity_members($event->comity_id);

//Send email for each member
    foreach ($members as $member){
    //Create mail body
    $mailbody = header("Content-Type: text/x-vCalendar");
    $mailbody .= header("Content-Disposition: inline;");

    $DescDump = str_replace("\r", "=0D=0A=", $event->description);

    $mailbody .= 'BEGIN:VCALENDAR'."\n";
    $mailbody .= 'BEGIN:VEVENT'."\n";
    $mailbody .= 'TZ:-0700'."\n";
    $mailbody .= "ATTENDEE;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:$member->email\n";
    $mailbody .= "ORGANIZER:MAILTO:$senderemail\n";
    $mailbody .= 'DTSTART;TZID="Mountain Standard Time":'.$event->stamp_t_start ."\n";
    $mailbody .= 'DTEND;TZID="Mountain Standard Time":'.$event->stamp_t_end . "\n";
    $mailbody .= 'SUMMARY:'.$event->summary . "\n";
    $mailbody .= 'DESCRIPTION;ENCODING=QUOTED-PRINTABLE:'.$DescDump . "\n";
    $mailbody .= 'END:VEVENT'."\n";
    $mailbody .= 'END:VCALENDAR'."\n";
    $user = $DB->get_record('user',array('id' => $member->id));
    email_to_user($user, $senderemail, $event->summary, $mailbody) ;
    }
}
?>
