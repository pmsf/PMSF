<?php
include('config.php');
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

if ($d["lastpokemon"] == "true") {
    if ($lastpokemon != 'true') {
        $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng);
    } else {
        $timestamp = 0;

        if (isset($_GET['timestamp'])) {
            $timestamp = $_GET['timestamp'];
            $timestamp = $timestamp - 1;
            $timestamp = date("Y-m-d H:i:s", $timestamp);
        }

        if ($newarea) {
            $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
        } else {
            $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng, $timestamp);
        }
    }

    if (isset($_GET['eids'])) {
        $ids = explode(",", $_GET['eids']);

        foreach ($d['pokemons'] as $elementKey => $element) {
            foreach ($element as $valueKey => $value) {
                if ($valueKey == 'pokemon_id') {
                    if (in_array($value, $ids)) {
                        //delete this particular object from the $array
                        unset($d['pokemons'][$elementKey]);
                    }
                }
            }
        }
    }
}

//currently really rubbish due to lack of data but need the formatting for tweaking later on!

if ($d["lastpokestops"] == "true") {
    if ($lastpokestops == "true") {
        $d["pokestops"] = get_stops($swLat, $swLng, $neLat, $neLng);
    } else {
        if ($newarea) {
            $d["pokestops"] = get_stops($swLat, $swLng, $neLat, $neLng);
        } else {
            $d["pokestops"] = get_stops($swLat, $swLng, $neLat, $neLng);
        }
    }
}

if ($d["lastgyms"] == "true") {
    if ($lastgyms == "true") {
        $d["gyms"] = get_gyms($swLat, $swLng, $neLat, $neLng);
    } else {
        if ($newarea) {
            $d["gyms"] = get_gyms($swLat, $swLng, $neLat, $neLng);
        } else {
            $d["gyms"] = get_gyms($swLat, $swLng, $neLat, $neLng);
        }
    }
}

$jaysson = json_encode($d);
echo $jaysson;

