<?php
$timing['start'] = microtime(true);
include('config/config.php');
global $map, $fork, $db;

$raid_bosses = [
    249 => ['name' => 'Lugia', 'cp'=>42753, 'level' => 5],
    76 => ['name' => 'Golem', 'cp'=>30572, 'level' => 4],
    221 => ['name' => 'Piloswine', 'cp'=>13663, 'level' => 3],
    135 => ['name' => 'Jolteon', 'cp'=>19883, 'level' => 3],
    124 => ['name' => 'Jynx', 'cp'=>18296, 'level' => 3],
    94 => ['name' => 'Gengar', 'cp'=>19768, 'level' => 3],
    310 => ['name' => 'Manectric', 'cp'=>11628, 'level' => 2],
    303 => ['name' => 'Mawile', 'cp'=>9403, 'level' => 2],
    302 => ['name' => 'Sableye', 'cp'=>8266, 'level' => 2],
    125 => ['name' => 'Electabuzz', 'cp'=>12390, 'level' => 2],
    103 => ['name' => 'Exeggutor', 'cp'=>13839, 'level' => 2],
    361 => ['name' => 'Snorunt', 'cp'=>2825, 'level' => 1],
    333 => ['name' => 'Swablu', 'cp'=>2766, 'level' => 1],
    320 => ['name' => 'Wailmer', 'cp'=>3369, 'level' => 1],
    129 => ['name' => 'Magikarp', 'cp'=>1165, 'level' => 1]
];

// brimful of asha on the:
$forty_five = 45*60;
$hour = 3600;

// set content type
header('Content-Type: application/json');

$now = new DateTime();
$now->sub(new DateInterval('PT20S'));

$d = array();

$d["timestamp"] = $now->getTimestamp();

//$db->debug();
// fetch fort_id
// $gym = $db->exec("SELECT * FROM forts where external_id =?", [$_POST['gymId']])->fetchAll();
$gymId = $db->get("forts", ['id'],  ['external_id' => $_POST['gymId']])['id'];
//print_r($gym);
if (stristr($_POST['pokemon_id'], "egg")) {
    // its a raid egg
} elseif(array_key_exists($_POST['pokemon_id'], $raid_bosses)) {

    $add_seconds = ($_POST['mins']*60)+$_POST['secs'];
    $time_end = time()+$add_seconds;
    $pokemonId = $_POST['pokemon_id'];
    // fake the battle start and spawn times cuz rip hashing :(
    $time_battle = $time_end - $forty_five;
    $time_spawn = $time_battle - $hour;
    $extId = rand(0,65535).rand(0,65535);
    $cols = [
        'external_id' => $extId,
        'fort_id' => $gymId,
        'level' => $raid_bosses[$pokemonId]['level'],
        'pokemon_id' => $pokemonId,
        'time_spawn' => $time_spawn,
        'time_battle' => $time_battle,
        'time_end' => $time_end,
        'cp' => $raid_bosses[$pokemonId]['cp'],
        'move_1' => 133, // struggle :(
        'move_2' => 133
    ];
    /*
    $query = "INSERT INTO raids SET
    `external_id` = 1337,
    `fort_id` = :fort_id,
    `level` = :level,
    `pokemon_id` = :pokemon_id,
    `time_spawn` = :time_spawn,
    `time_battle` = :time_battle,
    `time_end` = :time_end,
    `cp` = :cp,
    `move_1` = 133,
    `move_2` = 133";
    */

    $db->insert("raids", $cols);
} else {
    // no boss matched
}
header("Location: /");