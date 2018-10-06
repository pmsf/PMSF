<?php
include(dirname(__FILE__) . '/config/config.php');
global $map, $fork, $db, $raidBosses, $webhookUrl, $sendWebhook, $manualFiveStar, $noManualRaids, $noRaids;
if($noManualRaids === true || $noRaids === true){
    http_response_code(401);
    die();
}
if($db->info()['driver'] == 'pgsql'){
    $eggs = $db->query("
    SELECT * FROM gym
    WHERE raid_battle_timestamp < :raid_battle_timestamp AND raid_end_timestamp > :raid_battle_timestamp AND raid_pokemon_id = 0 AND raid_level = 5
", [':raid_battle_timestamp'=>time()])->fetchAll(PDO::FETCH_ASSOC);
}
else{
    $eggs = $db->query("
    SELECT * FROM gym
    WHERE raid_battle_timestamp < :raid_battle_timestamp AND raid_end_timestamp > :raid_battle_timestamp AND raid_pokemon_id = 0 AND raid_level = 5
", [':raid_battle_timestamp'=>time()])->fetchAll(PDO::FETCH_ASSOC);
}
if (count($eggs) > 0) {
    $fort_ids = [];
    foreach ($eggs as $egg) {
        // add each fort to the array for updating
        array_push($fort_ids, $egg['id']);
        // do we need to send to webhooks?
        if ($sendWebhook === true) {
            $webhook = [
                'message' => [
                    'gym_id' => $egg['id'],
                    'pokemon_id' => $manualFiveStar['pokemon_id'],
                    'cp' => $manualFiveStar['cp'],
                    'move_1' => $manualFiveStar['move_1'],
                    'move_2' => $manualFiveStar['move_2'],
                    'level' => 5,
                    'latitude' => $egg['lat'],
                    'longitude' => $egg['lon'],
                    'raid_begin' => time(),
                    'raid_end' => (float)$egg['raid_end_timestamp'],
                    'gym_name' => $egg['name']
                ],
                'type' => 'raid'
            ];
            foreach ($webhookUrl as $url) {
                sendToWebhook($url, $webhook);
            }
        }
    }
	
    // update raids table
    $db->update("gym", ["raid_pokemon_id" => $manualFiveStar['pokemon_id'], "raid_pokemon_move_1" => 133, "raid_pokemon_move_2" => 133, "raid_pokemon_cp" => $manualFiveStar['cp']], ["id" => $fort_ids]);

} else {
    echo "nothing to update";
}
