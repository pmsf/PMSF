<?php
include(dirname(__FILE__) . '/config/config.php');
global $map, $fork, $db, $raidBosses, $webhookUrl, $sendWebhook, $manualFiveStar;
// get all level 5 raids that are still eggs but haven't hatched

$eggs = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.`fort_id` = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);

if (count($eggs) > 0) {
    $fort_ids = "";
    foreach ($eggs as $egg) {
        // build the in() query for sql:
        $fort_ids .= $egg['fort_id'] . ",";

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
                    'raid_end' => (float)$egg['time_end'],
                    'team' => 0,
                    'name' => $egg['name']
                ],
                'type' => 'raid'
            ];
            sendToWebhook($webhookUrl, $webhook);
        }
    }
    // remove trailing comma
    $fort_ids = rtrim($fort_ids, ", ");

    // update raids table
    $db->query("UPDATE raids SET pokemon_id = 249, move_1 = 133, move_2 = 133, cp = 42753 WHERE fort_id IN(:fort_ids)", [':fort_ids' => $fort_ids]);

    // also mark fort_sightings as updated:
    $db->query("UPDATE fort_sightings SET updated = :updated WHERE fort_id IN (:fort_ids)", [':updated' => time(), ':fort_ids' => $fort_ids]);
} else {
    echo "nothing to update";
}
