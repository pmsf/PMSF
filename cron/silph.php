<?php
// This fille is provided for educationAL purposes
include(dirname(__FILE__).'/../config/config.php' );
global $map, $fork, $db, $nestCoords;

$url = 'https://thesilphroad.com/atlas/getLocalNests.json';

foreach ( $nestCoords as $c ) {
    $data = array(
        "data[lat1]"                                      => $c['lat1'],
        "data[lng1]"                                      => $c['lng1'],
        "data[lat2]"                                      => $c['lat2'],
        "data[lng2]"                                      => $c['lng2'],
        "data[zoom]"                                      => 1,
        "data[mapFilterValues][mapTypes][]"               => 1,
        "data[mapFilterValues][nestVerificationLevels][]" => 1,
        "data[mapFilterValues][nestTypes][]"              => - 1,
        "data[center_lat]"                                => 55.764428,
        "data[center_lng]"                                => 5.060553
    );

// use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query( $data )
        )
    );
    $context = stream_context_create( $options );
    $result  = file_get_contents( $url, false, $context );
    $nests = json_decode( $result, true )['localMarkers'];
    foreach ( $nests as $nest ) {
        if($nest['s'] == "2"){
            $nest['pokemon_id'] = "0";
        }
        if($db->info()['driver'] === 'pgsql'){
            $query = "INSERT INTO nests (nest_id, lat, lon, pokemon_id, updated,type) VALUES (" . $nest['id'] . "," . $nest['lt'] . "," . $nest['ln'] . "," . $nest['pokemon_id'] . "," . time() . ",1) ON CONFLICT (nest_id) DO UPDATE SET pokemon_id=" . $nest['pokemon_id'] . ", updated=" . time() . ", type=1";
        } else{
            $query = "INSERT INTO nests (nest_id, lat, lon, pokemon_id, updated,type) VALUES (" . $nest['id'] . "," . $nest['lt'] . "," . $nest['ln'] . "," . $nest['pokemon_id'] . "," . time() . ",1) ON DUPLICATE KEY UPDATE pokemon_id=" . $nest['pokemon_id'] . ", updated=" . time() . ", type=1";
        }
        $db->query( $query )->fetchAll();
    }
}
echo 'Done Successfully';
