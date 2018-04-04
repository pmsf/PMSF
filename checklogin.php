<?php
include('config/config.php');
header('Content-Type: application/json');
$info = array();

if ($enableLogin === true) {
    $info['enableLogin'] = true;
    if (isset($_SESSION['user'])) {
        $info['isLoggedIn'] = true;
        $info['current_timestamp'] = time();
        $info['user'] = $_SESSION['user'];
    } else {
        $info['isLoggedIn'] = false;
    }
} else {
    $info['enableLogin'] = false;
}

print json_encode($info);
