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

$id = optional_param('id',0,PARAM_INT);    // Course Module ID

$course_mod = get_record('course_modules', 'id', $id);
$comity_instance = get_record('comity', 'id', $course_mod->instance);

$comity_name = $comity_instance->name;

$course_number = get_record('course_modules', 'id', $id);
$course_name = get_record('course', 'id', $course_number->course);

$navlinks = array(
        array(
                'name'=>$course_name->shortname,
                'link'=>$CFG->wwwroot.'/course/view.php?id='.$course_name->id,
                'type'=>'misc'
        ),
        array(
                'name'=>$comity_name,
                'link'=>$CFG->wwwroot.'/mod/comity/view.php?id='.$id,
                'type'=>'misc'
        ),

        array(
                'name'=>get_string('about', 'comity'),
                'link'=>'',
                'type'=>'misc'
        )

);

$nav = build_navigation($navlinks);
print_header($comity_name, $comity_name, $nav);

//context needed for access rights
$context = get_context_instance(CONTEXT_USER, $USER->id);

print_content_header();

print_inner_content_header($style_nav);

comity_printnavbar($id);

print_inner_content_footer();

print_inner_content_header($style_content);

echo '<div><div class="title">'.get_string('description', 'comity').'</div>';

echo '<span class="content">'.$comity_instance->description;

echo '</span></div>';

print_inner_content_footer();

echo '<div style="clear:both;"></div>';

print_content_footer();


print_footer();

?>
