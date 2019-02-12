<?php
include('config/config.php');

// set content type
header('Content-Type: application/json');

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
