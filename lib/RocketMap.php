<?php

namespace Scanner;

class RocketMap extends Scanner
{
    public function get_active($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();
        global $map;
        $time = new \DateTime();
        $time->setTimeZone(new \DateTimeZone('UTC'));
        $time->setTimestamp(time());
        if ($swLat == 0) {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime", [':disappearTime' => date_format($time, 'Y-m-d H:i:s')])->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    last_modified > :lastModified
AND    latitude > :swLat 
AND    longitude > :swLng
AND    latitude < :neLat
AND    longitude < :neLng", [':disappearTime' => date_format($time, 'Y-m-d H:i:s'), ':lastModified' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    latitude > :swLat
AND    longitude > :swLng 
AND    latitude < :neLat 
AND    longitude < :neLng 
AND    NOT( 
              latitude > :oSwLat 
       AND    longitude > :oSwLng 
       AND    latitude < :oNeLat 
       AND    longitude < :oNeLng)", [':disappearTime' => date_format($time, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    latitude > :swLat
AND    longitude > :swLng 
AND    latitude < :neLat 
AND    longitude < :neLng", [':disappearTime' => date_format($time, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
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
            $i = 1;
            foreach ($ids as $id) {
                $pkmn_ids[':qry_' . $i] = $id;
                $pkmn_in .= ':' . 'qry_' . $i . ",";
                $i++;
            }
            $pkmn_in = substr($pkmn_in, 0, -1);
        } else {
            $pkmn_ids = [];
        }

        $time = new \DateTime();
        $time->setTimeZone(new \DateTimeZone('UTC'));
        $time->setTimestamp(time());
        if ($swLat == 0) {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    pokemon_id  IN ( $pkmn_in )", array_merge($pkmn_ids, [':disappearTime' => date_format($time, 'Y-m-d H:i:s')]))->fetchAll();
        } else {
            $datas = $db->query("SELECT *, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS expire_timestamp,
       latitude                                                                 AS lat, 
       longitude                                                                AS lon, 
       individual_attack                                                        AS atk_iv, 
       individual_defense                                                       AS def_iv, 
       individual_stamina                                                       AS sta_iv, 
       spawnpoint_id                                                            AS spawn_id 
FROM   pokemon 
WHERE  disappear_time > :disappearTime
AND    pokemon_id  IN ( $pkmn_in )
AND    latitude > :swLat
AND    longitude > :swLng 
AND    latitude < :neLat 
AND    longitude < :neLng", array_merge($pkmn_ids, [':disappearTime' => date_format($time, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng]))->fetchAll();
        }
        return $this->returnPokemon($datas);
    }


    public function get_stops($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lured = false)
    {
        global $db;

        $datas = array();
        if ($swLat == 0) {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) 
       AS 
       lure_expiration, 
       pokestop_id 
       AS external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon 
FROM   pokestop ")->fetchAll();
        } elseif ($tstamp > 0 && $lured == "true") {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))   AS last_modified,
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
       pokestop_id                                                               AS external_id,
       latitude                                                                  AS lat, 
       longitude                                                                 AS lon 
FROM   pokestop 
WHERE  last_updated > :lastUpdated
AND    active_fort_modifier IS NOT NULL 
AND    latitude > :swLat 
AND    longitude > :swLng 
AND    latitude < :neLat
AND    longitude < :neLng", [':lastUpdated' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))   AS last_modified,
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
       pokestop_id                                                               AS external_id,
       latitude                                                                  AS lat, 
       longitude                                                                 AS lon 
FROM   pokestop 
WHERE  last_updated > :lastUpdated
AND    latitude > :swLat
AND    longitude > :swLng 
AND    latitude < :neLat  
AND    longitude < :neLng", [':lastUpdated' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0 && $lured == "true") {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) 
       AS 
       lure_expiration, 
       pokestop_id 
       AS external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon 
FROM   pokestop 
WHERE  active_fort_modifier IS NOT NULL 
       AND ( latitude > :swLat
             AND longitude > :swLng
             AND latitude < :neLat 
             AND longitude < :neLng ) 
       AND NOT( latitude > :oSwLat
                AND longitude > :oSwLng 
                AND latitude < :oNeLat 
                AND longitude < :oNeLng ) 
       AND active_fort_modifier IS NOT NULL", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) 
       AS 
       lure_expiration, 
       pokestop_id 
       AS external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon 
FROM   pokestop 
WHERE  latitude > :swLat
       AND longitude > :swLng 
       AND latitude < :neLat 
       AND longitude < :neLng 
       AND NOT( latitude > :oSwLat 
                AND longitude > :oSwLng 
                AND latitude < :oNeLat 
                AND longitude < :oNeLng ) ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } elseif ($lured == "true") {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))   AS last_modified,
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
       pokestop_id                                                               AS external_id,
       latitude                                                                  AS lat, 
       longitude                                                                 AS lon 
