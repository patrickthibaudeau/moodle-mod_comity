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
 * The specialized moodle user selector.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/user/selector/lib.php");

class my_user_selector extends user_selector_base {


    function __construct($name,$rows=10,$options = array()) {
        parent::__construct($name,$options);
        $this->set_rows($rows);
    }

   function find_users($search) {
       global $DB;

       list($wherecondition, $params) = $this->search_sql($search, 'u');

       //print_object($wherecondition);
       //print_object($params);
        $sql = "SELECT * FROM {user} u where $wherecondition";
       $users = $DB->get_records_sql($sql,$params);
       
       return array(get_string('add_moodle_user','comity') => $users);
    }

    function get_options() {
        $options = parent::get_options();
        $options['file'] = '/mod/comity/meetingagenda/moodle_user_selector.php';
        return $options;
        
        }

}

?>
