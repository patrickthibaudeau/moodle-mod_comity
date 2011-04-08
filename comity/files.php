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
$file = optional_param('file',0,PARAM_INT); //File id (usually a folder)

comity_check($id);
comity_header($id,'files','files.php?id='.$id.'&file='.$file);

$course_mod = get_coursemodule_from_id('comity', $id);
$context = get_context_instance(CONTEXT_MODULE,$course_mod->id);

$folder = '';
if($file!=0) {
    $folderobj = $DB->get_record('comity_files',array('id'=>$file));
    $folder = '-'.$folderobj->name;
}
else {
    $folder = '-'.get_string('root','comity');
}

echo '<div><div class="title">';
echo get_string('files', 'comity').$folder;
echo '</div>';

echo '<span class="content">';

//Build breadcrumb trail recursively
$breadcrumb = breadcrumb($file);
//print_object($breadcrumb);
$length = sizeof($breadcrumb);
$counter = 1;
foreach($breadcrumb as $link) {
    echo '<a href="'.$link->url.'">'.$link->name.'</a>';
    if($counter!=$length) {
        //Print arrow
        echo '&nbsp;&nbsp;';
        echo '<img src="'.$CFG->wwwroot.'/pix/t/collapsed.png">';
        echo '&nbsp;&nbsp;';
    }
    $counter++;
}

$files = $DB->get_records('comity_files', array('comity_id'=>$id,'parent'=>$file),'type ASC, name ASC');

$PRIVATE = 0;
if(comity_isMember($id) || comity_isadmin($id)) {
    $PRIVATE = 1;
}

foreach($files as $fileobj) {
    //private files and folders
    if($fileobj->private==1 && $PRIVATE==1) {
        if($fileobj->type==0) {
            print_folder($fileobj);
        }
        else if($fileobj->type==1) {
            print_file($fileobj);
        }
    }
    else if($fileobj->private==0) {
        if($fileobj->type==0) {
            print_folder($fileobj);
        }
        else if($fileobj->type==1) {
            print_file($fileobj);
        }
    }
}

//echo '<a href="'.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'"><img id="back" src="'.$CFG->wwwroot.'/mod/comity/pix/up.png" title="'.get_string('goback','comity').'"></a>';

echo '</span></div>';

if($PRIVATE==1) {
    echo '<div class="add">';
    echo '<br/>';
    if (comity_isadmin($id)){
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/add_folder.php?id='.$id.'&file='.$file.'"><img src="'.$CFG->wwwroot.'/mod/comity/pix/switch_plus.gif'.'">'.get_string('addfolder', 'comity').'</a><br/>';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/add_file.php?id='.$id.'&file='.$file.'"><img src="'.$CFG->wwwroot.'/mod/comity/pix/switch_plus.gif'.'">'.get_string('addfile', 'comity').'</a>';
    }
    echo '</div>';
}

comity_footer();





function print_folder($fold) {
    global $id,$CFG,$PRIVATE;

    echo '<div class="file">';
    echo '<table>';
    echo '<tr>';
    echo '<td id="image_cell">';
    echo '<a href="'.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file='.$fold->id.'"><img id="icon" src="'.$CFG->wwwroot.'/mod/comity/pix/folder.png"></a>';
    echo '</td>';
    echo '<td>';
    echo '<b>'.$fold->name.'</b>';
    if($PRIVATE==1) {
        if (comity_isadmin($id)){
        echo ' - <a href="'.$CFG->wwwroot.'/mod/comity/delete_folder_script.php?id='.$id.'&fileid='.$fold->id.'" onClick="return confirm(\''.get_string('deletefolderquestion', 'comity').'\');"><img src="'.$CFG->wwwroot.'/mod/comity/pix/delete.gif"></a>';
        echo '<a href="'.$CFG->wwwroot.'/mod/comity/add_folder.php?id='.$id.'&fileid='.$fold->id.'"><img src="'.$CFG->wwwroot.'/mod/comity/pix/edit.gif"></a>';
        }

    }
    if($PRIVATE==1) {
        echo '<br/>(';
        if($fold->private==0) {
            echo get_string('public','comity');
        }
        else {
            echo get_string('private','comity');
        }
        echo ')';
    }
    echo '<br/><br/>';
    //echo '<a href="'.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file='.$fold->id.'">'.get_string('openfolder','comity').'</a>';
    echo '</td></tr></table>';
    echo '</div>';
}

function print_file($file) {
    global $CFG,$PRIVATE,$course_mod,$context,$id,$DB;

    echo '<div class="file">';
    echo '<table>';
    echo '<tr>';
    //Filetype logo
    echo '<td>';
    //$file_path = get_file_url($course_mod->course.'/comity/'.$id.'/'.$file->timemodified.'/'.$file->filename);
    $file_path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$context->id.'/mod_comity/comity/'.$course_mod->id.'/'.$file->timemodified.'/'.$file->name);

    echo '<a href="'.$file_path.'">';
    if(strpos($file->name, '.doc') || strpos($file->name, '.docx')) {
        //Word logo
        echo '<img id="icon" src="'.$CFG->wwwroot.'/mod/comity/pix/word.png">';
    }
    else if(strpos($file->name, '.pdf')) {
        //PDF logo
        echo '<img id="icon" src="'.$CFG->wwwroot.'/mod/comity/pix/pdf.png">';
    }
    else if(strpos($file->name, '.xls') || strpos($file->name, '.xlsx')) {
        //Excel logo
        echo '<img id="icon" src="'.$CFG->wwwroot.'/mod/comity/pix/excel.png">';
    }
    else if(strpos($file->name, '.ppt') || strpos($file->name, '.pptx')) {
        //Powerpoint logo
        echo '<img id="icon" src="'.$CFG->wwwroot.'/mod/comity/pix/ppt.png">';
    }
    else {
        //Generic Logo
        echo '<img id="icon" src="'.$CFG->wwwroot.'/mod/comity/pix/blank.png">';
    }
    echo '</td>';

    //File download link
    echo '<td>';
    echo '</a>';

    echo '<b>'.$file->name.'</b>';
    if($PRIVATE==1) {
        if (comity_isadmin($id)){
        echo ' - <a href="'.$CFG->wwwroot.'/mod/comity/delete_file_script.php?id='.$id.'&file_id='.$file->id.'&file='.$file->parent.'" onClick="return confirm(\''.get_string('deletefilequestion', 'comity').'\');"><img src="'.$CFG->wwwroot.'/mod/comity/pix/delete.gif"></a>';
        echo '<a href="'.$CFG->wwwroot.'/mod/comity/add_file.php?id='.$id.'&fileid='.$file->id.'"><img src="'.$CFG->wwwroot.'/mod/comity/pix/edit.gif"></a>';
        }
        echo '<br/>';
        if($file->private==0) {
            echo '('.get_string('public','comity').')';
        }
        else if($file->private==1) {
            echo '('.get_string('private','comity').')';
        }
    }
    echo '<br/>';
    $user_name = $DB->get_record('user', array('id'=>$file->user_id));
    echo get_string('uploadedon', 'comity').' '.date("m-d-y", $file->timemodified).' '.get_string('by', 'comity').' '.$user_name->firstname.' '.$user_name->lastname.'<br/><br/>';

    echo '<a href="'.$file_path.'">'.get_string('download', 'comity').'</a>';

    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';
}

?>
