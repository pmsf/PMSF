<?php

namespace Scanner;

class Monocle_PMSF extends Monocle
{
    public function get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $encId = 0)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $select = "pokemon_id, expire_timestamp AS disappear_time, encounter_id, lat AS latitude, lon AS longitude, gender, form, weather_boosted_condition";
        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", weight, atk_iv AS individual_attack, def_iv AS individual_defense, sta_iv AS individual_stamina, move_1, move_2, cp, level";
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
                $conds[] = '(atk_iv' . $float . ' + def_iv' . $float . ' + sta_iv' . $float . ') >= ' . $minIv;
            } else {
                $conds[] = '((atk_iv' . $float . ' + def_iv' . $float . ' + sta_iv' . $float . ') >= ' . $minIv . ' OR pokemon_id IN(' . $exMinIv . ') )';
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
            $encSql = " OR (encounter_id = " . $encId . " AND lat > '" . $swLat . "' AND lon > '" . $swLng . "' AND lat < '" . $neLat . "' AND lon < '" . $neLng . "' AND expire_timestamp > '" . $params[':time'] . "')";
        }
        return $this->query_active($select, $conds, $params, $encSql);
    }

    public function get_active_by_id($ids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $select = "pokemon_id, expire_timestamp AS disappear_time, encounter_id, lat AS latitude, lon AS longitude, gender, form, weather_boosted_condition";

        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", weight, atk_iv AS individual_attack, def_iv AS individual_defense, sta_iv AS individual_stamina, move_1, move_2, cp, level";
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
                $conds[] = '(atk_iv' . $float . ' + def_iv' . $float . ' + sta_iv' . $float . ') >= ' . $minIv;
            } else {
                $conds[] = '((atk_iv' . $float . ' + def_iv' . $float . ' + sta_iv' . $float . ') >= ' . $minIv . ' OR pokemon_id IN(' . $exMinIv . ') )';
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

    public function get_stops($qpeids, $qieids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $rocket, $quests, $dustamount)
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
        return $this->query_stops($conds, $params);
    }

    public function get_stops_quest($qpreids, $qireids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $quests, $dustamount, $reloaddustamount)
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
                $tmpSQL .= "quest_item_id IN ( $item_in )";
            } else {
                $tmpSQL .= "";
            }
            if ($reloaddustamount == "true") {
                $tmpSQL .= "(json_extract(json_extract(`quest_rewards`,'$[*].type'),'$[0]') = 3 AND json_extract(json_extract(`quest_rewards`,'$[*].info.amount'),'$[0]') > :amount)";
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
        global $db, $noTrainerName, $noManualQuests;

        $query = "SELECT external_id AS pokestop_id,
        expires AS lure_expiration,
        deployer AS lure_user,
        name AS pokestop_name,
        url,
        lat AS latitude,
        lon AS longitude,
        quest_type,
        quest_timestamp,
        quest_target,
        quest_rewards,
        quest_pokemon_id,
        quest_item_id,
        json_extract(json_extract(`quest_conditions`,'$[*].type'),'$[0]') AS quest_condition_type,
        json_extract(json_extract(`quest_conditions`,'$[*].info'),'$[0]') AS quest_condition_info,
        quest_reward_type,
        json_extract(json_extract(`quest_rewards`,'$[*].info'),'$[0]') AS quest_reward_info,
        json_extract(json_extract(`quest_rewards`,'$[*].info.amount'),'$[0]') AS quest_reward_amount,
        json_extract(json_extract(`quest_rewards`,'$[*].info.amount'),'$[0]') AS quest_dust_amount,
        json_extract(json_extract(`quest_rewards`,'$[*].info.form_id'),'$[0]') AS quest_pokemon_formid,
        json_extract(json_extract(`quest_rewards`,'$[*].info.shiny'),'$[0]') AS quest_pokemon_shiny
        FROM pokestops
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $pokestops = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($pokestops as $pokestop) {
            $pokestop["latitude"] = floatval($pokestop["latitude"]);
            $pokestop["longitude"] = floatval($pokestop["longitude"]);
            $pokestop["url"] = ! empty($pokestop["url"]) ? preg_replace("/^http:/i", "https:", $pokestop["url"]) : null;
            $pokestop["quest_type"] = intval($pokestop["quest_type"]);
            $pokestop["quest_condition_type"] = intval($pokestop["quest_condition_type"]);
            $pokestop["quest_reward_type"] = intval($pokestop["quest_reward_type"]);
            $pokestop["quest_target"] = intval($pokestop["quest_target"]);
            $pokestop["quest_pokemon_id"] = intval($pokestop["quest_pokemon_id"]);
            $pokestop["quest_pokemon_formid"] = intval($pokestop["quest_pokemon_formid"]);
            $pokestop["quest_item_id"] = intval($pokestop["quest_item_id"]);
            $pokestop["quest_reward_amount"] = intval($pokestop["quest_reward_amount"]);
            $pokestop["quest_dust_amount"] = intval($pokestop["quest_dust_amount"]);
            $pokestop["quest_pokemon_name"] = i8ln($this->data[$pokestop["quest_pokemon_id"]]['name']);
            $pokestop["quest_item_name"] = i8ln($this->items[$pokestop["quest_item_id"]]['name']);

            $data[] = $pokestop;

            unset($pokestops[$i]);
            $i++;
        }
        return $data;
    }

    public function get_gyms($swLat, $swLng, $neLat, $neLng, $exEligible = false, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        $conds = array();
        $params = array();

        $conds[] = "f.lat > :swLat AND f.lon > :swLng AND f.lat < :neLat AND f.lon < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        if ($oSwLat != 0) {
            $conds[] = "NOT (f.lat > :oswLat AND f.lon > :oswLng AND f.lat < :oneLat AND f.lon < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(f.lat,f.lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        if ($exEligible === "true") {
            $conds[] = "(park IS NOT NULL)";
        }

        return $this->query_gyms($conds, $params);
    }

    public function query_gyms($conds, $params)
    {
        global $db;

        $query = "SELECT f.external_id AS gym_id,
        fs.last_modified AS last_modified,
        updated AS last_scanned,
        f.lat AS latitude,
        f.lon AS longitude,
        f.name,
        f.url,
        f.park,
        fs.team AS team_id,
        fs.slots_available,
        r.level AS raid_level,
        r.pokemon_id AS raid_pokemon_id,
        r.time_battle AS raid_start,
        r.time_end AS raid_end,
        r.cp AS raid_pokemon_cp,
        r.move_1 AS raid_pokemon_move_1,
        r.move_2 AS raid_pokemon_move_2,
        r.form AS raid_pokemon_form
        FROM forts f
        LEFT JOIN fort_sightings fs ON fs.fort_id = f.id
        LEFT JOIN raids r ON r.fort_id = f.id
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
            $gym["slots_available"] = intval($gym["slots_available"]);
            $gym["last_modified"] = $gym["last_modified"] * 1000;
            $gym["last_scanned"] = $gym["last_scanned"] * 1000;
            $gym["raid_start"] = $gym["raid_start"] * 1000;
            $gym["raid_end"] = $gym["raid_end"] * 1000;
            $gym["url"] = ! empty($gym["url"]) ? preg_replace("/^http:/i", "https:", $gym["url"]) : null;
            $data[] = $gym;

            unset($gyms[$i]);
            $i++;
        }
        return $data;
    }
}
