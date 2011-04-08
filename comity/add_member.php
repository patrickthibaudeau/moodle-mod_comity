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

$PAGE->requires->js('/mod/comity/ajaxlib.js');

// print header
comity_check($id);
comity_header($id,'addmember','add_member.php?id='.$id);

if(comity_isadmin($id)){

    echo '<div><div class="title">'.get_string('addmember', 'comity').'</div>';

    echo '<form action="'.$CFG->wwwroot.'/mod/comity/add_member_script.php?id='.$id.'" method="POST" name="newmember">';
    echo '<table width=100% border=0>';

    $users = $DB->get_records('user', array('deleted'=>'0'),'lastname');

    echo '<tr><td>'.get_string('name', 'comity').' : </td>';
    echo '<td width=85%><div id="user"><select name="user">';
    foreach($users as $user) {
        if(!$DB->get_record('comity_members', array('user_id'=>$user->id,'comity_id'=>$id))) {
            echo '<option value="'.$user->id.'">'.$user->lastname.', '.$user->firstname.'</option>';
        }
    }
    echo '</select></div>';

    echo '</td></tr>';
    echo '<tr><td></td>';
    echo '<td><input style="color:grey;" type="text" onkeyup="ajax_post(\'searchmember.php?str=\'+this.value,\'user\');" value="'.get_string('search','comity').'" onfocus="if(this.value==\''.get_string('search','comity').'\'){this.value=\'\';}" onblur="if(this.value==\'\'){this.value=\''.get_string('search','comity').'\';}"></td>';
    echo '</tr>';
    echo '<tr><td>'.get_string('role', 'comity').' : </td>';
    echo '<td><select name="role">';
    echo '<option value="3">'.get_string('member', 'comity').'</option>';
    //Only one president
    if(!$DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>1))) {
        echo '<option value="1">'.get_string('president', 'comity').'</option>';
    }
    //Only one co-president
    if(!$DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>2))) {
        echo '<option value="2">'.get_string('vicepresident', 'comity').'</option>';
    }
    //Only one administrator
    if(!$DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>4))) {
        echo '<option value="4">'.get_string('administrator', 'comity').'</option>';
    }
    echo '</select></td></tr>';
    echo '<tr><td><br/></td><td></td></tr>';
    echo '<tr>';
    echo '<td></td>';
    echo '<td><input type="submit" value="'.get_string('addmember', 'comity').'">';
    echo '<input type="button" value="'.get_string('cancel', 'comity').'" onClick="parent.location=\''.$CFG->wwwroot.'/mod/comity/view.php?id='.$id.'\'"></td>';
    echo '</tr>';
    echo '</table>';
    echo '</form>';

    echo '<span class="content">';

    echo '</span></div>';

}

comity_footer();

?>
