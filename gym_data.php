<?php
include('config.php');
/**
 * Created by PhpStorm.
 * User: glenncarremans
 * Date: 10/06/2017
 * Time: 00:45
 */
if (!isset($_GET['id'])) {
    http_response_code(400);
    die();
}

$id = $_GET['id'];

global $db;

$row = $db->get("fort_sightings", ["[>]forts" => ["fort_id" => "id"]], "*", ["forts.external_id" => $id]);

$json_poke = "static/data/pokemon.json";
$json_contents = file_get_contents($json_poke);
$data = json_decode($json_contents, TRUE);

$p = array();

$lat = floatval($row["lat"]);
$lon = floatval($row["lon"]);
$gpid = intval($row["guard_pokemon_id"]);
$gp = intval($row["prestige"]);
$lm = intval($row["last_modified"] * 1000);
$ti = isset($row["team"]) ? intval($row["team"]) : null;

$p["enabled"] = true;
$p["guard_pokemon_id"] = $gpid;
$p["gym_id"] = $row["external_id"];
$p["gym_points"] = $gp;
$p["last_modified"] = $lm;
$p["latitude"] = $lat;
$p["longitude"] = $lon;
$p["name"] = null;
$p["team_id"] = $ti;
$p["guard_pokemon_name"] = $data[$gpid]['name'];

unset($row);

echo json_encode($p);