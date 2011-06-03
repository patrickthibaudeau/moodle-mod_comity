<?php // $Id: version.php,v 1.17.2.2 2008-07-11 02:54:54 moodler Exp $
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
 * @copyright 2011 Raymond Wainman, Patrick Thibaudeau, Dustin Durand (Campus St. Jean, University of Alberta)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of comity
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2011060301;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2010080100;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

?>
