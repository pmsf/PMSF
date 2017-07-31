<?php
include('config/config.php');

if (empty($_POST['id'])) {
    http_response_code(400);
    die();
}
if (!validateToken($_POST['token'])) {
    http_response_code(400);
    die();
}


$id = $_POST['id'];

global $db;

global $map;
if ($map == "monocle") {
    $row = $db->query("SELECT t3.external_id, 
       t3.lat, 
       t3.lon, 
       t1.last_modified, 
       t1.team, 
       t1.slots_available, 
       t1.guard_pokemon_id 
FROM   (SELECT fort_id, 
               Max(last_modified) AS MaxLastModified 
        FROM   fort_sightings 
        GROUP  BY fort_id) t2 
       LEFT JOIN fort_sightings t1 
              ON t2.fort_id = t1.fort_id 
                 AND t2.maxlastmodified = t1.last_modified 
       LEFT JOIN forts t3 
              ON t1.fort_id = t3.id 
WHERE  t3.external_id = :id ", [':id'=>$id])->fetch();
} else {
    global $fork;
    if ($fork != "sloppy")
        $row = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', 
       @@global.time_zone)) AS 
       last_scanned, 
       team_id 
       AS team, 
       enabled, 
       name, 
       level, 
       pokemon_id, 
       cp, 
       move_1, 
       move_2, 
       Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) 
       AS raid_start, 
       Unix_timestamp(Convert_tz(end, '+00:00', @@global.time_zone)) 
       AS raid_end 
FROM   gym 
       LEFT JOIN gymdetails 
              ON gym.gym_id = gymdetails.gym_id 
       LEFT JOIN raid 
              ON gym.gym_id = raid.gym_id 
WHERE  gym.gym_id = :id", [':id'=>$id])->fetch();
    else
        $row = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', 
       @@global.time_zone)) AS 
       last_scanned, 
       team_id 
       AS team, 
       enabled, 
       name, 
       raid_level 
       AS level, 
       raid_pokemon_id 
       AS pokemon_id, 
       raid_pokemon_cp 
       AS cp, 
       raid_pokemon_move_1 
       AS move_1, 
       raid_pokemon_move_2 
       AS move_2, 
       Unix_timestamp(Convert_tz(raid_battle, '+00:00', @@global.time_zone)) 
       AS 
       raid_start, 
       Unix_timestamp(Convert_tz(raid_end, '+00:00', @@global.time_zone)) 
       AS raid_end 
FROM   gym 
       LEFT JOIN gymdetails 
              ON gym.gym_id = gymdetails.gym_id 
WHERE  gym.gym_id = :id". [':id'=>$id])->fetch();
}

$json_poke = "static/data/pokemon.json";
$json_contents = file_get_contents($json_poke);
$data = json_decode($json_contents, TRUE);

$p = array();

$lat = floatval($row["lat"]);
$lon = floatval($row["lon"]);
$gpid = intval($row["guard_pokemon_id"]);
$sa = intval($row["slots_available"]);
$lm = $row["last_modified"] * 1000;
$ls = isset($row["last_scanned"]) ? $row["last_scanned"] * 1000 : null;
$ti = isset($row["team"]) ? intval($row["team"]) : null;

$p["enabled"] = isset($row["enabled"]) ? boolval($row["enabled"]) : true;
$p["guard_pokemon_id"] = $gpid;
$p["gym_id"] = $row["external_id"];
$p["slots_available"] = $sa;
$p["last_modified"] = $lm;
$p["last_scanned"] = $ls;
$p["latitude"] = $lat;
$p["longitude"] = $lon;
$p["name"] = isset($row["name"]) ? $row["name"] : null;
$p["team_id"] = $ti;
if ($gpid)
    $p["guard_pokemon_name"] = i8ln($data[$gpid]['name']);

if ($map != "monocle") {
    $rpid = intval($row['pokemon_id']);
    $p['raid_level'] = intval($row['level']);
    if ($rpid)
        $p['raid_pokemon_id'] = $rpid;
    if ($rpid)
        $p['raid_pokemon_name'] = i8ln($data[$rpid]['name']);
    $p['raid_pokemon_cp'] = isset($row['cp']) ? intval($row['cp']) : null;
    $p['raid_pokemon_move_1'] = isset($row['move_1']) ? intval($row['move_1']) : null;
    $p['raid_pokemon_move_2'] = isset($row['move_2']) ? intval($row['move_2']) : null;
    $p['raid_start'] = $row["raid_start"] * 1000;
    $p['raid_end'] = $row["raid_end"] * 1000;
}

unset($row);

