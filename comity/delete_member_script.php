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

$member = optional_param('member',0,PARAM_INT);

comity_check($id);
comity_header($id,'deletemember','view.php?id='.$id);

echo '<div><div class="title">'.get_string('deletemember', 'comity').'</div>';

echo get_string('deletingmember', 'comity');

echo '<span class="content">';

echo '</span></div>';

comity_footer();


$DB->delete_records('comity_planner_response', array('planner_user_id'=>$member));
$DB->delete_records('comity_planner_users', array('comity_member_id'=>$member));
$DB->delete_records('comity_members', array('id'=>$member));

echo '<script type="text/javascript">';
echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/view.php?id='.$id.'";';
echo '</script>';

?>
