<?php
include( 'config/config.php' );
global $map, $fork, $db, $noSearch, $noGyms, $noPokestops, $noRaids, $defaultUnit;
if ( $noSearch === true || ( $noGyms && $noRaids && $noPokestops ) ) {
    http_response_code( 401 );
    die();
}
$term = ! empty( $_POST['term'] ) ? $_POST['term'] : '';
$action = ! empty( $_POST['action'] ) ? $_POST['action'] : '';
$lat = ! empty( $_POST['lat'] ) ? $_POST['lat'] : '';
$lon = ! empty( $_POST['lon'] ) ? $_POST['lon'] : '';
$dbname = '';
if ( $action === "pokestops" ) {
    $dbname = "pokestops";
} elseif ( $action === "forts" ) {
    $dbname = "forts";
} elseif ( $action === "reward" ) {
    $dbname = "pokestops";
} elseif ( $action === "nests" ) {
    $dbname = "nests";
}

if ( $dbname !== '' ) {
    if ( $action === "reward" ) {
	    
        $json = file_get_contents( 'static/dist/data/rewards.min.json' );
        $rewards = json_decode( $json, true );
        $resids = [];
        foreach($rewards as $k => $reward){
            if( $k > 1500){
                break;
            }
            if(strpos(strtolower($reward['name']), strtolower($term)) !== false){
                $resids[] = $k;
                break;
            }
        }
        if ( $db->info()['driver'] === 'pgsql' ) {
            $query = "SELECT id,external_id,name,lat,lon,url,quest_id,reward_id, ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) as numeric),2) AS distance FROM " . $dbname . " WHERE LOWER(reward) LIKE :name ORDER BY distance LIMIT 10";
        } else {
            $query = "SELECT id,external_id,name,lat,lon,url,quest_id,reward_id, ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance FROM pokestops WHERE reward_id IN (" . implode(',',$resids) . ") ORDER BY distance LIMIT 10";
        }
	$data = $db->query($query,[ ':lat' => $lat, ':lon' => $lon])->fetchAll();
	foreach($data as $k => $r){
            $data[$k]['reward'] = $rewards[$r['reward_id']]['name'];
            if($defaultUnit === "km"){
                $data[$k]['distance'] = round($data[$k]['distance'] * 1.60934,2);
            }
        }
    } elseif ( $action === "nests" ) {

        $json = file_get_contents( 'static/dist/data/pokemon.min.json' );
        $mons = json_decode( $json, true );
        $resids = [];
        foreach($mons as $k => $mon){
            if( $k > 386){
                break;
            }
            if(strpos(strtolower($mon['name']), strtolower($term)) !== false){
                $resids[] = $k;
            } else{
                foreach($mon['types'] as $t){
                    if(strpos(strtolower($t['type']), strtolower($term)) !== false){
                        $resids[] = $k;
                        break;
                    }
                }
            }
        }
        if ( $db->info()['driver'] === 'pgsql' ) {
            $query = "SELECT nest_id,pokemon_id,lat,lon, ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) as numeric),2) AS distance FROM nests WHERE pokemon_id IN (" . implode(',',$resids) . ") ORDER BY distance LIMIT 10";
        } else{
            $query = "SELECT nest_id,pokemon_id,lat,lon, ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance FROM nests WHERE pokemon_id IN (" . implode(',',$resids) . ") ORDER BY distance LIMIT 10";
        }
        $data = $db->query($query,[ ':lat' => $lat, ':lon' => $lon])->fetchAll();
        foreach($data as $k => $p){
            $data[$k]['name'] = $mons[$p['pokemon_id']]['name'];
            if($defaultUnit === "km"){
                $data[$k]['distance'] = round($data[$k]['distance'] * 1.60934,2);
            }
        }
    } else {

        if ( $db->info()['driver'] === 'pgsql' ) {
            $query = "SELECT id,external_id,name,lat,lon,url, ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) as numeric),2) AS distance FROM " . $dbname . " WHERE LOWER(name) LIKE :name ORDER BY distance LIMIT 10";
        } else {
            $query = "SELECT id,external_id,name,lat,lon,url, ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance FROM " . $dbname . " WHERE LOWER(name) LIKE :name ORDER BY distance LIMIT 10";
        }
        $data = $db->query( $query, [ ':name' => "%" . strtolower( $term ) . "%",  ':lat' => $lat, ':lon' => $lon ] )->fetchAll();
    }

    foreach($data as $k => $p){
        if($defaultUnit === "km"){
            $data[$k]['distance'] = round($data[$k]['distance'] * 1.60934,2);
        }
    }
    //var_dump($db->last());

    // set content type
    header( 'Content-Type: application/json' );

    $jaysson = json_encode( $data );
    echo $jaysson;
}
