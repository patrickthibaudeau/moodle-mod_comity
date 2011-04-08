<?php


require_once('../../config.php');

global $DB,$CFG;
require_once($CFG->dirroot.'/mod/comity/meetingagenda/rooms_avaliable_form.php');


$id = required_param('id', PARAM_INT);
//----CHECK FOR SCHEDULER PLUGIN -----------------------------------------------
 $dbman = $DB->get_manager();
$table = new xmldb_table('roomscheduler_reservations');
$scheduler_plugin_installed = $dbman->table_exists($table);

$cm = get_coursemodule_from_id('comity', $id);
$context = get_context_instance(CONTEXT_COURSE, $cm->course);

if ($scheduler_plugin_installed && has_capability('block/roomscheduler:reserve', $context)) {   //plugin exists
global $DB;

$cm = get_coursemodule_from_id('comity', $id);
$comity = $DB->get_record("comity", array("id"=>$cm->instance));

$scheduler_form = new rooms_avaliable_form();
$scheduler_form->initalize_popup_newEvent($comity->course, $comity->name);
echo $scheduler_form;

echo '12345';
print '123445';

}
?>
