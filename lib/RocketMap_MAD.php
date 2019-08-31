<?php

namespace Scanner;

class RocketMap_MAD extends RocketMap
{
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
            $gym["form"] = intval($gym["raid_pokemon_form"]);
            $gym["latitude"] = floatval($gym["latitude"]);
            $gym["longitude"] = floatval($gym["longitude"]);
            $gym["last_modified"] = $gym["last_modified"] * 1000;
            $gym["last_scanned"] = $gym["last_scanned"] * 1000;
            $gym["raid_start"] = $gym["raid_start"] * 1000;
            $gym["raid_end"] = $gym["raid_end"] * 1000;
            $gym["slots_available"] = intval($gym["slots_available"]);
            $gym["url"] = ! empty($gym["url"]) ? str_replace("http://", "https://images.weserv.nl/?url=", $gym["url"]) : null;
            $gym["park"] = intval($gym["park"]);
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
        (SUBSTRING_INDEX(SUBSTRING_INDEX(calc_endminsec, ':', 1), ' ', -1)*60) + (SUBSTRING_INDEX(SUBSTRING_INDEX(calc_endminsec, ':', -1), ' ', -1)) AS despawn_time,
        calc_endminsec AS duration
        FROM trs_spawn
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $spawnpoints = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($spawnpoints as $spawnpoint) {
            $spawnpoint["latitude"] = floatval($spawnpoint["latitude"]);
            $spawnpoint["longitude"] = floatval($spawnpoint["longitude"]);
            $spawnpoint["time"] = intval($spawnpoint["despawn_time"]);
            $spawnpoint["duration"] = intval($spawnpoint["duration"]);
            $data[] = $spawnpoint;
            unset($spawnpoints[$i]);
            $i++;
        }
        return $data;
    }

    public function get_stops($qpeids, $qieids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lured = false, $rocket = false, $quests, $dustamount)
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
        if ($rocket == "true") {
            $conds[] = "incident_expiration IS NOT NULL";
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

    public function get_stops_quest($qpreids, $qireids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $rocket, $quests, $dustamount, $reloaddustamount)
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
            } else {
                $tmpSQL .= "";
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
            } else {
                $tmpSQL .= "";
            }
            if ($reloaddustamount == "true") {
                $tmpSQL .= "tq.quest_stardust > :amount";
                $params[':amount'] = intval($dustamount);
        } else {
                $tmpSQL .= "";
            }
            $conds[] = $tmpSQL;
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
        tq.quest_target,
        tq.quest_reward,
        tq.quest_pokemon_id,
        tq.quest_item_id,
        json_extract(json_extract(`quest_condition`,'$[*].type'),'$[0]') AS quest_condition_type,
        json_extract(json_extract(`quest_condition`,'$[*].type'),'$[1]') AS quest_condition_type_1,
        json_extract(json_extract(`quest_condition`,'$[*].with_pokemon_category'),'$[0]') AS quest_condition_pokemon_ids,
        json_extract(json_extract(`quest_condition`,'$[*].with_throw_type'),'$[0]') AS quest_condition_with_throw_type,
        json_extract(json_extract(`quest_condition`,'$[*].with_pokemon_type'),'$[0]') AS quest_condition_with_pokemon_type,
        json_extract(json_extract(`quest_condition`,'$[*].with_raid_level'),'$[0]') AS quest_condition_with_raid_level,
        json_extract(json_extract(`quest_condition`,'$[*].with_item'),'$[0]') AS quest_condition_with_item,
        tq.quest_reward_type,
        json_extract(json_extract(`quest_reward`,'$[*].info'),'$[0]') AS quest_reward_info,
        json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.form_value'),'$[0]') AS quest_pokemon_formid,
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
            $pokestop["url"] = ! empty($pokestop["url"]) ? str_replace("http://", "https://images.weserv.nl/?url=", $pokestop["url"]) : null;
            $pokestop["quest_type"] = intval($pokestop["quest_type"]);
            $pokestop["quest_condition_type"] = intval($pokestop["quest_condition_type"]);
            $pokestop["quest_reward_type"] = intval($pokestop["quest_reward_type"]);
            $pokestop["quest_target"] = intval($pokestop["quest_target"]);
            $pokestop["quest_pokemon_id"] = intval($pokestop["quest_pokemon_id"]);
            $pokestop["quest_pokemon_formid"] = intval($pokestop["quest_pokemon_formid"]);
            $pokestop["quest_item_id"] = intval($pokestop["quest_item_id"]);
            $pokestop["quest_reward_amount"] = intval($pokestop["quest_reward_amount"]);
            $pokestop["quest_dust_amount"] = intval($pokestop["quest_dust_amount"]);
            $pokestop["quest_item_name"] = empty($item_pid) ? null : i8ln($this->items[$item_pid]["name"]);
            $pokestop["quest_pokemon_name"] = empty($mon_pid) ? null : i8ln($this->data[$mon_pid]["name"]);
            if (!empty($pokestop["quest_condition_pokemon_ids"])) {
                $pokestop["quest_condition_info"] = str_replace('"category_name": "", ',"",$pokestop["quest_condition_pokemon_ids"]);
            } else if (!empty($pokestop["quest_condition_with_pokemon_type"])) {
                $pokestop["quest_condition_info"] = str_replace("pokemon_type","pokemon_type_ids",$pokestop["quest_condition_with_pokemon_type"]);
            } else if (!empty($pokestop["quest_condition_with_throw_type"])) {
                $pokestop["quest_condition_info"] = str_replace("throw_type","throw_type_id",$pokestop["quest_condition_with_throw_type"]);
            } else if (!empty($pokestop["quest_condition_with_raid_level"])) {
                $pokestop["quest_condition_info"] = str_replace("raid_level","raid_levels",$pokestop["quest_condition_with_raid_level"]);
            } else if (!empty($pokestop["quest_condition_with_item"])) {
                $pokestop["quest_condition_info"] = str_replace("item","item_id",$pokestop["quest_condition_with_item"]);
            } else {
                $pokestop["quest_condition_info"] = null;
            }
            $data[] = $pokestop;
            unset($pokestops[$i]);
            $i++;
        }
        return $data;
    }
}