function get_active($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
{
    global $db;

    $datas = "";

    if ($swLat == 0) {
        $datas = $db->select("sightings", "*", [
            "expire_timestamp[>]" => time()
        ]);

    } elseif ($tstamp > 0) {

        $datas = $db->select("sightings", "*", [
            "expire_timestamp[>]" => time(),
            "lat[>]" => $swLat,
            "lon[>]" => $swLng,
            "lat[<]" => $neLat,
            "lon[<]" => $neLng
        ]);

    } elseif ($oSwLat != 0) {

        $datas = $db->select("sightings", "*", [
            "expire_timestamp[>]" => time(),
            "lat[>]" => $swLat,
            "lon[>]" => $swLng,
            "lat[<]" => $neLat,
            "lon[<]" => $neLng
        ]);

    } else {

        $datas = $db->select("sightings", "*", [
            "expire_timestamp[>]" => time(),
            "lat[>]" => $swLat,
            "lon[>]" => $swLng,
            "lat[<]" => $neLat,
            "lon[<]" => $neLng
        ]);
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
        $cp = isset($row["cp"]) ? intval($row["cp"]) : null;
        $cpm = isset($row["cp_multiplier"]) ? floatval($row["cp_multiplier"]) : null;

        $p["disappear_time"] = $dissapear; //done
        $p["encounter_id"] = $row["encounter_id"]; //done
        $p["individual_attack"] = $atk; //done
        $p["individual_defense"] = $def; //done
        $p["individual_stamina"] = $sta; //done
        $p["latitude"] = $lat; //done
        $p["longitude"] = $lon; //done
        $p["move_1"] = $mv1; //done
        $p["move_2"] = $mv2;
        $p["pokemon_id"] = $pokeid;
        $p["pokemon_name"] = $data[$pokeid]['name'];
        $p["pokemon_rarity"] = $data[$pokeid]['rarity'];
        $p["cp"] = $cp;
        $p["cp_multiplier"] = $cpm;

        $p["pokemon_types"] = $data[$pokeid]["types"];
        $p["spawnpoint_id"] = $row["spawn_id"];

        $pokemons[] = $p;

        unset($datas[$i]);

        $i++;
    }

    return $pokemons;
}

function get_active_by_id($ids, $swLat, $swLng, $neLat, $neLng)
{
    //to be added for when it's never used...

    $pokemons = array();

    return $pokemons;
}

function get_stops($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
{

    global $db;

    $datas = "";

    if ($swLat == 0) {
        $datas = $db->select("pokestops", [
            "external_id",
            "lat",
            "lon"
        ]);
    } elseif ($tstamp > 0) {
        $datas = $db->select("pokestops", [
            "external_id",
            "lat",
            "lon"
        ], [
            "lat[>]" => $swLat,
            "lon[>]" => $swLng,
            "lat[<]" => $neLat,
            "lon[<]" => $neLng
        ]);
    } elseif ($oSwLat != 0) {
        $datas = $db->select("pokestops", [
            "external_id",
            "lat",
            "lon"
        ], [
            "lat[>]" => $swLat,
            "lon[>]" => $swLng,
            "lat[<]" => $neLat,
            "lon[<]" => $neLng
        ]);
    } else {
        $datas = $db->select("pokestops", [
            "external_id",
            "lat",
            "lon"
        ], [
            "lat[>]" => $swLat,
            "lon[>]" => $swLng,
            "lat[<]" => $neLat,
            "lon[<]" => $neLng
        ]);
    }

    $i = 0;

    $pokestops = array();

    /* fetch associative array */
    foreach ($datas as $row) {
        $p = array();

        $lat = floatval($row["lat"]);
        $lon = floatval($row["lon"]);

        $p["active_fort_modifier"] = null;
        $p["enabled"] = true;
        $p["last_modified"] = 0;
        $p["latitude"] = $lat;
        $p["longitude"] = $lon;
        $p["lure_expiration"] = null;
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

    $datas = "";

    if ($swLat == 0) {
        $datas = $db->query("select t1.fort_id, t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.prestige, t1.guard_pokemon_id from(select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id")->fetchAll();
    } elseif ($tstamp > 0) {
        $datas = $db->query("select t1.fort_id, t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.prestige, t1.guard_pokemon_id from(select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id where t3.lat > " . $swLat . " and t3.lon > " . $swLng . " and t3.lat < " . $neLat . " and t3.lon < " . $neLng)->fetchAll();
    } elseif ($oSwLat != 0) {
        $datas = $db->query("select t1.fort_id, t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.prestige, t1.guard_pokemon_id from(select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id where t3.lat > " . $swLat . " and t3.lon > " . $swLng . " and t3.lat < " . $neLat . " and t3.lon < " . $neLng)->fetchAll();
    } else {
        $datas = $db->query("select t1.fort_id, t3.external_id, t3.lat, t3.lon, t1.last_modified, t1.team, t1.prestige, t1.guard_pokemon_id from(select fort_id, MAX(last_modified) AS MaxLastModified from fort_sightings group by fort_id) t2 left join fort_sightings t1 on t2.fort_id = t1.fort_id and t2.MaxLastModified = t1.last_modified left join forts t3 on t1.fort_id = t3.id where t3.lat > " . $swLat . " and t3.lon > " . $swLng . " and t3.lat < " . $neLat . " and t3.lon < " . $neLng)->fetchAll();
    }

    $i = 0;

    $gyms = array();

    /* fetch associative array */
    foreach ($datas as $row) {
        $p = array();

        $lat = floatval($row["lat"]);
        $lon = floatval($row["lon"]);
        $gpid = intval($row["guard_pokemon_id"]);
        $gp = intval($row["prestige"]);
        $lm = intval($row["last_modified"] * 1000);
        $ti = isset($row["team"]) ? intval($row["team"]) : null;

        $p2 = array();

        $p2["enabled"] = true;
        $p2["guard_pokemon_id"] = $gpid;
        $p2["gym_id"] = $row["external_id"];
        $p2["gym_points"] = $gp;
        $p2["last_modified"] = $lm;
        $p2["latitude"] = $lat;
        $p2["longitude"] = $lon;
        $p2["name"] = null;
        $p2["team_id"] = $ti;

        $p[$row["external_id"]] = $p2;

        $gyms[] = $p2;

        unset($datas[$i]);

        $i++;
    }

    return $gyms;
}