FROM   pokestop 
WHERE  active_fort_modifier IS NOT NULL 
AND    latitude > :swLat 
AND    longitude > :swLng
AND    latitude < :neLat
AND    longitude < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT active_fort_modifier, 
       enabled, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))   AS last_modified,
       Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
       pokestop_id                                                               AS external_id,
       latitude                                                                  AS lat, 
       longitude                                                                 AS lon 
FROM   pokestop 
WHERE  latitude > :swLat
AND    longitude > :swLng
AND    latitude < :neLat
AND    longitude < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }

        $i = 0;

        return $this->returnPokestops($datas);
    }


    public function get_gyms($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();

        global $map;
        if ($swLat == 0) {
            $datas = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       total_cp, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', 
       @@global.time_zone)) AS 
       last_scanned, 
       team_id 
       AS team, 
       enabled, 
       name, 
       level, 
       pokemon_id, 
       cp, 
       move_1, 
       move_2, 
       Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) 
       AS raid_start, 
       Unix_timestamp(Convert_tz(end, '+00:00', @@global.time_zone)) 
       AS raid_end 
FROM   gym 
       LEFT JOIN gymdetails 
              ON gym.gym_id = gymdetails.gym_id 
       LEFT JOIN raid 
              ON gym.gym_id = raid.gym_id ")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT    gym.gym_id AS external_id, 
          latitude   AS lat, 
          longitude  AS lon, 
          guard_pokemon_id, 
          slots_available, 
          total_cp, 
          Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))    AS last_modified,
          Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
          team_id                                                                    AS team,
          enabled, 
          name, 
          level, 
          pokemon_id, 
          cp, 
          move_1, 
          move_2, 
          Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) AS raid_start, 
          Unix_timestamp(Convert_tz(end, '+00:00', @@global.time_zone)) AS raid_end 
FROM      gym 
LEFT JOIN gymdetails 
ON        gym.gym_id = gymdetails.gym_id 
LEFT JOIN raid 
ON        gym.gym_id = raid.gym_id 
WHERE     gym.last_scanned > :lastScanned
AND       latitude > :swLat
AND       longitude > :swLng
AND       latitude < :neLat
AND       longitude < :neLng", [':lastScanned' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       total_cp, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', 
       @@global.time_zone)) AS 
       last_scanned, 
       team_id 
       AS team, 
       enabled, 
       name, 
       level, 
       pokemon_id, 
       cp, 
       move_1, 
       move_2, 
       Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) 
       AS raid_start, 
       Unix_timestamp(Convert_tz(end, '+00:00', @@global.time_zone)) 
       AS raid_end 
FROM   gym 
       LEFT JOIN gymdetails 
              ON gym.gym_id = gymdetails.gym_id 
       LEFT JOIN raid 
              ON gym.gym_id = raid.gym_id 
