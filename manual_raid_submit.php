<?php
$timing['start'] = microtime(true);
include('config/config.php');
global $map, $fork, $db, $raidBosses, $webhookUrl, $sendWebhook;
$raidBosses = json_decode(file_get_contents("static/dist/data/raid-boss.min.json"), true);
$pokemonId = !empty($_POST['pokemonId']) ? $_POST['pokemonId'] : 0;
$gymId = !empty($_POST['gymId']) ? $_POST['gymId'] : 0;
$mins = !empty($_POST['mins']) ? $_POST['mins'] : 0;
$secs = !empty($_POST['secs']) ? $_POST['secs'] : 0;

// brimful of asha on the:
$forty_five = 45 * 60;
$hour = 3600;

// set content type
header('Content-Type: application/json');

$now = new DateTime();
$now->sub(new DateInterval('PT20S'));

$d = array();
$d['status'] = "ok";

$d["timestamp"] = $now->getTimestamp();

//$db->debug();
// fetch fort_id
$gym = $db->get("forts", ['id', 'name', 'lat', 'lon'], ['external_id' => $gymId]);
$gymId = $gym['id'];

$add_seconds = ($mins * 60) + $secs;
$time_battle = time() + $add_seconds;
$time_end = $time_battle + $forty_five;
// fake the battle start and spawn times cuz rip hashing :(
$time_spawn = time() - $forty_five;
$extId = rand(0, 65535) . rand(0, 65535);
$level = 0;
if(strpos($pokemonId,'egg_') !== false){
    $level = (int)substr($pokemonId,4,1);
    $time_spawn = time() + $add_seconds;
}


$cols = [
    'external_id' => $gymId,
    'fort_id' => $gymId,
    'level' => $level,
    'time_spawn' => $time_spawn,
    'time_battle' => $time_battle,
    'time_end' => $time_end,
    'cp' => 0,
    'pokemon_id' => 0,
    'move_1' => 0, // struggle
    'move_2' => 0

];
if (array_key_exists($pokemonId, $raidBosses)) {
    $time_end = time() + $add_seconds;
    // fake the battle start and spawn times cuz rip hashing :(
    $time_battle = $time_end - $forty_five;
    $time_spawn = $time_battle - $hour;
    $cols['pokemon_id'] = $pokemonId;
    $cols['move_1'] = 133; // struggle :(
    $cols['move_2'] = 133;
    $cols['level'] = $raidBosses[$pokemonId]['level']; // struggle :(
    $cols['cp'] = $raidBosses[$pokemonId]['cp'];
    $cols['time_spawn'] = $time_spawn;
    $cols['time_battle'] = $time_battle;
    $cols['time_end'] = $time_end;
} elseif($cols['level'] === 0) {
    // no boss or egg matched
    http_response_code(500);
}
$db->query('DELETE FROM raids WHERE fort_id = :gymId', [':gymId' => $gymId]);
$db->insert("raids", $cols);

// also update fort_sightings so PMSF knows the gym has changed
// todo: put team stuff in here too
$db->query("UPDATE fort_sightings SET updated = :updated WHERE fort_id = :gymId", ['updated'=>time(), ':gymId' => $gymId]);

if ($sendWebhook === true) {
    // webhook stuff:
    // build webhook array:

    $webhook = [
        'message' => [
            'gym_id' => $cols['external_id'],
            'pokemon_id' => $cols['pokemon_id'],
            'cp' => $cols['cp'],
            'move_1' => $cols['move_1'],
            'move_2' => $cols['move_2'],
            'level' => $cols['level'],
            'latitude' => $gym['lat'],
            'longitude' => $gym['lon'],
            'raid_begin' => $time_battle,
            'raid_end' => $time_end,
            'team' => 0,
            'name' => $gym['name']
        ],
        'type' => 'raid'
    ];
    if(strpos($pokemonId,'egg_') !== false) {
        $webhook['message']['raid_begin'] = $time_spawn;
    }

    sendToWebhook($webhookUrl, $webhook);

}

$jaysson = json_encode($d);
echo $jaysson;
