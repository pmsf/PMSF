<?php
include('config.php');

$datas = $db->query("select t1.fort_id, t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.prestige, t1.guard_pokemon_id from(select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id")->fetchAll();

$gyms = array();

/* fetch associative array */
foreach ($datas as $row) {
    $p = array();

    $lat    = floatval($row["lat"]);
    $lon    = floatval($row["lon"]);
    $gpid   = intval($row["guard_pokemon_id"]);
    $gp     = intval($row["prestige"]);
    $lm     = intval($row["last_modified"]);
    $ti     = isset($row["team"]) ? intval($row["team"]) : null;

    $p2                             = array();

    $p2["enabled"]                  = true;
    $p2["guard_pokemon_id"]         = $gpid;
    $p2["gym_id"]                   = $row["external_id"];
    $p2["gym_points"]               = $gp;
    $p2["last_modified"]            = $lm;
    $p2["last_scanned"]             = $lm;
    $p2["latitude"]                 = $lat;
    $p2["longitude"]                = $lon;
    $p2["name"]                     = null;
    $p2["team_id"]                  = $ti;

    $p[$row["external_id"]]         = $p2;

    $gyms[]                        = $p;

    unset($datas[$i]);

    $i++;
}

print_r($gyms);

//select t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.prestige, t1.guard_pokemon_id from
//
//(select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id)
//
//t2 join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified join forts t3 on t1.fort_id = t3.id