WHERE  latitude > :swLat
       AND longitude > :swLng
       AND latitude < :neLat 
       AND longitude < :neLng 
       AND NOT( latitude > :oSwLat 
                AND longitude > :oSwLng 
                AND latitude < :oNeLat 
                AND longitude < :oNeLng )", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT    gym.gym_id AS external_id, 
          latitude   AS lat, 
          longitude  AS lon, 
          guard_pokemon_id, 
          slots_available, 
          total_cp, 
          Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))    AS last_modified,
          Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
          team_id                                                                    AS team,
          enabled, 
          name, 
          level, 
          pokemon_id, 
          cp, 
          move_1, 
          move_2, 
          Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) AS raid_start, 
          Unix_timestamp(Convert_tz( 
end, '+00:00', @@global.time_zone)) AS raid_end 
FROM      gym 
LEFT JOIN gymdetails 
ON        gym.gym_id = gymdetails.gym_id 
LEFT JOIN raid 
ON        gym.gym_id = raid.gym_id 
WHERE     latitude > :swLat 
AND       longitude > :swLng 
AND       latitude < :neLat 
AND       longitude < :neLng", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }

        $gyminfo = $this->returnGyms($datas);
        $gyms = $gyminfo['gyms'];
        $gym_ids = $gyminfo['gym_ids'];

        $j = 0;

        $gym_in = '';
        if (count($gym_ids)) {
            $i = 1;
            foreach ($gym_ids as $id) {
                $gym_qry_ids[':qry_' . $i] = $id;
                $gym_in .= ':' . 'qry_' . $i . ",";
                $i++;
            }
            $gym_in = substr($gym_in, 0, -1);
        } else {
            $gym_qry_ids = [];
        }
        $pokemons = $db->query("SELECT gymmember.gym_id, 
       pokemon_id, 
       cp, 
       trainer.name, 
       trainer.level 
FROM   gymmember 
       JOIN gympokemon 
         ON gymmember.pokemon_uid = gympokemon.pokemon_uid 
       JOIN trainer 
         ON gympokemon.trainer_name = trainer.name 
       JOIN gym 
         ON gym.gym_id = gymmember.gym_id 
WHERE  gymmember.last_scanned > gym.last_modified 
       AND gymmember.gym_id IN ( $gym_in ) 
GROUP  BY name 
ORDER  BY gymmember.gym_id, 
          gympokemon.cp ", $gym_qry_ids)->fetchAll();

        foreach ($pokemons as $pokemon) {
            $p = array();

            $pid = $pokemon["pokemon_id"];

            $p["pokemon_id"] = $pid;
            $p["pokemon_name"] = $this->data[$pid]['name'];
            $p["trainer_name"] = $pokemon["name"];
            $p["trainer_level"] = $pokemon["level"];
            $p["pokemon_cp"] = $pokemon["cp"];

            $gyms[$pokemon["gym_id"]]["pokemon"][] = $p;

            unset($pokemons[$j]);

            $j++;
        }


        return $gyms;
    }


    public function get_spawnpoints($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();

        if ($swLat == 0) {
            $datas = $db->query("SELECT latitude 
       AS lat, 
       longitude 
       AS lon, 
       spawnpoint_id 
       AS spawn_id, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) 
       AS time, 
       Count(spawnpoint_id) 
       AS count 
FROM   pokemon 
GROUP  BY latitude, 
          longitude, 
          spawnpoint_id, 
          time ")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT   latitude                                                                 AS lat, 
         longitude                                                                AS lon, 
         spawnpoint_id                                                            AS spawn_id, 
         Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS time, 
         Count(spawnpoint_id)                                                     AS count 
FROM     pokemon 
WHERE    last_modified > :lastModified
AND      latitude > :swLat  
AND      longitude > :swLng  
AND      latitude < :neLat  
AND      longitude < :neLng 
GROUP BY latitude, 
         longitude, 
         spawnpoint_id, 
         time", [':lastModified' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $datas = $db->query("SELECT latitude 
       AS lat, 
       longitude 
       AS lon, 
       spawnpoint_id 
       AS spawn_id, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) 
       AS time, 
       Count(spawnpoint_id) 
       AS count 
FROM   pokemon 
WHERE  latitude > :swLat  
AND      longitude > :swLng  
AND      latitude < :neLat  
AND      longitude < :neLng 
       AND NOT( latitude >  :oSwLat 
                AND longitude >  :oSwLng
                AND latitude <  :oNeLat
                AND longitude <  :oNeLng ) 
GROUP  BY latitude, 
          longitude, 
          spawnpoint_id, 
          time ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT latitude 
       AS lat, 
       longitude 
       AS lon, 
       spawnpoint_id 
       AS spawn_id, 
       Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) 
       AS time, 
       Count(spawnpoint_id) 
       AS count 
