<?php

namespace Scanner;

class RDM_beta extends RDM
{
    public function get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $encId = 0)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $select = "pokemon_id, expire_timestamp AS disappear_time, id AS encounter_id, spawn_id, lat AS latitude, lon AS longitude, gender, form, weather AS weather_boosted_condition, costume, expire_timestamp_verified";
        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", weight, size AS height, atk_iv AS individual_attack, def_iv AS individual_defense, sta_iv AS individual_stamina, move_1, move_2, cp, level";
        }

        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng AND expire_timestamp > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $params[':time'] = time();

        if ($oSwLat != 0) {
            $conds[] = "NOT (lat > :oswLat AND lon > :oswLng AND lat < :oneLat AND lon < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        $tmpSQL = '';
        if (!empty($tinyRat) && $tinyRat === 'true' && ($key = array_search("19", $eids)) === false) {
            $tmpSQL .= ' OR (pokemon_id = 19 AND weight' . $float . ' < 2.41)';
            $eids[] = "19";
        }
        if (!empty($bigKarp) && $bigKarp === 'true' && ($key = array_search("129", $eids)) === false) {
            $tmpSQL .= ' OR (pokemon_id = 129 AND weight' . $float . ' > 13.13)';
            $eids[] = "129";
        }
        global $noDittoDetection, $possibleDitto;
        if (!$noDittoDetection && ($key = array_search("132", $eids)) === false) {
            $pDitto = implode(",", $possibleDitto);
            $tmpSQL .= " OR (weather > 0 AND (level < 6 OR atk_iv < 4 OR def_iv < 4 OR sta_iv < 4) and pokemon_id in (" . $pDitto . "))";
            $eids[] = "132";
        }
        if (count($eids)) {
            $pkmn_in = '';
            $i = 1;
            foreach ($eids as $id) {
                $params[':qry_' . $i . "_"] = $id;
                $pkmn_in .= ':qry_' . $i . "_,";
                $i++;
            }
            $pkmn_in = substr($pkmn_in, 0, -1);
            $conds[] = "(pokemon_id NOT IN ( $pkmn_in )" . $tmpSQL . ")";
        }
        if (!empty($minIv) && !is_nan((float)$minIv) && $minIv != 0) {
            $minIv = $minIv * .45;
            if (empty($exMinIv)) {
                $conds[] = '(iv' . $float . ') >= ' . $minIv;
            } else {
                $conds[] = '((iv' . $float . ') >= ' . $minIv . ' OR pokemon_id IN(' . $exMinIv . ') )';
            }
        }
        if (!empty($minLevel) && !is_nan((float)$minLevel) && $minLevel != 0) {
            if (empty($exMinIv)) {
                $conds[] = 'level >= ' . $minLevel;
            } else {
                $conds[] = '(level >= ' . $minLevel . ' OR pokemon_id IN(' . $exMinIv . ') )';
            }
        }
        $encSql = '';
        if ($encId != 0) {
            $encSql = " OR (id = " . $encId . " AND lat > '" . $swLat . "' AND lon > '" . $swLng . "' AND lat < '" . $neLat . "' AND lon < '" . $neLng . "' AND expire_timestamp > '" . $params[':time'] . "')";
        }
        return $this->query_active($select, $conds, $params, $encSql);
    }

    public function get_active_by_id($ids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $select = "pokemon_id, expire_timestamp AS disappear_time, id AS encounter_id, spawn_id, lat AS latitude, lon AS longitude, gender, form, weather AS weather_boosted_condition, costume, expire_timestamp_verified";

        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", weight, size AS height, atk_iv AS individual_attack, def_iv AS individual_defense, sta_iv AS individual_stamina, move_1, move_2, cp, level";
        }

        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng AND expire_timestamp > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $params[':time'] = time();

        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if (count($ids)) {
            $tmpSQL = '';
            if (!empty($tinyRat) && $tinyRat === 'true' && ($key = array_search("19", $ids)) !== false) {
                $tmpSQL .= ' OR (pokemon_id = 19 AND weight' . $float . ' < 2.41)';
                unset($ids[$key]);
            }
            if (!empty($bigKarp) && $bigKarp === 'true' && ($key = array_search("129", $ids)) !== false) {
                $tmpSQL .= ' OR (pokemon_id = 129 AND weight' . $float . ' > 13.13)';
                unset($ids[$key]);
            }
            global $noDittoDetection, $possibleDitto;
            if (!$noDittoDetection && ($key = array_search("132", $ids)) !== false) {
                $pDitto = implode(",", $possibleDitto);
                $tmpSQL .= " OR (weather > 0 AND (level < 6 OR atk_iv < 4 OR def_iv < 4 OR sta_iv < 4) and pokemon_id in (" . $pDitto . "))";
            }
            $pkmn_in = '';
            $i = 1;
            foreach ($ids as $id) {
                $params[':qry_' . $i . "_"] = $id;
                $pkmn_in .= ':qry_' . $i . "_,";
                $i++;
            }
            if (count($ids)) {
                $pkmn_in = substr($pkmn_in, 0, -1);
                $conds[] = "(pokemon_id IN ( $pkmn_in )" . $tmpSQL . ")";
            } else {
                $conds[] = str_replace("OR", "", $tmpSQL);
            }
        }

        if (!empty($minIv) && !is_nan((float)$minIv) && $minIv != 0) {
            $minIv = $minIv * .45;
            if (empty($exMinIv)) {
                $conds[] = '(iv' . $float . ') >= ' . $minIv;
            } else {
                $conds[] = '((iv' . $float . ') >= ' . $minIv . ' OR pokemon_id IN(' . $exMinIv . ') )';
            }
        }
        if (!empty($minLevel) && !is_nan((float)$minLevel) && $minLevel != 0) {
            if (empty($exMinIv)) {
                $conds[] = 'level >= ' . $minLevel;
            } else {
                $conds[] = '(level >= ' . $minLevel . ' OR pokemon_id IN(' . $exMinIv . ') )';
            }
        }
        return $this->query_active($select, $conds, $params);
    }

    public function query_active($select, $conds, $params, $encSql = '')
    {
        global $db;

        $query = "SELECT :select
        FROM pokemon 
        WHERE :conditions ORDER BY lat,lon ";

        $query = str_replace(":select", $select, $query);
        $query = str_replace(":conditions", '(' . join(" AND ", $conds) . ')' . $encSql, $query);
        $pokemons = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        $lastlat = 0;
        $lastlon = 0;
        $lasti = 0;
        
        foreach ($pokemons as $pokemon) {
            // Jitter pokemon when they have no spawn_id
            if (empty($pokemon['spawn_id'])) {
                $pokemon["latitude"] = floatval($pokemon["latitude"]);
                $pokemon["longitude"] = floatval($pokemon["longitude"]);
                $lastlat = floatval($pokemon["latitude"]);
                $lastlon = floatval($pokemon["longitude"]);
                if (abs($pokemon["latitude"] - $lastlat) < 0.0001 && abs($pokemon["longitude"] - $lastlon) < 0.0001) {
                    $lasti = $lasti + 1;
                } else {
                    $lasti = 0;
                }
                $pokemon["latitude"] = $pokemon["latitude"] + 0.0003*cos(deg2rad($lasti*45));
                $pokemon["longitude"] = $pokemon["longitude"] + 0.0003*sin(deg2rad($lasti*45));
            } else {
                $pokemon["latitude"] = floatval($pokemon["latitude"]);
                $pokemon["longitude"] = floatval($pokemon["longitude"]);
            }
            $pokemon["disappear_time"] = $pokemon["disappear_time"] * 1000;

            $pokemon["weight"] = isset($pokemon["weight"]) ? floatval($pokemon["weight"]) : null;
            $pokemon["height"] = isset($pokemon["height"]) ? floatval($pokemon["height"]) : null;
            $pokemon["individual_attack"] = isset($pokemon["individual_attack"]) ? intval($pokemon["individual_attack"]) : null;
            $pokemon["individual_defense"] = isset($pokemon["individual_defense"]) ? intval($pokemon["individual_defense"]) : null;
            $pokemon["individual_stamina"] = isset($pokemon["individual_stamina"]) ? intval($pokemon["individual_stamina"]) : null;

            $pokemon["weather_boosted_condition"] = isset($pokemon["weather_boosted_condition"]) ? intval($pokemon["weather_boosted_condition"]) : 0;

            $pokemon["pokemon_id"] = intval($pokemon["pokemon_id"]);
            $pokemon["pokemon_name"] = i8ln($this->data[$pokemon["pokemon_id"]]['name']);
            $pokemon["pokemon_rarity"] = i8ln($this->data[$pokemon["pokemon_id"]]['rarity']);
            if (isset($pokemon["form"]) && $pokemon["form"] > 0) {
                $forms = $this->data[$pokemon["pokemon_id"]]["forms"];
                foreach ($forms as $f => $v) {
                    if ($pokemon["form"] === $v['protoform']) {
                        $types = $v['formtypes'];
                        foreach ($v['formtypes'] as $ft => $v) {
                            $types[$ft]['type'] = $v['type'];
                        }
                        $pokemon["pokemon_types"] = $types;
                    } elseif ($pokemon["form"] === $v['assetsform']) {
                        $types = $v['formtypes'];
                        foreach ($v['formtypes'] as $ft => $v) {
                            $types[$ft]['type'] = $v['type'];
                        }
                        $pokemon["pokemon_types"] = $types;
                    }
                }
            } else {
                $types = $this->data[$pokemon["pokemon_id"]]["types"];
                foreach ($types as $k => $v) {
                    $types[$k]['type'] = $v['type'];
                }
                $pokemon["pokemon_types"] = $types;
            }

            // Ditto detection
            global $noDittoDetection, $possibleDitto;
            if (!$noDittoDetection) {
                if (in_array($pokemon["pokemon_id"], $possibleDitto) && $pokemon["weather_boosted_condition"] > 0 && $pokemon["level"] !== null) {
                    if ($pokemon["level"] < 6 || $pokemon["individual_attack"] < 4 || $pokemon["individual_defense"] < 4 || $pokemon["individual_stamina"] < 4) {
                        if ($pokemon["weather_boosted_condition"] != 3) {
                            $pokemon["weather_boosted_condition"] = 0;
                        }
                        $pokemon["pokemon_id"] = 132;
                        $pokemon["form"] = 0;
                        $pokemon["pokemon_name"] = $pokemon["pokemon_name"] . ' (' . i8ln('Ditto') . ')';
                        $pokemon["move_1"] = 242;
                        $pokemon["move_2"] = 133;
                        $pokemon["height"] = null;
                        $pokemon["weight"] = null;
                        $pokemon["gender"] = 3;
                    }
                }
            }

            $data[] = $pokemon;
            unset($pokemons[$i]);
            $i++;
        }
        return $data;
    }

    public function get_stops($qpeids, $qieids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $rocket, $quests, $dustamount)
    {
        $conds = array();
        $params = array();
        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if (!empty($quests) && $quests === 'true') {
            $pokemonSQL = '';
            if (count($qpeids)) {
                $pkmn_in = '';
                $p = 1;
                foreach ($qpeids as $qpeid) {
                    $params[':pqry_' . $p . "_"] = $qpeid;
                    $pkmn_in .= ':pqry_' . $p . "_,";
                    $p++;
                }
                $pkmn_in = substr($pkmn_in, 0, -1);
                $pokemonSQL .= "quest_pokemon_id NOT IN ( $pkmn_in )";
            } else {
                $pokemonSQL .= "quest_pokemon_id IS NOT NULL";
            }
            $itemSQL = '';
            if (count($qieids)) {
                $item_in = '';
                $i = 1;
                foreach ($qieids as $qieid) {
                    $params[':iqry_' . $i . "_"] = $qieid;
                    $item_in .= ':iqry_' . $i . "_,";
                    $i++;
                }
                $item_in = substr($item_in, 0, -1);
                $itemSQL .= "quest_item_id NOT IN ( $item_in )";
            } else {
                $itemSQL .= "quest_item_id IS NOT NULL";
            }
            $dustSQL = '';
            if (!empty($dustamount) && !is_nan((float)$dustamount) && $dustamount > 0) {
                $dustSQL .= "OR (json_extract(json_extract(`quest_rewards`,'$[*].type'),'$[0]') = 3 AND json_extract(json_extract(`quest_rewards`,'$[*].info.amount'),'$[0]') > :amount) AND lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng";
                $params[':amount'] = intval($dustamount);
                $params[':swLat'] = $swLat;
                $params[':swLng'] = $swLng;
                $params[':neLat'] = $neLat;
                $params[':neLng'] = $neLng;
                if (!$noBoundaries) {
                    $dustSQL .= " AND (ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
                }
            }
            $conds[] = "(" . $pokemonSQL . " OR " . $itemSQL . ")" . $dustSQL . "";
        }
        if ($oSwLat != 0) {
            $conds[] = "NOT (lat > :oswLat AND lon > :oswLng AND lat < :oneLat AND lon < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        if (!empty($lures) && $lures === 'true') {
            $conds[] = "lure_expire_timestamp > :time";
            $params[':time'] = time();
        }
        if (!empty($rocket) && $rocket === 'true') {
            $conds[] = "incident_expire_timestamp > :time";
            $params[':time'] = time();
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        return $this->query_stops($conds, $params);
    }


    public function get_stops_quest($qpreids, $qireids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $rocket, $quests, $dustamount, $reloaddustamount)
    {
        $conds = array();
        $params = array();
        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if (!empty($quests) && $quests === 'true') {
            $tmpSQL = '';
            if (count($qpreids)) {
                $pkmn_in = '';
                $p = 1;
                foreach ($qpreids as $qpreid) {
                    $params[':pqry_' . $p . "_"] = $qpreid;
                    $pkmn_in .= ':pqry_' . $p . "_,";
                    $p++;
                }
                $pkmn_in = substr($pkmn_in, 0, -1);
                $tmpSQL .= "quest_pokemon_id IN ( $pkmn_in )";
            }
            if (count($qireids)) {
                $item_in = '';
                $i = 1;
                foreach ($qireids as $qireid) {
                    $params[':iqry_' . $i . "_"] = $qireid;
                    $item_in .= ':iqry_' . $i . "_,";
                    $i++;
                }
                $item_in = substr($item_in, 0, -1);
                $tmpSQL .= "quest_item_id IN ( $item_in )";
            }
            if ($reloaddustamount == "true") {
                $tmpSQL .= "(json_extract(json_extract(`quest_rewards`,'$[*].type'),'$[0]') = 3 AND json_extract(json_extract(`quest_rewards`,'$[*].info.amount'),'$[0]') > :amount)";
                $params[':amount'] = intval($dustamount);
            }
            $conds[] = $tmpSQL;
        }
        return $this->query_stops($conds, $params);
    }

    public function query_stops($conds, $params)
    {
        global $db;

        $query = "SELECT id AS pokestop_id,
        lat AS latitude,
        lon AS longitude,
        name AS pokestop_name,
        url,
        lure_expire_timestamp AS lure_expiration,
        incident_expire_timestamp AS incident_expiration,
        lure_id,
        grunt_type,
        quest_type,
        quest_timestamp,
        quest_target,
        quest_rewards,
        quest_pokemon_id,
        quest_item_id,
        json_extract(json_extract(`quest_conditions`,'$[*].type'),'$[0]') AS quest_condition_type,
        json_extract(json_extract(`quest_conditions`,'$[*].type'),'$[1]') AS quest_condition_type_1,
        json_extract(json_extract(`quest_conditions`,'$[*].info'),'$[0]') AS quest_condition_info,
        quest_reward_type,
        json_extract(json_extract(`quest_rewards`,'$[*].info.amount'),'$[0]') AS quest_reward_amount,
        json_extract(json_extract(`quest_rewards`,'$[*].info.amount'),'$[0]') AS quest_dust_amount,
        json_extract(json_extract(`quest_rewards`,'$[*].info.form_id'),'$[0]') AS quest_pokemon_formid,
        json_extract(json_extract(`quest_rewards`,'$[*].info.shiny'),'$[0]') AS quest_pokemon_shiny
        FROM pokestop
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $pokestops = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;

        foreach ($pokestops as $pokestop) {
            $item_pid = $pokestop["quest_item_id"];
            if ($item_pid == "0") {
                $item_pid = null;
                $pokestop["quest_item_id"] = null;
            }
            $mon_pid = $pokestop["quest_pokemon_id"];
            if ($mon_pid == "0") {
                $mon_pid = null;
                $pokestop["quest_pokemon_id"] = null;
            }
            $grunttype_pid = $pokestop["grunt_type"];
            if ($grunttype_pid == "0") {
                $grunttype_pid = null;
                $pokestop["grunt_type"] = null;
            }
            $pokestop["latitude"] = floatval($pokestop["latitude"]);
            $pokestop["longitude"] = floatval($pokestop["longitude"]);
            $pokestop["quest_type"] = intval($pokestop["quest_type"]);
            $pokestop["quest_condition_type"] = intval($pokestop["quest_condition_type"]);
            $pokestop["quest_condition_type_1"] = intval($pokestop["quest_condition_type_1"]);
            $pokestop["quest_reward_type"] = intval($pokestop["quest_reward_type"]);
            $pokestop["quest_target"] = intval($pokestop["quest_target"]);
            $pokestop["quest_pokemon_id"] = intval($pokestop["quest_pokemon_id"]);
            $pokestop["quest_pokemon_formid"] = intval($pokestop["quest_pokemon_formid"]);
            $pokestop["quest_item_id"] = intval($pokestop["quest_item_id"]);
            $pokestop["quest_reward_amount"] = intval($pokestop["quest_reward_amount"]);
            $pokestop["quest_dust_amount"] = intval($pokestop["quest_dust_amount"]);
            $pokestop["url"] = ! empty($pokestop["url"]) ? preg_replace("/^http:/i", "https:", $pokestop["url"]) : null;
            $pokestop["lure_expiration"] = $pokestop["lure_expiration"] * 1000;
            $pokestop["incident_expiration"] = $pokestop["incident_expiration"] * 1000;
            $pokestop["lure_id"] = $pokestop["lure_id"] - 500;
            $pokestop["quest_item_name"] = empty($item_pid) ? null : i8ln($this->items[$item_pid]["name"]);
            $pokestop["quest_pokemon_name"] = empty($mon_pid) ? null : i8ln($this->data[$mon_pid]["name"]);
            $pokestop["grunt_type_name"] = empty($grunttype_pid) ? null : i8ln($this->grunttype[$grunttype_pid]["type"]);
            $pokestop["grunt_type_gender"] = empty($grunttype_pid) ? null : i8ln($this->grunttype[$grunttype_pid]["grunt"]);
            $pokestop["encounters"] = empty($this->grunttype[$grunttype_pid]["encounters"]) ? null : $this->grunttype[$grunttype_pid]["encounters"];
            $pokestop["second_reward"] = empty($this->grunttype[$grunttype_pid]["second_reward"]) ? null : $this->grunttype[$grunttype_pid]["second_reward"];

            $data[] = $pokestop;
            unset($pokestops[$i]);
            $i++;
        }
        return $data;
    }
    public function get_spawnpoints($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        $conds = array();
        $params = array();
        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        if ($oSwLat != 0) {
            $conds[] = "NOT (lat > :oswLat AND lon > :oswLng AND lat < :oneLat AND lon < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat,lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        return $this->query_spawnpoints($conds, $params);
    }

    private function query_spawnpoints($conds, $params)
    {
        global $db;
        $query = "SELECT lat AS latitude,
        lon AS longitude,
        id AS spawnpoint_id,
        despawn_sec
        FROM spawnpoint
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $spawnpoints = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($spawnpoints as $spawnpoint) {
            $spawnpoint["latitude"] = floatval($spawnpoint["latitude"]);
            $spawnpoint["longitude"] = floatval($spawnpoint["longitude"]);
            $spawnpoint["time"] = intval($spawnpoint["despawn_sec"]);
            $data[] = $spawnpoint;
            unset($spawnpoints[$i]);
            $i++;
        }
        return $data;
    }
}
