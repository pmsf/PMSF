<?php
include('config/config.php');
global $map, $fork;
header('Content-Type: application/json');
// init map
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
if (isset($_POST['cell_id'])) {
    $return_weather = $scanner->get_weather_by_cell_id($_POST['cell_id']);
} else {
    // $timestamp = (isset($_POST['ts']) ? $_POST['ts'] : null);
    $return_weather  = $scanner->get_weather();
}
$d['weather'] = $return_weather;
$d['timestamp'] = time();
$jaysson = json_encode($d);
echo $jaysson;
