<?php
include('config/config.php');
global $map, $fork, $db;
$term = !empty($_POST['term']) ? $_POST['term'] : '';
if($db->info()['driver'] === 'pgsql') {
    $gyms = $db->query("SELECT id,name,lat,lon,url FROM forts WHERE LOWER(name) LIKE :name LIMIT 20",  [':name' => "%" . strtolower($term) . "%"])->fetchAll();
}
else {
  $gyms = $db->select("forts", ['id', 'name', 'lat', 'lon', 'url'], ['name[~]' => $term, 'LIMIT' => 20]);
}

// set content type
header('Content-Type: application/json');

$jaysson = json_encode($gyms);
echo $jaysson;
