<?php
/// This file is part of Moodle - http://moodle.org/
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
 * A script to update the status of a given topic.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

global $DB;


if(isset($_POST['topic_id'])){
$topic_id = $_POST['topic_id'];
$topic_status = $_POST['status'];
$return_url = $_POST['return_url'];


if($DB->record_exists('comity_agenda_topics', array('id'=>$topic_id)))  {

    $dataobject = new stdClass();
    $dataobject->id = $topic_id;
    $dataobject->status = $topic_status;

$DB->update_record('comity_agenda_topics', $dataobject, $bulk=false);

}


redirect($return_url);

}




?>
