<?php
namespace Scanner;

class Monocle extends Scanner
{
    function get_active($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();
        global $map;
        if ($swLat == 0) {
            $datas = $db->query("SELECT * FROM sightings WHERE expire_timestamp > :time", [':time'=> time()])->fetchAll();
        } elseif ($tstamp > 0) {
            $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :time 
AND    lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':time' => time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
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
            AND lon < :oNeLng ) " , [':time' => time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
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

    public function get_active_by_id($ids, $swLat, $swLng, $neLat, $neLng)
    {
        global $db;

        $datas = array();
        global $map;
        $pkmn_in = '';
        if (count($ids)) {
            $i=1;
            foreach ($ids as $id) {
                $pkmn_ids[':qry_'.$i] = $id;
                $pkmn_in .= ':'.'qry_'.$i.",";
                $i++;
            }
            $pkmn_in = substr($pkmn_in, 0, -1);
        } else {
            $pkmn_ids = [];
        }

            if ($swLat == 0) {
                $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  `expire_timestamp` > :time
       AND pokemon_id IN ( $pkmn_in ) ", array_merge($pkmn_ids, [':time'=>time()]))->fetchAll();
            } else {
                $datas = $db->query("SELECT * 
FROM   sightings 
WHERE  expire_timestamp > :timeStamp
AND    pokemon_id IN ( $pkmn_in ) 
AND    lat > :swLat 
AND    lon > :swLng
AND    lat < :neLat
AND    lon < :neLng", array_merge($pkmn_ids, [':timeStamp'=> time(), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng]))->fetchAll();
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
WHERE  lat > :swLat 
AND    lon > :swLng 
AND    lat < :neLat 
AND    lon < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
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
                AND lon < :oNeLng ) ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
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

    public function get_gyms($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {

        global $db;

        $datas = array();

        global $map;
            if ($swLat == 0) {
                $datas = $db->query("SELECT t3.external_id, 
       t3.lat, 
       t3.lon, 
       t1.last_modified, 
       t1.team, 
       t1.slots_available, 
       t1.guard_pokemon_id 
FROM   (SELECT fort_id, 
               Max(last_modified) AS MaxLastModified 
        FROM   fort_sightings 
        GROUP  BY fort_id) t2 
       LEFT JOIN fort_sightings t1 
              ON t2.fort_id = t1.fort_id 
                 AND t2.maxlastmodified = t1.last_modified 
       LEFT JOIN forts t3 
              ON t1.fort_id = t3.id")->fetchAll();
            } elseif ($tstamp > 0) {
                $datas = $db->query("SELECT t3.external_id, 
       t3.lat, 
       t3.lon, 
       t1.last_modified, 
       t1.team, 
       t1.slots_available, 
       t1.guard_pokemon_id 
FROM   (SELECT fort_id, 
               Max(last_modified) AS MaxLastModified 
        FROM   fort_sightings 
        GROUP  BY fort_id) t2 
       LEFT JOIN fort_sightings t1 
              ON t2.fort_id = t1.fort_id 
                 AND t2.maxlastmodified = t1.last_modified 
       LEFT JOIN forts t3 
              ON t1.fort_id = t3.id 
WHERE  t3.lat > :swLat 
       AND t3.lon > :swLng 
       AND t3.lat < :neLat 
       AND t3.lon < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
            } elseif ($oSwLat != 0) {
                $datas = $db->query("SELECT t3.external_id, 
       t3.lat, 
       t3.lon, 
       t1.last_modified, 
       t1.team, 
       t1.slots_available, 
       t1.guard_pokemon_id 
FROM   (SELECT fort_id, 
               Max(last_modified) AS MaxLastModified 
        FROM   fort_sightings 
        GROUP  BY fort_id) t2 
       LEFT JOIN fort_sightings t1 
              ON t2.fort_id = t1.fort_id 
                 AND t2.maxlastmodified = t1.last_modified 
       LEFT JOIN forts t3 
              ON t1.fort_id = t3.id 
WHERE  t3.lat > :swLat 
       AND t3.lon > :swLng
       AND t3.lat < :neLat
       AND t3.lon < :neLng
       AND NOT( t3.lat > :oSwLat
                AND t3.lon > :oSwLng
                AND t3.lat < :oNeLat
                AND t3.lon < :oNeLng)", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
            } else {
                $datas = $db->query("SELECT    t3.external_id, 
          t3.lat, 
          t3.lon, 
          t1.last_modified, 
          t1.team, 
          t1.slots_available, 
          t1.guard_pokemon_id 
FROM      ( 
                   SELECT   fort_id, 
                            Max(last_modified) AS maxlastmodified 
                   FROM     fort_sightings 
                   GROUP BY fort_id) t2 
LEFT JOIN fort_sightings t1 
ON        t2.fort_id = t1.fort_id 
AND       t2.maxlastmodified = t1.last_modified 
LEFT JOIN forts t3 
ON        t1.fort_id = t3.id 
WHERE     t3.lat > :swLat
AND       t3.lon > :swLng 
AND       t3.lat < :neLat 
AND       t3.lon < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
            }



        $gyminfo = $this->returnGyms($datas);
        $gyms = $gyminfo['gyms'];
        $gym_ids = $gyminfo['gym_ids'];


        $j = 0;

            $gyms_in = '';
            if (count($gym_ids)) {
                $i=1;
                foreach ($gym_ids as $id) {
                    $gym_in_ids[':qry_'.$i] = $id;
                    $gyms_in .= ':'.'qry_'.$i.",";
                    $i++;
                }
                $gyms_in = substr($gyms_in, 0, -1);
            } else {
                $gym_in_ids = [];
            }
                $raids = $db->query("SELECT t3.external_id,
       t1.fort_id, 
       level, 
       pokemon_id, 
       time_battle AS raid_start, 
       time_end    AS raid_end,
       move_1,
       move_2
FROM   (SELECT fort_id, 
               Max(time_end) AS MaxTimeEnd 
        FROM   raids 
        GROUP  BY fort_id) t1 
       LEFT JOIN raids t2 
              ON t1.fort_id = t2.fort_id 
                 AND maxtimeend = time_end 
       LEFT JOIN forts t3
               ON t3.id = t1.fort_id
 WHERE  t3.external_id IN ( $gyms_in ) ", $gym_in_ids)->fetchAll();

            foreach ($raids as $raid) {
                $id = $raid["external_id"];
                $rpid = intval($raid['pokemon_id']);
                $gyms[$id]['raid_level'] = intval($raid['level']);
                if ($rpid)
                    $gyms[$id]['raid_pokemon_id'] = $rpid;
                if ($rpid)
                    $gyms[$id]['raid_pokemon_name'] = i8ln($this->data[$rpid]['name']);
                $gyms[$id]['raid_pokemon_cp'] = !empty($raid['cp']) ? intval($raid['cp']) : null;
                $gyms[$id]['raid_pokemon_move_1'] = !empty($raid['move_1']) ? intval($raid['move_1']) : null;
                $gyms[$id]['raid_pokemon_move_2'] = !empty($raid['move_2']) ? intval($raid['move_2']) : null;
                $gyms[$id]['raid_start'] = $raid["raid_start"] * 1000;
                $gyms[$id]['raid_end'] = $raid["raid_end"] * 1000;

                unset($raids[$j]);

                $j++;
            }


        return $gyms;
    }

    public function get_spawnpoints($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();

            if ($swLat == 0) {
                $datas = $db->query("SELECT lat, lon, spawn_id, despawn_time FROM spawnpoints WHERE updated > 0")->fetchAll();
            } elseif ($tstamp > 0) {
                $datas = $db->query("SELECT lat, 
       lon, 
       spawn_id, 
       despawn_time 
FROM   spawnpoints 
WHERE  updated > :updated
AND    lat > :swLat 
AND    lon > :swLng
AND    lat < :neLat 
AND    lon < :neLng", ['updated'=> $tstamp,':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
            } elseif ($oSwLat != 0) {
                $datas = $db->query("SELECT lat, 
       lon, 
       spawn_id, 
       despawn_time 
FROM   spawnpoints 
WHERE  updated > 0 
       AND lat > :swLat  
       AND lon > :swLng 
       AND lat < :neLat 
       AND lon <  :neLng  
       AND NOT( lat >  :oSwLat 
                AND lon >  :oSwLng
                AND lat <  :oNeLat
                AND lon <  :oNeLng ) ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng,  ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
            } else {
                $datas = $db->query("SELECT lat, 
       lon, 
       spawn_id, 
       despawn_time 
FROM   spawnpoints 
WHERE  updated > 0 
AND    lat >  :swLat  
AND    lon >  :swLng 
AND    lat < :neLat 
AND    lon < :neLng",[':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
            }

            $spawnpoints = array();
            $i = 0;

            foreach ($datas as $row) {
                $p = array();

                $p["latitude"] = floatval($row["lat"]);
                $p["longitude"] = floatval($row["lon"]);
                $p["spawnpoint_id"] = $row["spawn_id"];
                $p["time"] = intval($row["despawn_time"]);

                $spawnpoints[] = $p;

                unset($row[$i]);

                $i++;
            }

            return $spawnpoints;

    }


    public function get_recent($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();

        $recent = array();
        // Monocle doesn't currently do anything for this.

        return $recent;
    }


    // Used in gym_data.php
    public function get_gym($id)
    {
        global $db;
        $row = $db->query("SELECT t3.external_id, 
       t3.lat, 
       t3.lon, 
       t1.last_modified, 
       t1.team, 
       t1.slots_available, 
       t1.guard_pokemon_id 
FROM   (SELECT fort_id, 
               Max(last_modified) AS MaxLastModified 
        FROM   fort_sightings 
        GROUP  BY fort_id) t2 
       LEFT JOIN fort_sightings t1 
              ON t2.fort_id = t1.fort_id 
                 AND t2.maxlastmodified = t1.last_modified 
       LEFT JOIN forts t3 
              ON t1.fort_id = t3.id 
WHERE  t3.external_id = :id ", [':id'=>$id])->fetch();



        $raid = $db->query("SELECT t3.external_id,
       t1.fort_id, 
       level, 
       pokemon_id, 
       time_battle AS raid_start, 
       time_end    AS raid_end,
       move_1,
       move_2
FROM   (SELECT fort_id, 
               Max(time_end) AS MaxTimeEnd 
        FROM   raids 
        GROUP  BY fort_id) t1 
       LEFT JOIN raids t2 
              ON t1.fort_id = t2.fort_id 
                 AND maxtimeend = time_end  
        LEFT JOIN forts t3
               ON t3.id = t1.fort_id
 WHERE  t3.external_id IN ( :id ) ", [':id'=>$id])->fetch();


        $return = $this->returnGymInfo($row, $raid);
        unset($raid);

        return $return;
    }

    public function returnGymInfo($row, $raid)
    {

        $p = array();

        $lat = floatval($row["lat"]);
        $lon = floatval($row["lon"]);
        $gpid = intval($row["guard_pokemon_id"]);
        $sa = intval($row["slots_available"]);
        $lm = $row["last_modified"] * 1000;
        $ls = !empty($row["last_scanned"]) ? $row["last_scanned"] * 1000 : null;
        $ti = isset($row["team"]) ? intval($row["team"]) : null;

        $p["enabled"] = isset($row["enabled"]) ? boolval($row["enabled"]) : true;
        $p["guard_pokemon_id"] = $gpid;
        $p["gym_id"] = $row["external_id"];
        $p["slots_available"] = $sa;
        $p["last_modified"] = $lm;
        $p["last_scanned"] = $ls;
        $p["latitude"] = $lat;
        $p["longitude"] = $lon;
        $p["name"] = !empty($row["name"]) ? $row["name"] : null;
        $p["team_id"] = $ti;
        if ($gpid)
            $p["guard_pokemon_name"] = i8ln($this->data[$gpid]['name']);


        $rpid = intval($raid['pokemon_id']);
        $p['raid_level'] = intval($raid['level']);
        if ($rpid)
            $p['raid_pokemon_id'] = $rpid;
        if ($rpid)
            $p['raid_pokemon_name'] = i8ln($this->data[$rpid]['name']);
            $p['raid_pokemon_cp'] = !empty($row['cp']) ? intval($row['cp']) : null;
            $p['raid_pokemon_move_1'] = !empty($row['move_1']) ? intval($row['move_1']) : null;
            $p['raid_pokemon_move_2'] = !empty($row['move_2']) ? intval($row['move_2']) : null;
        $p['raid_start'] = $raid["raid_start"] * 1000;
        $p['raid_end'] = $raid["raid_end"] * 1000;

        return $p;
    }



}