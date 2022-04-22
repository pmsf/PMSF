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
$rocket = !empty($_POST['rocket']) ? $_POST['rocket'] : false;
$raids = !empty($_POST['raids']) ? $_POST['raids'] : false;
$quests = !empty($_POST['quests']) ? $_POST['quests'] : false;
$dustamount = isset($_POST['dustamount']) ? $_POST['dustamount'] : false;
$nestavg = isset($_POST['nestavg']) ? $_POST['nestavg'] : false;
$reloaddustamount = !empty($_POST['reloaddustamount']) ? $_POST['reloaddustamount'] : false;
$newportals = !empty($_POST['newportals']) ? $_POST['newportals'] : 0;
$minIv = isset($_POST['minIV']) ? floatval($_POST['minIV']) : false;
$prevMinIv = !empty($_POST['prevMinIV']) ? $_POST['prevMinIV'] : false;
$minLevel = isset($_POST['minLevel']) ? intval($_POST['minLevel']) : false;
$prevMinLevel = !empty($_POST['prevMinLevel']) ? $_POST['prevMinLevel'] : false;
$exMinIv = !empty($_POST['exMinIV']) ? $_POST['exMinIV'] : '';
$bigKarp = !empty($_POST['bigKarp']) ? $_POST['bigKarp'] : false;
$tinyRat = !empty($_POST['tinyRat']) ? $_POST['tinyRat'] : false;
$seenType = !empty($_POST['seenType']) ? $_POST['seenType'] : 0;
$despawnTimeType = !empty($_POST['despawnTimeType']) ? $_POST['despawnTimeType'] : 0;
$pokemonGender = !empty($_POST['pokemonGender']) ? $_POST['pokemonGender'] : 0;
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
$d["lastscanlocations"] = !empty($_POST['scanlocations']) ? $_POST['scanlocations'] : false;
$d["lastpokestops"] = !empty($_POST['pokestops']) ? $_POST['pokestops'] : false;
$d["lastgyms"] = !empty($_POST['gyms']) ? $_POST['gyms'] : false;
$d["lastslocs"] = !empty($_POST['scanned']) ? $_POST['scanned'] : false;
$d["lastspawns"] = !empty($_POST['spawnpoints']) ? $_POST['spawnpoints'] : false;
$d["lastpokemon"] = !empty($_POST['pokemon']) ? $_POST['pokemon'] : false;
$d["lastnests"] = !empty($_POST['nests']) ? $_POST['nests'] : false;
$d["lastcommunities"] = !empty($_POST['communities']) ? $_POST['communities'] : false;
$d["lastportals"] = !empty($_POST['portals']) ? $_POST['portals'] : false;
$d["lastpois"] = !empty($_POST['pois']) ? $_POST['pois'] : false;

$loadOverviewStats = !empty($_POST['loadOverviewStats']) ? $_POST['loadOverviewStats'] : false;
$loadPokemonStats = !empty($_POST['loadPokemonStats']) ? $_POST['loadPokemonStats'] : false;
$loadRewardStats = !empty($_POST['loadRewardStats']) ? $_POST['loadRewardStats'] : false;
$loadShinyStats = !empty($_POST['loadShinyStats']) ? $_POST['loadShinyStats'] : false;

if ($minIv < $prevMinIv || $minLevel < $prevMinLevel) {
    $lastpokemon = false;
}
$enc_id = !empty($_POST['encId']) ? $_POST['encId'] : null;

$timestamp = !empty($_POST['timestamp']) ? $_POST['timestamp'] : 0;

$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
if (empty($swLat) || empty($swLng) || empty($neLat) || empty($neLng) || preg_match("/curl|libcurl/", $useragent)) {
    http_response_code(403);
    die();
}
if ($maxLatLng > 0 && ((($neLat - $swLat) > $maxLatLng) || (($neLng - $swLng) > $maxLatLng))) {
    http_response_code(413);
    die();
}
$validity = validateToken($_POST['token']);
if (!empty($validity)) {
    switch ($validity) {
        case 'invalid':
            http_response_code(401);
            break;
        case 'no-id':
            http_response_code(404);
            break;
    }
}

$debug['0_after_auth'] = microtime(true) - $timing['start'];

