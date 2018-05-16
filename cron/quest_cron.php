<?php
include('config/config.php');
global $map, $fork, $db, $noManualQuests;

if($noManualQuests === true){
    http_response_code(401);
    die();
}

$db->update('pokestops',['quest_id' => null, 'reward' => null]);
echo 'updated pokestops';
