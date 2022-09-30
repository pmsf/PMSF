<?php

namespace Search;

class RocketMap_MAD extends Search
{
    public function search_reward($lat, $lon, $term, $quests_with_ar)
    {
        global $db, $defaultUnit, $maxSearchResults, $maxSearchNameLength, $numberOfPokemon;

        $conds = [];
        $params = [];

        $params[':lat'] = $lat;
        $params[':lon'] = $lon;

        $pjson = file_get_contents('static/dist/data/pokemon.min.json');
        $prewardsjson = json_decode($pjson, true);
        $presids = [];
        $forms = [];
        foreach ($prewardsjson as $p => $preward) {
            if ($p > $numberOfPokemon) {
                break;
            }
            if (strpos(strtolower(i8ln($preward['name'])), strtolower($term)) !== false) {
                $presids[] = $p;
            }
            if (isset($preward['forms'])) {
                foreach ($preward['forms'] as $f => $v) {
                    if (strpos(strtolower(i8ln($v['nameform'])), strtolower($term)) !== false) {
                        $forms[] = $v['protoform'];
                    }
                    if (strpos(strtolower($term), strtolower(i8ln($v['nameform']))) !== false && strpos(strtolower($term), strtolower(i8ln($preward['name']))) !== false) {
                        $conds[] = "(quest_pokemon_id = " . $p . " AND json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.form_value'),'$[0]') = " . $v['protoform'] . ")";
                    }
                }
            }
        }
        $ijson = file_get_contents('static/dist/data/items.min.json');
        $irewardsjson = json_decode($ijson, true);
        $iresids = [];
        foreach ($irewardsjson as $i => $ireward) {
            if (strpos(strtolower(i8ln($ireward['name'])), strtolower($term)) !== false) {
                $iresids[] = $i;
            }
        }
        if (!empty($presids)) {
            $conds[] = "tq.quest_pokemon_id IN (" . implode(',', $presids) . ")";
        }
        if (!empty($iresids)) {
            $conds[] = "tq.quest_item_id IN (" . implode(',', $iresids) . ")";
        }
        if (!empty($forms)) {
            $conds[] = "json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.form_value'),'$[0]') IN (" . implode(',', $forms) . ")";
        }
        if (strpos(strtolower(i8ln('XP')), strtolower($term)) !== false) {
            $conds[] = "tq.quest_reward_type = 1";
        }
        if (strpos(strtolower(i8ln('Stardust')), strtolower($term)) !== false) {
            $conds[] = "tq.quest_reward_type = 3";
        }
        if (strpos(strtolower(i8ln('Candy')), strtolower($term)) !== false) {
            $conds[] = "tq.quest_reward_type = 4";
        }
        if (strpos(strtolower(i8ln('Mega')), strtolower($term)) !== false || strpos(strtolower(i8ln('Energy')), strtolower($term)) !== false) {
            $conds[] = "tq.quest_reward_type = 12";
        }
        $query = "SELECT p.pokestop_id AS id,
        p.name,
        p.latitude AS lat,
        p.longitude AS lon,
        p.image AS url,
        tq.quest_type,
        tq.quest_reward_type,
        tq.quest_pokemon_id AS reward_pokemon_id,
        tq.quest_item_id AS reward_item_id,
        tq.quest_stardust AS reward_dust_amount,
        tq.quest_item_amount AS reward_item_amount,
        json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.form_value'),'$[0]') AS reward_pokemon_formid,
        json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.costume_value'),'$[0]') AS reward_pokemon_costumeid,
        json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.gender_value'),'$[0]') AS reward_pokemon_genderid,
        json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.is_shiny'),'$[0]') AS reward_pokemon_shiny,
        json_extract(json_extract(`quest_reward`,'$[*].candy.pokemon_id'),'$[0]') AS reward_candy_pokemon_id,
        json_extract(json_extract(`quest_reward`,'$[*].candy.amount'),'$[0]') AS reward_candy_amount,
        json_extract(json_extract(`quest_reward`,'$[*].exp'),'$[0]') AS reward_xp_amount,
        ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( latitude ) ) ) ),2) AS distance
        FROM pokestop p
        LEFT JOIN trs_quest tq ON tq.GUID = p.pokestop_id
        WHERE (:conditions)";
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $query .= " AND (ST_WITHIN(point(p.latitude,p.longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        $query .= " AND DATE(FROM_UNIXTIME(tq.quest_timestamp)) = CURDATE()
        ORDER BY distance LIMIT " . $maxSearchResults . "";
        $query = str_replace(":conditions", join(" OR ", $conds), $query);
        $rewards = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        foreach ($rewards as $reward) {
            switch ($reward["quest_reward_type"]) {
                case 1:
                    $reward["reward_amount"] = intval($reward["reward_xp_amount"]);
                    break;
                case 2:
                    $reward["reward_amount"] = intval($reward["reward_item_amount"]);
                    break;
                case 3:
                    $reward["reward_amount"] = intval($reward["reward_dust_amount"]);
                    break;
                case 4:
                    $reward["reward_pokemon_id"] = intval($reward["reward_candy_pokemon_id"]);
                    $reward["reward_amount"] = intval($reward["reward_candy_amount"]);
                    break;
                case 7:
                    $reward["reward_pokemon_id"] = intval($reward["reward_pokemon_id"]);
                    break;
                case 12:
                    $reward["reward_pokemon_id"] = intval($reward["reward_pokemon_id"]);
                    $reward["reward_amount"] = intval($reward["reward_item_amount"]);
                    break;
                default:
                    $reward["reward_pokemon_id"] = null;
                    $reward["reward_amount"] = null;
            }
            $reward['reward_pokemon_name'] = !empty($reward['reward_pokemon_id']) ? $prewardsjson[$reward['reward_pokemon_id']]['name'] : null;
            $reward['reward_pokemon_formid'] = intval($reward['reward_pokemon_formid']);
            $reward['quest_reward_type'] = intval($reward['quest_reward_type']);
            $reward['reward_item_name'] = !empty($reward['reward_item_id']) ? $irewardsjson[$reward['reward_item_id']]['name'] : null;
            $reward['reward_item_id'] = intval($reward['reward_item_id']);
            $reward['url'] = preg_replace("/^http:/i", "https:", $reward['url']);
            $reward['name'] = ($maxSearchNameLength > 0) ? htmlspecialchars(substr($reward['name'], 0, $maxSearchNameLength)) : htmlspecialchars($reward['name']);
            if ($defaultUnit === "km") {
                $reward['distance'] = round($reward['distance'] * 1.60934, 2);
            }
            $data[] = $reward;
        }
        return $data;
    }

    public function search_nests($lat, $lon, $term)
    {
        global $manualdb, $defaultUnit, $maxSearchResults, $noBoundaries, $boundaries, $numberOfPokemon;

        $json = file_get_contents('static/dist/data/pokemon.min.json');
        $mons = json_decode($json, true);
        $resids = [];
        foreach ($mons as $k => $mon) {
            if ($k > $numberOfPokemon) {
                break;
            }
            if (strpos(strtolower(i8ln($mon['name'])), strtolower($term)) !== false) {
                $resids[] = $k;
            } else {
                foreach ($mon['types'] as $t) {
                    if (strpos(strtolower(i8ln($t['type'])), strtolower($term)) !== false) {
                        $resids[] = $k;
                        break;
                    }
                }
            }
        }
        if (empty($resids)) {
            http_response_code(404);
        }
        if (!$noBoundaries) {
            $coords = " AND (ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))'))) ";
        } else {
            $coords = "";
        }

        $query = "SELECT nest_id,pokemon_id,lat,lon,
        ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance
        FROM nests WHERE pokemon_id IN (" . implode(',', $resids) . ") " . $coords . "ORDER BY distance LIMIT " . $maxSearchResults . "";

        $data = $manualdb->query($query, [ ':lat' => $lat, ':lon' => $lon])->fetchAll();
        foreach ($data as $k => $p) {
            $data[$k]['name'] = $mons[$p['pokemon_id']]['name'];
            if ($defaultUnit === "km") {
                $data[$k]['distance'] = round($data[$k]['distance'] * 1.60934, 2);
            }
        }
        return $data;
    }

    public function search_pokemon($lat, $lon, $term)
    {
        global $db, $defaultUnit, $maxSearchResults, $noBoundaries, $boundaries, $numberOfPokemon;

        $json = file_get_contents('static/dist/data/pokemon.min.json');
        $mons = json_decode($json, true);
        $resids = [];
        foreach ($mons as $k => $mon) {
            if ($k > $numberOfPokemon) {
                break;
            }
            if (strpos(strtolower(i8ln($mon['name'])), strtolower($term)) !== false) {
                $resids[] = $k;
            } else {
                foreach ($mon['types'] as $t) {
                    if (strpos(strtolower(i8ln($t['type'])), strtolower($term)) !== false) {
                        $resids[] = $k;
                        break;
                    }
                }
            }
        }
        if (empty($resids)) {
            http_response_code(404);
        }
        if (!$noBoundaries) {
            $coords = " AND (ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))'))) ";
        } else {
            $coords = "";
        }

        $query = "SELECT pokemon_id,latitude AS lat,longitude AS lon,
        ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance
        FROM pokemon WHERE disappear_time > TIMESTAMP(NOW()) AND pokemon_id IN (" . implode(',', $resids) . ") " . $coords . "ORDER BY distance LIMIT " . $maxSearchResults . "";

        $data = $db->query($query, [ ':lat' => $lat, ':lon' => $lon])->fetchAll();
        foreach ($data as $k => $p) {
            $data[$k]['name'] = $mons[$p['pokemon_id']]['name'];
            if ($defaultUnit === "km") {
                $data[$k]['distance'] = round($data[$k]['distance'] * 1.60934, 2);
            }
        }
        return $data;
    }

    public function search_portals($lat, $lon, $term)
    {
        global $manualdb, $defaultUnit, $maxSearchResults, $noBoundaries, $boundaries;

        if (!$noBoundaries) {
            $coords = " AND (ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))'))) ";
        } else {
            $coords = "";
        }

        $query = "SELECT id,name,lat,lon,url,
        ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ),2) AS distance
        FROM ingress_portals WHERE LOWER(name) LIKE :name " . $coords . "ORDER BY distance LIMIT " . $maxSearchResults . "";

        $searches = $manualdb->query($query, [ ':name' => "%" . strtolower($term) . "%",  ':lat' => $lat, ':lon' => $lon ])->fetchAll();

        $data = array();
        $i = 0;

        foreach ($searches as $search) {
            $search['url'] = preg_replace("/^http:/i", "https:", $search['url']);
            if ($defaultUnit === "km") {
                $search['distance'] = round($search['distance'] * 1.60934, 2);
            }
            $data[] = $search;
            unset($searches[$i]);
            $i++;
        }
        return $data;
    }

    public function search($dbname, $lat, $lon, $term)
    {
        global $db, $defaultUnit, $maxSearchResults, $noBoundaries, $boundaries;

        if (!$noBoundaries && $dbname === 'pokestop') {
            $coords = " AND (ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))'))) ";
        } elseif (!$noBoundaries && $dbname === 'gym') {
            $coords = " AND (ST_WITHIN(point(g.latitude,g.longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))'))) ";
        } else {
            $coords = "";
        }

        if ($dbname === 'pokestop') {
            if ($db->info()['driver'] === 'pgsql') {
                $query = "SELECT pokestop_id,name,latitude AS lat,longitude AS lon,image AS url,
                ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( latitude ) ) ) as numeric),2) AS distance
                FROM " . $dbname . " WHERE LOWER(name) LIKE :name " . $coords . "ORDER BY distance LIMIT " . $maxSearchResults . "";
            } else {
                $query = "SELECT pokestop_id,name,latitude AS lat,longitude AS lon,image AS url,
                ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( latitude ) ) ) ),2) AS distance
                FROM " . $dbname . " WHERE LOWER(name) LIKE :name " . $coords . "ORDER BY distance LIMIT " . $maxSearchResults . "";
            }
        } elseif ($dbname === 'gym') {
            if ($db->info()['driver'] === 'pgsql') {
                $query = "SELECT g.gym_id,gd.name,g.latitude AS lat,g.longitude AS lon,gd.url,
                ROUND(cast( 3959 * acos( cos( radians(:lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( latitude ) ) ) as numeric),2) AS distance
                FROM " . $dbname . " g LEFT JOIN gymdetails gd ON gd.gym_id = g.gym_id WHERE LOWER(name) LIKE :name " . $coords . "ORDER BY distance LIMIT " . $maxSearchResults . "";
            } else {
                $query = "SELECT g.gym_id,gd.name,g.latitude AS lat,g.longitude AS lon,gd.url,
                ROUND(( 3959 * acos( cos( radians(:lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( latitude ) ) ) ),2) AS distance
                FROM " . $dbname . " g LEFT JOIN gymdetails gd ON gd.gym_id = g.gym_id WHERE LOWER(name) LIKE :name " . $coords . "ORDER BY distance LIMIT " . $maxSearchResults . "";
            }
        }
        $searches = $db->query($query, [ ':name' => "%" . strtolower($term) . "%",  ':lat' => $lat, ':lon' => $lon ])->fetchAll();
        $data = array();
        $i = 0;
        foreach ($searches as $search) {
            $search['url'] = preg_replace("/^http:/i", "https:", $search['url']);
            if ($defaultUnit === "km") {
                $search['distance'] = round($search['distance'] * 1.60934, 2);
            }
            $data[] = $search;
            unset($searches[$i]);
            $i++;
        }
        return $data;
    }
}
