<?php

require_once('../../config.php');



print '<link rel="stylesheet" type="text/css" href="rooms_available.css" />';
print '<link rel="stylesheet" type="text/css" href="/blocks/roomscheduler/fancybox/jquery.fancybox-1.3.1.css" />';

print '<script type="text/javascript" src="/blocks/roomscheduler/fancybox/jquery.min.js"></script>';
print '<script type="text/javascript" src="/blocks/roomscheduler/fancybox/jquery.fancybox-1.3.1.pack.js"></script>';
print '<script type="text/javascript" src="/blocks/roomscheduler/fancybox/roomscheduler.js"></script>';

require_once('rooms_avaliable_form.php');
print '<script type="text/javascript" src="rooms_available.js"></script>';



$test = new rooms_avaliable_form();
echo $test;



print '<input type="image" type="image" src="img/success.gif" value="Avaliable Rooms" onclick="rooms_avaliable_popup(\''.rooms_avaliable_form::apptForm_formName().'\');"/>';

?>
