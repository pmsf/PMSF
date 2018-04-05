<?php
include('config/config.php');
global $map, $fork, $db;
$term = !empty($_POST['term']) ? $_POST['term'] : '';
$action = !empty($_POST['action']) ? $_POST['action'] : '';

if($db->info()['driver'] === 'pgsql') {
    $data = $db->query("SELECT id,name,lat,lon,url FROM :table WHERE LOWER(name) LIKE :name LIMIT 10",  [':name' => "%" . strtolower($term) . "%", ':table' => $action])->fetchAll();
}
else {
    $data = $db->select($action, ['id', 'name', 'lat', 'lon', 'url'], ['name[~]' => $term, 'LIMIT' => 10]);
}

// set content type
header('Content-Type: application/json');

$jaysson = json_encode($data);
echo $jaysson;