// init map
if (strtolower($map) === "rdm") {
    if (strtolower($fork) === "default" || strtolower($fork) === "beta") {
        $scanner = new \Scanner\RDM();
        $stats = new \Stats\RDM();
    }
} elseif (strtolower($map) === "rocketmap") {
    if (strtolower($fork) === "mad") {
        $scanner = new \Scanner\RocketMap_MAD();
        $stats = new \Stats\RocketMap_MAD();
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
$qeeids = array();
$qereids = array();
$qceids = array();
$qcreids = array();
$geids = array();
$greids = array();
$rbeids = array();
$rbreids = array();
$reeids = array();
$rereids = array();

$debug['1_before_functions'] = microtime(true) - $timing['start'];

global $noPokemon;
if (!$noPokemon) {
    if ($d["lastpokemon"] == "true") {
        $eids = !empty($_POST['eids']) ? explode(",", $_POST['eids']) : array();
        if ($lastpokemon != 'true') {
            $d["pokemons"] = $scanner->get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $seenType, $despawnTimeType, $pokemonGender, $swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $enc_id);
        } else {
            if ($newarea) {
                $d["pokemons"] = $scanner->get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $seenType, $despawnTimeType, $pokemonGender, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $enc_id);
            } else {
                $d["pokemons"] = $scanner->get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $seenType, $despawnTimeType, $pokemonGender, $swLat, $swLng, $neLat, $neLng, $timestamp, 0, 0, 0, 0, $enc_id);
            }
        }
        $d["preMinIV"] = $minIv;
        $d["preMinLevel"] = $minLevel;
        if (!empty($_POST['reids'])) {
            $reids = !empty($_POST['reids']) ? array_unique(explode(",", $_POST['reids'])) : array();

            $reidsDiff = array_diff($reids, $eids);
            if (count($reidsDiff)) {
                $d["pokemons"] = array_merge($d["pokemons"], $scanner->get_active_by_id($reidsDiff, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $seenType, $despawnTimeType, $pokemonGender, $swLat, $swLng, $neLat, $neLng));
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
        $qeeids = !empty($_POST['qeeids']) ? explode(",", $_POST['qeeids']) : array();
        $qceids = !empty($_POST['qceids']) ? explode(",", $_POST['qceids']) : array();
        $geids = !empty($_POST['geids']) ? explode(",", $_POST['geids']) : array();
        if ($lastpokestops != "true") {
            $d["pokestops"] = $scanner->get_stops($geids, $qpeids, $qeeids, $qceids, $qieids, $swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $lures, $rocket, $quests, $dustamount);
        } else {
            if ($newarea) {
                $d["pokestops"] = $scanner->get_stops($geids, $qpeids, $qeeids, $qceids, $qieids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $rocket, $quests, $dustamount);
            } else {
                $d["pokestops"] = $scanner->get_stops($geids, $qpeids, $qeeids, $qceids, $qieids, $swLat, $swLng, $neLat, $neLng, $timestamp, 0, 0, 0, 0, $lures, $rocket, $quests, $dustamount);
            }
        }
        if ((strtolower($map) === "rdm") || (strtolower($map) === "rocketmap" && strtolower($fork) === "mad")) {
            if ($reloaddustamount == "true") {
                $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($greids, $qpreids, $qereids, $qcreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $rocket, $quests, $dustamount, $reloaddustamount));
            }
            if (!empty($_POST['qpreids'])) {
                $qpreids = !empty($_POST['qpreids']) ? array_unique(explode(",", $_POST['qpreids'])) : array();

                $qpreidsDiff = array_diff($qpreids, $qpeids);
                if (count($qpreidsDiff)) {
                    $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($greids, $qpreids, $qereids, $qcreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $rocket, $quests, $dustamount, $reloaddustamount));
                }

                $d["qpreids"] = $qpreids;
            }
            if (!empty($_POST['qereids'])) {
                $qereids = !empty($_POST['qereids']) ? array_unique(explode(",", $_POST['qereids'])) : array();

                $qereidsDiff = array_diff($qereids, $qeeids);
                if (count($qereidsDiff)) {
                    $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($greids, $qpreids, $qereids, $qcreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $rocket, $quests, $dustamount, $reloaddustamount));
                }

                $d["qereids"] = $qereids;
            }
            if (!empty($_POST['qcreids'])) {
                $qcreids = !empty($_POST['qcreids']) ? array_unique(explode(",", $_POST['qcreids'])) : array();

                $qcreidsDiff = array_diff($qcreids, $qceids);
                if (count($qcreidsDiff)) {
                    $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($greids, $qpreids, $qereids, $qcreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $rocket, $quests, $dustamount, $reloaddustamount));
                }

                $d["qcreids"] = $qcreids;
            }
            if (!empty($_POST['qireids'])) {
                $qireids = !empty($_POST['qireids']) ? array_unique(explode(",", $_POST['qireids'])) : array();

                $qireidsDiff = array_diff($qireids, $qieids);
                if (count($qireidsDiff)) {
                    $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($greids, $qpreids, $qereids, $qcreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $rocket, $quests, $dustamount, $reloaddustamount));
                }

                $d["qireids"] = $qireids;
            }
            if (!empty($_POST['greids'])) {
                $greids = !empty($_POST['greids']) ? array_unique(explode(",", $_POST['greids'])) : array();

                $greidsDiff = array_diff($greids, $geids);
                if (count($greidsDiff)) {
                    $d["pokestops"] = array_merge($d["pokestops"], $scanner->get_stops_quest($greids, $qpreids, $qereids, $qcreids, $qireids, $swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $lures, $rocket, $quests, $dustamount, $reloaddustamount));
                }

                $d["greids"] = $greids;
            }
        }
    }
}
$debug['3_after_pokestops'] = microtime(true) - $timing['start'];

