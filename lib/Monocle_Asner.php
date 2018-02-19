<?php

namespace Scanner;

class Monocle_Asner extends Monocle
{
    public function get_active($eids, $minIv, $minLevel, $exMinIv, $bigKarp, $tinyRat, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $encId = 0)
    {
        global $db;
        $conds = array();
        $params = array();

        $select = "pokemon_id, expire_timestamp AS disappear_time, encounter_id, lat AS latitude, lon AS longitude";
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
        if (count($eids)) {
            $pkmn_in = '';
            $i = 1;
            foreach ($eids as $id) {
                $params[':qry_' . $i . "_"] = $id;
                $pkmn_in .= ':qry_' . $i . "_,";
                $i++;
            }
            $pkmn_in = substr($pkmn_in, 0, -1);
            $conds[] = "(pokemon_id IN ( $pkmn_in ))";
        }
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";
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

        $select = "pokemon_id, expire_timestamp AS disappear_time, encounter_id, lat AS latitude, lon AS longitude";
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
            $conds[] = "pokemon_id NOT IN ( $pkmn_in )";
        }
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";
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

        return $this->query_gyms($conds, $params);
    }

    public function get_gym($gymId)
    {
        $conds = array();
        $params = array();

        $conds[] = "f.external_id = :gymId";
        $params[':gymId'] = $gymId;

        $gyms = $this->query_gyms($conds, $params);
        $gym = $gyms[0];
        return $gym;
    }

    public function query_gyms($conds, $params)
    {
        global $db;

        $query = "SELECT f.external_id AS gym_id, 
        f.lat AS latitude, 
        f.lon AS longitude, 
        fs.last_modified, 
        fs.team AS team_id, 
        fs.slots_available, 
        fs.guard_pokemon_id,
        raid_level, 
        pokemon_id AS raid_pokemon_id, 
        cp AS raid_pokemon_cp, 
        move_1 AS raid_pokemon_move_1, 
        move_2 AS raid_pokemon_move_2, 
        raid_start, 
        raid_end 
        FROM (SELECT f.id,
          f.external_id,
          f.lat,
          f.lon, 
          MAX(fs.id) AS fort_sightings_id,
          MAX(r.id)  AS raid_id
          FROM   forts f
          LEFT JOIN fort_sightings fs ON fs.fort_id = f.id
          LEFT JOIN raid_info r ON r.fort_id = f.id
          GROUP  BY f.id) f
        LEFT JOIN fort_sightings fs ON fs.id = f.fort_sightings_id
        LEFT JOIN raid_info r ON r.id = f.raid_id
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
            $gym["slots_available"] = intval($gym["slots_available"]);
            $gym["last_modified"] = $gym["last_modified"] * 1000;
            $gym["raid_start"] = $gym["raid_start"] * 1000;
            $gym["raid_end"] = $gym["raid_end"] * 1000;
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
        raid_level,
        pokemon_id AS raid_pokemon_id,
        cp AS raid_pokemon_cp,
        raid_start,
        raid_end,
        move_1 AS raid_pokemon_move_1,
        move_2 AS raid_pokemon_move_2
        FROM (SELECT f.id,
          f.external_id,
          f.lat,
          f.lon, 
          MAX(r.id) AS raid_id
          FROM   forts f
          LEFT JOIN raid_info r ON r.fort_id = f.id
          GROUP  BY f.id) f
        LEFT JOIN raid_info r ON r.id = f.raid_id
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
