<?php
include('config/config.php');
global $map, $fork;
header('Content-Type: application/json');
// init map
if (strtolower($map) === "rdm") {
    $scanner = new \Scanner\RDM();
} elseif (strtolower($map) === "golbat") {
    $scanner = new \Scanner\Golbat();
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
