<?php

include('config/config.php');


$now = new DateTime();

$d = array();

$d["timestamp"] = $now->getTimestamp();

$swLat = !empty($_POST['swLat']) ? $_POST['swLat'] : 0;
$neLng = !empty($_POST['neLng']) ? $_POST['neLng'] : 0;
$swLng = !empty($_POST['swLng']) ? $_POST['swLng'] : 0;
$neLat = !empty($_POST['neLat']) ? $_POST['neLat'] : 0;
$oSwLat = !empty($_POST['oSwLat']) ? $_POST['oSwLat'] : 0;
$oSwLng = !empty($_POST['oSwLng']) ? $_POST['oSwLng'] : 0;
$oNeLat = !empty($_POST['oNeLat']) ? $_POST['oNeLat'] : 0;
$oNeLng = !empty($_POST['oNeLng']) ? $_POST['oNeLng'] : 0;
$luredonly = !empty($_POST['luredonly']) ? $_POST['luredonly'] : false;
$lastpokemon = !empty($_POST['lastpokemon']) ? $_POST['lastpokemon'] : false;
$lastgyms = !empty($_POST['lastgyms']) ? $_POST['lastgyms'] : false;
$lastpokestops = !empty($_POST['lastpokestops']) ? $_POST['lastpokestops'] : false;
$lastlocs = !empty($_POST['lastslocs']) ? $_POST['lastslocs'] : false;
$lastspawns = !empty($_POST['lastspawns']) ? $_POST['lastspawns'] : false;
$d["lastpokestops"] = !empty($_POST['pokestops']) ? $_POST['pokestops'] : false;
$d["lastgyms"] = !empty($_POST['gyms']) ? $_POST['gyms'] : false;
$d["lastslocs"] = !empty($_POST['scanned']) ? $_POST['scanned'] : false;
$d["lastspawns"] = !empty($_POST['spawnpoints']) ? $_POST['spawnpoints'] : false;
$d["lastpokemon"] = !empty($_POST['pokemon']) ? $_POST['pokemon'] : false;

$timestamp = !empty($_POST['timestamp']) ? $_POST['timestamp'] : 0;

$useragent = $_SERVER['HTTP_USER_AGENT'];
if (empty($swLat) || empty($swLng) || empty($neLat) || empty($neLng) || preg_match("/curl|libcurl/", $useragent)) {
    http_response_code(400);
    die();
}
if ($maxLatLng > 0 && ((($neLat - $swLat) > $maxLatLng) || (($neLng - $swLng) > $maxLatLng))) {
    http_response_code(400);
    die();
}

