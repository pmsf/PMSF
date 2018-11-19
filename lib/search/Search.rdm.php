<?php

namespace Search;

class RDM extends Search
{
    public function search_reward($lat, $lon, $term)
    {
        global $db, $defaultUnit, $maxSearchResults;

        $json = file_get_contents( 'static/dist/data/pokemon.min.json' );
        $rewardsjson = json_decode( $json, true );
        $resids = [];
        foreach($rewardsjson as $k => $reward){
            if( $k > 493){
                break;
            }
            if(strpos(strtolower($reward['name']), strtolower($term)) !== false){
                $resids[] = $k;
                break;
            }
        }

        $query = "SELECT id,name,lat,lon,url,quest_type,quest_pokemon_id, ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance FROM pokestop WHERE quest_pokemon_id IN (" . implode(',',$resids) . ") ORDER BY distance LIMIT " . $maxSearchResults . "";

	$rewards = $db->query($query,[ ':lat' => $lat, ':lon' => $lon])->fetchAll(\PDO::FETCH_ASSOC);

	$data = array();
	$i = 0;

	foreach($rewards as $reward){
            $reward['pokemon_name'] = $rewardsjson[$reward['quest_pokemon_id']]['name'];
	    $reward['quest_pokemon_id'] = intval($reward['quest_pokemon_id']);
            if($defaultUnit === "km"){
                $reward[$reward]['distance'] = round($data[$reward]['distance'] * 1.60934,2);
	    }
	    $data[] = $reward;
	    unset($pokestops[$i]);
	    $i++;
	}
        return $data;
    }

    public function search_nests($lat, $lon, $term)
    {
        global $db, $defaultUnit, $maxSearchResults;

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
            $query = "SELECT nest_id,pokemon_id,lat,lon, ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) as numeric),2) AS distance FROM nests WHERE pokemon_id IN (" . implode(',',$resids) . ") ORDER BY distance LIMIT " . $maxSearchResults . "";
        } else{
            $query = "SELECT nest_id,pokemon_id,lat,lon, ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance FROM nests WHERE pokemon_id IN (" . implode(',',$resids) . ") ORDER BY distance LIMIT " . $maxSearchResults . "";
        }
        $data = $db->query($query,[ ':lat' => $lat, ':lon' => $lon])->fetchAll();
        foreach($data as $k => $p){
            $data[$k]['name'] = $mons[$p['pokemon_id']]['name'];
            if($defaultUnit === "km"){
                $data[$k]['distance'] = round($data[$k]['distance'] * 1.60934,2);
            }
	}
        return $data;
    }

    public function search($dbname, $lat, $lon, $term)
    {
        global $db, $defaultUnit, $maxSearchResults;

        if ( $db->info()['driver'] === 'pgsql' ) {
            $query = "SELECT id,external_id,name,lat,lon,url, ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) as numeric),2) AS distance FROM " . $dbname . " WHERE LOWER(name) LIKE :name ORDER BY distance LIMIT " . $maxSearchResults . "";
        } else {
            $query = "SELECT id,name,lat,lon,url, ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance FROM " . $dbname . " WHERE LOWER(name) LIKE :name ORDER BY distance LIMIT " . $maxSearchResults . "";
        }
        $searches = $db->query( $query, [ ':name' => "%" . strtolower( $term ) . "%",  ':lat' => $lat, ':lon' => $lon ] )->fetchAll();

	$data = array();
	$i = 0;

        foreach($searches as $search){
            $search['url'] = str_replace("http://", "https://images.weserv.nl/?url=", $search['url']);
            if($defaultUnit === "km"){
                $search['distance'] = round($search['distance'] * 1.60934,2);
	    }
	    $data[] = $search;
	    unset($searches[$i]);
	    $i++;
        }
        return $data;
    }
}
