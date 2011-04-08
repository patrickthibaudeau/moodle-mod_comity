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
 * @copyright 2011 Raymond Wainman, Dustin Durand, Patrick Thibaudeau (Campus St. Jean, University of Alberta)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once('lib.php');
require_once('lib_comity.php');
echo '<link rel="stylesheet" type="text/css" href="style.php">';

$id = optional_param('id',0,PARAM_INT);    // Course Module ID

// print header
comity_check($id);
comity_header($id,'members','view.php?id='.$id);

//If there is a president, show here
if($president = $DB->get_record('comity_members', array('comity_id'=>$id , 'role_id'=>1))) {
    $president_user = $DB->get_record('user', array('id'=>$president->user_id));

    echo '<div><div class="title">'.get_string('president', 'comity').'</div>';

    echo '<span class="content"><a href="mailto:'.$president_user->email.'">'.$president_user->firstname.' '.$president_user->lastname.'</a>';
    
    if(comity_isadmin($id)) {
        echo ' - <a href="'.$CFG->wwwroot.'/mod/comity/delete_member_script.php?id='.$id.'&member='.$president->id.'" onClick="return confirm(\''.get_string('deletememberquestion', 'comity').'\');"><img src="'.$CFG->wwwroot.'/mod/comity/pix/delete.gif"></a>';
    }
    echo '<br/><br/></span></div>';
}


//If there is a vice-president, show here
if($vicepresident = $DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>2))) {
    $vicepresident_user = $DB->get_record('user', array('id'=>$vicepresident->user_id));

    echo '<div><div class="title">'.get_string('vicepresident', 'comity').'</div>';

    echo '<span class="content"><a href="mailto:'.$vicepresident_user->email.'">'.$vicepresident_user->firstname.' '.$vicepresident_user->lastname.'</a>';

    if(comity_isadmin($id)) {
        echo ' - <a href="'.$CFG->wwwroot.'/mod/comity/delete_member_script.php?id='.$id.'&member='.$vicepresident->id.'" onClick="return confirm(\''.get_string('deletememberquestion', 'comity').'\');"><img src="'.$CFG->wwwroot.'/mod/comity/pix/delete.gif"></a>';
    }
    echo '<br/><br/></span></div>';
}

//If there is an administrator, show here
if($administrator = $DB->get_record('comity_members', array('comity_id'=>$id, 'role_id'=>4))) {
    $administrator_user = $DB->get_record('user', array('id'=>$administrator->user_id));

    echo '<div><div class="title">'.get_string('administrator', 'comity').'</div>';

    echo '<span class="content"><a href="mailto:'.$administrator_user->email.'">'.$administrator_user->firstname.' '.$administrator_user->lastname.'</a>';

    if(comity_isadmin($id)) {
        echo ' - <a href="'.$CFG->wwwroot.'/mod/comity/delete_member_script.php?id='.$id.'&member='.$administrator->id.'" onClick="return confirm(\''.get_string('deletememberquestion', 'comity').'\');"><img src="'.$CFG->wwwroot.'/mod/comity/pix/delete.gif"></a>';
    }
    echo '<br/><br/></span></div>';
}

//All other members show up here
$memberssql = 'SELECT mdl_user.id, mdl_user.email, mdl_user.lastname, mdl_user.firstname, mdl_comity_members.comity_id, mdl_comity_members.id
            FROM mdl_comity_members INNER JOIN mdl_user ON mdl_comity_members.user_id = mdl_user.id
            WHERE mdl_comity_members.comity_id ='.$id.' AND mdl_comity_members.role_id =3 ORDER BY mdl_user.lastname';

//echo $memberssql;
//   if($member_check = get_record('comity_members', 'comity_id', $id, 'role_id', 3)){
if ($members = $DB->get_records_sql($memberssql)) {
    
    echo '<div><div class="title">'.get_string('members', 'comity').'</div>';

    foreach($members as $member) {

        echo '<span class="content"><a href="mailto:'.$member->email.'">'.$member->firstname.' '.$member->lastname.'</a>';

        if(comity_isadmin($id)) {
            echo ' - <a href="'.$CFG->wwwroot.'/mod/comity/delete_member_script.php?id='.$id.'&member='.$member->id.'" onClick="return confirm(\''.get_string('deletememberquestion', 'comity').'\');"><img src="'.$CFG->wwwroot.'/mod/comity/pix/delete.gif"></a>';
        }
        echo '<br/>';

    }

    echo '<br/></span></div>';
}

//use to create email button
$sql = 'SELECT '.$CFG->prefix.'user.email, '.$CFG->prefix.'comity_members.comity_id
            FROM '.$CFG->prefix.'comity_members INNER JOIN '.$CFG->prefix.'user ON '.$CFG->prefix.'comity_members.user_id = '.$CFG->prefix.'user.id
            WHERE '.$CFG->prefix.'comity_members.comity_id='.$id;
$email = '';
if($comity_members = $DB->get_records_sql($sql)) {
    foreach ($comity_members as $comity_member) {
        $email .= $comity_member->email.';';
    }
    $email_button = '<a href="mailto:'.$email.'"><img src="'.$CFG->wwwroot.'/mod/comity/pix/switch_plus.gif'.'">'.get_string('sendto','comity').'</a><br/>';
}
else {
    $email_button = '';
}

if(comity_isMember($id) || comity_isadmin($id)) {

    echo '<div class="add">';
    echo $email_button;
    if (comity_isadmin($id)){
        echo '<a href="'.$CFG->wwwroot.'/mod/comity/add_member.php?id='.$id.'"><img src="'.$CFG->wwwroot.'/mod/comity/pix/switch_plus.gif'.'">'.get_string('addmember', 'comity').'</a>';
    }
    echo '</div>';

}

comity_footer();

?>
