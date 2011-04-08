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
 * @copyright 2010 Raymond Wainman, Dustin Durand, Patrick Thibaudeau (Campus St. Jean, University of Alberta)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once('lib.php');
require_once('lib_comity.php');
echo '<link rel="stylesheet" type="text/css" href="style.php">';

$id = optional_param('id',0,PARAM_INT);    // Course Module ID

//Values from form

$day = optional_param('day',null, PARAM_INT);
$day = trim($day);
if (strlen($day) == 1) {
    $day = '0'.$day;
}
$month = optional_param('month',null, PARAM_INT);
$month = trim($month);
if (strlen($month) == 1) {
    $month = '0'.$month;
}
$year = optional_param('year',null, PARAM_INT);

$starthour = optional_param('starthour',null, PARAM_INT);
$startminutes = optional_param('startminutes',null, PARAM_INT);

$endhour = optional_param('endhour',null, PARAM_INT);
$endminutes = optional_param('endminutes',null, PARAM_INT);

$summary = optional_param('summary',null, PARAM_TEXT);
$description = optional_param('description',null, PARAM_TEXT);


$room_reservation_id = optional_param('room_reservation_id',0, PARAM_TEXT);
//

comity_check($id);
comity_header($id,'addevent','add_event.php?id='.$id);

echo '<div><div class="title">'.get_string('addevent', 'comity').'</div>';

echo get_string('addingeventpleasewait', 'comity');

echo '<span class="content">';

echo '</span></div>';

comity_footer();

$new_event = new stdClass();
$new_event->user_id = $USER->id;
$new_event->comity_id = $id;
$new_event->day = $day;
$new_event->month = $month;
$new_event->year = $year;
$new_event->starthour = $starthour;
$new_event->startminutes = $startminutes;
$new_event->endhour = $endhour;
$new_event->endminutes = $endminutes;
$new_event->summary = $summary;
$new_event->description = $description;
$new_event->room_reservation_id =$room_reservation_id;
$new_event->stamp_start = $year.$month.$day.$starthour.$startminutes.'00';
$new_event->stamp_end = $year.$month.$day.$endhour.$endminutes.'00';
$new_event->stamp_t_start = $year.$month.$day.'T'.$starthour.$startminutes.'00';
$new_event->stamp_t_end = $year.$month.$day.'T'.$endhour.$endminutes.'00';

if (!$DB->insert_record('comity_events', $new_event)) {
    echo 'Data was not saved';
}

echo '<script type="text/javascript">';
echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/events.php?id='.$id.'";';
echo '</script>';

?>
