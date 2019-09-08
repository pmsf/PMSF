<?php
include(dirname(__FILE__).'/../config/config.php');
global $map, $fork, $db, $raidBosses, $webhookUrl, $sendWebhook, $manualFiveStar, $noManualRaids, $noRaids;

if ($noManualRaids === true || $noRaids === true) {
    http_response_code(401);
    die();
}

$eggs5 = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.`fort_id` = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0 AND level = 5
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);

if (count($eggs5) > 0) {
    $fort_ids = [];
    foreach ($eggs5 as $egg) {
        // add each fort to the array for updating
        array_push($fort_ids, $egg['fort_id']);
        $gym = $db->get("forts", ['external_id'], ['id' => $fort_ids]);

        // do we need to send to webhooks?
        if ($sendWebhook === true && $manualFiveStar['webhook'] === true) {
            $webhook = [
                'message' => [
                    'gym_id' => $gym['external_id'],
                    'pokemon_id' => $manualFiveStar['pokemon_id'],
                    'cp' => $manualFiveStar['cp'],
                    'move_1' => 133,
                    'move_2' => 133,
                    'level' => 5,
                    'latitude' => $egg['lat'],
                    'longitude' => $egg['lon'],
                    'start' => time(),
                    'end' => (float)$egg['time_end'],
                    'team_id' => 0,
                    'name' => $egg['name']
                ],
                'type' => 'raid'
            ];
            foreach ($webhookUrl as $url) {
                sendToWebhook($url, array($webhook));
            }
        }
    }

    // update raids table
    $db->update("raids", ["pokemon_id" => $manualFiveStar['pokemon_id'], "move_1" => $manualFiveStar['move_1'], "move_2" => $manualFiveStar['move_2'], "cp" => $manualFiveStar['cp'], "form" => $manualFiveStar['form']], ["fort_id" => $fort_ids]);

    // also mark fort_sightings as updated:
    $db->update("fort_sightings", ["updated" => time()], ["fort_id" => $fort_ids]);
} else {
    echo "No level 5 egg to update";
}
