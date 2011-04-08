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

/// Library of functions and constants for module comity

function comity_add_instance($comity) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    global $DB;

    $comity->name = format_string($comity->name);
    $comity->timecreated = time();
    $comity->timemodified = time();
    $comity->description = null;
	$comity->intro = '';
	$comity->introformat = 0;

    return $DB->insert_record('comity', $comity);
}


function comity_update_instance($comity) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod_form.php) this function
/// will update an existing instance with new data.

    global $DB;

    $comity->id = $comity->instance;
    $comity->timemodified = time();
    $comity->name = format_string($comity->name);
    $comity->description = null;

    return $DB->update_record("comity", $comity);
}


function comity_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

    global $DB;

    if (! $comity = $DB->get_record("comity", array("id"=>$id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("comity", array("id"=>$comity->id))) {
        $result = false;
    }

    return $result;
}

function comity_get_participants($comityid) {
//Returns the users with data in one resource
//(NONE, but must exist on EVERY mod !!)

    return false;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 */
function comity_get_coursemodule_info($coursemodule) {
    global $DB;

    if ($comity = $DB->get_record('comity', array('id'=>$coursemodule->instance))) {
        $info = new object();
        $info->name = $comity->name;
        return $info;
    } else {
        return null;
    }
}

function comity_get_view_actions() {
    return array();
}

function comity_get_post_actions() {
    return array();
}

function comity_get_types() {
    $types = array();

    $type = new object();
    $type->modclass = MOD_CLASS_ACTIVITY;
    $type->type = "comity";
    $type->typestr = get_string('modulename', 'comity');
    $types[] = $type;

    return $types;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function comity_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 */
function comity_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */
function comity_supports($feature) {
    switch($feature) {
        case FEATURE_IDNUMBER:                return false;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return false;
        case FEATURE_MOD_INTRO:               return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return false;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:          return false;

        default: return null;
    }
}

function comity_pluginfile($course, $cminfo, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    $fs = get_file_storage();
    $relativepath = '/'.implode('/', $args);
    
    $hash = $fs->get_pathname_hash($context->id, 'mod_comity', 'comity', 0, $relativepath, '');

    $file = $fs->get_file_by_hash($hash);

    // finally send the file
    send_stored_file($file, 86400, 0, true);
}

?>