$j = 0;
if ($map != "monocle") {
    $json_moves = "static/data/moves.json";
    $json_contents = file_get_contents($json_moves);
    $moves = json_decode($json_contents, TRUE);
    $pokemons = $db->query("SELECT gymmember.gym_id, 
       pokemon_id, 
       cp, 
       trainer.name, 
       trainer.level, 
       move_1, 
       move_2, 
       iv_attack, 
       iv_defense, 
       iv_stamina 
FROM   gymmember 
       JOIN gympokemon 
         ON gymmember.pokemon_uid = gympokemon.pokemon_uid 
       JOIN trainer 
         ON gympokemon.trainer_name = trainer.name 
       JOIN gym 
         ON gym.gym_id = gymmember.gym_id 
WHERE  gymmember.last_scanned > gym.last_modified 
       AND gymmember.gym_id IN ( :id ) 
GROUP  BY name 
ORDER  BY gympokemon.cp DESC ", [':id'=>$id])->fetchAll();

    foreach ($pokemons as $pokemon) {
        $pid = $pokemon["pokemon_id"];

        $p1 = array();

        $p1["pokemon_id"] = $pid;
        $p1["pokemon_name"] = i8ln($data[$pid]['name']);
        $p1["trainer_name"] = $pokemon["name"];
        $p1["trainer_level"] = $pokemon["level"];
        $p1["pokemon_cp"] = $pokemon["cp"];

        $p1["iv_attack"] = intval($pokemon["iv_attack"]);
        $p1["iv_defense"] = intval($pokemon["iv_defense"]);
        $p1["iv_stamina"] = intval($pokemon["iv_stamina"]);

        $p1['move_1_name'] = i8ln($moves[$pokemon['move_1']]['name']);
        $p1['move_1_damage'] = $moves[$pokemon['move_1']]['damage'];
        $p1['move_1_energy'] = $moves[$pokemon['move_1']]['energy'];
        $p1['move_1_type']['type'] = i8ln($moves[$pokemon['move_1']]['type']);
        $p1['move_1_type']['type_en'] = $moves[$pokemon['move_1']]['type'];

        $p1['move_2_name'] = i8ln($moves[$pokemon['move_2']]['name']);
        $p1['move_2_damage'] = $moves[$pokemon['move_2']]['damage'];
        $p1['move_2_energy'] = $moves[$pokemon['move_2']]['energy'];
        $p1['move_2_type']['type'] = i8ln($moves[$pokemon['move_2']]['type']);
        $p1['move_2_type']['type_en'] = $moves[$pokemon['move_2']]['type'];

        $p['pokemon'][] = $p1;

        unset($pokemons[$j]);

        $j++;
    }
} else {
    global $fork;
    if ($fork != "asner")
        $raid = $db->query("SELECT t1.fort_id, 
       level, 
       pokemon_id, 
       time_battle AS raid_start, 
       time_end    AS raid_end 
FROM   (SELECT fort_id, 
               Max(time_end) AS MaxTimeEnd 
        FROM   raids 
        GROUP  BY fort_id) t1 
       LEFT JOIN raids t2 
              ON t1.fort_id = t2.fort_id 
                 AND maxtimeend = time_end 
WHERE  t1.fort_id IN ( :id ) ", [':id'=>$id])->fetch();
    else
        $raid = $db->query("SELECT t3.external_id, 
       t1.fort_id, 
       raid_level AS level, 
       pokemon_id, 
       cp, 
       move_1, 
       move_2, 
       raid_start, 
       raid_end 
FROM   (SELECT fort_id, 
               Max(raid_end) AS MaxTimeEnd 
        FROM   raid_info 
        GROUP  BY fort_id) t1 
       LEFT JOIN raid_info t2 
              ON t1.fort_id = t2.fort_id 
                 AND maxtimeend = raid_end 
       JOIN forts t3 
         ON t2.fort_id = t3.id 
WHERE  t3.external_id IN ( :id ) ", [':id'=>$id])->fetch();

    $rpid = intval($raid['pokemon_id']);
    $p['raid_level'] = intval($raid['level']);
    if ($rpid)
        $p['raid_pokemon_id'] = $rpid;
    if ($rpid)
        $p['raid_pokemon_name'] = i8ln($data[$rpid]['name']);
    $p['raid_pokemon_cp'] = isset($raid['cp']) ? intval($raid['cp']) : null;
    $p['raid_pokemon_move_1'] = isset($raid['move_1']) ? intval($raid['move_1']) : null;
    $p['raid_pokemon_move_2'] = isset($raid['move_2']) ? intval($raid['move_2']) : null;
    $p['raid_start'] = $raid["raid_start"] * 1000;
    $p['raid_end'] = $raid["raid_end"] * 1000;

    unset($raid);
}

$p['token'] = refreshCsrfToken();

echo json_encode($p);