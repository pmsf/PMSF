<?php
include(dirname(__FILE__) . '/config/config.php');
global $map, $fork, $db, $raidBosses, $webhookUrl, $sendWebhook, $manualFiveStar, $noManualRaids, $noRaids;

if($noManualRaids === true || $noRaids === true){
    http_response_code(401);
    die();
}

if($db->info()['driver'] == 'pgsql'){
    $eggs = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.fort_id = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0 AND level = 5
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);
}
else{
    $eggs = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.`fort_id` = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0 AND level = 5
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);
}


if (count($eggs) > 0) {
    $fort_ids = [];
    foreach ($eggs as $egg) {
        // add each fort to the array for updating
        array_push($fort_ids, $egg['fort_id']);

        // do we need to send to webhooks?
        if ($sendWebhook === true) {
            $webhook = [
                'message' => [
                    'gym_id' => $egg['fort_id'],
                    'pokemon_id' => $manualFiveStar['pokemon_id'],
                    'cp' => $manualFiveStar['cp'],
                    'move_1' => $manualFiveStar['move_1'],
                    'move_2' => $manualFiveStar['move_2'],
                    'level' => 5,
                    'latitude' => $egg['lat'],
                    'longitude' => $egg['lon'],
                    'raid_begin' => time(),
                    'raid_end' => (float)$egg['time_end'],
                    'team' => 0,
                    'name' => $egg['name']
                ],
                'type' => 'raid'
            ];
            foreach ($webhookUrl as $url) {
                sendToWebhook($url, $webhook);
            }
        }
    }

    // update raids table
    $db->update("raids", ["pokemon_id" => $manualFiveStar['pokemon_id'], "move_1" => 133, "move_2" => 133, "cp" => $manualFiveStar['cp']], ["fort_id" => $fort_ids]);

    // also mark fort_sightings as updated:
    $db->update("fort_sightings", ["updated" => time()], ["fort_id" => $fort_ids]);
} else {
    echo "nothing to update";
}
