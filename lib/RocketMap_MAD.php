<?php

namespace Scanner;

class RocketMap_MAD extends RocketMap
{
    public function get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $encId = 0)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $select = "ts.calc_endminsec AS expire_timestamp_verified, pokemon_id, Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS disappear_time, encounter_id, p.latitude, p.longitude, gender, form, weather_boosted_condition, costume";
        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", weight, height, individual_attack, individual_defense, individual_stamina, move_1, move_2, cp, cp_multiplier";
        }

        $conds[] = "p.latitude > :swLat AND p.longitude > :swLng AND p.latitude < :neLat AND p.longitude < :neLng AND disappear_time > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date->setTimestamp(time());
        $params[':time'] = date_format($date, 'Y-m-d H:i:s');

        if ($oSwLat != 0) {
            $conds[] = "NOT (p.latitude > :oswLat AND p.longitude > :oswLng AND p.latitude < :oneLat AND p.longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(p.latitude,p.longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $date->setTimestamp($tstamp);
            $conds[] = "last_modified > :lastUpdated";
            $params[':lastUpdated'] = date_format($date, 'Y-m-d H:i:s');
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
                $conds[] = '(individual_attack' . $float . ' + individual_defense' . $float . ' + individual_stamina' . $float . ') >= ' . $minIv;
            } else {
                $conds[] = '((individual_attack' . $float . ' + individual_defense' . $float . ' + individual_stamina' . $float . ') >= ' . $minIv . ' OR pokemon_id IN(' . $exMinIv . ') )';
            }
        }
        if (!empty($minLevel) && !is_nan((float)$minLevel) && $minLevel != 0) {
            if (empty($exMinIv)) {
                $conds[] = 'cp_multiplier >= ' . $this->cpMultiplier[$minLevel];
            } else {
                $conds[] = '(cp_multiplier >= ' . $this->cpMultiplier[$minLevel] . ' OR pokemon_id IN(' . $exMinIv . ') )';
            }
        }
        $encSql = '';
        if ($encId != 0) {
            $encSql = " OR (encounter_id = " . $encId . " AND p.latitude > '" . $swLat . "' AND p.longitude > '" . $swLng . "' AND p.latitude < '" . $neLat . "' AND p.longitude < '" . $neLng . "' AND disappear_time > '" . $params[':time'] . "')";
        }
        return $this->query_active($select, $conds, $params, $encSql);
    }

    public function get_active_by_id($ids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $select = "ts.calc_endminsec AS expire_timestamp_verified, pokemon_id, Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS disappear_time, encounter_id, p.latitude, p.longitude, gender, form, weather_boosted_condition, costume";
        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", weight, height, individual_attack, individual_defense, individual_stamina, move_1, move_2, cp, cp_multiplier";
        }

        $conds[] = "p.latitude > :swLat AND p.longitude > :swLng AND p.latitude < :neLat AND p.longitude < :neLng AND disappear_time > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date->setTimestamp(time());
        $params[':time'] = date_format($date, 'Y-m-d H:i:s');

        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(p.latitude,p.longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
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
                $conds[] = '(individual_attack' . $float . ' + individual_defense' . $float . ' + individual_stamina' . $float . ') >= ' . $minIv;
            } else {
                $conds[] = '((individual_attack' . $float . ' + individual_defense' . $float . ' + individual_stamina' . $float . ') >= ' . $minIv . ' OR pokemon_id IN(' . $exMinIv . ') )';
            }
        }
        if (!empty($minLevel) && !is_nan((float)$minLevel) && $minLevel != 0) {
            if (empty($exMinIv)) {
                $conds[] = 'cp_multiplier >= ' . $this->cpMultiplier[$minLevel];
            } else {
                $conds[] = '(cp_multiplier >= ' . $this->cpMultiplier[$minLevel] . ' OR pokemon_id IN(' . $exMinIv . ') )';
            }
        }
        return $this->query_active($select, $conds, $params);
    }

    public function query_active($select, $conds, $params, $encSql = '')
    {
        global $db;

        $query = "SELECT :select
        FROM pokemon p
        LEFT JOIN trs_spawn ts ON ts.spawnpoint = p.spawnpoint_id
        WHERE :conditions";

        $query = str_replace(":select", $select, $query);
        $query = str_replace(":conditions", '(' . join(" AND ", $conds) . ')' . $encSql, $query);
        $pokemons = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($pokemons as $pokemon) {
            $pokemon["latitude"] = floatval($pokemon["latitude"]);
            $pokemon["longitude"] = floatval($pokemon["longitude"]);
            $pokemon["expire_timestamp_verified"] = isset($pokemon["expire_timestamp_verified"]) ? 1 : null;
            $pokemon["disappear_time"] = $pokemon["disappear_time"] * 1000;

            $pokemon["weight"] = isset($pokemon["weight"]) ? floatval($pokemon["weight"]) : null;
            $pokemon["height"] = isset($pokemon["height"]) ? floatval($pokemon["height"]) : null;

            $pokemon["individual_attack"] = isset($pokemon["individual_attack"]) ? intval($pokemon["individual_attack"]) : null;
            $pokemon["individual_defense"] = isset($pokemon["individual_defense"]) ? intval($pokemon["individual_defense"]) : null;
            $pokemon["individual_stamina"] = isset($pokemon["individual_stamina"]) ? intval($pokemon["individual_stamina"]) : null;
            $pokemon["weather_boosted_condition"] = intval($pokemon["weather_boosted_condition"]);

            $pokemon["pokemon_id"] = intval($pokemon["pokemon_id"]);
            $pokemon["pokemon_name"] = i8ln($this->data[$pokemon["pokemon_id"]]['name']);
            $pokemon["pokemon_rarity"] = i8ln($this->data[$pokemon["pokemon_id"]]['rarity']);
            $types = $this->data[$pokemon["pokemon_id"]]["types"];
            foreach ($types as $k => $v) {
                $types[$k]['type'] = $v['type'];
            }
            $pokemon["pokemon_types"] = $types;
            $pokemon["cp_multiplier"] = isset($pokemon["cp_multiplier"]) ? floatval($pokemon["cp_multiplier"]) : null;
            if (isset($pokemon["form"]) && $pokemon["form"] > 0) {
                $forms = $this->data[$pokemon["pokemon_id"]]["forms"];
                foreach ($forms as $f => $v) {
                    if ($pokemon["form"] === $v['protoform']) {
                        $pokemon["form_name"] = $v['nameform'];
                    }
                }
            }
            $data[] = $pokemon;

            unset($pokemons[$i]);
            $i++;
        }
        return $data;
    }

    // This is based on assumption from the last version I saw
    public function get_weather_by_cell_id($cell_id)
    {
        global $db;
        $query = "SELECT s2_cell_id, gameplay_weather FROM weather WHERE s2_cell_id = :cell_id";
        $params = [':cell_id' => intval((float)$cell_id)]; // use float to intval because RM is signed int
        $weather_info = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        if ($weather_info) {
            // force re-bind of gameplay_weather to condition
            $weather_info[0]['condition'] = $weather_info[0]['gameplay_weather'];
            unset($weather_info[0]['gameplay_weather']);
            return $weather_info[0];
        } else {
            return null;
        }
    }

    public function get_weather($updated = null)
    {
        global $db;
        $query = "SELECT s2_cell_id, gameplay_weather FROM weather";
        $weathers = $db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        foreach ($weathers as $weather) {
            $weather['s2_cell_id'] = sprintf("%u", $weather['s2_cell_id']);
            $data["weather_" . $weather['s2_cell_id']] = $weather;
            $data["weather_" . $weather['s2_cell_id']]['condition'] = $data["weather_" . $weather['s2_cell_id']]['gameplay_weather'];
            unset($data["weather_" . $weather['s2_cell_id']]['gameplay_weather']);
        }
        return $data;
    }

    public function get_gyms($swLat, $swLng, $neLat, $neLng, $exEligible = false, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        $conds = array();
        $params = array();

        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        if ($oSwLat != 0) {
            $conds[] = "NOT (latitude > :oswLat AND longitude > :oswLng AND latitude < :oneLat AND longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $conds[] = "gym.last_scanned > :lastUpdated";
            $params[':lastUpdated'] = date_format($date, 'Y-m-d H:i:s');
        }
        if ($exEligible === "true") {
            $conds[] = "(is_ex_raid_eligible = 1)";
        }

        return $this->query_gyms($conds, $params);
    }

    public function query_gyms($conds, $params)
    {
        global $db;

        $query = "SELECT gym.gym_id, 
        latitude, 
        longitude, 
        slots_available, 
        Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified, 
        Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned, 
        team_id, 
        name,
        url,
        is_ex_raid_eligible AS park,
        level AS raid_level, 
        raid.pokemon_id AS raid_pokemon_id, 
        raid.form AS raid_pokemon_form, 
        raid.costume AS raid_pokemon_costume,
        raid.gender AS raid_pokemon_gender,
        cp AS raid_pokemon_cp, 
        move_1 AS raid_pokemon_move_1, 
        move_2 AS raid_pokemon_move_2, 
        Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) AS raid_start, 
        Unix_timestamp(Convert_tz(end, '+00:00', @@global.time_zone)) AS raid_end 
        FROM gym 
        LEFT JOIN gymdetails 
        ON gym.gym_id = gymdetails.gym_id 
        LEFT JOIN raid 
        ON gym.gym_id = raid.gym_id 
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $gyms = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($gyms as $gym) {
            $raid_pid = $gym["raid_pokemon_id"];
            if ($raid_pid == "0") {
                $raid_pid = null;
                $gym["raid_pokemon_id"] = null;
            }
            $gym["team_id"] = intval($gym["team_id"]);
            $gym["pokemon"] = [];
            $gym["raid_pokemon_name"] = empty($raid_pid) ? null : i8ln($this->data[$raid_pid]["name"]);
            $gym["raid_pokemon_gender"] = intval($gym["raid_pokemon_gender"]);
            $gym["raid_pokemon_costume"] = intval($gym["raid_pokemon_costume"]);
            $gym["form"] = intval($gym["raid_pokemon_form"]);
            $gym["latitude"] = floatval($gym["latitude"]);
            $gym["longitude"] = floatval($gym["longitude"]);
            $gym["last_modified"] = $gym["last_modified"] * 1000;
            $gym["last_scanned"] = $gym["last_scanned"] * 1000;
            $gym["raid_start"] = $gym["raid_start"] * 1000;
            $gym["raid_end"] = $gym["raid_end"] * 1000;
            $gym["slots_available"] = intval($gym["slots_available"]);
            $gym["url"] = ! empty($gym["url"]) ? preg_replace("/^http:/i", "https:", $gym["url"]) : null;
            $gym["park"] = intval($gym["park"]);
            if (isset($gym["form"]) && $gym["form"] > 0) {
                $forms = $this->data[$gym["raid_pokemon_id"]]["forms"];
                foreach ($forms as $f => $v) {
                    if ($gym["raid_pokemon_form"] === $v['protoform']) {
                        $gym["form_name"] = $v['nameform'];
                    }
                }
            }
            $data[] = $gym;

            unset($gyms[$i]);
            $i++;
        }
        return $data;
    }

    public function get_spawnpoints($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        $conds = array();
        $params = array();
        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        if ($oSwLat != 0) {
            $conds[] = "NOT (latitude > :oswLat AND longitude > :oswLng AND latitude < :oneLat AND longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $conds[] = "last_scanned > from_unixtime(:lastUpdated)";
            $params[':lastUpdated'] = $tstamp;
        }
        return $this->query_spawnpoints($conds, $params);
    }

    private function query_spawnpoints($conds, $params)
    {
        global $db;
        $query = "SELECT latitude,
        longitude,
        spawnpoint AS spawnpoint_id,
        (SUBSTRING_INDEX(SUBSTRING_INDEX(calc_endminsec, ':', 1), ' ', -1)*60) + (SUBSTRING_INDEX(SUBSTRING_INDEX(calc_endminsec, ':', -1), ' ', -1)) AS time
        FROM trs_spawn
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $spawnpoints = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($spawnpoints as $spawnpoint) {
            $spawnpoint["latitude"] = floatval($spawnpoint["latitude"]);
            $spawnpoint["longitude"] = floatval($spawnpoint["longitude"]);
            $spawnpoint["time"] = intval($spawnpoint["time"]);
            $data[] = $spawnpoint;
            unset($spawnpoints[$i]);
            $i++;
        }
        return $data;
    }

    public function get_stops($geids, $qpeids, $qieids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lured = false, $rocket = false, $quests, $dustamount)
    {
        $conds = array();
        $params = array();
        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        if ($oSwLat != 0) {
            $conds[] = "NOT (latitude > :oswLat AND longitude > :oswLng AND latitude < :oneLat AND longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }

        if ($lured == "true") {
            $conds[] = "active_fort_modifier IS NOT NULL";
        }
        if (!empty($rocket) && $rocket === 'true') {
            $rocketSQL = '';
            if (count($geids)) {
                $rocket_in = '';
                $r = 1;
                foreach ($geids as $geid) {
                    $params[':rqry_' . $r . "_"] = $geid;
                    $rocket_in .= ':rqry_' . $r . "_,";
                    $r++;
                }
                $rocket_in = substr($rocket_in, 0, -1);
                $rocketSQL .= "incident_grunt_type NOT IN ( $rocket_in )";
            } else {
                $rocketSQL .= "incident_grunt_type > 0";
            }
            $conds[] = "" . $rocketSQL . "";
        }
        if ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $conds[] = "last_updated > :lastUpdated";
            $params[':lastUpdated'] = date_format($date, 'Y-m-d H:i:s');
        }
        return $this->query_stops($conds, $params);
    }

    public function get_stops_quest($greids, $qpreids, $qireids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $rocket, $quests, $dustamount, $reloaddustamount)
    {
        $conds = array();
        $params = array();
        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
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
                $tmpSQL .= "tq.quest_pokemon_id IN ( $pkmn_in )";
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
                $tmpSQL .= "tq.quest_item_id IN ( $item_in )";
            }
            if ($reloaddustamount == "true") {
                $tmpSQL .= "tq.quest_stardust > :amount";
                $params[':amount'] = intval($dustamount);
            }
            $conds[] = $tmpSQL;
        }
        if (!empty($rocket) && $rocket === 'true') {
            $rocketSQL = '';
            if (count($greids)) {
                $rocket_in = '';
                $r = 1;
                foreach ($greids as $greid) {
                    $params[':rqry_' . $r . "_"] = $greid;
                    $rocket_in .= ':rqry_' . $r . "_,";
                    $r++;
                }
                $rocket_in = substr($rocket_in, 0, -1);
                $rocketSQL .= "incident_grunt_type IN ( $rocket_in )";
            }
            $conds[] = "" . $rocketSQL . "";
        }
        return $this->query_stops($conds, $params);
    }

    public function query_stops($conds, $params)
    {
        global $db;

        $query = "SELECT Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
        Unix_timestamp(Convert_tz(incident_expiration, '+00:00', @@global.time_zone)) AS incident_expiration,
        pokestop_id,
        latitude,
        name AS pokestop_name,
        image AS url,
        longitude,
        active_fort_modifier AS lure_id,
        incident_grunt_type AS grunt_type,
        tq.quest_type,
        tq.quest_timestamp,
        tq.quest_reward,
        tq.quest_pokemon_id,
        tq.quest_item_id,
        tq.quest_task,
        tq.quest_reward_type,
        tq.quest_pokemon_form_id AS quest_pokemon_formid,
        json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.is_shiny'),'$[0]') AS quest_pokemon_shiny,
        tq.quest_item_amount AS quest_reward_amount,
        tq.quest_stardust AS quest_dust_amount
        FROM pokestop p
        LEFT JOIN trs_quest tq ON tq.GUID = p.pokestop_id
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
            $pokestop["lure_expiration"] = !empty($pokestop["lure_expiration"]) ? $pokestop["lure_expiration"] * 1000 : null;
            $pokestop["incident_expiration"] = !empty($pokestop["incident_expiration"]) ? $pokestop["incident_expiration"] * 1000 : null;
            $pokestop["grunt_type_name"] = empty($grunttype_pid) ? null : i8ln($this->grunttype[$grunttype_pid]["type"]);
            $pokestop["grunt_type_gender"] = empty($grunttype_pid) ? null : i8ln($this->grunttype[$grunttype_pid]["grunt"]);
            $pokestop["encounters"] = empty($this->grunttype[$grunttype_pid]["encounters"]) ? null : $this->grunttype[$grunttype_pid]["encounters"];
            $pokestop["second_reward"] = empty($this->grunttype[$grunttype_pid]["second_reward"]) ? null : $this->grunttype[$grunttype_pid]["second_reward"];
            $pokestop["lure_id"] = $pokestop["lure_id"] - 500;
            $pokestop["url"] = ! empty($pokestop["url"]) ? preg_replace("/^http:/i", "https:", $pokestop["url"]) : null;
            $pokestop["quest_type"] = intval($pokestop["quest_type"]);
            $pokestop["quest_condition_type"] = 0;
            $pokestop["quest_condition_type_1"] = 0;
            $pokestop["quest_reward_type"] = intval($pokestop["quest_reward_type"]);
            $pokestop["quest_target"] = 0;
            $pokestop["quest_pokemon_id"] = intval($pokestop["quest_pokemon_id"]);
            $pokestop["quest_pokemon_formid"] = intval($pokestop["quest_pokemon_formid"]);
            $pokestop["quest_item_id"] = intval($pokestop["quest_item_id"]);
            $pokestop["quest_reward_amount"] = intval($pokestop["quest_reward_amount"]);
            $pokestop["quest_dust_amount"] = intval($pokestop["quest_dust_amount"]);
            $pokestop["quest_item_name"] = empty($item_pid) ? null : i8ln($this->items[$item_pid]["name"]);
            $pokestop["quest_pokemon_name"] = empty($mon_pid) ? null : i8ln($this->data[$mon_pid]["name"]);
            $pokestop["quest_condition_info"] = null;
            $data[] = $pokestop;
            unset($pokestops[$i]);
            $i++;
        }
        return $data;
    }

    public function get_scanlocation($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        $conds = array();
        $params = array();
        $conds[] = "origin is not :null";
        $params[':null'] = null;
        return $this->query_scanlocation($conds, $params);
    }

    public function generated_exclude_list($type)
    {
        global $db;
        if ($type === 'pokemonlist') {
            $pokestops = $db->query("SELECT distinct quest_pokemon_id FROM trs_quest WHERE quest_pokemon_id > 0 AND DATE(FROM_UNIXTIME(quest_timestamp)) = CURDATE() order by quest_pokemon_id;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['quest_pokemon_id'];
            }
        } elseif ($type === 'itemlist') {
            $pokestops = $db->query("SELECT distinct quest_item_id FROM trs_quest WHERE quest_item_id > 0 AND DATE(FROM_UNIXTIME(quest_timestamp)) = CURDATE() order by quest_item_id;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['quest_item_id'];
            }
        } elseif ($type === 'gruntlist') {
            $pokestops = $db->query("SELECT distinct incident_grunt_type FROM pokestop WHERE incident_grunt_type > 0 AND incident_expiration > UTC_TIMESTAMP() order by incident_grunt_type;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['incident_grunt_type'];
            }
        }
        return $data;
    }
    private function query_scanlocation($conds, $params)
    {
        global $db;
        $query = "SELECT currentPos AS latLon,
        Unix_timestamp(lastProtoDateTime) AS last_seen,
        origin AS uuid,
        routemanager AS instance_name
        FROM trs_status
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $scanlocations = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($scanlocations as $scanlocation) {
            $parts = explode(", ", $scanlocation["latLon"]);
            $scanlocation["latitude"] = floatval($parts['0']);
            $scanlocation["longitude"] = floatval($parts['1']);
            $data[] = $scanlocation;
            unset($scanlocations[$i]);
            $i++;
        }
        return $data;
    }
}
