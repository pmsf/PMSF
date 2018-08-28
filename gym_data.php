<?php
include('config/config.php');

// set content type
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

if (empty($_POST['id'])) {
    http_response_code(400);
    die();
}
if (!validateToken($_POST['token'])) {
    http_response_code(400);
    die();
}

// init novadb
global $novabotDb;
if ($novabotDb !== false) {
    $novabotScanner = new \NovaBot\NovaBot();
} else {
    $novabotScanner = false;
}

$id = $_POST['id'];

$gyms = array($scanner->get_gym($id));
if ($novabotScanner !== false) {
    $novabotScanner->addLobbies($gyms);
}
$p = $gyms[0];

if ($novabotScanner !== false) {
    if (!is_null($p['lobby_id'])) {
        $p['lobby'] = $novabotScanner->getLobbyInfo($p['lobby_id']);
    } else {
        $p['lobby'] = null;
    }
}
$p['token'] = refreshCsrfToken();

echo json_encode($p);
