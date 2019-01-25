<?php

namespace Scanner;

class RDM extends Scanner
{
    public function get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $encId = 0)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $select = "pokemon_id, expire_timestamp AS disappear_time, id AS encounter_id, spawn_id, lat AS latitude, lon AS longitude, gender, form, weight, weather AS weather_boosted_condition";
        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", atk_iv AS individual_attack, def_iv AS individual_defense, sta_iv AS individual_stamina, move_1, move_2, cp, level";
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

        $select = "pokemon_id, expire_timestamp AS disappear_time, id AS encounter_id, spawn_id, lat AS latitude, lon AS longitude, gender, form, weight, weather AS weather_boosted_condition";

        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", atk_iv AS individual_attack, def_iv AS individual_defense, sta_iv AS individual_stamina, move_1, move_2, cp, level";
        }

        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng AND expire_timestamp > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $params[':time'] = time();
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
        $lastlon=0;
        $lasti = 0;
        
        foreach ($pokemons as $pokemon) {
            // Jitter pokemon when they have no spawn_id
            if ( empty($pokemon['spawn_id'])) {
                $pokemon["latitude"] = floatval($pokemon["latitude"]);
                $pokemon["longitude"] = floatval($pokemon["longitude"]);
                $lastlat = floatval($pokemon["latitude"]);
                $lastlon = floatval($pokemon["longitude"]);
                if (abs($pokemon["latitude"] - $lastlat) < 0.0001 && abs($pokemon["longitude"] - $lastlon) < 0.0001){
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
                            $types[$ft]['type'] = i8ln($v['type']);
                        }
                        $pokemon["pokemon_types"] = $types;
                    } else if ($pokemon["form"] === $v['assetsform']) {
                        $types = $v['formtypes'];
                        foreach ($v['formtypes'] as $ft => $v) {
                            $types[$ft]['type'] = i8ln($v['type']);
                        }
                        $pokemon["pokemon_types"] = $types;
                    }
                }
            } else {
                $types = $this->data[$pokemon["pokemon_id"]]["types"];
                foreach ($types as $k => $v) {
                    $types[$k]['type'] = i8ln($v['type']);
                }
                $pokemon["pokemon_types"] = $types;
            }
            $data[] = $pokemon;

            unset($pokemons[$i]);
            $i++;
        }
        return $data;
    }

    public function get_stops($qpeids, $qieids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $quests, $dustamount)
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
        if (!empty($lures) && $lures === 'true') {
            $conds[] = "lure_expire_timestamp > :time";
            $params[':time'] = time();
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        return $this->query_stops($conds, $params);
    }

    public function query_stops($conds, $params)
    {
        global $db, $noTrainerName, $noManualQuests;

        $query = "SELECT id AS pokestop_id,
        lat AS latitude,
        lon AS longitude,
        name AS pokestop_name,
        url,
        lure_expire_timestamp AS lure_expiration
        FROM pokestop
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $pokestops = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($pokestops as $pokestop) {
            $pokestop["latitude"] = floatval($pokestop["latitude"]);
            $pokestop["longitude"] = floatval($pokestop["longitude"]);
            $pokestop["url"] = str_replace("http://", "https://images.weserv.nl/?url=", $pokestop["url"]);
            if ($noTrainerName === true) {
                // trainer names hidden, so don't show trainer who lured
                unset($pokestop["lure_user"]);
            }
            $data[] = $pokestop;

            unset($pokestops[$i]);
            $i++;
        }
        return $data;
    }

    public function get_gym($gymId)
    {
        $conds = array();
        $params = array();

        $conds[] = "id = :gymId";
        $params[':gymId'] = $gymId;

        $gyms = $this->query_gyms($conds, $params);
        $gym = $gyms[0];

        $select = "guarding_pokemon_id";
        global $noTrainerName, $noTrainerLevel;
        if (!$noTrainerName) {
            $select .= ", owner_name AS trainer_name";
        }
        if (!$noTrainerLevel) {
            $select .= ", owner_level AS trainer_level";
        }
        $gym["pokemon"] = $this->query_gym_defenders($gymId, $select);
        $gym["pokemon_cp"] = !empty($gym["pokemon_cp"]) ? $gym["pokemon_cp"] : null;
        $gym["move_1"] = !empty($gym["move_1"]) ? $gym["move_1"] : null;
        $gym["move_2"] = !empty($gym["move_2"]) ? $gym["move_2"] : null;
        $gym["nickname"] = !empty($gym["nickname"]) ? $gym["nickname"] : null;
        $gym["iv_attack"] = !empty($gym["iv_attack"]) ? $gym["iv_attack"] : null;
        $gym["iv_defense"] = !empty($gym["iv_defense"]) ? $gym["iv_defense"] : null;
        $gym["iv_stamina"] = !empty($gym["iv_stamina"]) ? $gym["iv_defense"] : null;
        return $gym;
    }

    public function get_gyms($swLat, $swLng, $neLat, $neLng, $exEligible = false, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
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
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        if ($exEligible === "true") {
            $conds[] = "(ex_raid_eligible IS NOT NULL AND ex_raid_eligible != '0')";
        }

        return $this->query_gyms($conds, $params);
    }

    public function query_gyms($conds, $params)
    {
        global $db;

        $query = "SELECT id AS gym_id,
        lat AS latitude,
        lon AS longitude,
        name,
        url,
        last_modified_timestamp AS last_modified,
        raid_end_timestamp AS raid_end,
        raid_battle_timestamp AS raid_start,
        updated AS last_scanned,
        raid_pokemon_id,
        guarding_pokemon_id AS guard_pokemon_id,
        availble_slots AS slots_available,
        team_id,
        raid_level,
        raid_pokemon_move_1,
        raid_pokemon_move_2,
        raid_pokemon_form AS form,
        raid_pokemon_cp,
        ex_raid_eligible AS park
        FROM gym
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $gyms = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($gyms as $gym) {
            $guard_pid = $gym["guard_pokemon_id"];
            if ($guard_pid == "0") {
                $guard_pid = null;
                $gym["guard_pokemon_id"] = null;
            }
            $raid_pid = $gym["raid_pokemon_id"];
            if ($raid_pid == "0") {
                $raid_pid = null;
                $gym["raid_pokemon_id"] = null;
            }
            $gym["team_id"] = intval($gym["team_id"]);
            $gym["pokemon"] = [];
            $gym["guard_pokemon_name"] = empty($guard_pid) ? null : i8ln($this->data[$guard_pid]["name"]);
            $gym["raid_pokemon_name"] = empty($raid_pid) ? null : i8ln($this->data[$raid_pid]["name"]);
            $gym["latitude"] = floatval($gym["latitude"]);
            $gym["longitude"] = floatval($gym["longitude"]);
            //$gym["sponsor"] = intval($gym["sponsor"]);
            $gym["slots_available"] = intval($gym["slots_available"]);
            $gym["last_modified"] = $gym["last_modified"] * 1000;
            $gym["last_scanned"] = $gym["last_scanned"] * 1000;
            $gym["raid_start"] = $gym["raid_start"] * 1000;
            $gym["raid_end"] = $gym["raid_end"] * 1000;
            $gym["sponsor"] = !empty($gym["sponsor"]) ? $gym["sponsor"] : null;
            $gym["url"] = str_replace("http://", "https://images.weserv.nl/?url=", $gym["url"]);
            $data[] = $gym;

            unset($gyms[$i]);
            $i++;
        }
        return $data;
    }

    private function query_gym_defenders($gymId, $select)
    {
        global $db;


        $query = "SELECT :select
      FROM gym
      WHERE id = :gymId";

        $query = str_replace(":select", $select, $query);
        $gym_defenders = $db->query($query, [":gymId" => $gymId])->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($gym_defenders as $defender) {
            $pid = $defender["pokemon_id"];
            if ($defender['nickname']) {
                // If defender has nickname, eg Pippa, put it alongside poke
                $defender["pokemon_name"] = i8ln($this->data[$pid]["name"]) . "<br><small style='font-size: 70%;'>(" . $defender['nickname'] . ")</small>";
            } else {
                $defender["pokemon_name"] = i8ln($this->data[$pid]["name"]);
            }
            $defender["iv_attack"] = floatval($defender["iv_attack"]);
            $defender["iv_defense"] = floatval($defender["iv_defense"]);
            $defender["iv_stamina"] = floatval($defender["iv_stamina"]);

            $defender['move_1_name'] = i8ln($this->moves[$defender['move_1']]['name']);
            $defender['move_1_damage'] = $this->moves[$defender['move_1']]['damage'];
            $defender['move_1_energy'] = $this->moves[$defender['move_1']]['energy'];
            $defender['move_1_type']['type'] = i8ln($this->moves[$defender['move_1']]['type']);
            $defender['move_1_type']['type_en'] = $this->moves[$defender['move_1']]['type'];

            $defender['move_2_name'] = i8ln($this->moves[$defender['move_2']]['name']);
            $defender['move_2_damage'] = $this->moves[$defender['move_2']]['damage'];
            $defender['move_2_energy'] = $this->moves[$defender['move_2']]['energy'];
            $defender['move_2_type']['type'] = i8ln($this->moves[$defender['move_2']]['type']);
            $defender['move_2_type']['type_en'] = $this->moves[$defender['move_2']]['type'];

            $data[] = $defender;

            unset($gym_defenders[$i]);
            $i++;
        }
        return $data;
    }

    public function get_gyms_api($swLat, $swLng, $neLat, $neLng)
    {
        $conds = array();
        $params = array();

        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        global $sendRaidData;
        if (!$sendRaidData) {
            return $this->query_gyms_api($conds, $params);
        } else {
            return $this->query_raids_api($conds, $params);
        }
    }

    public function query_gyms_api($conds, $params)
    {
        global $db;

        $query = "SELECT id AS gym_id,
        lat AS latitude,
        lon AS longitude,
        name
        FROM gym
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $gyms = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($gyms as $gym) {
            $gym["latitude"] = floatval($gym["latitude"]);
            $gym["longitude"] = floatval($gym["longitude"]);
            $data[] = $gym;

            unset($gyms[$i]);
            $i++;
        }
        return $data;
    }

    public function query_raids_api($conds, $params)
    {
        global $db;

        $query = "SELECT id AS gym_id,
        lat AS latitude,
        lon AS longitude,
        name,
        level AS raid_level,
        pokemon_id AS raid_pokemon_id,
        raid_battle_timestamp AS raid_start,
        raid_end_timestamp AS raid_end,
        raid_pokemon_cp,
        raid_pokemon_move_1,
        raid_pokemon_move_2
        FROM gym
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $gyms = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($gyms as $gym) {
            $gym["latitude"] = floatval($gym["latitude"]);
            $gym["longitude"] = floatval($gym["longitude"]);
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
        id AS spawnpoint_id
        FROM spawnpoint
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $spawnpoints = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($spawnpoints as $spawnpoint) {
            $spawnpoint["latitude"] = floatval($spawnpoint["latitude"]);
            $spawnpoint["longitude"] = floatval($spawnpoint["longitude"]);
            //$spawnpoint["time"] = intval($spawnpoint["despawn_time"]);
            //$spawnpoint["duration"] = intval($spawnpoint["duration"]);
            $spawnpoint["time"] = !empty($spawnpoint["despawn_time"]) ? $spawnpoint["despawn_time"] : null;
            $spawnpoint["duration"] = !empty($spawnpoint["duration"]) ? $spawnpoint["duration"] : null;
            $data[] = $spawnpoint;
            unset($spawnpoints[$i]);
            $i++;
        }
        return $data;
    }
}
