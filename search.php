<?php
include('config/config.php');
global $map, $fork;

if ($noSearch === true) {
    http_response_code(401);
    die();
}
$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
if (preg_match("/curl|libcurl/", $useragent)) {
    http_response_code(400);
    die();
}
$data = array();
$term = ! empty($_POST['term']) ? $_POST['term'] : '';
$action = ! empty($_POST['action']) ? $_POST['action'] : '';
$lat = ! empty($_POST['lat']) ? $_POST['lat'] : '';
$lon = ! empty($_POST['lon']) ? $_POST['lon'] : '';
$quests_with_ar = (!empty($_POST['quests_with_ar']) && $_POST['quests_with_ar'] == "false") ? false : true;

$dbname = '';
if (strtolower($map) === "rdm") {
    if ($action === "pokestops") {
        $dbname = "pokestop";
    } elseif ($action === "forts") {
        $dbname = "gym";
    }
    $search = new \Search\RDM();
} elseif (strtolower($map) === "golbat") {
    if ($action === "pokestops") {
        $dbname = "pokestop";
    } elseif ($action === "forts") {
        $dbname = "gym";
    }
    $search = new \Search\Golbat();
} elseif (strtolower($map) === "rocketmap") {
    if ($action === "pokestops") {
        $dbname = "pokestop";
    } elseif ($action === "forts") {
        $dbname = "gym";
    }
    $search = new \Search\RocketMap_MAD();
}
if ($action === "pokemon") {
    $data["pokemon"] = $search->search_pokemon($lat, $lon, $term);
}
if ($action === "reward") {
    $data["reward"] = $search->search_reward($lat, $lon, $term, $quests_with_ar);
}
if ($action === "nests") {
    $data["nests"] = $search->search_nests($lat, $lon, $term);
}
if ($action === "portals") {
    $data["portals"] = $search->search_portals($lat, $lon, $term);
}
if ($action === "pokestops") {
    $data["pokestops"] = $search->search($dbname, $lat, $lon, $term);
}
if ($action === "forts") {
    $data["forts"] = $search->search($dbname, $lat, $lon, $term);
}
$jaysson = json_encode($data);
echo $jaysson;
