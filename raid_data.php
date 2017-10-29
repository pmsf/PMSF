<?php
include('config/config.php');
global $map, $fork;

if (empty($raidApiKey)) {
    http_response_code(401);
    die();
}

if (!empty($_POST['key']) && $_POST['key'] == $raidApiKey && !empty($_POST['type'])) {
    if ($_POST['type'] == "config") {
        $data['lat'] = $startingLat;
        $data['lon'] = $startingLng;
        $data['maxLatlng'] = $maxLatLng;
        $data['maxZoomOut'] = $maxZoomOut;
        $data['title'] = $title;

        header('Content-Type: application/json');
        echo json_encode($data);
    } elseif ($_POST['type'] == "data") {
        $swLat = !empty($_POST['swLat']) ? $_POST['swLat'] : 0;
        $neLng = !empty($_POST['neLng']) ? $_POST['neLng'] : 0;
        $swLng = !empty($_POST['swLng']) ? $_POST['swLng'] : 0;
        $neLat = !empty($_POST['neLat']) ? $_POST['neLat'] : 0;

        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (empty($swLat) || empty($swLng) || empty($neLat) || empty($neLng) || preg_match("/curl|libcurl/", $useragent)) {
            http_response_code(400);
            die();
        }
        if ($maxLatLng > 0 && ((($neLat - $swLat) > $maxLatLng) || (($neLng - $swLng) > $maxLatLng))) {
            http_response_code(400);
            die();
        }

        if (strtolower($map) == "monocle") {
            if (strtolower($fork) == "monkey") {
                $scanner = new \Scanner\Monocle_Monkey();
            } else {
                $scanner = new \Scanner\Monocle();
            }
        } elseif (strtolower($map) == "rm") {
            $scanner = new \Scanner\RocketMap();
        }

        $data = $scanner->get_gyms_api($swLat, $swLng, $neLat, $neLng);
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        http_response_code(401);
        die();
    }
} else {
    http_response_code(401);
    die();
}
