<?php
include('config/config.php');
global $map, $fork;
header('Content-Type: application/json');
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
