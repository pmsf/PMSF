<?php
include(dirname(__FILE__) . '/config/config.php');
global $map, $fork, $db;

$url = 'https://thesilphroad.com/atlas/getLocalNests.json';
$coords = array(
    array(
        'lat1' => 42.8307723529682,
        'lng1' => -88.7527692278689,
        'lat2' => 42.1339901128552,
        'lng2' => -88.0688703020877
    ),
    array(
        'lat1' => 42.8529250952743,
        'lng1' => -88.1292951067752,
        'lat2' => 41.7929306950085,
        'lng2' => -87.5662457903689
    ),
    array(
        'lat1' => 42.125842369748,
        'lng1' => -88.7280499895877,
        'lat2' => 41.4315162889464,
        'lng2' => -88.0056989153689
    )
);
foreach($coords as $c){
    $data = array(
        "data[lat1]"=> $c['lat1'],
        "data[lng1]"=> $c['lng1'],
        "data[lat2]"=> $c['lat2'],
        "data[lng2]"=> $c['lng2'],
        "data[zoom]"=> 1,
        "data[mapFilterValues][mapTypes][]"=> 1,
        "data[mapFilterValues][nestVerificationLevels][]"=> 1,
        "data[mapFilterValues][nestTypes][]"=> -1,
        "data[center_lat]"=> 42.237,
        "data[center_lng]"=> -88.26822);

// use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { /* Handle error */ }

    $nests = json_decode($result,true)['localMarkers'];
//var_dump($nests);
    foreach($nests as $nest){
        $query = "INSERT INTO nests (nest_id, lat, lon, pokemon_id, updated) VALUES (" . $nest['id'] . "," . $nest['lt'] . "," . $nest['ln'] . "," . $nest['pokemon_id'] . "," . time() . ") ON DUPLICATE KEY UPDATE pokemon_id=" . $nest['pokemon_id'] . ", updated=" . time();
        $db->query($query)->fetchAll();
        var_dump($db->last());
    }
}
