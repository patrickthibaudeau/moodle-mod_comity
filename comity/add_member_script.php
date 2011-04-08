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

$user = optional_param('user',0,PARAM_INT);
$role = optional_param('role',0,PARAM_INT);

comity_check($id);
comity_header($id,'addmember','add_member.php?id='.$id);

echo '<div><div class="title">'.get_string('addmember', 'comity').'</div>';

echo get_string('addingmemberpleasewait', 'comity');

echo '<span class="content">';

echo '</span></div>';

comity_footer();

$new_member = new stdClass();
$new_member->user_id = $user;
$new_member->role_id = $role;
$new_member->comity_id =$id;

if(!$new_member->user_id==0){ //Check if not adding an invalid user
$DB->insert_record('comity_members', $new_member);
}


echo '<script type="text/javascript">';
echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/view.php?id='.$id.'";';
echo '</script>';

?>
