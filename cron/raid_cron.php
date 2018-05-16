<?php
include('config/config.php');
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
        $eggs1 = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.`fort_id` = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0 AND level = 1
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);
    $eggs2 = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.`fort_id` = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0 AND level = 2
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);
    $eggs3 = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.`fort_id` = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0 AND level = 3
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);
    $eggs4 = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.`fort_id` = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0 AND level = 4
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);
    $eggs5 = $db->query("
    SELECT * FROM raids
    LEFT JOIN forts ON raids.`fort_id` = forts.id
    WHERE time_battle < :time_battle AND time_end > :time_battle AND pokemon_id = 0 AND level = 5
", [':time_battle'=>time()])->fetchAll(PDO::FETCH_ASSOC);
}
if (count($eggs1) > 0) {
    $fort_ids = [];
    foreach ($eggs1 as $egg) {
        // add each fort to the array for updating
        array_push($fort_ids, $egg['fort_id']);
        $gym = $db->get("forts", ['external_id'], ['id' => $fort_ids]);

        // do we need to send to webhooks?
        if ($sendWebhook === true) {
            $webhook = [
                'message' => [
                    'gym_id' => $gym['external_id'],
                    'pokemon_id' => $manualOneStar['pokemon_id'],
                    'cp' => $manualOneStar['cp'],
                    'move_1' => 133,
                    'move_2' => 133,
                    'level' => 1,
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
    $db->update("raids", ["pokemon_id" => $manualOneStar['pokemon_id'], "move_1" => $manualOneStar['move_1'], "move_2" => $manualOneStar['move_2'], "cp" => $manualOneStar['cp']], ["fort_id" => $fort_ids]);

    // also mark fort_sightings as updated:
    $db->update("fort_sightings", ["updated" => time()], ["fort_id" => $fort_ids]);
} else {
    echo "No level 1 egg to update";
}
if (count($eggs2) > 0) {
    $fort_ids = [];
    foreach ($eggs2 as $egg) {
        // add each fort to the array for updating
        array_push($fort_ids, $egg['fort_id']);
        $gym = $db->get("forts", ['external_id'], ['id' => $fort_ids]);

        // do we need to send to webhooks?
        if ($sendWebhook === true) {
            $webhook = [
                'message' => [
                    'gym_id' => $gym['external_id'],
                    'pokemon_id' => $manualTwoStar['pokemon_id'],
                    'cp' => $manualTwoStar['cp'],
                    'move_1' => 133,
                    'move_2' => 133,
                    'level' => 2,
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
    $db->update("raids", ["pokemon_id" => $manualTwoStar['pokemon_id'], "move_1" => $manualTwoStar['move_1'], "move_2" => $manualTwoStar['move_2'], "cp" => $manualTwoStar['cp']], ["fort_id" => $fort_ids]);

    // also mark fort_sightings as updated:
    $db->update("fort_sightings", ["updated" => time()], ["fort_id" => $fort_ids]);
} else {
    echo "No level 2 egg to update";
}
if (count($eggs3) > 0) {
    $fort_ids = [];
    foreach ($eggs3 as $egg) {
        // add each fort to the array for updating
        array_push($fort_ids, $egg['fort_id']);
        $gym = $db->get("forts", ['external_id'], ['id' => $fort_ids]);

        // do we need to send to webhooks?
        if ($sendWebhook === true) {
            $webhook = [
                'message' => [
                    'gym_id' => $gym['external_id'],
                    'pokemon_id' => $manualThreeStar['pokemon_id'],
                    'cp' => $manualThreeStar['cp'],
                    'move_1' => 133,
                    'move_2' => 133,
                    'level' => 3,
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
    $db->update("raids", ["pokemon_id" => $manualThreeStar['pokemon_id'], "move_1" => $manualThreeStar['move_1'], "move_2" => $manualThreeStar['move_2'], "cp" => $manualThreeStar['cp']], ["fort_id" => $fort_ids]);

    // also mark fort_sightings as updated:
    $db->update("fort_sightings", ["updated" => time()], ["fort_id" => $fort_ids]);
} else {
    echo "No level 3 egg to update";
}
if (count($eggs4) > 0) {
    $fort_ids = [];
    foreach ($eggs4 as $egg) {
        // add each fort to the array for updating
        array_push($fort_ids, $egg['fort_id']);
        $gym = $db->get("forts", ['external_id'], ['id' => $fort_ids]);

        // do we need to send to webhooks?
        if ($sendWebhook === true) {
            $webhook = [
                'message' => [
                    'gym_id' => $gym['external_id'],
                    'pokemon_id' => $manualFourStar['pokemon_id'],
                    'cp' => $manualFourStar['cp'],
                    'move_1' => 133,
                    'move_2' => 133,
                    'level' => 4,
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
    $db->update("raids", ["pokemon_id" => $manualFourStar['pokemon_id'], "move_1" => $manualFourStar['move_1'], "move_2" => $manualFourStar['move_2'], "cp" => $manualFourStar['cp']], ["fort_id" => $fort_ids]);

    // also mark fort_sightings as updated:
    $db->update("fort_sightings", ["updated" => time()], ["fort_id" => $fort_ids]);
} else {
    echo "No level 4 egg to update";
}
if (count($eggs5) > 0) {
    $fort_ids = [];
    foreach ($eggs5 as $egg) {
        // add each fort to the array for updating
        array_push($fort_ids, $egg['fort_id']);
		$gym = $db->get("forts", ['external_id'], ['id' => $fort_ids]);

        // do we need to send to webhooks?
        if ($sendWebhook === true) {
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
    $db->update("raids", ["pokemon_id" => $manualFiveStar['pokemon_id'], "move_1" => $manualFiveStar['move_1'], "move_2" => $manualFiveStar['move_2'], "cp" => $manualFiveStar['cp']], ["fort_id" => $fort_ids]);

    // also mark fort_sightings as updated:
    $db->update("fort_sightings", ["updated" => time()], ["fort_id" => $fort_ids]);
} else {
    echo "No level 5 egg to update";
}
