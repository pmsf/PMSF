<?php
include('config/config.php');
include('utils.php');

$now = new DateTime();

$d = array();

$d["timestamp"] = $now->getTimestamp();

$swLat = isset($_GET['swLat']) ? $_GET['swLat'] : 0;
$neLng = isset($_GET['neLng']) ? $_GET['neLng'] : 0;
$swLng = isset($_GET['swLng']) ? $_GET['swLng'] : 0;
$neLat = isset($_GET['neLat']) ? $_GET['neLat'] : 0;
$oSwLat = isset($_GET['oSwLat']) ? $_GET['oSwLat'] : 0;
$oSwLng = isset($_GET['oSwLng']) ? $_GET['oSwLng'] : 0;
$oNeLat = isset($_GET['oNeLat']) ? $_GET['oNeLat'] : 0;
$oNeLng = isset($_GET['oNeLng']) ? $_GET['oNeLng'] : 0;
$luredonly = isset($_GET['luredonly']) ? $_GET['luredonly'] : false;
$lastpokemon = isset($_GET['lastpokemon']) ? $_GET['lastpokemon'] : false;
$lastgyms = isset($_GET['lastgyms']) ? $_GET['lastgyms'] : false;
$lastpokestops = isset($_GET['lastpokestops']) ? $_GET['lastpokestops'] : false;
$lastlocs = isset($_GET['lastslocs']) ? $_GET['lastslocs'] : false;
$lastspawns = isset($_GET['lastspawns']) ? $_GET['lastspawns'] : false;
$d["lastpokestops"] = isset($_GET['pokestops']) ? $_GET['pokestops'] : false;
$d["lastgyms"] = isset($_GET['gyms']) ? $_GET['gyms'] : false;
$d["lastslocs"] = isset($_GET['scanned']) ? $_GET['scanned'] : false;
$d["lastspawns"] = isset($_GET['spawnpoints']) ? $_GET['spawnpoints'] : false;
$d["lastpokemon"] = isset($_GET['pokemon']) ? $_GET['pokemon'] : false;

$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : 0;

$useragent = $_SERVER['HTTP_USER_AGENT'];
if (empty($swLat) || empty($swLng) || empty($neLat) || empty($neLng) || preg_match("/curl|libcurl/", $useragent)) {
    http_response_code(400);
    die();
}
if ($maxLatLng > 0 && ((($neLat - $swLat) > $maxLatLng) || (($neLng - $swLng) > $maxLatLng))) {
    http_response_code(400);
    die();
}

$newarea = false;

if (($oSwLng < $swLng) && ($oSwLat < $swLat) && ($oNeLat > $neLat) && ($oNeLng > $neLng)) {
    $newarea = false;
} elseif (($oSwLat != $swLat) && ($oSwLng != $swLng) && ($oNeLat != $neLat) && ($oNeLng != $neLng)) {
    $newarea = true;
} else {
    $newarea = false;
}

$d["oSwLat"] = $swLat;
$d["oSwLng"] = $swLng;
$d["oNeLat"] = $neLat;
$d["oNeLng"] = $neLng;

$ids = array();
$eids = array();
$reids = array();

global $noPokemon;
if (!$noPokemon) {
    if ($d["lastpokemon"] == "true") {
        if ($lastpokemon != 'true') {
            $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }

        if (isset($_GET['eids'])) {
            $eids = explode(",", $_GET['eids']);

            foreach ($d['pokemons'] as $elementKey => $element) {
                foreach ($element as $valueKey => $value) {
                    if ($valueKey == 'pokemon_id') {
                        if (in_array($value, $eids)) {
                            //delete this particular object from the $array
                            unset($d['pokemons'][$elementKey]);
                        }
                    }
                }
            }
        }

        if (isset($_GET['reids'])) {
            $reids = explode(",", $_GET['reids']);

            $d["pokemons"] = $d["pokemons"] + (get_active_by_id($reids, $swLat, $swLng, $neLat, $neLng));

            $d["reids"] = !empty($_GET['reids']) ? $reids : null;
        }
    }
}

