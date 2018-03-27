<?php
$timing['start'] = microtime(true);
include('config/config.php');
global $map, $fork, $db, $raid_bosses;
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
// $gym = $db->exec("SELECT * FROM forts where external_id =?", [$_POST['gymId']])->fetchAll();
$gymId = $db->get("forts", ['id'], ['external_id' => $gymId])['id'];
//print_r($gym);

$add_seconds = ($mins * 60) + $secs;
$time_battle = time() + $add_seconds;
//$time_end = $time_battle + $forty_five;
$time_end = $time_battle + $forty_five;
// fake the battle start and spawn times cuz rip hashing :(
$time_spawn = time() - $forty_five;
$extId = rand(0, 65535) . rand(0, 65535);
$level = 0;
if(strpos($pokemonId,'egg_') !== false){
    $level = (int)substr($pokemonId,4,1);
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
if (array_key_exists($pokemonId, $raid_bosses)) {
    $time_end = time() + $add_seconds;
// fake the battle start and spawn times cuz rip hashing :(
    $time_battle = $time_end - $forty_five;
    $time_spawn = $time_battle - $hour;
    $cols['pokemon_id'] = $pokemonId;
    $cols['move_1'] = 133; // struggle :(
    $cols['move_2'] = 133;
    $cols['level'] = $raid_bosses[$pokemonId]['level']; // struggle :(
    $cols['cp'] = $raid_bosses[$pokemonId]['cp'];
} else {
    // no boss matched
}
$db->query('DELETE FROM raids WHERE fort_id IN ( SELECT id FROM forts WHERE external_id = ":external")', [':external' => $gymId]);
$db->insert("raids", $cols);
$jaysson = json_encode($d);
echo $jaysson;
//header("Location: /");
