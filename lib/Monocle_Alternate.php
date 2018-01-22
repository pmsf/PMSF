<?php

namespace Scanner;

class Monocle_Alternate extends Monocle
{
    public function get_active($eids, $minIv, $minLevel, $exMinIv, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;
        $conds = array();
        $params = array();

        $select = "pokemon_id, expire_timestamp AS disappear_time, encounter_id, lat AS latitude, lon AS longitude, gender, form, weight";
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
        if (count($eids)) {
            $pkmn_in = '';
            $i = 1;
            foreach ($eids as $id) {
                $params[':qry_' . $i . "_"] = $id;
                $pkmn_in .= ':qry_' . $i . "_,";
                $i++;
            }
            $pkmn_in = substr($pkmn_in, 0, -1);
            $conds[] = "pokemon_id NOT IN ( $pkmn_in )";
        }
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";
        if (!empty($minIv) && !is_nan((float)$minIv) && $minIv != 0) {
            if (empty($exMinIv)) {
                $conds[] = '((atk_iv' . $float . ' + def_iv' . $float . ' + sta_iv' . $float . ') / 45.00)' . $float . ' * 100.00 >= ' . $minIv;
            } else {
                $conds[] = '(((atk_iv' . $float . ' + def_iv' . $float . ' + sta_iv' . $float . ') / 45.00)' . $float . ' * 100.00 >= ' . $minIv . ' OR pokemon_id IN(' . $exMinIv . ') )';
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

    public function get_active_by_id($ids, $minIv, $minLevel, $exMinIv, $swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        $conds = array();
        $params = array();

        $select = "pokemon_id, expire_timestamp AS disappear_time, encounter_id, lat AS latitude, lon AS longitude, gender, form, weight";

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
            $pkmn_in = '';
            $i = 1;
            foreach ($ids as $id) {
                $params[':qry_' . $i . "_"] = $id;
                $pkmn_in .= ':qry_' . $i . "_,";
                $i++;
            }
            $pkmn_in = substr($pkmn_in, 0, -1);
            $conds[] = "pokemon_id IN ( $pkmn_in )";
        }
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";
        if (!empty($minIv) && !is_nan((float)$minIv) && $minIv != 0) {
            if (empty($exMinIv)) {
                $conds[] = '((atk_iv' . $float . ' + def_iv' . $float . ' + sta_iv' . $float . ') / 45.00)' . $float . ' * 100.00 >= ' . $minIv;
            } else {
                $conds[] = '(((atk_iv' . $float . ' + def_iv' . $float . ' + sta_iv' . $float . ') / 45.00)' . $float . ' * 100.00 >= ' . $minIv . ' OR pokemon_id IN(' . $exMinIv . ') )';
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

    public function get_stops($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lured = false)
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
        return $this->query_stops($conds, $params);
    }

    public function query_stops($conds, $params)
    {
        global $db;

        $query = "SELECT external_id AS pokestop_id,
        name AS pokestop_name,
        lat AS latitude,
        lon AS longitude
        FROM pokestops
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $pokestops = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($pokestops as $pokestop) {
            $pokestop["latitude"] = floatval($pokestop["latitude"]);
            $pokestop["longitude"] = floatval($pokestop["longitude"]);
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

        $conds[] = "f.external_id = :gymId";
        $params[':gymId'] = $gymId;

        $gyms = $this->query_gyms($conds, $params);
        $gym = $gyms[0];

        $select = "gd.pokemon_id, gd.cp AS pokemon_cp, gd.move_1, gd.move_2, gd.nickname, gd.atk_iv AS iv_attack, gd.def_iv AS iv_defense, gd.sta_iv AS iv_stamina, gd.cp AS pokemon_cp";
        global $noTrainerName;
        if (!$noTrainerName) {
            $select .= ", gd.owner_name AS trainer_name";
        }
        $gym["pokemon"] = $this->query_gym_defenders($gymId, $select);
        return $gym;
    }

    public function get_gyms($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
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
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
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
        f.sponsor,
        f.park,
        fs.team AS team_id,
        fs.guard_pokemon_id,
        fs.slots_available,
        r.level AS raid_level,
        r.pokemon_id AS raid_pokemon_id,
        r.time_battle AS raid_start,
        r.time_end AS raid_end,
        r.cp AS raid_pokemon_cp,
        r.move_1 AS raid_pokemon_move_1,
        r.move_2 AS raid_pokemon_move_2
        FROM forts f
        LEFT JOIN fort_sightings fs ON fs.fort_id = f.id
        LEFT JOIN raids r ON r.fort_id = f.id
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
            $gym["last_modified"] = $gym["last_modified"] * 1000;
            $gym["last_scanned"] = $gym["last_scanned"] * 1000;
            $gym["raid_start"] = $gym["raid_start"] * 1000;
            $gym["raid_end"] = $gym["raid_end"] * 1000;
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
      FROM gym_defenders gd
      LEFT JOIN forts f ON gd.fort_id = f.id
      WHERE f.external_id = :gymId";

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

        $conds[] = "f.lat > :swLat AND f.lon > :swLng AND f.lat < :neLat AND f.lon < :neLng";
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

        $query = "SELECT f.external_id AS gym_id,
        f.lat AS latitude,
        f.lon AS longitude,
        name
        FROM forts f
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

        $query = "SELECT f.external_id AS gym_id,
        f.lat AS latitude,
        f.lon AS longitude,
        name,
        level AS raid_level,
        pokemon_id AS raid_pokemon_id,
        time_battle AS raid_start,
        time_end AS raid_end,
        cp AS raid_pokemon_cp,
        move_1 AS raid_pokemon_move_1,
        move_2 AS raid_pokemon_move_2
        FROM forts f
        LEFT JOIN raids r ON r.fort_id = f.id
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
}
