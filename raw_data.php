<?php
$now = new DateTime();

$d = array();
$d["gyms"]          = "";
$d["timestamp"]     = $now->getTimestamp();
$d["lastgyms"]      = true;
$d["lastpokemon"]   = true;
$d["lastpokestops"] = true;
$d["lastslocs"]     = true;

$swLat         = isset($_GET['swLat'])         ? $_GET['swLat']           : 0;
$neLng         = isset($_GET['neLng'])         ? $_GET['neLng']           : 0;
$swLng         = isset($_GET['swLng'])         ? $_GET['swLng']           : 0;
$neLat         = isset($_GET['neLat'])         ? $_GET['neLat']           : 0;
$oSwLat        = isset($_GET['oSwLat'])        ? $_GET['oSwLat']          : 0;
$oSwLng        = isset($_GET['oSwLng'])        ? $_GET['oSwLng']          : 0;
$oNeLat        = isset($_GET['oNeLat'])        ? $_GET['oNeLat']          : 0;
$oNeLng        = isset($_GET['oNeLng'])        ? $_GET['oNeLng']          : 0;
$lastpokestops = isset($_GET['lastgyms'])      ? $_GET['lastgyms']        : false;
$lastgyms      = isset($_GET['lastpokestops']) ? $_GET['lastpokestops']   : false;
$lastslocs     = isset($_GET['lastslocs'])     ? $_GET['lastslocs']       : false;
$lastspawns    = isset($_GET['lastspawns'])    ? $_GET['lastspawns']      : false;
$lastpokemon   = isset($_GET['lastpokemon'])   ? $_GET['lastpokemon']     : true;

$d["oSwLat"]     = $swLat;
$d["oSwLng"]     = $swLng;
$d["oNeLat"]     = $neLat;
$d["oNeLng"]     = $neLng;

if (isset($_GET['gyms']))        $d["lastgyms"]      = ($_GET['gyms']         == "true");
if (isset($_GET['pokestops']))   $d["lastpokestops"] = ($_GET['pokestops']    == "true");
if (isset($_GET['pokemon']))     $d["lastpokemon"]   = ($_GET['pokemon']      == "true");
if (isset($_GET['scanned']))     $d["lastslocs"]     = ($_GET['scanned']      == "true");
if (isset($_GET['spawnpoints'])) $d["lastspawns"]    = !($_GET['spawnpoints'] == "false");

$newarea = false;

if (($oSwLng < $swLng) && ($oSwLat < $swLat) && ($oNeLat > $neLat) && ($oNeLng > $neLng)) {
    $newarea = false;
} elseif (($oSwLat != $swLat) && ($oSwLng != $swLng) && ($oNeLat != $neLat) && ($oNeLng != $neLng)) {
    $newarea = true;
} else {
    $newarea = false;
}

$ids   = array();
$eids  = array();
$reids = array();

if (isset($_GET['pokemon'])) {
    if ($_GET['pokemon'] == "true") {
        if ($lastpokemon != 'true') {
            $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng);
        } else {
            $timestamp = 0;

            if (isset($_GET['timestamp'])) {
                $timestamp = $_GET['timestamp'];
                $timestamp = $timestamp - 10;
                $timestamp = date("Y-m-d H:i:s",$timestamp);
            }

            if ($newarea) {
                $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["pokemons"] = get_active($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }

        if (isset($_GET['eids'])) {
            $ids = explode(",", $_GET['eids']);

            foreach($d['pokemons'] as $elementKey => $element) {
                foreach($element as $valueKey => $value) {
                    if($valueKey == 'pokemon_id'){
                        if (in_array($value, $ids)) {
                            //delete this particular object from the $array
                            unset($d['pokemons'][$elementKey]);
                        }
                    }
                }
            }
        }
    }
}

$jaysson = json_encode($d);
echo $jaysson;

function get_active($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
{
    require 'config.php';
    $datas = "";

    if ($swLat == 0) {

        $datas = $database->select("sightings",[
            "expire_timestamp",
            "lat",
            "lon",
            "pokemon_id",
            "atk_iv",
            "def_iv",
            "sta_iv",
            "move_1",
            "move_2",
            "last_updated",
            "encounter_id",
            "spawn_id"
        ],[
            "expire_timestamp[>]" => time()
        ]);

    } elseif ($tstamp > 0) {

        $datas = $database->select("sightings",[
            "expire_timestamp",
            "lat",
            "lon",
            "pokemon_id",
            "atk_iv",
            "def_iv",
            "sta_iv",
            "move_1",
            "move_2",
            "last_updated",
            "encounter_id",
            "spawn_id"
        ],[
            "expire_timestamp[>]" => time(),
            "last_updated[>]" => $tstamp,
            "lat[>]" => $swLat,
            "lon[>]" => $swLng,
            "lat[<]" => $neLat,
            "lon[<]" => $neLng
        ]);

    } elseif ($oSwLat != 0) {

        $datas = $database->select("sightings",[
            "expire_timestamp",
            "lat",
            "lon",
            "pokemon_id",
            "atk_iv",
            "def_iv",
            "sta_iv",
            "move_1",
            "move_2",
            "last_updated",
            "encounter_id",
            "spawn_id"
        ],[
            "expire_timestamp[>]" => time(),
            "lat[>]" => $swLat,
            "lon[>]" => $swLng,
            "lat[<]" => $neLat,
            "lon[<]" => $neLng
        ]);

    } else {

        $datas = $database->select("sightings",[
            "expire_timestamp",
            "lat",
            "lon",
            "pokemon_id",
            "atk_iv",
            "def_iv",
            "sta_iv",
            "move_1",
            "move_2",
            "last_updated",
            "encounter_id",
            "spawn_id"
        ],[
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
        $lat    = floatval($row["lat"]);
        $lon    = floatval($row["lon"]);
        $pokeid = intval($row["pokemon_id"]);

        $atk         = isset($row["atk_iv"])         ? intval($row["atk_iv"])        : null;
        $def         = isset($row["def_iv"])         ? intval($row["def_iv"])        : null;
        $sta         = isset($row["sta_iv"])         ? intval($row["sta_iv"])        : null;
        $mv1         = isset($row["move_1"])         ? intval($row["move_1"])        : null;
        $mv2         = isset($row["move_2"])         ? intval($row["move_2"])        : null;

        $p["last_updated"]          = $row["last_updated"]; //done
        $p["disappear_time"]        = $dissapear; //done
        $p["encounter_id"]          = $row["encounter_id"]; //done
        $p["individual_attack"]     = $atk; //done
        $p["individual_defense"]    = $def; //done
        $p["individual_stamina"]    = $sta; //done
        $p["latitude"]              = $lat; //done
        $p["longitude"]             = $lon; //done
        $p["move_1"]                = $mv1; //done
        $p["move_2"]                = $mv2;
        $p["pokemon_id"]            = $pokeid;
        $p["pokemon_name"]          = $data[$pokeid]['name'];
        $p["pokemon_rarity"]        = $data[$pokeid]['rarity'];

        $p2                         = array();
        $p2["color"]                = "#8a8a59";
        $p2["type"]                 = "#Normal";

        $p["pokemon_types"]         = $p2;
        $p["spawnpoint_id"]         = $row["spawn_id"];

        $pokemons[]                 = $p;

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