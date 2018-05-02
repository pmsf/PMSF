<?php
include(dirname(__FILE__) . '/config/config.php');
global $db, $noManualNests;


if($noManualNests === true){
    http_response_code(401);
    die();
}

$db->update('nests',['pokemon_id' => 0, 'updated' => time(),'type' => 0]);
echo 'updated nests';