if (!validateToken($_POST['token'])) {
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

        if (!empty($_POST['eids'])) {
            $eids = explode(",", $_POST['eids']);

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

        if (!empty($_POST['reids'])) {
            $reids = explode(",", $_POST['reids']);

            $d["pokemons"] = $d["pokemons"] + (get_active_by_id($reids, $swLat, $swLng, $neLat, $neLng));

            $d["reids"] = !empty($_POST['reids']) ? $reids : null;
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
if (!$noGyms || !$noRaids) {
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

$d['token'] = refreshCsrfToken();

$jaysson = json_encode($d);
echo $jaysson;

function get_active($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
{
    global $db;

    $datas = array();
    global $map;
    if ($map == "monocle") {
        if ($swLat == 0) {
            $datas = $db->query("SELECT * FROM sightings WHERE expire_timestamp > :time", [':time'=> time()])->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :time 
AND    lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':time' => time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :time 
       AND lat > :swLat
       AND lon > :swLng 
       AND lat < :neLat 
       AND lon < :neLng 
       AND NOT( lat > :oSwLat 
                AND lon > :oSwLng 
                AND lat < :oNeLat 
                AND lon < :oNeLng ) " , [':time' => time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {

            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :time 
AND    lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':time' => time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }
    } else {
        $time = new DateTime();
        $time->setTimeZone(new DateTimeZone('UTC'));
        $time->setTimestamp(time());
        if ($swLat == 0) {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime", [':disappearTime' => date_format($time, 'y-m-d H:I:s')])->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    last_modified > :lastModified
AND    latitude > :swLat 
AND    longitude > :swLng
AND    latitude < :neLat
AND    longitude < :neLng", [':disappearTime' => date_format($time, 'y-m-d H:I:s'), ':lastModified'=>date_format($date, 'y -m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    latitude > :swLat
AND    longitude > :swLng 
AND    latitude < :neLat 
AND    longitude < :neLng 
AND    NOT( 
              latitude > :oSwLat 
       AND    longitude > :oSwLng 
       AND    latitude < :oNeLat 
       AND    longitude < :oNeLng)", [':disappearTime' => date_format($time, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    latitude > :swLat
AND    longitude > :swLng 
AND    latitude < :neLat 
AND    longitude < :neLng",  [':disappearTime' => date_format($time, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
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

        $atk = !empty($row["atk_iv"]) ? intval($row["atk_iv"]) : null;
        $def = !empty($row["def_iv"]) ? intval($row["def_iv"]) : null;
        $sta = !empty($row["sta_iv"]) ? intval($row["sta_iv"]) : null;
        $mv1 = !empty($row["move_1"]) ? intval($row["move_1"]) : null;
        $mv2 = !empty($row["move_2"]) ? intval($row["move_2"]) : null;
        $weight = !empty($row["weight"]) ? floatval($row["weight"]) : null;
        $height = !empty($row["height"]) ? floatval($row["height"]) : null;
        $gender = !empty($row["gender"]) ? intval($row["gender"]) : null;
        $form = !empty($row["form"]) ? intval($row["form"]) : null;
        $cp = !empty($row["cp"]) ? intval($row["cp"]) : null;
        $cpm = !empty($row["cp_multiplier"]) ? floatval($row["cp_multiplier"]) : null;
        $level = !empty($row["level"]) ? intval($row["level"]) : null;

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
    $pkmn_in = '';
    if (count($ids)) {
        $i=1;
        foreach ($ids as $id) {
            $pkmn_ids[':qry_'.$i] = $id;
            $pkmn_in .= ':'.'qry_'.$i.",";
            $i++;
        }
        $pkmn_in = substr($pkmn_in, 0, -1);
    } else {
        $pkmn_ids = [];
    }


    if ($map == "monocle") {
        if ($swLat == 0) {
            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  `expire_timestamp` > :time
       AND pokemon_id IN ( $pkmn_in ) ", array_merge($pkmn_ids, [':time'=>time()]))->fetchAll();
        } else {
            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :timeStamp
AND    pokemon_id IN ( $pkmn_in ) 
AND    lat > :swLat 
AND    lon > :swLng
AND    lat < :neLat
AND    lon < :neLng", array_merge($pkmn_ids, [':timeStamp'=> time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng]))->fetchAll();
        }
    } else {
        $time = new DateTime();
        $time->setTimeZone(new DateTimeZone('UTC'));
        $time->setTimestamp(time());
        if ($swLat == 0) {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    pokemon_id  IN ( $pkmn_in )", array_merge($pkmn_ids, [':disappearTime' => date_format($time, 'y-m-d H:I:s')]))->fetchAll();
        } else {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    pokemon_id  IN ( $pkmn_in )
AND    latitude > :swLat
AND    longitude > :swLng 
AND    latitude < :neLat 
AND    longitude < :neLng", array_merge($pkmn_ids, [':disappearTime' => date_format($time, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng]))->fetchAll();
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

        $atk = !empty($row["atk_iv"]) ? intval($row["atk_iv"]) : null;
        $def = !empty($row["def_iv"]) ? intval($row["def_iv"]) : null;
        $sta = !empty($row["sta_iv"]) ? intval($row["sta_iv"]) : null;
        $mv1 = !empty($row["move_1"]) ? intval($row["move_1"]) : null;
        $mv2 = !empty($row["move_2"]) ? intval($row["move_2"]) : null;
        $weight = !empty($row["weight"]) ? floatval($row["weight"]) : null;
        $height = !empty($row["height"]) ? floatval($row["height"]) : null;
        $gender = !empty($row["gender"]) ? intval($row["gender"]) : null;
        $form = !empty($row["form"]) ? intval($row["form"]) : null;
        $cp = !empty($row["cp"]) ? intval($row["cp"]) : null;
        $cpm = !empty($row["cp_multiplier"]) ? floatval($row["cp_multiplier"]) : null;
        $level = !empty($row["level"]) ? intval($row["level"]) : null;

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
            $datas = $db->query("SELECT external_id, lat, lon FROM pokestops")->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("SELECT external_id, 
       lat, 
       lon 
FROM   pokestops 
WHERE  lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT external_id, 
       lat, 
       lon 
FROM   pokestops 
WHERE  lat > :swLat
       AND lon > :swLng 
       AND lat < :neLat 
       AND lon < :neLng
       AND NOT( lat > :oSwLat 
                AND lon > :oSwLng 
                AND lat < :oNeLat 
                AND lon < :oNeLng ) ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT external_id, 
       lat, 
       lon 
FROM   pokestops 
WHERE  lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }
    } else {
        if ($swLat == 0) {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) 
       AS 
       lure_expiration, 
       pokestop_id 
       AS external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon 
FROM   pokestop ")->fetchAll();
        } elseif ($tstamp > 0 && $lured == "true") {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))   AS last_modified,
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
       pokestop_id                                                               AS external_id,
       latitude                                                                  AS lat, 
       longitude                                                                 AS lon 
FROM   pokestop 
WHERE  last_updated > :lastUpdated
AND    active_fort_modifier IS NOT NULL 
AND    latitude > :swLat 
AND    longitude > :swLng 
AND    latitude < :neLat
AND    longitude < :neLng", [':lastUpdated' => date_format($date, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))   AS last_modified,
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
       pokestop_id                                                               AS external_id,
       latitude                                                                  AS lat, 
       longitude                                                                 AS lon 
FROM   pokestop 
WHERE  last_updated > :lastUpdated
AND    latitude > :swLat
AND    longitude > :swLng 
AND    latitude < :neLat  
AND    longitude < :neLng", [':lastUpdated' => date_format($date, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0 && $lured == "true") {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) 
       AS 
       lure_expiration, 
       pokestop_id 
       AS external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon 
FROM   pokestop 
WHERE  active_fort_modifier IS NOT NULL 
       AND ( latitude > :swLat
             AND longitude > :swLng
             AND latitude < :neLat 
             AND longitude < :neLng ) 
       AND NOT( latitude > :oSwLat
                AND longitude > :oSwLng 
                AND latitude < :oNeLat 
                AND longitude < :oNeLng ) 
       AND active_fort_modifier IS NOT NULL", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) 
       AS 
       lure_expiration, 
       pokestop_id 
       AS external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon 
FROM   pokestop 
WHERE  latitude > :swLat
       AND longitude > :swLng 
       AND latitude < :neLat 
       AND longitude < :neLng 
       AND NOT( latitude > :oSwLat 
                AND longitude > :oSwLng 
                AND latitude < :oNeLat 
                AND longitude < :oNeLng ) ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } elseif ($lured == "true") {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))   AS last_modified,
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
       pokestop_id                                                               AS external_id,
       latitude                                                                  AS lat, 
       longitude                                                                 AS lon 
FROM   pokestop 
WHERE  active_fort_modifier IS NOT NULL 
AND    latitude > :swLat 
AND    longitude > :swLng
AND    latitude < :neLat
AND    longitude < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))   AS last_modified,
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
       pokestop_id                                                               AS external_id,
       latitude                                                                  AS lat, 
       longitude                                                                 AS lon 
FROM   pokestop 
WHERE  latitude > :swLat
AND    longitude > :swLng
AND    latitude < :neLat
AND    longitude < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }
    }

    $i = 0;

    $pokestops = array();

    /* fetch associative array */
    foreach ($datas as $row) {
        $p = array();

        $lat = floatval($row["lat"]);
        $lon = floatval($row["lon"]);

        $p["active_fort_modifier"] = !empty($row["active_fort_modifier"]) ? $row["active_fort_modifier"] : null;
        $p["enabled"] = !empty($row["enabled"]) ? boolval($row["enabled"]) : true;
        $p["last_modified"] = !empty($row["last_modified"]) ? $row["last_modified"] * 1000 : 0;
        $p["latitude"] = $lat;
        $p["longitude"] = $lon;
        $p["lure_expiration"] = !empty($row["lure_expiration"]) ? $row["lure_expiration"] * 1000 : null;
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
            $datas = $db->query("SELECT t3.external_id, 
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
              ON t1.fort_id = t3.id")->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("SELECT t3.external_id, 
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
WHERE  t3.lat > :swLat 
       AND t3.lon > :swLng 
       AND t3.lat < :neLat 
       AND t3.lon < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT t3.external_id, 
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
WHERE  t3.lat > :swLat 
       AND t3.lon > :swLng
       AND t3.lat < :neLat
       AND t3.lon < :neLng
       AND NOT( t3.lat > :oSwLat
                AND t3.lon > :oSwLng
                AND t3.lat < :oNeLat
                AND t3.lon < :oNeLng)", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT    t3.external_id, 
          t3.lat, 
          t3.lon, 
          t1.last_modified, 
          t1.team, 
          t1.slots_available, 
          t1.guard_pokemon_id 
FROM      ( 
                   SELECT   fort_id, 
                            Max(last_modified) AS maxlastmodified 
                   FROM     fort_sightings 
                   GROUP BY fort_id) t2 
LEFT JOIN fort_sightings t1 
ON        t2.fort_id = t1.fort_id 
AND       t2.maxlastmodified = t1.last_modified 
LEFT JOIN forts t3 
ON        t1.fort_id = t3.id 
WHERE     t3.lat > :swLat
AND       t3.lon > :swLng 
AND       t3.lat < :neLat 
AND       t3.lon < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }
    } else {
        global $fork;
        if ($fork != "sloppy") {
            if ($swLat == 0) {
                $datas = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       total_cp, 
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
              ON gym.gym_id = raid.gym_id ")->fetchAll();
            } elseif ($tstamp > 0) {
                $date = new DateTime();
                $date->setTimezone(new DateTimeZone('UTC'));
                $date->setTimestamp($tstamp);
                $datas = $db->query("SELECT    gym.gym_id AS external_id, 
          latitude   AS lat, 
          longitude  AS lon, 
          guard_pokemon_id, 
          slots_available, 
          total_cp, 
          Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))    AS last_modified,
          Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
          team_id                                                                    AS team,
          enabled, 
          name, 
          level, 
          pokemon_id, 
          cp, 
          move_1, 
          move_2, 
          Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) AS raid_start, 
          Unix_timestamp(Convert_tz(end, '+00:00', @@global.time_zone)) AS raid_end 
FROM      gym 
LEFT JOIN gymdetails 
ON        gym.gym_id = gymdetails.gym_id 
LEFT JOIN raid 
ON        gym.gym_id = raid.gym_id 
WHERE     gym.last_scanned > '" . date_format($date, 'y-m-d H:I:s') . "' 
AND       latitude > :swLat
AND       longitude > :swLat
AND       latitude < :neLat
AND       longitude < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
            } elseif ($oSwLat != 0) {
                $datas = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       total_cp, 
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
WHERE  latitude > :swLat
       AND longitude > :swLng
       AND latitude < :neLat 
       AND longitude < :neLng 
       AND NOT( latitude > :oSwLat 
                AND longitude > :oSwLng 
                AND latitude < :oNeLat 
                AND longitude < :oNeLng )", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
            } else {
                $datas = $db->query("SELECT    gym.gym_id AS external_id, 
          latitude   AS lat, 
          longitude  AS lon, 
          guard_pokemon_id, 
          slots_available, 
          total_cp, 
          Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))    AS last_modified,
          Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
          team_id                                                                    AS team,
          enabled, 
          name, 
          level, 
          pokemon_id, 
          cp, 
          move_1, 
          move_2, 
          Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) AS raid_start, 
          Unix_timestamp(Convert_tz( 
end, '+00:00', @@global.time_zone)) AS raid_end 
FROM      gym 
LEFT JOIN gymdetails 
ON        gym.gym_id = gymdetails.gym_id 
LEFT JOIN raid 
ON        gym.gym_id = raid.gym_id 
WHERE     latitude > :swLat 
AND       longitude > :swLng 
AND       latitude < :neLat 
AND       longitude < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
            }
        } else {
            if ($swLat == 0) {
                $datas = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       total_gym_cp 
       AS total_cp, 
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
              ON gym.gym_id = gymdetails.gym_id")->fetchAll();
            } elseif ($tstamp > 0) {
                $date = new DateTime();
                $date->setTimezone(new DateTimeZone('UTC'));
                $date->setTimestamp($tstamp);
                $datas = $db->query("SELECT    gym.gym_id AS external_id, 
          latitude   AS lat, 
          longitude  AS lon, 
          guard_pokemon_id, 
          slots_available, 
          total_gym_cp                                                               AS total_cp,
          Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))    AS last_modified,
          Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
          team_id                                                                    AS team,
          enabled, 
          name, 
          raid_level                                                            AS level, 
          raid_pokemon_id                                                       AS pokemon_id,
          raid_pokemon_cp                                                       AS cp, 
          raid_pokemon_move_1                                                   AS move_1, 
          raid_pokemon_move_2                                                   AS move_2, 
          Unix_timestamp(Convert_tz(raid_battle, '+00:00', @@global.time_zone)) AS raid_start,
          Unix_timestamp(Convert_tz(raid_end, '+00:00', @@global.time_zone))    AS raid_end 
FROM      gym 
LEFT JOIN gymdetails 
ON        gym.gym_id = gymdetails.gym_id 
WHERE     gym.last_scanned > :lastScanned
AND       latitude > :swLat 
AND       longitude > :swLng 
AND       latitude < :neLat 
AND       longitude < :neLng". ['lastScanned'=>date_format($date, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
            } elseif ($oSwLat != 0) {
                $datas = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       total_gym_cp 
       AS total_cp, 
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
WHERE  latitude > :swLat
       AND longitude > :swLng 
       AND latitude < :neLat 
       AND longitude < :neLng 
       AND NOT( latitude > :oSwLat 
                AND longitude > :oSwLng 
                AND latitude < :oNeLat 
                AND longitude < :oNeLng)", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
            } else {
                $datas = $db->query("SELECT    gym.gym_id AS external_id, 
          latitude   AS lat, 
          longitude  AS lon, 
          guard_pokemon_id, 
          slots_available, 
          total_gym_cp                                                               AS total_cp,
          Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))    AS last_modified,
          Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
          team_id                                                                    AS team,
          enabled, 
          name, 
          raid_level                                                            AS level, 
          raid_pokemon_id                                                       AS pokemon_id,
          raid_pokemon_cp                                                       AS cp, 
          raid_pokemon_move_1                                                   AS move_1, 
          raid_pokemon_move_2                                                   AS move_2, 
          Unix_timestamp(Convert_tz(raid_battle, '+00:00', @@global.time_zone)) AS raid_start,
          Unix_timestamp(Convert_tz(raid_end, '+00:00', @@global.time_zone))    AS raid_end 
