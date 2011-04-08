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

// print header
comity_check($id);
comity_header($id,'planner','planner.php?id='.$id);

//content
if(comity_isadmin($id)){
    echo '<input type="button" value="'.get_string('newplanner','comity').'" onclick="window.location.href=\''.$CFG->wwwroot.'/mod/comity/newplanner.php?id='.$id.'\';" /><br/><br/>';
}

//If a person is not a member(ANY ROLE-pres, member, etc), no content shown.
if(!comity_isMember($id)){
   if (comity_isadmin($id)){
       comity_footer();
       return;
   } else {
   print "<b>".get_string('not_member','comity')."</b>";

}
comity_footer();
exit();
}






$planners = $DB->get_records('comity_planner',array('comity_id'=>$id),'id DESC');



foreach($planners as $planner){
    echo '<div class="title">'.$planner->name;
    if(comity_isadmin($id)){
        echo '&nbsp;&nbsp;&nbsp;<a href="'.$CFG->wwwroot.'/mod/comity/newplanner.php?id='.$id.'&planner='.$planner->id.'"><img src="'.$CFG->wwwroot.'/pix/t/edit.gif"></a>';
        if($planner->active==1){
            echo '&nbsp;&nbsp;&nbsp;<a href="'.$CFG->wwwroot.'/mod/comity/planner_active.php?id='.$planner->id.'"><img src="'.$CFG->wwwroot.'/pix/i/hide.gif"></a>';
        }
        else{
            echo '&nbsp;&nbsp;&nbsp;<a href="'.$CFG->wwwroot.'/mod/comity/planner_active.php?id='.$planner->id.'"><img src="'.$CFG->wwwroot.'/pix/t/show.gif"></a>';
        }
        
    }
 echo '</div>';
    print '<table><tr><td>';
   
    echo $planner->description;
    if($planner->active==0){
        echo '<div style="font-size:10px;font-weight:bold;margin-top:10px;margin-bottom:10px;">'.get_string('closed','comity').'</div>';
        echo '<input type="button" value="'.get_string('viewresults','comity').'" onclick="window.location.href=\''.$CFG->wwwroot.'/mod/comity/viewplanner.php?id='.$planner->id.'\';">';
    }
    else{


        $comity_member = $DB->get_record('comity_members', array('comity_id'=>$id,'user_id'=>$USER->id));
        $planner_user = $DB->get_record('comity_planner_users', array('planner_id'=>$planner->id,'comity_member_id'=>$comity_member->id));

        //Member added to committee, trying to view event -- must add them
        if(!$planner_user){
        $planner_user = new stdClass;
        $planner_user->planner_id = $planner->id;;
        $planner_user->comity_member_id = $comity_member->id;
        $planner_user->rule = 0;  //added as optional -- someone with edit can update in planner if needed

        $planner_id = $DB->insert_record('comity_planner_users', $planner_user, $returnid=true, $bulk=false);
        $planner_user = $DB->get_record('comity_planner_users', array('planner_id'=>$planner->id,'comity_member_id'=>$comity_member->id));

        }



        if($DB->get_records('comity_planner_response', array('planner_user_id'=>$planner_user->id))){
            echo '<div style="color:green;font-size:10px;font-weight:bold;margin-top:10px;margin-bottom:10px;">'.get_string('youhaveresponded','comity').'</div>';
        }
        else{
            echo '<div style="color:red;font-size:10px;font-weight:bold;margin-top:10px;margin-bottom:10px;">'.get_string('youhavenotresponded','comity').'</div>';
        }

        print '</td><td>';
        display_planner_results($planner->id, $id);
        print '</td></tr></table>';

        echo '<input type="button" value="'.get_string('respond','comity').'" onclick="window.location.href=\''.$CFG->wwwroot.'/mod/comity/viewplanner.php?id='.$planner->id.'\';">';
    }
    echo '</table><br/><br/>';
}


//footer
comity_footer();

?>
