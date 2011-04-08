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

$name = optional_param('name',0,PARAM_RAW);
$private = optional_param('private',0,PARAM_INT);
$parent = optional_param('file',0,PARAM_INT);
$fileid = optional_param('fileid',0,PARAM_INT);

comity_check($id);
if($fileid==''){
    $title = 'addfolder';
}
else {
    $title = 'editfolder';
}
comity_header($id,$title,'file.php?id='.$id.'&file='.$parent);

echo '<div><div class="title">'.get_string('addfolder', 'comity').'</div>';

if($fileid=='')
    echo get_string('addingfolderpleasewait', 'comity');
else
    echo get_string('editingfolderpleasewait','comity');

echo '<span class="content">';

echo '</span></div>';

comity_footer();

if($fileid=='') {

    $new_folder = new stdClass();
    $new_folder->comity_id = $id;
    $new_folder->name = $name;
    $new_folder->parent = $parent;
    $new_folder->private = $private;
    $new_folder->type = 0;
    $new_folder->user_id = $USER->id;
    $new_folder->timemodified = time();

    $DB->insert_record('comity_files', $new_folder);
}
else {
    $folder = new stdClass();
    $folder->id = $fileid;
    $folder->name = $name;
    $folder->parent = $parent;
    $folder->private = $private;

    $DB->update_record('comity_files', $folder);
}

echo '<script type="text/javascript">';
echo 'window.location.href="'.$CFG->wwwroot.'/mod/comity/files.php?id='.$id.'&file='.$parent.'";';
echo '</script>';

?>
