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
global $CFG;
?>
.cornerBox { position: relative; background: #cfcfcf; }
.lightcornerBox { position: relative; background: #dadbda; }
.corner { position: absolute; width: 10px; height: 10px; background: url('<?php echo $CFG->wwwroot;?>/mod/comity/pix/corners.gif') no-repeat; font-size: 0%; }
.lightcorner { position: absolute; width: 10px; height: 10px; background: url('<?php echo $CFG->wwwroot;?>/mod/comity/pix/lightcorners.jpg') no-repeat; font-size: 0%; }
.cornerBoxInner { padding: 10px; }
.TL { top: 0; left: 0; background-position: 0 0; }
.TR { top: 0; right: 0; background-position: -10px 0; }
.BL { bottom: 0; left: 0; background-position: 0 -10px; }
.BR { bottom: 0; right: 0; background-position: -10px -10px; }

.content { font-size: 12px; }
.title {font-weight: bold; border-bottom: 1px solid black; margin-bottom:10px; }
.add {font-size: 12px; font-weight:0; color:black;}
.addentrylink:link {color:black; }
.addentrylink:hover {color:black; }

.file { border: 1px solid black; margin:10px;}

#image_cell{ margin-right:3px; }
#icon { margin:10px; }
#back { margin-left:10px; }