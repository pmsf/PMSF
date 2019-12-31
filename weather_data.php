<?php
include('config/config.php');
global $map, $fork;
header('Content-Type: application/json');
// init map
$swLat = !empty($_POST['swLat']) ? $_POST['swLat'] : 0;
$neLng = !empty($_POST['neLng']) ? $_POST['neLng'] : 0;
$swLng = !empty($_POST['swLng']) ? $_POST['swLng'] : 0;
$neLat = !empty($_POST['neLat']) ? $_POST['neLat'] : 0;
$oSwLat = !empty($_POST['oSwLat']) ? $_POST['oSwLat'] : 0;
$oSwLng = !empty($_POST['oSwLng']) ? $_POST['oSwLng'] : 0;
$oNeLat = !empty($_POST['oNeLat']) ? $_POST['oNeLat'] : 0;
$oNeLng = !empty($_POST['oNeLng']) ? $_POST['oNeLng'] : 0;

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
if ((! $noDiscordLogin || ! $noNativeLogin) && !empty($_SESSION['user']->id)) {
    $info = $manualdb->query("SELECT expire_timestamp FROM users WHERE id = :id", [":id" => $_SESSION['user']->id])->fetch();
        if ($info['expire_timestamp'] !== $_SESSION['user']->expire_timestamp) {
        http_response_code(400);
        die();
    }
}

if (strtolower($map) === "monocle") {
    if (strtolower($fork) === "default") {
        $scanner = new \Scanner\Monocle();
    } else {
        $scanner = new \Scanner\Monocle_PMSF();
    }
} elseif (strtolower($map) === "rdm") {
    if (strtolower($fork) === "beta") {
        $scanner = new \Scanner\RDM_beta();
    }
} elseif (strtolower($map) === "rocketmap") {
    if (strtolower($fork) === "mad") {
        $scanner = new \Scanner\RocketMap_MAD();
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

if (isset($_POST['cell_id'])) {
    $return_weather = $scanner->get_weather_by_cell_id($_POST['cell_id']);
} else {
    if ($newarea) {
        $return_weather  = $scanner->get_weather($swLat, $swLng, $neLat, $neLng, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
    } else {
        $return_weather  = $scanner->get_weather($swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0);
    }
}
$d['weather'] = $return_weather;
$d['timestamp'] = time();
$jaysson = json_encode($d);
echo $jaysson;
