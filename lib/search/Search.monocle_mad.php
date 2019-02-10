<?php

namespace Search;

class Monocle_MAD extends Search
{
    public function search_reward($lat, $lon, $term)
    {
        global $db, $defaultUnit, $maxSearchResults;

	$conds = array();
	$params = array();

	$params[':lat'] = $lat;
	$params[':lon'] = $lon;

        $pjson = file_get_contents( 'static/dist/data/pokemon.min.json' );
        $prewardsjson = json_decode( $pjson, true );
        $presids = array();
        foreach($prewardsjson as $p => $preward){
            if( $p > 493){
                break;
            }
            if(strpos(strtolower($preward['name']), strtolower($term)) !== false){
                $presids[] = $p;
            }
        }
        $ijson = file_get_contents( 'static/dist/data/items.min.json' );
        $irewardsjson = json_decode( $ijson, true );
        $iresids = [];
        foreach($irewardsjson as $i => $ireward){
            if(strpos(strtolower($ireward['name']), strtolower($term)) !== false){
                $iresids[] = $i;
            }
        }
	if (!empty($presids)) {
		$conds[] = "tq.quest_pokemon_id IN (" . implode(',',$presids) . ")";
	}
	if (!empty($iresids)) {
		$conds[] = "tq.quest_item_id IN (" . implode(',',$iresids) . ")";
	}
	$query = "SELECT p.external_id AS id,
	p.name,
	p.lat,
	p.lon,
	p.url,
	tq.quest_type,
	tq.quest_pokemon_id,
	tq.quest_item_id,
	ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance 
	FROM pokestops p
	LEFT JOIN trs_quest tq ON tq.GUID = p.external_id
	WHERE :conditions
	ORDER BY distance LIMIT " . $maxSearchResults . "";

	$query = str_replace(":conditions", join(" OR ", $conds), $query);

	$rewards = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

	$data = array();

	foreach($rewards as $reward){
            $reward['pokemon_name'] = !empty($reward['pokemon_name']) ? $prewardsjson[$reward['quest_pokemon_id']]['name'] : null;
	    $reward['quest_pokemon_id'] = intval($reward['quest_pokemon_id']);
            $reward['item_name'] = !empty($reward['item_name']) ? $irewardsjson[$reward['quest_item_id']]['name'] : null;
	    $reward['quest_item_id'] = intval($reward['quest_item_id']);
            if($defaultUnit === "km"){
                $reward['distance'] = round($reward['distance'] * 1.60934,2);
	    }
	    $data[] = $reward;
	}
        return $data;
    }

    public function search_nests($lat, $lon, $term)
    {
        global $manualdb, $defaultUnit, $maxSearchResults;

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
        if ( $manualdb->info()['driver'] === 'pgsql' ) {
            $query = "SELECT nest_id,pokemon_id,lat,lon, ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) as numeric),2) AS distance FROM nests WHERE pokemon_id IN (" . implode(',',$resids) . ") ORDER BY distance LIMIT " . $maxSearchResults . "";
        } else{
            $query = "SELECT nest_id,pokemon_id,lat,lon, ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance FROM nests WHERE pokemon_id IN (" . implode(',',$resids) . ") ORDER BY distance LIMIT " . $maxSearchResults . "";
        }
        $data = $manualdb->query($query,[ ':lat' => $lat, ':lon' => $lon])->fetchAll();
        foreach($data as $k => $p){
            $data[$k]['name'] = $mons[$p['pokemon_id']]['name'];
            if($defaultUnit === "km"){
                $data[$k]['distance'] = round($data[$k]['distance'] * 1.60934,2);
            }
	}
        return $data;
    }

    public function search_portals($lat, $lon, $term)
    {
        global $manualdb, $defaultUnit, $maxSearchResults;

        if ( $manualdb->info()['driver'] === 'pgsql' ) {
            $query = "SELECT id,external_id,name,lat,lon,url, ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) as numeric),2) AS distance FROM ingress_portals WHERE LOWER(name) LIKE :name ORDER BY distance LIMIT " . $maxSearchResults . "";
        } else {
            $query = "SELECT id,name,lat,lon,url, ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance FROM ingress_portals WHERE LOWER(name) LIKE :name ORDER BY distance LIMIT " . $maxSearchResults . "";
        }
        $searches = $manualdb->query( $query, [ ':name' => "%" . strtolower( $term ) . "%",  ':lat' => $lat, ':lon' => $lon ] )->fetchAll();

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
