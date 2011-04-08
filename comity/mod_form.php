<?php // $Id: mod_form.php,v 1.11.2.1 2008-02-21 14:11:18 skodak Exp $
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
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_comity_mod_form extends moodleform_mod {

    function definition() {
		global $CFG;

        $mform    =& $this->_form;

        //comity name
        $mform->addElement('text', 'name', get_string('name','comity'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        //intro
        //$this->add_intro_editor(true, get_string('description', 'comity'));

        $this->standard_coursemodule_elements();

        // buttons
        $this->add_action_buttons(true);

    }

}
?>
