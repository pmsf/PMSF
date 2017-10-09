<?php

namespace Scanner;

class Monocle_Monkey extends Monocle
{
    public function get_active($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();
        global $map;
        if ($swLat == 0) {
            $datas = $db->query("SELECT * FROM sightings WHERE expire_timestamp > :time", [':time' => time()])->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :time 
AND    updated > :lastUpdated
AND    lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':time' => time(), ':lastUpdated' => $tstamp, ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :time 
   AND lat > :swLat
   AND lon > :swLng 
   AND lat < :neLat 
   AND lon < :neLng 
   AND NOT( lat > :oSwLat 
            AND lon > :oSwLng 
            AND lat < :oNeLat 
            AND lon < :oNeLng ) ", [':time' => time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :time 
AND    lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':time' => time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }
        return $this->returnPokemon($datas);
    }

    public function get_stops($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lured = false)
    {
        global $db;

        $datas = array();
        global $map;
        if ($swLat == 0) {
            $datas = $db->query("SELECT external_id, lat, lon FROM pokestops")->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("SELECT external_id, 
       lat, 
       lon 
FROM   pokestops 
WHERE  updated > :lastUpdated
AND    lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':lastUpdated' => $tstamp, ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT external_id, 
       lat, 
       lon 
FROM   pokestops 
WHERE  lat > :swLat
       AND lon > :swLng 
       AND lat < :neLat 
       AND lon < :neLng
       AND NOT( lat > :oSwLat 
                AND lon > :oSwLng 
                AND lat < :oNeLat 
                AND lon < :oNeLng ) ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT external_id, 
       lat, 
       lon 
FROM   pokestops 
WHERE  lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }

        return $this->returnPokestops($datas);
    }

    public function get_gym($gymId)
    {
        $conds = array();
        $params = array();

        $conds[] = "f.external_id = :gymId";
        $params[':gymId'] = $gymId;

        $gyms = $this->query_gyms($conds, $params);
        $gym = $gyms[$gymId];
        $gym["pokemon"] = $this->query_gym_defenders($gymId);
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

    private function query_gyms($conds, $params)
    {
        global $db;

        $query = "SELECT f.external_id as gym_id,
      fs.last_modified as last_modified,
      updated as last_scanned,
      f.lat as latitude,
      f.lon as longitude,
      f.name,
      fs.team team_id,
      fs.guard_pokemon_id,
      fs.slots_available,
      r.level raid_level,
      r.pokemon_id raid_pokemon_id,
      r.time_battle raid_start,
      r.time_end raid_end,
      r.cp raid_pokemon_cp,
      r.move_1 raid_pokemon_move_1,
      r.move_2 raid_pokemon_move_2
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
            $gym["enabled"] = true;
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
            $data[$gym["gym_id"]] = $gym;

            unset($gyms[$i]);
            $i++;
        }
        return $data;
    }

    private function query_gym_defenders($gymId)
    {
        global $db;


        $query = "SELECT gd.pokemon_id,
        gd.cp pokemon_cp,
        gd.move_1,
        gd.move_2,
        gd.nickname,
        gd.atk_iv iv_attack,
        gd.def_iv iv_defense,
        gd.sta_iv iv_stamina,
        gd.cp pokemon_cp,
        gd.owner_name trainer_name
      FROM gym_defenders gd
      LEFT JOIN forts f ON gd.fort_id = f.id
      WHERE f.external_id = :gymId";

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
            $defender["trainer_level"] = "";

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
}