global $noGyms, $noRaids;
if (!$noGyms || !$noRaids) {
    if ($d["lastgyms"] == "true" || $raids == "true") {
        $gyms = $d["lastgyms"];
        $rbeids = !empty($_POST['rbeids']) ? explode(",", $_POST['rbeids']) : array();
        $reeids = !empty($_POST['reeids']) ? explode(",", $_POST['reeids']) : array();
        if ($lastgyms != "true") {
            $d["gyms"] = $scanner->get_gyms($rbeids, $reeids, $swLat, $swLng, $neLat, $neLng, $exEligible, 0, 0, 0, 0, 0, $raids, $gyms);
        } else {
            if ($newarea) {
                $d["gyms"] = $scanner->get_gyms($rbeids, $reeids, $swLat, $swLng, $neLat, $neLng, $exEligible, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $raids, $gyms);
            } else {
                $d["gyms"] = $scanner->get_gyms($rbeids, $reeids, $swLat, $swLng, $neLat, $neLng, $exEligible, $timestamp, 0, 0, 0, 0, $raids, $gyms);
            }
        }
        if (!empty($_POST['rbreids'])) {
            $rbreids = !empty($_POST['rbreids']) ? array_unique(explode(",", $_POST['rbreids'])) : array();
            $rbreidsDiff = array_diff($rbreids, $rbeids);
            if (count($rbreidsDiff)) {
                $d["gyms"] = array_merge($d["gyms"], $scanner->get_gyms($rbeids, $reeids, $swLat, $swLng, $neLat, $neLng, $exEligible, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $raids, $gyms));
            }
            $d["rbreids"] = $rbreids;
        }
        if (!empty($_POST['rereids'])) {
            $rereids = !empty($_POST['rereids']) ? array_unique(explode(",", $_POST['rereids'])) : array();
            $rereidsDiff = array_diff($rereids, $reeids);
            if (count($rereidsDiff)) {
                $d["gyms"] = array_merge($d["gyms"], $scanner->get_gyms($rbeids, $reeids, $swLat, $swLng, $neLat, $neLng, $exEligible, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $raids, $gyms));
            }
            $d["rereids"] = $rereids;
        }
    }
}
$debug['4_after_gyms'] = microtime(true) - $timing['start'];

global $noNests;
if (!$noNests) {
    if ($d["lastnests"] == "true") {
        if ($lastnests != "true") {
            $d["nests"] = $manual->get_nests($swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $nestavg);
        } else {
            if ($newarea) {
                $d["nests"] = $manual->get_nests($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $nestavg);
            } else {
                $d["nests"] = $manual->get_nests($swLat, $swLng, $neLat, $neLng, time(), 0, 0, 0, 0, $nestavg);
            }
        }
    }
}
$debug['5_after_nest'] = microtime(true) - $timing['start'];

global $noCommunity;
if (!$noCommunity) {
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
$debug['6_after_community'] = microtime(true) - $timing['start'];

global $noPortals;
if (!$noPortals) {
    if ($d["lastportals"] == "true") {
        if ($lastportals != "true") {
            $d["portals"] = $manual->get_portals($swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $newportals);
        } else {
            if ($newarea) {
                $d["portals"] = $manual->get_portals($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $newportals);
            } else {
                $d["portals"] = $manual->get_portals($swLat, $swLng, $neLat, $neLng, time(), 0, 0, 0, 0, $newportals);
            }
        }
    }
}
$debug['7_after_portals'] = microtime(true) - $timing['start'];

global $noPoi;
if (!$noPoi) {
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
$debug['8_after_poi'] = microtime(true) - $timing['start'];

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
$debug['9_after_spawnpoints'] = microtime(true) - $timing['start'];

global $noLiveScanLocation;
if (!$noLiveScanLocation) {
    if ($d["lastscanlocations"] == "true") {
        if ($newarea) {
            $d["scanlocations"] = $scanner->get_scanlocation($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
        } else {
            $d["scanlocations"] = $scanner->get_scanlocation($swLat, $swLng, $neLat, $neLng, $timestamp);
        }
    }
}
$debug['10_after_devices'] = microtime(true) - $timing['start'];

if ($loadOverviewStats == "true") {
  $d["overviewStats"] = $stats->get_overview_stats();
  $d["teamStats"] = $stats->get_team_stats();
  $d["pokestopStats"] = $stats->get_pokestop_stats();
  $d["spawnpointStats"] = $stats->get_spawnpoint_stats();
}
if ($loadPokemonStats == "true") {
    $d["pokemonStats"] = $stats->get_pokemon_stats();
}
if ($loadRewardStats == "true") {
    $d["rewardStats"] = $stats->get_reward_stats();
}
if ($loadShinyStats == "true") {
    $d["shinyStats"] = $stats->get_shiny_stats();
}

$d['token'] = refreshCsrfToken();
$debug['11_end'] = microtime(true) - $timing['start'];

if ($enableDebug == true) {
    foreach ($debug as $k => $v) {
        header("X-Debug-Time-" . $k . ": " . $v);
    }
}

$jaysson = json_encode($d);
echo $jaysson;