FROM      gym 
LEFT JOIN gymdetails 
ON        gym.gym_id = gymdetails.gym_id 
WHERE     latitude > :swLat 
AND       longitude > :swLng 
AND       latitude < :neLat 
AND       longitude < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
            }
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
        $ls = !empty($row["last_scanned"]) ? $row["last_scanned"] * 1000 : null;
        $ti = !empty($row["team"]) ? intval($row["team"]) : null;
        $tc = !empty($row["total_cp"]) ? intval($row["total_cp"]) : null;
        $sa = intval($row["slots_available"]);

        $p = array();

        $p["enabled"] = !empty($row["enabled"]) ? boolval($row["enabled"]) : true;
        $p["guard_pokemon_id"] = $gpid;
        $p["gym_id"] = $row["external_id"];
        $p["slots_available"] = $sa;
        $p["last_modified"] = $lm;
        $p["last_scanned"] = $ls;
        $p["latitude"] = $lat;
        $p["longitude"] = $lon;
        $p["name"] = !empty($row["name"]) ? $row["name"] : null;
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
            $p['raid_pokemon_cp'] = !empty($row['cp']) ? intval($row['cp']) : null;
            $p['raid_pokemon_move_1'] = !empty($row['move_1']) ? intval($row['move_1']) : null;
            $p['raid_pokemon_move_2'] = !empty($row['move_2']) ? intval($row['move_2']) : null;
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
        $gym_in = '';
        if (count($gym_ids)) {
            $i=1;
            foreach ($gym_ids as $id) {
                $gym_qry_ids[':qry_'.$i] = $id;
                $gym_in .= ':'.'qry_'.$i.",";
                $i++;
            }
            $gym_in = substr($gym_in, 0, -1);
        } else {
            $gym_qry_ids = [];
        }
        $pokemons = $db->query("SELECT gymmember.gym_id, 
       pokemon_id, 
       cp, 
       trainer.name, 
       trainer.level 
FROM   gymmember 
       JOIN gympokemon 
         ON gymmember.pokemon_uid = gympokemon.pokemon_uid 
       JOIN trainer 
         ON gympokemon.trainer_name = trainer.name 
       JOIN gym 
         ON gym.gym_id = gymmember.gym_id 
WHERE  gymmember.last_scanned > gym.last_modified 
       AND gymmember.gym_id IN ( $gym_in ) 
GROUP  BY name 
ORDER  BY gymmember.gym_id, 
          gympokemon.cp ", $gym_qry_ids)->fetchAll();

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
        $gyms_in = '';
        if (count($gym_ids)) {
            $i=1;
            foreach ($gym_ids as $id) {
                $gym_in_ids[':qry_'.$i] = $id;
                $gyms_in .= ':'.'qry_'.$i.",";
                $i++;
            }
            $gyms_in = substr($gyms_in, 0, -1);
        } else {
            $gym_in_ids = [];
        }
        if ($fork != "asner")
            $raids = $db->query("SELECT t1.fort_id, 
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
WHERE  t1.fort_id IN ( $gyms_in ) ", $gym_in_ids)->fetchAll();
        else
            $raids = $db->query("SELECT t3.external_id, 
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
WHERE  t3.external_id IN ( $gyms_in ) ", $gym_in_ids)->fetchAll();

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
            $gyms[$id]['raid_pokemon_cp'] = !empty($raid['cp']) ? intval($raid['cp']) : null;
            $gyms[$id]['raid_pokemon_move_1'] = !empty($raid['move_1']) ? intval($raid['move_1']) : null;
            $gyms[$id]['raid_pokemon_move_2'] = !empty($raid['move_2']) ? intval($raid['move_2']) : null;
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
            $datas = $db->query("SELECT lat, lon, spawn_id, despawn_time FROM spawnpoints WHERE updated > 0")->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("SELECT lat, 
       lon, 
       spawn_id, 
       despawn_time 
FROM   spawnpoints 
WHERE  updated > :updated
AND    lat > :swLat 
AND    lon > :swLng
AND    lat < :neLat 
AND    lon < :neLng", ['updated'=> $tstamp,':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT lat, 
       lon, 
       spawn_id, 
       despawn_time 
FROM   spawnpoints 
WHERE  updated > 0 
       AND lat > :swLat  
       AND lon > :swLng 
       AND lat < :neLat 
       AND lon <  :neLng  
       AND NOT( lat >  :oSwLat 
                AND lon >  :oSwLng
                AND lat <  :oNeLat
                AND lon <  :oNeLng ) ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT lat, 
       lon, 
       spawn_id, 
       despawn_time 
FROM   spawnpoints 
WHERE  updated > 0 
AND    lat >  :swLat  
AND    lon >  :swLng 
AND    lat < :neLat 
AND    lon < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
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
            $datas = $db->query("SELECT latitude 
       AS lat, 
       longitude 
       AS lon, 
       spawnpoint_id 
       AS spawn_id, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) 
       AS time, 
       Count(spawnpoint_id) 
       AS count 
FROM   pokemon 
GROUP  BY latitude, 
          longitude, 
          spawnpoint_id, 
          time ")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT   latitude                                                                 AS lat, 
         longitude                                                                AS lon, 
         spawnpoint_id                                                            AS spawn_id, 
         Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS time, 
         Count(spawnpoint_id)                                                     AS count 
FROM     pokemon 
WHERE    last_modified > :lastModified
AND      latitude > :swLat  
AND      longitude > :swLng  
AND      latitude < :neLat  
AND      longitude < :neLng 
GROUP BY latitude, 
         longitude, 
         spawnpoint_id, 
         time", [':lastModified' => date_format($date, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT latitude 
       AS lat, 
       longitude 
       AS lon, 
       spawnpoint_id 
       AS spawn_id, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) 
       AS time, 
       Count(spawnpoint_id) 
       AS count 
FROM   pokemon 
WHERE  latitude > :swLat  
AND      longitude > :swLng  
AND      latitude < :neLat  
AND      longitude < :neLng 
       AND NOT( latitude >  :oSwLat 
                AND longitude >  :oSwLng
                AND latitude <  :oNeLat
                AND longitude <  :oNeLng ) 
GROUP  BY latitude, 
          longitude, 
          spawnpoint_id, 
          time ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT latitude 
       AS lat, 
       longitude 
       AS lon, 
       spawnpoint_id 
       AS spawn_id, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) 
       AS time, 
       Count(spawnpoint_id) 
       AS count 
FROM   pokemon 
WHERE  latitude > :swLat  
AND      longitude > :swLng  
AND      latitude < :neLat  
AND      longitude < :neLng 
GROUP  BY latitude, 
          longitude, 
          spawnpoint_id, 
          time ", [ ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
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
            $datas = $db->query("SELECT latitude, 
       longitude, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified 
FROM   scannedlocation 
WHERE  last_modified >= '2017-06-16 15:57:32' 
ORDER  BY last_modified ASC ")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT   latitude, 
         longitude, 
         Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified
FROM     scannedlocation 
WHERE    last_modified >= :lastModified
AND      latitude > :swLat 
AND      longitude > :swLng
AND      latitude < :neLat 
AND      longitude < :neLng 
ORDER BY last_modified ASC", [':lastModified' => date_format($date, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->sub(new DateInterval('PT15M'));
            $datas = $db->query("SELECT   latitude, 
         longitude, 
         Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified
FROM     scannedlocation 
WHERE    last_modified >= :lastModified
AND      latitude > :swLat 
AND      longitude > :swLng
AND      latitude < :neLat 
AND      longitude < :neLng 
AND      NOT( latitude >  :oSwLat 
                AND longitude >  :oSwLng
                AND latitude <  :oNeLat
                AND longitude <  :oNeLng ) 
AND      last_modified >= :lastModified
ORDER BY last_modified ASC", [':lastModified' => date_format($date, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->sub(new DateInterval('PT15M'));
            $datas = $db->query("SELECT   latitude, 
         longitude, 
         Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified
FROM     scannedlocation 
WHERE    last_modified >= :lastModified
AND      latitude > :swLat 
AND      longitude > :swLng
AND      latitude < :neLat 
AND      longitude < :neLng 
ORDER BY last_modified ASC", [':lastModified' => date_format($date, 'y-m-d H:I:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
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