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
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->dirroot.'/lib/uploadlib.php');
echo '<link rel="stylesheet" type="text/css" href="style.php">';

$id = optional_param('id',0,PARAM_INT);    // Course Module ID
$file = optional_param('file',0,PARAM_INT);
$fileid = optional_param('fileid',0,PARAM_INT);

if($fileobj = $DB->get_record('comity_files',array('id'=>$fileid))){
    $file = $fileobj->parent;
}

comity_check($id);
comity_header($id,'addfile','file.php?id='.$id.'&file='.$file);

if(comity_isadmin($id)) {

    $submitted = optional_param('submitted',null,PARAM_TEXT);
    $private = optional_param('private',null, PARAM_INT);

    if ($submitted == "yes") {

        $course_mod = get_coursemodule_from_id('comity', $id);
        $mod = get_context_instance(CONTEXT_MODULE,$course_mod->id);

        if($fileid=='') {
            $now = time();

            if (isset($_FILES['userfile']['name'])) {
                $name = $_FILES['userfile']['name'];
                $filename = $name;

                $fs = get_file_storage();

                $file_record = array('contextid'=>$mod->id,'component'=>'mod_comity', 'filearea'=>'comity', 'itemid'=>0, 'filepath'=>'/'.$course_mod->id.'/'.$now.'/',
                        'filename'=>$filename, 'timecreated'=>$now, 'timemodified'=>$now);

                $tmpfile = $_FILES['userfile']['tmp_name'];

                $fs->create_file_from_pathname($file_record,$tmpfile);

                //enter data into database table
                $insert = new object();
                $insert->user_id = $USER->id;
                $insert->name = $filename;
                $insert->parent = $file;
                $insert->private = $private;
                $insert->timemodified = $now;
                $insert->type = 1;
                //$insert->private = $private;
                $insert->comity_id = $id;

                // print_object($insert);

                if (!$DB->insert_record('comity_files',$insert)) {
                    echo 'not saved';
                    //print_object($insert);
                } else {
                    echo '<script type="text/javascript">';
                    echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file='.$file.'";';
                    echo '</script>';
                }
            }
        }
        else {
            $insert = new object();
            $insert->id = $fileid;
            $insert->private = $private;
            $insert->user_id = $USER->id;
            //$insert->timemodified = time();

            $DB->update_record('comity_files', $insert);
            echo '<script type="text/javascript">';
            echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file='.$file.'";';
            echo '</script>';
        }
    }

    if($fileid=='') {
        echo '<div><div class="title">'.get_string('addfile', 'comity').'</div>';
    }
    else {
        echo '<div><div class="title">'.get_string('editfile', 'comity').'</div>';
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
        if($link->private==1){
            $private = true;
        }
        $counter++;
    }

    echo '<table width=100% border=0>';

    echo '<form name="uploadform" id="uploadform" action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data" method="post">';
    echo '<input type="hidden" name="submitted" value="yes">';
    echo '<input type="hidden" name="id" value="'.$id.'">';

    echo '<tr><td>'.get_string('file', 'comity').' : </td>';
    if($fileid=='') {
        echo '<td width="85%"><input type="file" name="userfile"></td></tr>';
        $file_obj->private = 0;
    }
    else {
        $file_obj = $DB->get_record('comity_files',array('id'=>$fileid));
        echo '<td width="85%">'.$file_obj->name.'</td>';
    }
    echo '<tr><td>'.get_string('private', 'comity').' : </td>';
    if($private) {
        echo '<td>'.get_string('yes','comity').'</td>';
        echo '<input type="hidden" name="private" value="1">';
    }
    else {
        echo '<td><select name="private">';
        echo '<option value="0" ';
        if($file_obj->private==0)
            echo 'SELECTED';
        echo '>'.get_string('no', 'comity').'</option>';
        echo '<option value="1" ';
        if($file_obj->private==1)
            echo 'SELECTED';
        echo '>'.get_string('yes', 'comity').'</option>';
        echo '</select></td>';
    }
    echo '</tr>';
    echo '<input type="hidden" name="fileid" value="'.$fileid.'">';
    echo '<input type="hidden" name="file" value="'.$file.'">';
    echo '<tr><td><br/></td><td></td></tr>';
    echo '<tr><td></td><td><input type="submit" value="'.get_string('submit','comity').'">';
    echo '<input type="button" value="'.get_string('cancel', 'comity').'" onClick="parent.location=\''.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file='.$file.'\'"></td></tr>';

    echo '</table>';
    echo '</form>';

    echo '<span class="content">';

    echo '</span></div>';

}

comity_footer();

?>
