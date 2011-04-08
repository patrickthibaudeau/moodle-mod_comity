<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once('ajax_lib.php');

$function = required_param('function',PARAM_TEXT);
$params = required_param('params',PARAM_TEXT);

$parameters = explode(',',$params);

$no_parameters = sizeof($parameters);

switch($no_parameters){
    case 0:
        $function();
        break;
    case 1:
        $function($parameters[0]);
        break;
    case 2:
        $function($parameters[0],$parameters[1]);
        break;
}

?>
