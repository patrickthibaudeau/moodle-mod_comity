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
require_once('lib.php');
require_once('lib_comity.php');
echo '<link rel="stylesheet" type="text/css" href="style.php">';

$id = required_param('id',PARAM_INT);
$comity_id = required_param('comity_id',PARAM_INT);

//Values from form

$day = optional_param('day',0, PARAM_INT);
$day = trim($day);
if (strlen($day) == 1) {
    $day = '0'.$day;
}
$month = optional_param('month',0, PARAM_INT);
$month = trim($month);
if (strlen($month) == 1) {
    $month = '0'.$month;
}
$year = optional_param('year',0, PARAM_INT);

$starthour = optional_param('starthour',0, PARAM_INT);
$startminutes = optional_param('startminutes',0, PARAM_INT);

$endhour = optional_param('endhour',0, PARAM_INT);
$endminutes = optional_param('endminutes',0, PARAM_INT);

$summary = optional_param('summary',null, PARAM_TEXT);
$description = optional_param('description',null, PARAM_TEXT);

//

comity_check($comity_id);
comity_header($comity_id,'editevent','edit_event.php?id='.$comity_id.'&event_id='.$id);

echo '<div><div class="title">'.get_string('editevent', 'comity').'</div>';

echo get_string('modifyingeventpleasewait', 'comity');

echo '<span class="content">';

echo '</span></div>';

comity_footer();


$edit_event = new stdClass();
$edit_event->id = $id;
$edit_event->user_id = $USER->id;
$edit_event->comity_id = $comity_id;
$edit_event->day = $day;
$edit_event->month = $month;
$edit_event->year = $year;
$edit_event->starthour = $starthour;
$edit_event->startminutes = $startminutes;
$edit_event->endhour = $endhour;
$edit_event->endminutes = $endminutes;
$edit_event->summary = $summary;
$edit_event->description = $description;
$edit_event->stamp_start = $year.$month.$day.$starthour.$startminutes.'00';
$edit_event->stamp_end = $year.$month.$day.$endhour.$endminutes.'00';
$edit_event->stamp_t_start = $year.$month.$day.'T'.$starthour.$startminutes.'00';
$edit_event->stamp_t_end = $year.$month.$day.'T'.$endhour.$endminutes.'00';

if (!$DB->update_record('comity_events', $edit_event)) {
    echo 'Data was not saved';
}

echo '<script type="text/javascript">';
echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/events.php?id='.$comity_id.'";';
echo '</script>';

?>
