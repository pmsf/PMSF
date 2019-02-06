<?php
include(dirname(__FILE__).'/../config/config.php');
global $db, $noManualNests, $deleteNestsOlderThan, $migrationDay;

if($noManualNests === true){
    http_response_code(401);
    die();
}

$nestMigrationInterval = 14;
$days = floor((time() - $migrationDay)/86400) % $nestMigrationInterval;

if ($days === 0){
    $timeNow = date('U');
    $deleteBefore = $timeNow - ($deleteNestsOlderThan * 24 * 60 * 60);
    $manualdb->delete('nests',['updated[<]' => $deleteBefore]);
    echo 'outdatet nest deleted';

    $manualdb->update('nests',['pokemon_id' => 0, 'updated' => time(),'type' => 0], ['pokemon_id[!]' => 0]);
    echo 'updated new nests';
} else {
    echo 'No nests migration today. ' . ((($days = 14 - $days)>13)?$days-14:$days) .  'days to go.';
}
