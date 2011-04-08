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
 * The general library for Agenda/Meeting Extension to Committee Module.
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * Converts month, as a number, to a literary term.
 *
 * @param int $monthAsNum Numerical representation of a month
 */
function toMonth($monthAsNum) {
    if ($monthAsNum == 1) {
        $month = get_string('january', 'comity');
    }
    if ($monthAsNum == 2) {
        $month = get_string('february', 'comity');
    }
    if ($monthAsNum == 3) {
        $month = get_string('march', 'comity');
    }
    if ($monthAsNum == 4) {
        $month = get_string('april', 'comity');
    }
    if ($monthAsNum == 5) {
        $month = get_string('may', 'comity');
    }
    if ($monthAsNum == 6) {
        $month = get_string('june', 'comity');
    }
    if ($monthAsNum == 7) {
        $month = get_string('july', 'comity');
    }
    if ($monthAsNum == 8) {
        $month = get_string('august', 'comity');
    }
    if ($monthAsNum == 9) {
        $month = get_string('september', 'comity');
    }
    if ($monthAsNum == 10) {
        $month = get_string('october', 'comity');
    }
    if ($monthAsNum == 11) {
        $month = get_string('november', 'comity');
    }
    if ($monthAsNum == 12) {
        $month = get_string('december', 'comity');
    }

    return $month;
}

/*
 * Pads an amount of minutes of time less than 10 to be 0X, ex: 4 = 04
 *
 * @param int $time An integer representing an amount of minutes.
 *
 */
function ZeroPaddingTime($time) {

    if (strlen($time) == 1) {
        return '0' . $time;
    }
    return $time;
}

/*
 * Turns a given amount of secounds into time, hh:mm
 *
 * @param int $secs An amount of seconds.
 */

function formatTime($secs) {
    $times = array(3600, 60, 1);
    $time = '';
    $tmp = '';
    for ($i = 0; $i < 2; $i++) {
        $tmp = floor($secs / $times[$i]);
        if ($tmp < 1) {
            $tmp = '00';
        } elseif ($tmp < 10) {
            $tmp = '0' . $tmp;
        }
        $time .= $tmp;
        if ($i < 1) {
            $time .= ':';
        }
        $secs = $secs % $times[$i];
    }
    return $time . " " . get_string('hours', 'comity');
    ;
}

  /*
     * Adds an element if $data is not null or empty
     *
     * @param object $mform Form Object
     * @param mixed $data Data to be put into object at a later time
     * @param string $html_id Unique html element identifier
     * @param string $string_label Label printed next to element
     */
    function conditionally_add_static($mform, $data, $html_id, $string_label){

        if(isset($data) && $data!=""){
      $mform->addElement('static', $html_id, $string_label, $data);
        }


    }

    /*
     * Loads given content to a specified DIV.
     *
     * @param string $div The div to load content into
     * @param string $content The content to load.
     */
function load_content($div, $content){

print '<script type="text/javascript">';
print 'document.getElementById(\''.$div.'\').innerHTML=\''.$content."'";
print '</script>';
}

/*
 * Runs a javascript fucntion with given parameters
 *
 * @param string $func The javascript function to call.
 * @param string $params The parameters of the javascript function delimted by ":"
 *
 */
function js_function($func,$params){

$parameters = explode(':',$params);

$string =  '<script type="text/javascript">';
$string .=  "$func(";

foreach($parameters as $parameter){
   $string .= "'$parameter',";
}

$string = substr($string,0,-2);
$string .= '\');</script>';


print $string;
}
?>
