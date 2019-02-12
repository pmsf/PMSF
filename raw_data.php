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
$lures = !empty($_POST['lures']) ? $_POST['lures'] : false;
$quests = !empty($_POST['quests']) ? $_POST['quests'] : false;
$dustamount = isset($_POST['dustamount']) ? $_POST['dustamount'] : false;
$reloaddustamount = !empty($_POST['reloaddustamount']) ? $_POST['reloaddustamount'] : false;
$newportals = !empty($_POST['newportals']) ? $_POST['newportals'] : 0;
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
$lastnests = !empty($_POST['lastnests']) ? $_POST['lastnests'] : false;
$lastcommunities = !empty($_POST['lastcommunities']) ? $_POST['lastcommunities'] : false;
$lastportals = !empty($_POST['lastportals']) ? $_POST['lastportals'] : false;
$lastpois = !empty($_POST['lastpois']) ? $_POST['lastpois'] : false;
$exEligible = !empty($_POST['exEligible']) ? $_POST['exEligible'] : false;
$d["lastpokestops"] = !empty($_POST['pokestops']) ? $_POST['pokestops'] : false;
$d["lastgyms"] = !empty($_POST['gyms']) ? $_POST['gyms'] : false;
$d["lastslocs"] = !empty($_POST['scanned']) ? $_POST['scanned'] : false;
$d["lastspawns"] = !empty($_POST['spawnpoints']) ? $_POST['spawnpoints'] : false;
$d["lastpokemon"] = !empty($_POST['pokemon']) ? $_POST['pokemon'] : false;
$d["lastnests"] = !empty($_POST['nests']) ? $_POST['nests'] : false;
$d["lastcommunities"] = !empty($_POST['communities']) ? $_POST['communities'] : false;
$d["lastportals"] = !empty($_POST['portals']) ? $_POST['portals'] : false;
$d["lastpois"] = !empty($_POST['pois']) ? $_POST['pois'] : false;
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
    if (strtolower($fork) === "default") {
        $scanner = new \Scanner\Monocle();
    } elseif (strtolower($fork) === "mad") {
        $scanner = new \Scanner\Monocle_MAD();
    } else {
        $scanner = new \Scanner\Monocle_PMSF();
    }
} elseif (strtolower($map) === "rdm") {
    if (strtolower($fork) === "public") {
        $scanner = new \Scanner\RDM();
    } else {
        $scanner = new \Scanner\RDM_beta();
    }
} elseif (strtolower($map) === "rocketmap") {
    if (strtolower($fork) === "mad") {
        $scanner = new \Scanner\RocketMap_MAD();
    }
}

$manual = new \Manual\Manual();

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
$qpeids = array();
$qpreids = array();
$qieids = array();
$qireids = array();
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

global $noPokestops, $map, $fork;
if (!$noPokestops) {
    if ($d["lastpokestops"] == "true") {
        $qpeids = !empty($_POST['qpeids']) ? explode(",", $_POST['qpeids']) : array();
        $qieids = !empty($_POST['qieids']) ? explode(",", $_POST['qieids']) : array();
        if ($lastpokestops != "true") {
            $d["pokestops"] = $scanner->get_stops($qpeids, $qieids, $swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $lures, $quests, $dustamount);
        } else {
            if ($newarea) {
                $d["pokestops"] = $scanner->get_stops($qpeids, $qieids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $quests, $dustamount);
            } else {
                $d["pokestops"] = $scanner->get_stops($qpeids, $qieids, $swLat, $swLng, $neLat, $neLng, $timestamp, 0, 0, 0, 0, $lures, $quests, $dustamount);
            }
	}
        if ((strtolower($map) === "rdm" && strtolower($fork) === "beta") || (strtolower($map) === "monocle" && strtolower($fork) === "mad") || (strtolower($map) === "rocketmap" && strtolower($fork) === "mad")) {
            if ($reloaddustamount == "true") {
                $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($qpreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $quests, $dustamount, $reloaddustamount));
            }
	    if (!empty($_POST['qpreids'])) {
                $qpreids = !empty($_POST['qpreids']) ? array_unique(explode(",", $_POST['qpreids'])) : array();

                $qpreidsDiff = array_diff($qpreids, $qpeids);
                if (count($qpreidsDiff)) {
                    $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($qpreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $quests, $dustamount, $reloaddustamount));
	        }

                $d["qpreids"] = $qpreids;
            }
            if (!empty($_POST['qireids'])) {
                $qireids = !empty($_POST['qireids']) ? array_unique(explode(",", $_POST['qireids'])) : array();

                $qireidsDiff = array_diff($qireids, $qieids);
	        if (count($qireidsDiff)) {
                    $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($qpreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $quests, $dustamount, $reloaddustamount));
	        }

	        $d["qireids"] = $qireids;
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

global $noNests;
if (!$noNests ) {
    if ($d["lastnests"] == "true") {
        if ($lastnests != "true") {
            $d["nests"] = $manual->get_nests($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["nests"] = $manual->get_nests($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["nests"] = $manual->get_nests($swLat, $swLng, $neLat, $neLng, time());
            }
        }
    }
}

global $noCommunity;
if (!$noCommunity ) {
    if ($d["lastcommunities"] == "true") {
        if ($lastcommunities != "true") {
            $d["communities"] = $manual->get_communities($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["communities"] = $manual->get_communities($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["communities"] = $manual->get_communities($swLat, $swLng, $neLat, $neLng, time());
            }
        }
    }
}

global $noPortals;
if (!$noPortals ) {
    if ($d["lastportals"] == "true") {
        if ($lastportals != "true") {
            $d["portals"] = $manual->get_portals($swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $newportals);
        } else {
            if ($newarea) {
                $d["portals"] = $manual->get_portals($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $newportals);
            } else {
                $d["portals"] = $manual->get_portals($swLat, $swLng, $neLat, $neLng, time(), 0, 0, 0, 0,$newportals);
            }
        }
    }
}

global $noPoi;
if (!$noPoi ) {
    if ($d["lastpois"] == "true") {
        if ($lastpois != "true") {
            $d["pois"] = $manual->get_poi($swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0);
        } else {
            if ($newarea) {
                $d["pois"] = $manual->get_poi($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["pois"] = $manual->get_poi($swLat, $swLng, $neLat, $neLng, time(), 0, 0, 0, 0);
            }
        }
    }
}

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

$d['login'] = $noNativeLogin === true && $noDiscordLogin === true ? false : true;
if ($d['login'] === true) {
    if (isset($_SESSION['user']->login_timestamp)) {
        $info = $manualdb->query(
        "SELECT login_timestamp FROM users WHERE email = :email", [
            ":email" => $_SESSION['user']->email
        ]
        )->fetch();

        $d["expire_timestamp"] = (int)$_SESSION['user']->expire_timestamp;
    } else {
        $d["expire_timestamp"] = 0;
    }
}

$jaysson = json_encode($d);
echo $jaysson;
