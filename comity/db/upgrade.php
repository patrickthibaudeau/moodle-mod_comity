<?php  //$Id: upgrade.php,v 1.1.8.2 2008-07-11 02:54:54 moodler Exp $

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

// This file keeps track of upgrades to 
// the comity module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_comity_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;
$dbman = $DB->get_manager();
    $result = true;


       if ($oldversion < 2011030102) {

        // Define field room_reservation_id to be added to comity_events
        $table = new xmldb_table('comity_events');
        $field = new xmldb_field('room_reservation_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'stamp_t_end');

        // Conditionally launch add field room_reservation_id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('comity_agenda');
        $field = new xmldb_field('room_reservation_id');

        // Conditionally launch drop field room_reservation_id
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // comity savepoint reached
        upgrade_mod_savepoint(true, 2011030102, 'comity');
    }

	
	    if ($oldversion < 2011030101) {

        // Define field room_reservation_id to be dropped from comity_agenda
        $table = new xmldb_table('comity_agenda');
        $field = new xmldb_field('completed');

        // Conditionally launch drop field room_reservation_id
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
		
		 // Define field room_reservation_id to be added to comity_agenda
        $table = new xmldb_table('comity_agenda');
        $field = new xmldb_field('room_reservation_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'location');

        // Conditionally launch add field room_reservation_id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // comity savepoint reached
        upgrade_mod_savepoint(true, 2011030101, 'comity');
    }
	
	    if ($oldversion < 2011030100) {
	
	   // Define field hidden to be added to comity_agenda_topics
        $table = new xmldb_table('comity_agenda_topics');
         $field = new xmldb_field('hidden', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'status');

        // Conditionally launch add field hidden
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
	
	 // comity savepoint reached
        upgrade_mod_savepoint(true, 2011030100, 'comity');
	
	}

    if ($oldversion < 2011022200) {

       // Define field completed to be added to comity_agenda
        $table = new xmldb_table('comity_agenda');
        $field = new xmldb_field('completed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'location');

        // Conditionally launch add field completed
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // comity savepoint reached
        upgrade_mod_savepoint(true, 2011022200, 'comity');
    }


 if ($oldversion < 2011021700) {


// Define table comity_agenda to be created
        $table = new xmldb_table('comity_agenda');

        // Adding fields to table comity_agenda
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('comity_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('comity_events_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('location', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table comity_agenda
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for comity_agenda
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

         // Define table comity_agenda_guests to be created
        $table = new xmldb_table('comity_agenda_guests');

        // Adding fields to table comity_agenda_guests
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('comity_agenda', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('firstname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('lastname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('moodleid', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table comity_agenda_guests
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('comity_agenda', XMLDB_KEY_FOREIGN, array('comity_agenda'), 'comity_agenda', array('id'));

        // Conditionally launch create table for comity_agenda_guests
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

         // Define table comity_agenda_topics to be created
        $table = new xmldb_table('comity_agenda_topics');

        // Adding fields to table comity_agenda_topics
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('comity_agenda', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('duration', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('notes', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('filename', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('follow_up', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('modifiedby', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table comity_agenda_topics
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('comity_agenda', XMLDB_KEY_FOREIGN, array('comity_agenda'), 'comity_agenda', array('id'));

        // Conditionally launch create table for comity_agenda_topics
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

           // Define table comity_agenda_motions to be created
        $table = new xmldb_table('comity_agenda_motions');

        // Adding fields to table comity_agenda_motions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('comity_agenda', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('comity_agenda_topics', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('motion', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('motionby', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('secondedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('carried', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('unanimous', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('yea', XMLDB_TYPE_INTEGER, '3', null, null, null, null);
        $table->add_field('nay', XMLDB_TYPE_INTEGER, '3', null, null, null, null);
        $table->add_field('abstained', XMLDB_TYPE_INTEGER, '3', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table comity_agenda_motions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('comity_agenda', XMLDB_KEY_FOREIGN, array('comity_agenda'), 'comity_agenda', array('id'));

        // Conditionally launch create table for comity_agenda_motions
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

          // Define table comity_agenda_attendance to be created
        $table = new xmldb_table('comity_agenda_attendance');

        // Adding fields to table comity_agenda_attendance
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('comity_agenda', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('comity_members', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('absent', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('unexcused_absence', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('notes', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

        // Adding keys to table comity_agenda_attendance
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for comity_agenda_attendance
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

         // Define table comity_agenda_members to be created
        $table = new xmldb_table('comity_agenda_members');

        // Adding fields to table comity_agenda_members
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('comity_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('role_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('agenda_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table comity_agenda_members
        $table->add_key('id', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for comity_agenda_members
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

     
 
       
        // comity savepoint reached
        upgrade_mod_savepoint(true, 2011021700, 'comity');
    }


	if($oldversion < 2010072801) {
	
		$dbman = $DB->get_manager();
		$result = true;
		
		/*
		*	comity_planner
		*/
		
		//Create new table
        $table = new xmldb_table('comity_planner');
        //Add fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('comity_id', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
		$table->add_field('description', XMLDB_TYPE_TEXT, 'medium');
        //Add primary key
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        //Launch table
        $dbman->create_table($table);
	
	
		/*
		*	comity_planner_dates
		*/
		
		//Create new table
        $table = new xmldb_table('comity_planner_dates');
        //Add fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('planner_id', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('from_time', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('to_time', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        //Add primary key
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        //Launch table
        $dbman->create_table($table);
		
		
		/*
		*	comity_planner_response
		*/
		
		//Create new table
        $table = new xmldb_table('comity_planner_response');
        //Add fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('planner_user_id', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('planner_date_id', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('response', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        //Add primary key
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        //Launch table
        $dbman->create_table($table);
		
		/*
		*	comity_planner_users
		*/
		
		//Create new table
        $table = new XMLDBTable('comity_planner_users');
        //Add fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('planner_id', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('comity_member_id', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('rule', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        //Add primary key
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        //Launch table
        $dbman->create_table($table);
		
		return $result;
	
	}

//===== 1.9.0 upgrade line ======//
    if ($oldversion < 2010051200) {
        //Create new table
        $table = new XMLDBTable('comity_folders');
        //Add fields
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('comity_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('private', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1');
        //Add primary key
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        //Launch table
        $result = $result && create_table($table);

        //Create public and private folders for all existing comities
        //Get module id
        $moduleid = get_record('modules','name','comity');
        $moduleid = $moduleid->id;
        //Get all instances and create folders
        $instances = get_records('course_modules','module',$moduleid);
        foreach($instances as $instance) {
            $insert_obj = new stdClass();
            $insert_obj->comity_id = $instance->id;
            $insert_obj->name = get_string('public','comity');
            $insert_obj->private = get_string('private','comity');
            $public = insert_record('comity_folders',$insert_obj);
            //Check
            $result = $result && ($public != 0);
            $insert_obj->name = 'private';
            $insert_obj->private = '1';
            $private = insert_record('comity_folders',$insert_obj);
            //Check
            $result = $result && ($private != 0);

            //Move all existing files into respective folders
            $files = get_records('comity_files','comity_id',$instance->id);
            foreach($files as $file) {
                $update_obj = new stdClass();
                $update_obj->id = $file->id;

                if($file->private==0) {
                    $update_obj->folder = $public;
                }
                if($file->private==1) {
                    $update_obj->folder = $private;
                }
                $result = update_record('comity_files',$update_obj);
            }
        }


    }

      if ($oldversion < 2011060301) {

	  //Update for pdf ouptut. Minutes and agenda
          //Bug fixes - NULL use in lang files
          //Hard coded mdl_user removed.


	 // comity savepoint reached
        upgrade_mod_savepoint(true, 2011060301, 'comity');

	}

    return $result;
}

?>