FROM   pokemon 
WHERE  latitude > :swLat  
AND      longitude > :swLng  
AND      latitude < :neLat  
AND      longitude < :neLng 
GROUP  BY latitude, 
          longitude, 
          spawnpoint_id, 
          time ", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }

        $spawnpoints = array();
        $spawnpoint_values = array();
        $i = 0;

        foreach ($datas as $row) {
            $key = $row["spawn_id"];
            $count = intval($row["count"]);
            $time = ($row["time"] + 2700) % 3600;

            $p = array();

            if (!array_key_exists($key, $spawnpoints)) {
                $p[$key]["spawnpoint_id"] = $key;
                $p[$key]["latitude"] = floatval($row["lat"]);
                $p[$key]["longitude"] = floatval($row["lon"]);
            } else {
                $p[$key]["special"] = true;
            }

            if (!array_key_exists("time", $p[$key]) || $count >= $p[$key]["count"]) {
                $p[$key]["time"] = $time;
                $p[$key]["count"] = $count;
            }

            $spawnpoints[] = $p;
            $spawnpoint_values[] = $p[$key];

            unset($datas[$i]);

            $i++;
        }

        foreach ($spawnpoint_values as $key => $subArr) {
            unset($subArr['count']);
            $spawnpoint_values[$key] = $subArr;
        }

        return $spawnpoint_values;
    }


    public function get_recent($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();

        global $map;
        if ($swLat == 0) {
            $datas = $db->query("SELECT latitude, 
       longitude, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified 
FROM   scannedlocation 
WHERE  last_modified >= '2017-06-16 15:57:32' 
ORDER  BY last_modified ASC ")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT   latitude, 
         longitude, 
         Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified
FROM     scannedlocation 
WHERE    last_modified >= :lastModified
AND      latitude > :swLat 
AND      longitude > :swLng
AND      latitude < :neLat 
AND      longitude < :neLng 
ORDER BY last_modified ASC", [':lastModified' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        } elseif ($oSwLat != 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->sub(new \DateInterval('PT15M'));
            $datas = $db->query("SELECT   latitude, 
         longitude, 
         Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified
FROM     scannedlocation 
WHERE    last_modified >= :lastModified
AND      latitude > :swLat 
AND      longitude > :swLng
AND      latitude < :neLat 
AND      longitude < :neLng 
AND      NOT( latitude >  :oSwLat 
                AND longitude >  :oSwLng
                AND latitude <  :oNeLat
                AND longitude <  :oNeLng ) 
AND      last_modified >= :lastModified
ORDER BY last_modified ASC", [':lastModified' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->sub(new \DateInterval('PT15M'));
            $datas = $db->query("SELECT   latitude, 
         longitude, 
         Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified
FROM     scannedlocation 
WHERE    last_modified >= :lastModified
AND      latitude > :swLat 
AND      longitude > :swLng
AND      latitude < :neLat 
AND      longitude < :neLng 
ORDER BY last_modified ASC", [':lastModified' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
        }

        $recent = array();
        $i = 0;

        foreach ($datas as $row) {
            $p = array();

            $p["latitude"] = floatval($row["latitude"]);
            $p["longitude"] = floatval($row["longitude"]);

            $lm = $row["last_modified"] * 1000;
            $p["last_modified"] = $lm;

            $recent[] = $p;

            unset($datas[$i]);

            $i++;
        }

        return $recent;
    }


    public function get_gym($id)
    {
        global $db;
        $row = $db->query("SELECT gym.gym_id 
       AS 
       external_id, 
       latitude 
       AS lat, 
       longitude 
       AS lon, 
       guard_pokemon_id, 
       slots_available, 
       Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) 
       AS 
       last_modified, 
       Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', 
       @@global.time_zone)) AS 
       last_scanned, 
       team_id 
       AS team, 
       enabled, 
       name, 
       level, 
       pokemon_id, 
       cp, 
       move_1, 
       move_2, 
       Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) 
       AS raid_start, 
       Unix_timestamp(Convert_tz(end, '+00:00', @@global.time_zone)) 
       AS raid_end 
FROM   gym 
       LEFT JOIN gymdetails 
              ON gym.gym_id = gymdetails.gym_id 
       LEFT JOIN raid 
              ON gym.gym_id = raid.gym_id 
WHERE  gym.gym_id = :id", [':id' => $id])->fetch();

        $pokemons = $db->query("SELECT gymmember.gym_id, 
       pokemon_id, 
       cp, 
       trainer.name, 
       trainer.level, 
       move_1, 
       move_2, 
       iv_attack, 
       iv_defense, 
       iv_stamina 
FROM   gymmember 
       JOIN gympokemon 
         ON gymmember.pokemon_uid = gympokemon.pokemon_uid 
       JOIN trainer 
         ON gympokemon.trainer_name = trainer.name 
       JOIN gym 
         ON gym.gym_id = gymmember.gym_id 
WHERE  gymmember.last_scanned > gym.last_modified 
       AND gymmember.gym_id IN ( :id ) 
GROUP  BY name 
ORDER  BY gympokemon.cp DESC ", [':id' => $id])->fetchAll();

        $p = array();
        $j = 0;

        $p = $this->returnGymInfo($row);

        foreach ($pokemons as $pokemon) {
            $pid = $pokemon["pokemon_id"];

            $p1 = array();

            $p1["pokemon_id"] = $pid;
            $p1["pokemon_name"] = i8ln($this->data[$pid]['name']);
            $p1["trainer_name"] = $pokemon["name"];
            $p1["trainer_level"] = $pokemon["level"];
            $p1["pokemon_cp"] = $pokemon["cp"];

            $p1["iv_attack"] = intval($pokemon["iv_attack"]);
            $p1["iv_defense"] = intval($pokemon["iv_defense"]);
            $p1["iv_stamina"] = intval($pokemon["iv_stamina"]);

            $p1['move_1_name'] = i8ln($this->moves[$pokemon['move_1']]['name']);
            $p1['move_1_damage'] = $this->moves[$pokemon['move_1']]['damage'];
            $p1['move_1_energy'] = $this->moves[$pokemon['move_1']]['energy'];
            $p1['move_1_type']['type'] = i8ln($this->moves[$pokemon['move_1']]['type']);
            $p1['move_1_type']['type_en'] = $this->moves[$pokemon['move_1']]['type'];

            $p1['move_2_name'] = i8ln($this->moves[$pokemon['move_2']]['name']);
            $p1['move_2_damage'] = $this->moves[$pokemon['move_2']]['damage'];
            $p1['move_2_energy'] = $this->moves[$pokemon['move_2']]['energy'];
            $p1['move_2_type']['type'] = i8ln($this->moves[$pokemon['move_2']]['type']);
            $p1['move_2_type']['type_en'] = $this->moves[$pokemon['move_2']]['type'];

            $p['pokemon'][] = $p1;

            unset($pokemons[$j]);
            $j++;
        }

        return $p;
    }


    public function returnGymInfo($row)
    {
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
        if ($gpid) {
            $p["guard_pokemon_name"] = i8ln($this->data[$gpid]['name']);
        }

        $rpid = intval($row['pokemon_id']);
        $p['raid_level'] = intval($row['level']);
        if ($rpid) {
            $p['raid_pokemon_id'] = $rpid;
            $p['raid_pokemon_name'] = i8ln($this->data[$rpid]['name']);
        }
        $p['raid_pokemon_cp'] = !empty($row['cp']) ? intval($row['cp']) : null;
        $p['raid_pokemon_move_1'] = !empty($row['move_1']) ? intval($row['move_1']) : null;
        $p['raid_pokemon_move_2'] = !empty($row['move_2']) ? intval($row['move_2']) : null;
        $p['raid_start'] = $row["raid_start"] * 1000;
        $p['raid_end'] = $row["raid_end"] * 1000;

        return $p;
    }
}
