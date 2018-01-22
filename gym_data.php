<?php
include('config/config.php');

// set content type
header('Content-Type: application/json');

// init map
if (strtolower($map) == "monocle") {
    if (strtolower($fork) == "asner") {
        $scanner = new \Scanner\Monocle_Asner();
    } elseif (strtolower($fork) == "alternate") {
        $scanner = new \Scanner\Monocle_Alternate();
    } else {
        $scanner = new \Scanner\Monocle();
    }
} elseif (strtolower($map) == "rm") {
    if (strtolower($fork) == "sloppy") {
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


$id = $_POST['id'];

$p = $scanner->get_gym($id);

$p['token'] = refreshCsrfToken();

echo json_encode($p);
