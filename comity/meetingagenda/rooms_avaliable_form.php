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
 * @package   block_roomscheduler
 * @copyright 2010 Raymond Wainman - University of Alberta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
class rooms_avaliable_form {

    private static $formname= 'AvaliableRooms';
    private $course = "";

    public function __toString() {
        global $CFG;

        $formname = rooms_avaliable_form::$formname;

     // $string = '<a href="#apptForm_2" id="avaliable_rooms_link" class="inline"></a>';


        $string = '<div style="display:none">';
        $string .= '<div id="apptForm_2">';

        //reservation id
        $string .= '<input type="hidden" name="reservation_id" value="">';
        $string .= '<input type="hidden" name="form_name" value="'.$formname.'">';
        $string .= '<input type="hidden" name="base_url" value="'.$CFG->wwwroot.'">';
        $string .= '<input type="hidden" name="courseid" value="'.$this->course.'">';
        $string .= '<input type="hidden" name="committee_Name" value="'.$this->course.'">';
        $string .= '<input type="hidden" name="eventid" value="'.$this->course.'">';


        //Main Form
        $string .= '<div id="details" class="calendar_apptForm_box">';


        //Start Time
        $string .= '<input type="hidden" name="' . $formname . '_startTime_date" size="8">';
        $string .= '<input type="hidden" name="' . $formname . '_startTime" size="8">';

       
        //End Time
        $string .= '<input type="hidden" name="' . $formname . '_endTime_date" size="8" onkeyup="get_avaliable_rooms(\''.$formname.'\');">';
        $string .= '<input type="hidden" name="' . $formname . '_endTime" size="8" onkeyup="get_avaliable_rooms(\''.$formname.'\');">';
$string .= '</form>';

        $string.= '<div id="rooms_available_header"><center></center></div>';
        $string.= '<div id="rooms_available"><center></center></div>';

        $string .= '<button id="' . $formname . '_close" value="' . get_string('close', 'block_roomscheduler') . '" onclick="$.fancybox.close();return false;">Close</button>';
        $string .= '</div>';    //End of main form
   

        


        $string .= '</div>';
        $string .= '</div>';

        return $string;
    }

    /* STATIC FUNCTIONS */

     /**
     * Generates a dropdown for selecting time
     *
     * @param string $name form element name
     * @param int $selectedvalue selected value (eg.1830 == 18h30)
     * @return string markup for output
     */
    public static function apptForm_timeDropdown($name, $selectedvalue='', $onchange='') {
        $string = '<select name="' . $name . '" onchange="' . $onchange . '">';
        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute+=10) {
                if ($minute == 0) {
                    $minute = '00';
                }
                $string .= '<option value="' . $hour . $minute . '" ';
                if ($selectedvalue == ($hour . $minute)) {
                    $string .= 'SELECTED';
                }
                $string .= '>';
                $string .= $hour . 'h' . $minute;
                $string .= '</option>';
            }
        }
        $string .= '</select>';
        return $string;
    }


    public static function apptForm_formName(){
        return rooms_avaliable_form::$formname;
    }

public function initalize_popup($eventid,$courseid, $starttime, $endtime, $committeeName){

print '<script type="text/javascript">';
print "rooms_avaliable_popup('$eventid','".rooms_avaliable_form::$formname."','$starttime','$endtime',$courseid,'$committeeName');";
print '</script>';
}

public function initalize_popup_newEvent($courseid, $committeeName){

print '<script type="text/javascript">';
print "rooms_avaliable_popup_newEvent('".rooms_avaliable_form::$formname."',$courseid,'$committeeName');";
print '</script>';
}

}
?>
