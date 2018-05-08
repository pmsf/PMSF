<?php
include('config/config.php');
global $map, $fork, $db, $noSearch, $noGyms, $noPokestops, $noRaids;
if($noSearch === true || ($noGyms && $noRaids && $noPokestops)){
    http_response_code(401);
    die();
}
$term = !empty($_POST['term']) ? $_POST['term'] : '';
$action = !empty($_POST['action']) ? $_POST['action'] : '';
$dbname = '';
if($action === "pokestops") {
    $dbname = "pokestops";
} elseif($action === "forts") {
    $dbname = "forts";
} elseif($action === "reward") {
    $dbname = "pokestops";
}

if($dbname !== '') {
    if($action === "reward"){
        if($db->info()['driver'] === 'pgsql') {
            $data = $db->query("SELECT id,external_id,name,lat,lon,url,quest_id,reward FROM " . $dbname . " WHERE LOWER(reward) LIKE :name LIMIT 10",  [':name' => "%" . strtolower($term) . "%"])->fetchAll();
        }
        else {
            $data = $db->select("pokestops", ['id', 'external_id', 'name', 'lat', 'lon', 'url','quest_id','reward'], ['reward[~]' => $term, 'LIMIT' => 10]);
        }
    } else{
        if($db->info()['driver'] === 'pgsql') {
            $data = $db->query("SELECT id,external_id,name,lat,lon,url FROM " . $dbname . " WHERE LOWER(name) LIKE :name LIMIT 10",  [':name' => "%" . strtolower($term) . "%"])->fetchAll();
        }
        else {
            $data = $db->select($action, ['id', 'external_id', 'name', 'lat', 'lon', 'url'], ['name[~]' => $term, 'LIMIT' => 10]);
        }
    }
    //var_dump($db->last());

    // set content type
    header('Content-Type: application/json');

    $jaysson = json_encode($data);
    echo $jaysson;
}
