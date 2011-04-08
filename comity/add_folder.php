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

//If new
$id = optional_param('id',0,PARAM_INT);    // Course Module ID
$file = optional_param('file',0,PARAM_INT); // parent

//If editing
$fileid = optional_param('fileid',0,PARAM_INT); // if editing
if($fileobj = $DB->get_record('comity_files',array('id'=>$fileid))) {
    $file = $fileobj->parent;
}
else {
    $fileobj->parent = 0;
}

comity_check($id);
if($fileid=='') {
    $title = 'addfolder';
}
else {
    $title = 'editfolder';
}
comity_header($id,$title,'files.php?id='.$id.'&file='.$file);

if(comity_isadmin($id)) {

    if($fileid=='') {
        echo '<div><div class="title">'.get_string('addfolder', 'comity').'</div>';
    }
    else {
        echo '<div><div class="title">'.get_string('editfolder', 'comity').'</div>';
    }

    //Build breadcrumb trail recursively
    $breadcrumb = breadcrumb($file);
    //print_object($breadcrumb);
    $length = sizeof($breadcrumb);
    $counter = 1;
    $private = false;
    foreach($breadcrumb as $link) {
        echo '<a href="'.$link->url.'">'.$link->name.'</a>';
        if($counter!=$length) {
            //Print arrow
            echo '&nbsp;&nbsp;';
            echo '<img src="'.$CFG->wwwroot.'/pix/t/collapsed.png">';
            echo '&nbsp;&nbsp;';
        }
        if($link->private==1) {
            $private = true;
        }
        $counter++;
    }

    echo '<form action="'.$CFG->wwwroot.'/mod/comity/add_folder_script.php?id='.$id.'" method="POST" name="newfolder">';
    echo '<table width=100% border=0>';

    if($fileid!='') {
        $fold = $DB->get_record('comity_files', array('id'=>$fileid));
        $file = $fold->parent;
    }
    else {
        $fold->name = '';
        $fold->private = 0;
    }

    echo '<tr><td>'.get_string('name', 'comity').' : </td>';
    echo '<td width=85%><input type="text" name="name" value="'.$fold->name.'">';
    echo '</td></tr>';
    echo '<tr><td>'.get_string('private', 'comity').' : </td>';
    if($private) {
        echo '<td>'.get_string('yes','comity').'</td>';
        echo '<input type="hidden" name="private" value="1">';
    }
    else {
        echo '<td><select name="private">';
        echo '<option value="0" ';
        if($fold->private==0)
            echo 'SELECTED';
        echo '>'.get_string('no', 'comity').'</option>';
        echo '<option value="1" ';
        if($fold->private==1)
            echo 'SELECTED';
        echo '>'.get_string('yes', 'comity').'</option>';
        echo '</select></td>';
    }
    echo '</tr>';
    echo '<tr><td><br/></td><td></td></tr>';
    echo '<tr>';
    echo '<input type="hidden" name="file" value="'.$file.'">';
    echo '<input type="hidden" name="fileid" value="'.$fileid.'">';
    echo '<td></td><td><input type="submit" value="'.get_string('submit', 'comity').'">';
    echo '<input type="button" value="'.get_string('cancel', 'comity').'" onClick="window.location=\''.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file='.$file.'\'"></td>';
    echo '</tr>';
    echo '</table>';
    echo '</form>';

    echo '<span class="content">';

    echo '</span></div>';

}

comity_footer();

?>
