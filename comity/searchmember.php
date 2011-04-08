<?php

//$timestart = microtime(true);

require_once('../../config.php');

$string = optional_param('str',null,PARAM_TEXT); //search query

if($string=='') {
    $users = $DB->get_records('user',array('deleted'=>0),'lastname ASC');
}
else {
    $users = $DB->get_records_select('user','upper(firstname) LIKE upper("%'.$string.'%") OR upper(lastname) LIKE upper("%'.$string.'%")',null,'lastname ASC');
}

echo '<select name="user">';
foreach($users as $user) {
    if(!$DB->get_record('comity_members', array('user_id'=>$user->id,'comity_id'=>$id))) {
        echo '<option value="'.$user->id.'">'.$user->lastname.', '.$user->firstname.'</option>';
    }
}
echo '</select>';

//$timeend = microtime(true);
//$total = $timeend-$timestart;

//echo $total;

?>
