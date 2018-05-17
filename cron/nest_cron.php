<?php
include(dirname(__FILE__).'/../config/config.php');
global $db, $noManualNests, $deleteNestsOlderThan;


if($noManualNests === true){
    http_response_code(401);
    die();
}
$timeNow = date('U');
$deleteBefore = $timeNow - ($deleteNestsOlderThan * 24 * 60 * 60);
$db->delete('nests',['updated[<]' => $deleteBefore]);
echo 'outdatet nest deleted';

$db->update('nests',['pokemon_id' => 0, 'updated' => time(),'type' => 0]);
echo 'updated new nests';
