<?php
include('config/config.php');

header('Content-Type: application/json');
if ($motdTitle && $motdContent) {
    echo(json_encode(array('title' => $motdTitle, 'content' => $motdContent)));
} else {
    header("HTTP/1.0 404 Not Found");
    echo(json_encode(array("message" => "No MOTD set")));
}