global $noPokestops;
if (!$noPokestops) {
    if ($d["lastpokestops"] == "true") {
        if ($lastpokestops != "true") {
            $d["pokestops"] = get_stops($swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $luredonly);
        } else {
            if ($newarea) {
                $d["pokestops"] = get_stops($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $luredonly);
            } else {
                $d["pokestops"] = get_stops($swLat, $swLng, $neLat, $neLng, $timestamp, 0, 0, 0, 0, $luredonly);
            }
        }
    }
}

global $noGyms, $noRaids;
if (!$noGyms && !$noRaids) {
    if ($d["lastgyms"] == "true") {
        if ($lastgyms != "true") {
            $d["gyms"] = get_gyms($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["gyms"] = get_gyms($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["gyms"] = get_gyms($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }
    }
}

global $noSpawnPoints;
if (!$noSpawnPoints) {
    if ($d["lastspawns"] == "true") {
        if ($lastspawns != "true") {
            $d["spawnpoints"] = get_spawnpoints($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["spawnpoints"] = get_spawnpoints($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["spawnpoints"] = get_spawnpoints($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }
    }
}

global $noScannedLocations;
if (!$noScannedLocations) {
    if ($d["lastslocs"] == "true") {
        if ($lastlocs != "true") {
            $d["scanned"] = get_recent($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["scanned"] = get_recent($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["scanned"] = get_recent($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }
    }
}

$jaysson = json_encode($d);
echo $jaysson;

function get_active($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
{
    global $db;

    $datas = array();

    global $map;
    if ($map == "monocle") {
        if ($swLat == 0) {
            $datas = $db->query("select * from sightings where expire_timestamp > " . time())->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("select * from sightings where expire_timestamp > " . time() . " and lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng)->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("select * from sightings where expire_timestamp > " . time() . " and lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng . " and not(lat > " . $oSwLat . " and lon > " . $oSwLng . " and lat < " . $oNeLat . " and lon < " . $oNeLng . ")")->fetchAll();
        } else {
            $datas = $db->query("select * from sightings where expire_timestamp > " . time() . " and lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng)->fetchAll();
        }
    } else {
        $time = new DateTime();
        $time->setTimeZone(new DateTimeZone('UTC'));
        $time->setTimestamp(time());
        if ($swLat == 0) {
            $datas = $db->query("select *, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as expire_timestamp, latitude as lat, longitude as lon, individual_attack as atk_iv, individual_defense as def_iv, individual_stamina as sta_iv, spawnpoint_id as spawn_id from pokemon where disappear_time > '" . date_format($time, 'Y-m-d H:i:s') . "'")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("select *, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as expire_timestamp, latitude as lat, longitude as lon, individual_attack as atk_iv, individual_defense as def_iv, individual_stamina as sta_iv, spawnpoint_id as spawn_id from pokemon where disappear_time > '" . date_format($time, 'Y-m-d H:i:s') . "' and last_modified > '" . date_format($date, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("select *, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as expire_timestamp, latitude as lat, longitude as lon, individual_attack as atk_iv, individual_defense as def_iv, individual_stamina as sta_iv, spawnpoint_id as spawn_id from pokemon where disappear_time > '" . date_format($time, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " and not(latitude > " . $oSwLat . " and longitude > " . $oSwLng . " and latitude < " . $oNeLat . " and longitude < " . $oNeLng . ")")->fetchAll();
        } else {
            $datas = $db->query("select *, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as expire_timestamp, latitude as lat, longitude as lon, individual_attack as atk_iv, individual_defense as def_iv, individual_stamina as sta_iv, spawnpoint_id as spawn_id from pokemon where disappear_time > '" . date_format($time, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        }
    }

    $pokemons = array();

    $json_poke = "static/data/pokemon.json";
    $json_contents = file_get_contents($json_poke);
    $data = json_decode($json_contents, TRUE);

    $i = 0;

    /* fetch associative array */
    foreach ($datas as $row) {
        $p = array();

        $dissapear = $row["expire_timestamp"] * 1000;
        $lat = floatval($row["lat"]);
        $lon = floatval($row["lon"]);
        $pokeid = intval($row["pokemon_id"]);

        $atk = isset($row["atk_iv"]) ? intval($row["atk_iv"]) : null;
        $def = isset($row["def_iv"]) ? intval($row["def_iv"]) : null;
        $sta = isset($row["sta_iv"]) ? intval($row["sta_iv"]) : null;
        $mv1 = isset($row["move_1"]) ? intval($row["move_1"]) : null;
        $mv2 = isset($row["move_2"]) ? intval($row["move_2"]) : null;
        $weight = isset($row["weight"]) ? floatval($row["weight"]) : null;
        $height = isset($row["height"]) ? floatval($row["height"]) : null;
        $gender = isset($row["gender"]) ? intval($row["gender"]) : null;
        $form = isset($row["form"]) ? intval($row["form"]) : null;
        $cp = isset($row["cp"]) ? intval($row["cp"]) : null;
        $cpm = isset($row["cp_multiplier"]) ? floatval($row["cp_multiplier"]) : null;
        $level = isset($row["level"]) ? intval($row["level"]) : null;

        $p["disappear_time"] = $dissapear; //done
        $p["encounter_id"] = $row["encounter_id"]; //done

        global $noHighLevelData;
        if (!$noHighLevelData) {
            $p["individual_attack"] = $atk; //done
            $p["individual_defense"] = $def; //done
            $p["individual_stamina"] = $sta; //done
            $p["move_1"] = $mv1; //done
            $p["move_2"] = $mv2;
            $p["weight"] = $weight;
            $p["height"] = $height;
            $p["cp"] = $cp;
            $p["cp_multiplier"] = $cpm;
            $p["level"] = $level;
        }

        $p["latitude"] = $lat; //done
        $p["longitude"] = $lon; //done
        $p["gender"] = $gender;
        $p["form"] = $form;
        $p["pokemon_id"] = $pokeid;
        $p["pokemon_name"] = i8ln($data[$pokeid]['name']);
        $p["pokemon_rarity"] = i8ln($data[$pokeid]['rarity']);

        $types = $data[$pokeid]["types"];
        foreach ($types as $k => $v) {
            $types[$k]['type'] = i8ln($v['type']);
        }
        $p["pokemon_types"] = $types;
        $p["spawnpoint_id"] = $row["spawn_id"];

        $pokemons[] = $p;

        unset($datas[$i]);

        $i++;
    }

    return $pokemons;
}

function get_active_by_id($ids, $swLat, $swLng, $neLat, $neLng)
{
    global $db;

    $datas = array();

    global $map;
    if ($map == "monocle") {
        if ($swLat == 0) {
            $datas = $db->query("select * from sightings where expire_timestamp > " . time() . " and pokemon_id in (" . implode(',', array_map('intval', $ids)) . ")")->fetchAll();
        } else {
            $datas = $db->query("select * from sightings where expire_timestamp > " . time() . " and pokemon_id in (" . implode(',', array_map('intval', $ids)) . ") and lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng)->fetchAll();
        }
    } else {
        $time = new DateTime();
        $time->setTimeZone(new DateTimeZone('UTC'));
        $time->setTimestamp(time());
        if ($swLat == 0) {
            $datas = $db->query("select *, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as expire_timestamp, latitude as lat, longitude as lon, individual_attack as atk_iv, individual_defense as def_iv, individual_stamina as sta_iv, spawnpoint_id as spawn_id from pokemon where disappear_time > '" . date_format($time, 'Y-m-d H:i:s') . "' and pokemon_id in (" . implode(',', array_map('intval', $ids)) . ")")->fetchAll();
        } else {
            $datas = $db->query("select *, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as expire_timestamp, latitude as lat, longitude as lon, individual_attack as atk_iv, individual_defense as def_iv, individual_stamina as sta_iv, spawnpoint_id as spawn_id from pokemon where disappear_time > '" . date_format($time, 'Y-m-d H:i:s') . "' and pokemon_id in (" . implode(',', array_map('intval', $ids)) . ") and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        }
    }

    $pokemons = array();

    $json_poke = "static/data/pokemon.json";
    $json_contents = file_get_contents($json_poke);
    $data = json_decode($json_contents, TRUE);

    $i = 0;

    /* fetch associative array */
    foreach ($datas as $row) {
        $p = array();

        $dissapear = $row["expire_timestamp"] * 1000;
        $lat = floatval($row["lat"]);
        $lon = floatval($row["lon"]);
        $pokeid = intval($row["pokemon_id"]);

        $atk = isset($row["atk_iv"]) ? intval($row["atk_iv"]) : null;
        $def = isset($row["def_iv"]) ? intval($row["def_iv"]) : null;
        $sta = isset($row["sta_iv"]) ? intval($row["sta_iv"]) : null;
        $mv1 = isset($row["move_1"]) ? intval($row["move_1"]) : null;
        $mv2 = isset($row["move_2"]) ? intval($row["move_2"]) : null;
        $weight = isset($row["weight"]) ? floatval($row["weight"]) : null;
        $height = isset($row["height"]) ? floatval($row["height"]) : null;
        $gender = isset($row["gender"]) ? intval($row["gender"]) : null;
        $form = isset($row["form"]) ? intval($row["form"]) : null;
        $cp = isset($row["cp"]) ? intval($row["cp"]) : null;
        $cpm = isset($row["cp_multiplier"]) ? floatval($row["cp_multiplier"]) : null;
        $level = isset($row["level"]) ? intval($row["level"]) : null;

        $p["disappear_time"] = $dissapear; //done
        $p["encounter_id"] = $row["encounter_id"]; //done

        global $noHighLevelData;
        if (!$noHighLevelData) {
            $p["individual_attack"] = $atk; //done
            $p["individual_defense"] = $def; //done
            $p["individual_stamina"] = $sta; //done
            $p["move_1"] = $mv1; //done
            $p["move_2"] = $mv2;
            $p["weight"] = $weight;
            $p["height"] = $height;
            $p["cp"] = $cp;
            $p["cp_multiplier"] = $cpm;
            $p["level"] = $level;
        }

        $p["latitude"] = $lat; //done
        $p["longitude"] = $lon; //done
        $p["gender"] = $gender;
        $p["form"] = $form;
        $p["pokemon_id"] = $pokeid;
        $p["pokemon_name"] = i8ln($data[$pokeid]['name']);
        $p["pokemon_rarity"] = i8ln($data[$pokeid]['rarity']);

        $p["pokemon_types"] = $data[$pokeid]["types"];
        $p["spawnpoint_id"] = $row["spawn_id"];

        $pokemons[] = $p;

        unset($datas[$i]);

        $i++;
    }

    return $pokemons;
}

function get_stops($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lured = false)
{

    global $db;

    $datas = array();

    global $map;
    if ($map == "monocle") {
        if ($swLat == 0) {
            $datas = $db->query("select external_id, lat, lon from pokestops")->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("select external_id, lat, lon from pokestops where lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng)->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("select external_id, lat, lon from pokestops where lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng . " and not(lat > " . $oSwLat . " and lon > " . $oSwLng . " and lat < " . $oNeLat . " and lon < " . $oNeLng . ")")->fetchAll();
        } else {
            $datas = $db->query("select external_id, lat, lon from pokestops where lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng)->fetchAll();
        }
    } else {
        if ($swLat == 0) {
            $datas = $db->query("select active_fort_modifier, enabled, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(lure_expiration, '+00:00', @@global.time_zone)) as lure_expiration, pokestop_id as external_id, latitude as lat, longitude as lon from pokestop")->fetchAll();
        } elseif ($tstamp > 0 && $lured == "true") {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("select active_fort_modifier, enabled, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(lure_expiration, '+00:00', @@global.time_zone)) as lure_expiration, pokestop_id as external_id, latitude as lat, longitude as lon from pokestop where last_updated > '" . date_format($date, 'Y-m-d H:i:s') . "' and active_fort_modifier is not null and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("select active_fort_modifier, enabled, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(lure_expiration, '+00:00', @@global.time_zone)) as lure_expiration, pokestop_id as external_id, latitude as lat, longitude as lon from pokestop where last_updated > '" . date_format($date, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        } elseif ($oSwLat != 0 && $lured == "true") {
            $datas = $db->query("select active_fort_modifier, enabled, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(lure_expiration, '+00:00', @@global.time_zone)) as lure_expiration, pokestop_id as external_id, latitude as lat, longitude as lon from pokestop where active_fort_modifier is not null and (latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . ") and not(latitude > " . $oSwLat . " and longitude > " . $oSwLng . " and latitude < " . $oNeLat . " and longitude < " . $oNeLng . ") and active_fort_modifier is not null")->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("select active_fort_modifier, enabled, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(lure_expiration, '+00:00', @@global.time_zone)) as lure_expiration, pokestop_id as external_id, latitude as lat, longitude as lon from pokestop where latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " and not(latitude > " . $oSwLat . " and longitude > " . $oSwLng . " and latitude < " . $oNeLat . " and longitude < " . $oNeLng . ")")->fetchAll();
        } elseif ($lured == "true") {
            $datas = $db->query("select active_fort_modifier, enabled, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(lure_expiration, '+00:00', @@global.time_zone)) as lure_expiration, pokestop_id as external_id, latitude as lat, longitude as lon from pokestop where active_fort_modifier is not null and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        } else {
            $datas = $db->query("select active_fort_modifier, enabled, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(lure_expiration, '+00:00', @@global.time_zone)) as lure_expiration, pokestop_id as external_id, latitude as lat, longitude as lon from pokestop where latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        }
    }

    $i = 0;

    $pokestops = array();

    /* fetch associative array */
    foreach ($datas as $row) {
        $p = array();

        $lat = floatval($row["lat"]);
        $lon = floatval($row["lon"]);

        $p["active_fort_modifier"] = isset($row["active_fort_modifier"]) ? $row["active_fort_modifier"] : null;
        $p["enabled"] = isset($row["enabled"]) ? boolval($row["enabled"]) : true;
        $p["last_modified"] = isset($row["last_modified"]) ? $row["last_modified"] * 1000 : 0;
        $p["latitude"] = $lat;
        $p["longitude"] = $lon;
        $p["lure_expiration"] = isset($row["lure_expiration"]) ? $row["lure_expiration"] * 1000 : null;
        $p["pokestop_id"] = $row["external_id"];

        $pokestops[] = $p;

        unset($datas[$i]);

        $i++;
    }

    return $pokestops;
}

function get_gyms($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
{

    global $db;

    $datas = array();

    global $map;
    if ($map == "monocle") {
        if ($swLat == 0) {
            $datas = $db->query("select t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.slots_available, t1.guard_pokemon_id from (select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id")->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("select t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.slots_available, t1.guard_pokemon_id from (select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id where t3.lat > " . $swLat . " and t3.lon > " . $swLng . " and t3.lat < " . $neLat . " and t3.lon < " . $neLng)->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("select t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.slots_available, t1.guard_pokemon_id from (select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id where t3.lat > " . $swLat . " and t3.lon > " . $swLng . " and t3.lat < " . $neLat . " and t3.lon < " . $neLng . " and not(t3.lat > " . $oSwLat . " and t3.lon > " . $oSwLng . " and t3.lat < " . $oNeLat . " and t3.lon < " . $oNeLng . ")")->fetchAll();
        } else {
            $datas = $db->query("select t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.slots_available, t1.guard_pokemon_id from (select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id where t3.lat > " . $swLat . " and t3.lon > " . $swLng . " and t3.lat < " . $neLat . " and t3.lon < " . $neLng)->fetchAll();
        }
    } else {
        if ($swLat == 0) {
            $datas = $db->query("select gym.gym_id as external_id, latitude as lat, longitude as lon, guard_pokemon_id, slots_available, total_cp, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(gym.last_scanned, '+00:00', @@global.time_zone)) as last_scanned, team_id as team, enabled, name, level, pokemon_id, cp, move_1, move_2, UNIX_TIMESTAMP(CONVERT_TZ(start, '+00:00', @@global.time_zone)) as raid_start, UNIX_TIMESTAMP(CONVERT_TZ(end, '+00:00', @@global.time_zone)) as raid_end from gym left join gymdetails on gym.gym_id = gymdetails.gym_id left join raid on gym.gym_id = raid.gym_id")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("select gym.gym_id as external_id, latitude as lat, longitude as lon, guard_pokemon_id, slots_available, total_cp, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(gym.last_scanned, '+00:00', @@global.time_zone)) as last_scanned, team_id as team, enabled, name, level, pokemon_id, cp, move_1, move_2, UNIX_TIMESTAMP(CONVERT_TZ(start, '+00:00', @@global.time_zone)) as raid_start, UNIX_TIMESTAMP(CONVERT_TZ(end, '+00:00', @@global.time_zone)) as raid_end from gym left join gymdetails on gym.gym_id = gymdetails.gym_id left join raid on gym.gym_id = raid.gym_id where gym.last_scanned > '" . date_format($date, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("select gym.gym_id as external_id, latitude as lat, longitude as lon, guard_pokemon_id, slots_available, total_cp, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(gym.last_scanned, '+00:00', @@global.time_zone)) as last_scanned, team_id as team, enabled, name, level, pokemon_id, cp, move_1, move_2, UNIX_TIMESTAMP(CONVERT_TZ(start, '+00:00', @@global.time_zone)) as raid_start, UNIX_TIMESTAMP(CONVERT_TZ(end, '+00:00', @@global.time_zone)) as raid_end from gym left join gymdetails on gym.gym_id = gymdetails.gym_id left join raid on gym.gym_id = raid.gym_id where latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " and not(latitude > " . $oSwLat . " and longitude > " . $oSwLng . " and latitude < " . $oNeLat . " and longitude < " . $oNeLng . ")")->fetchAll();
        } else {
            $datas = $db->query("select gym.gym_id as external_id, latitude as lat, longitude as lon, guard_pokemon_id, slots_available, total_cp, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified, UNIX_TIMESTAMP(CONVERT_TZ(gym.last_scanned, '+00:00', @@global.time_zone)) as last_scanned, team_id as team, enabled, name, level, pokemon_id, cp, move_1, move_2, UNIX_TIMESTAMP(CONVERT_TZ(start, '+00:00', @@global.time_zone)) as raid_start, UNIX_TIMESTAMP(CONVERT_TZ(end, '+00:00', @@global.time_zone)) as raid_end from gym left join gymdetails on gym.gym_id = gymdetails.gym_id left join raid on gym.gym_id = raid.gym_id where latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng)->fetchAll();
        }
    }

    $i = 0;

    $gyms = array();
    $gym_ids = array();

    $json_poke = "static/data/pokemon.json";
    $json_contents = file_get_contents($json_poke);
    $data = json_decode($json_contents, TRUE);

    /* fetch associative array */
    foreach ($datas as $row) {
        $lat = floatval($row["lat"]);
        $lon = floatval($row["lon"]);
        $gpid = intval($row["guard_pokemon_id"]);
        $lm = $row["last_modified"] * 1000;
        $ls = isset($row["last_scanned"]) ? $row["last_scanned"] * 1000 : null;
        $ti = isset($row["team"]) ? intval($row["team"]) : null;
        $tc = isset($row["total_cp"]) ? intval($row["total_cp"]) : null;
        $sa = intval($row["slots_available"]);

        $p = array();

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
        $p["pokemon"] = [];
        $p['total_gym_cp'] = $tc;

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

        $gym_ids[] = $row["external_id"];

        $gyms[$row["external_id"]] = $p;

        unset($datas[$i]);

        $i++;
    }

    $j = 0;
    if ($map != "monocle") {
        $ids = join("','", $gym_ids);
        $pokemons = $db->query("select gymmember.gym_id, pokemon_id, cp, trainer.name, trainer.level from gymmember join gympokemon on gymmember.pokemon_uid = gympokemon.pokemon_uid join trainer on gympokemon.trainer_name = trainer.name join gym on gym.gym_id = gymmember.gym_id where gymmember.last_scanned > gym.last_modified and gymmember.gym_id in ('" . $ids . "') group by name order by gymmember.gym_id, gympokemon.cp")->fetchAll();

        foreach ($pokemons as $pokemon) {
            $p = array();

            $pid = $pokemon["pokemon_id"];

            $p["pokemon_id"] = $pid;
            $p["pokemon_name"] = $data[$pid]['name'];
            $p["trainer_name"] = $pokemon["name"];
            $p["trainer_level"] = $pokemon["level"];
            $p["pokemon_cp"] = $pokemon["cp"];

            $gyms[$pokemon["gym_id"]]["pokemon"][] = $p;

            unset($pokemons[$j]);

            $j++;
        }
    } else {
        global $fork;
        $ids = join("','", $gym_ids);
        if ($fork != "asner")
            $raids = $db->query("select t1.fort_id, level, pokemon_id, time_battle as raid_start, time_end as raid_end from (select fort_id, MAX(time_end) AS MaxTimeEnd from raids group by fort_id) t1 left join raids t2 on t1.fort_id = t2.fort_id and MaxTimeEnd = time_end where t1.fort_id in ('" . $ids . "')")->fetchAll();
        else
            $raids = $db->query("select t3.external_id, t1.fort_id, raid_level as level, pokemon_id, cp, move_1, move_2, raid_start, raid_end from (select fort_id, MAX(raid_end) AS MaxTimeEnd from raid_info group by fort_id) t1 left join raid_info t2 on t1.fort_id = t2.fort_id and MaxTimeEnd = raid_end join forts t3 on t2.fort_id = t3.id where t3.external_id in ('" . $ids . "')")->fetchAll();

        foreach ($raids as $raid) {
            if ($fork != "asner")
                $id = $raid["fort_id"];
            else
                $id = $raid["external_id"];

            $rpid = intval($raid['pokemon_id']);
            $gyms[$id]['raid_level'] = intval($raid['level']);
            if ($rpid)
                $gyms[$id]['raid_pokemon_id'] = $rpid;
            if ($rpid)
                $gyms[$id]['raid_pokemon_name'] = i8ln($data[$rpid]['name']);
            $gyms[$id]['raid_pokemon_cp'] = isset($raid['cp']) ? intval($raid['cp']) : null;
            $gyms[$id]['raid_pokemon_move_1'] = isset($raid['move_1']) ? intval($raid['move_1']) : null;
            $gyms[$id]['raid_pokemon_move_2'] = isset($raid['move_2']) ? intval($raid['move_2']) : null;
            $gyms[$id]['raid_start'] = $raid["raid_start"] * 1000;
            $gyms[$id]['raid_end'] = $raid["raid_end"] * 1000;

            unset($raids[$j]);

            $j++;
        }
    }

    return $gyms;
}

function get_spawnpoints($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
{
    global $db;

    $datas = array();

    global $map;
    if ($map == "monocle") {
        if ($swLat == 0) {
            $datas = $db->query("select lat, lon, spawn_id, despawn_time from spawnpoints where updated > 0")->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("select lat, lon, spawn_id, despawn_time from spawnpoints where updated > '" . $tstamp . "' and lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng)->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("select lat, lon, spawn_id, despawn_time from spawnpoints where updated > 0 and lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng . " and not(lat > " . $oSwLat . " and lon > " . $oSwLng . " and lat < " . $oNeLat . " and lon < " . $oNeLng . ")")->fetchAll();
        } else {
            $datas = $db->query("select lat, lon, spawn_id, despawn_time from spawnpoints where updated > 0 and lat > " . $swLat . " and lon > " . $swLng . " and lat < " . $neLat . " and lon < " . $neLng)->fetchAll();
        }

        $spawnpoints = array();
        $i = 0;

        foreach ($datas as $row) {
            $p = array();

            $p["latitude"] = floatval($row["lat"]);
            $p["longitude"] = floatval($row["lon"]);
            $p["spawnpoint_id"] = $row["spawn_id"];
            $p["time"] = intval($row["despawn_time"]);

            $spawnpoints[] = $p;

            unset($row[$i]);

            $i++;
        }

        return $spawnpoints;
    } else {
        if ($swLat == 0) {
            $datas = $db->query("select latitude as lat, longitude as lon, spawnpoint_id as spawn_id, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as time, count(spawnpoint_id) as count from pokemon group by latitude, longitude, spawnpoint_id, time")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("select latitude as lat, longitude as lon, spawnpoint_id as spawn_id, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as time, count(spawnpoint_id) as count from pokemon where last_modified > '" . date_format($date, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " group by latitude, longitude, spawnpoint_id, time")->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("select latitude as lat, longitude as lon, spawnpoint_id as spawn_id, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as time, count(spawnpoint_id) as count from pokemon where latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " and not(latitude > " . $oSwLat . " and longitude > " . $oSwLng . " and latitude < " . $oNeLat . " and longitude < " . $oNeLng . ") group by latitude, longitude, spawnpoint_id, time")->fetchAll();
        } else {
            $datas = $db->query("select latitude as lat, longitude as lon, spawnpoint_id as spawn_id, UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) as time, count(spawnpoint_id) as count from pokemon where latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " group by latitude, longitude, spawnpoint_id, time")->fetchAll();
        }

        $spawnpoints = array();
        $spawnpoint_values = array();
        $i = 0;

        foreach ($datas as $row) {
            $key = $row["spawn_id"];
            $count = intval($row["count"]);
            $time = ($row["time"] + 2700) % 3600;

            $p = array();

            if (!array_key_exists($key, $spawnpoints)) {
                $p[$key]["spawnpoint_id"] = $key;
                $p[$key]["latitude"] = floatval($row["lat"]);
                $p[$key]["longitude"] = floatval($row["lon"]);
            } else {
                $p[$key]["special"] = true;
            }

            if (!array_key_exists("time", $p[$key]) || $count >= $p[$key]["count"]) {
                $p[$key]["time"] = $time;
                $p[$key]["count"] = $count;
            }

            $spawnpoints[] = $p;
            $spawnpoint_values[] = $p[$key];

            unset($datas[$i]);

            $i++;
        }

        foreach ($spawnpoint_values as $key => $subArr) {
            unset($subArr['count']);
            $spawnpoint_values[$key] = $subArr;
        }

        return $spawnpoint_values;
    }
}

function get_recent($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
{
    global $db;

    $datas = array();

    global $map;
    if ($map == "monocle") {

    } else {
        if ($swLat == 0) {
            $datas = $db->query("select latitude, longitude, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified from scannedlocation where last_modified >= '2017-06-16 15:57:32' order by last_modified asc")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("select latitude, longitude, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified from scannedlocation where last_modified >= '" . date_format($date, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " order by last_modified asc")->fetchAll();
        } elseif ($oSwLat != 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->sub(new DateInterval('PT15M'));
            $datas = $db->query("select latitude, longitude, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified from scannedlocation where last_modified >= '" . date_format($date, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " and not(latitude > " . $oSwLat . " and longitude > " . $oSwLng . " and latitude < " . $oNeLat . " and longitude < " . $oNeLng . ") and last_modified >= '" . date_format($date, 'Y-m-d H:i:s') . "' order by last_modified asc")->fetchAll();
        } else {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->sub(new DateInterval('PT15M'));
            $datas = $db->query("select latitude, longitude, UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) as last_modified from scannedlocation where last_modified >= '" . date_format($date, 'Y-m-d H:i:s') . "' and latitude > " . $swLat . " and longitude > " . $swLng . " and latitude < " . $neLat . " and longitude < " . $neLng . " order by last_modified asc")->fetchAll();
        }
    }

    $recent = array();
    $i = 0;

    foreach ($datas as $row) {
        $p = array();

        $p["latitude"] = floatval($row["latitude"]);
        $p["longitude"] = floatval($row["longitude"]);

        $lm = $row["last_modified"] * 1000;
        $p["last_modified"] = $lm;

        $recent[] = $p;

        unset($datas[$i]);

        $i++;
    }

    return $recent;
}