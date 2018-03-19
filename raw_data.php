<?php
$timing['start'] = microtime(true);
include('config/config.php');
global $map, $fork;

// set content type
header('Content-Type: application/json');

$now = new DateTime();
$now->sub(new DateInterval('PT20S'));

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
$minIv = isset($_POST['minIV']) ? floatval($_POST['minIV']) : false;
$prevMinIv = !empty($_POST['prevMinIV']) ? $_POST['prevMinIV'] : false;
$minLevel = isset($_POST['minLevel']) ? intval($_POST['minLevel']) : false;
$prevMinLevel = !empty($_POST['prevMinLevel']) ? $_POST['prevMinLevel'] : false;
$exMinIv = !empty($_POST['exMinIV']) ? $_POST['exMinIV'] : '';
$bigKarp = !empty($_POST['bigKarp']) ? $_POST['bigKarp'] : false;
$tinyRat = !empty($_POST['tinyRat']) ? $_POST['tinyRat'] : false;
$lastpokemon = !empty($_POST['lastpokemon']) ? $_POST['lastpokemon'] : false;
$lastgyms = !empty($_POST['lastgyms']) ? $_POST['lastgyms'] : false;
$lastpokestops = !empty($_POST['lastpokestops']) ? $_POST['lastpokestops'] : false;
$lastlocs = !empty($_POST['lastslocs']) ? $_POST['lastslocs'] : false;
$lastspawns = !empty($_POST['lastspawns']) ? $_POST['lastspawns'] : false;
$exEligible = !empty($_POST['exEligible']) ? $_POST['exEligible'] : false;
$d["lastpokestops"] = !empty($_POST['pokestops']) ? $_POST['pokestops'] : false;
$d["lastgyms"] = !empty($_POST['gyms']) ? $_POST['gyms'] : false;
$d["lastslocs"] = !empty($_POST['scanned']) ? $_POST['scanned'] : false;
$d["lastspawns"] = !empty($_POST['spawnpoints']) ? $_POST['spawnpoints'] : false;
$d["lastpokemon"] = !empty($_POST['pokemon']) ? $_POST['pokemon'] : false;
if ($minIv < $prevMinIv || $minLevel < $prevMinLevel) {
    $lastpokemon = false;
}
$enc_id = !empty($_POST['encId']) ? $_POST['encId'] : null;

$timestamp = !empty($_POST['timestamp']) ? $_POST['timestamp'] : 0;

$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
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

// init map
if (strtolower($map) === "monocle") {
    if (strtolower($fork) === "asner") {
        $scanner = new \Scanner\Monocle_Asner();
    } elseif (strtolower($fork) === "default") {
        $scanner = new \Scanner\Monocle();
    } else {
        $scanner = new \Scanner\Monocle_Alternate();
    }
} elseif (strtolower($map) === "rm") {
    if (strtolower($fork) === "sloppy") {
        $scanner = new \Scanner\RocketMap_Sloppy();
    } else {
        $scanner = new \Scanner\RocketMap();
    }
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
$debug['1_before_functions'] = microtime(true) - $timing['start'];

global $noPokemon;
if (!$noPokemon) {
    if ($d["lastpokemon"] == "true") {
        $eids = !empty($_POST['eids']) ? explode(",", $_POST['eids']) : array();
        if ($lastpokemon != 'true') {
            $d["pokemons"] = $scanner->get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $enc_id);
        } else {
            if ($newarea) {
                $d["pokemons"] = $scanner->get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $enc_id);
            } else {
                $d["pokemons"] = $scanner->get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng, $timestamp, 0, 0, 0, 0, $enc_id);
            }
        }
        $d["preMinIV"] = $minIv;
        $d["preMinLevel"] = $minLevel;
        if (!empty($_POST['reids'])) {
            $reids = !empty($_POST['reids']) ? array_unique(explode(",", $_POST['reids'])) : array();

            $reidsDiff = array_diff($reids, $eids);
            if (count($reidsDiff)) {
                $d["pokemons"] = array_merge($d["pokemons"], $scanner->get_active_by_id($reidsDiff, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng));
            }

            $d["reids"] = $reids;
        }
    }
}
$debug['2_after_pokemon'] = microtime(true) - $timing['start'];

global $noPokestops;
if (!$noPokestops) {
    if ($d["lastpokestops"] == "true") {
        if ($lastpokestops != "true") {
            $d["pokestops"] = $scanner->get_stops($swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $luredonly);
        } else {
            if ($newarea) {
                $d["pokestops"] = $scanner->get_stops($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $luredonly);
            } else {
                $d["pokestops"] = $scanner->get_stops($swLat, $swLng, $neLat, $neLng, $timestamp, 0, 0, 0, 0, $luredonly);
            }
        }
    }
}
$debug['3_after_pokestops'] = microtime(true) - $timing['start'];

global $noGyms, $noRaids;
if (!$noGyms || !$noRaids) {
    if ($d["lastgyms"] == "true") {
        if ($lastgyms != "true") {
            $d["gyms"] = $scanner->get_gyms($swLat, $swLng, $neLat, $neLng, $exEligible);
        } else {
            if ($newarea) {
                $d["gyms"] = $scanner->get_gyms($swLat, $swLng, $neLat, $neLng, $exEligible, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["gyms"] = $scanner->get_gyms($swLat, $swLng, $neLat, $neLng, $exEligible, $timestamp);
            }
        }
    }
}
$debug['4_after_gyms'] = microtime(true) - $timing['start'];

global $noSpawnPoints;
if (!$noSpawnPoints) {
    if ($d["lastspawns"] == "true") {
        if ($lastspawns != "true") {
            $d["spawnpoints"] = $scanner->get_spawnpoints($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["spawnpoints"] = $scanner->get_spawnpoints($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["spawnpoints"] = $scanner->get_spawnpoints($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }
    }
}
$debug['5_after_spawnpoints'] = microtime(true) - $timing['start'];

global $noScannedLocations;
if (!$noScannedLocations) {
    if ($d["lastslocs"] == "true") {
        if ($lastlocs != "true") {
            $d["scanned"] = $scanner->get_recent($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["scanned"] = $scanner->get_recent($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["scanned"] = $scanner->get_recent($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }
    }
}
$debug['6_after_recent'] = microtime(true) - $timing['start'];

$d['token'] = refreshCsrfToken();
$debug['7_end'] = microtime(true) - $timing['start'];

if ($enableDebug == true) {
    foreach ($debug as $k => $v) {
        header("X-Debug-Time-" . $k . ": " . $v);
    }
}

$jaysson = json_encode($d);
echo $jaysson;
