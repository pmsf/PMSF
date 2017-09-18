<?php
include('config/config.php');
// init map
if ($map == "monocle") {
    if ($fork == "asner") {
        $scanner = new \Scanner\Asner();
    } elseif ($fork == "monkey") {
        $scanner = new \Scanner\Monkey();
    } else {
        $scanner = new \Scanner\Monocle();
    }
} elseif ($map =="rm") {
    if ($fork == "sloppy") {
        $scanner = new \Scanner\Sloppy();
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