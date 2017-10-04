<?php

namespace Scanner;

class RocketMap_Sloppy extends RocketMap
{
    public function get_gyms($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        global $db;

        $datas = array();

        global $map;
        global $fork;
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
       total_gym_cp 
       AS total_cp, 
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
       raid_level 
       AS level, 
       raid_pokemon_id 
       AS pokemon_id, 
       raid_pokemon_cp 
       AS cp, 
       raid_pokemon_move_1 
       AS move_1, 
       raid_pokemon_move_2 
       AS move_2, 
       Unix_timestamp(Convert_tz(raid_battle, '+00:00', @@global.time_zone)) 
       AS 
       raid_start, 
       Unix_timestamp(Convert_tz(raid_end, '+00:00', @@global.time_zone)) 
       AS raid_end 
FROM   gym 
       LEFT JOIN gymdetails 
              ON gym.gym_id = gymdetails.gym_id")->fetchAll();
        } elseif ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $datas = $db->query("SELECT    gym.gym_id AS external_id, 
          latitude   AS lat, 
          longitude  AS lon, 
          guard_pokemon_id, 
          slots_available, 
          total_gym_cp                                                               AS total_cp,
          Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))    AS last_modified,
          Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
          team_id                                                                    AS team,
          enabled, 
          name, 
          raid_level                                                            AS level, 
          raid_pokemon_id                                                       AS pokemon_id,
          raid_pokemon_cp                                                       AS cp, 
          raid_pokemon_move_1                                                   AS move_1, 
          raid_pokemon_move_2                                                   AS move_2, 
          Unix_timestamp(Convert_tz(raid_battle, '+00:00', @@global.time_zone)) AS raid_start,
          Unix_timestamp(Convert_tz(raid_end, '+00:00', @@global.time_zone))    AS raid_end 
FROM      gym 
LEFT JOIN gymdetails 
ON        gym.gym_id = gymdetails.gym_id 
WHERE     gym.last_scanned > :lastScanned
AND       latitude > :swLat 
AND       longitude > :swLng 
AND       latitude < :neLat 
AND       longitude < :neLng" . ['lastScanned' => date_format($date, 'Y-m-d H:i:s'), ':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng])->fetchAll();
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
       total_gym_cp 
       AS total_cp, 
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
       raid_level 
       AS level, 
       raid_pokemon_id 
       AS pokemon_id, 
       raid_pokemon_cp 
       AS cp, 
       raid_pokemon_move_1 
       AS move_1, 
       raid_pokemon_move_2 
       AS move_2, 
       Unix_timestamp(Convert_tz(raid_battle, '+00:00', @@global.time_zone)) 
       AS 
       raid_start, 
       Unix_timestamp(Convert_tz(raid_end, '+00:00', @@global.time_zone)) 
       AS raid_end 
FROM   gym 
       LEFT JOIN gymdetails 
              ON gym.gym_id = gymdetails.gym_id 
WHERE  latitude > :swLat
       AND longitude > :swLng 
       AND latitude < :neLat 
       AND longitude < :neLng 
       AND NOT( latitude > :oSwLat 
                AND longitude > :oSwLng 
                AND latitude < :oNeLat 
                AND longitude < :oNeLng)", [':swLat' => $swLat, ':swLng' => $swLng, ':neLat' => $neLat, ':neLng' => $neLng, ':oSwLat' => $oSwLat, ':oSwLng' => $oSwLng, ':oNeLat' => $oNeLat, ':oNeLng' => $oNeLng])->fetchAll();
        } else {
            $datas = $db->query("SELECT    gym.gym_id AS external_id, 
          latitude   AS lat, 
          longitude  AS lon, 
          guard_pokemon_id, 
          slots_available, 
          total_gym_cp                                                               AS total_cp,
          Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone))    AS last_modified,
          Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
          team_id                                                                    AS team,
          enabled, 
          name, 
          raid_level                                                            AS level, 
          raid_pokemon_id                                                       AS pokemon_id,
          raid_pokemon_cp                                                       AS cp, 
          raid_pokemon_move_1                                                   AS move_1, 
          raid_pokemon_move_2                                                   AS move_2, 
          Unix_timestamp(Convert_tz(raid_battle, '+00:00', @@global.time_zone)) AS raid_start,
          Unix_timestamp(Convert_tz(raid_end, '+00:00', @@global.time_zone))    AS raid_end 
FROM      gym 
LEFT JOIN gymdetails 
ON        gym.gym_id = gymdetails.gym_id 
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
       raid_level 
       AS level, 
       raid_pokemon_id 
       AS pokemon_id, 
       raid_pokemon_cp 
       AS cp, 
       raid_pokemon_move_1 
       AS move_1, 
       raid_pokemon_move_2 
       AS move_2, 
       Unix_timestamp(Convert_tz(raid_battle, '+00:00', @@global.time_zone)) 
       AS 
       raid_start, 
       Unix_timestamp(Convert_tz(raid_end, '+00:00', @@global.time_zone)) 
       AS raid_end 
FROM   gym 
       LEFT JOIN gymdetails 
              ON gym.gym_id = gymdetails.gym_id 
WHERE  gym.gym_id = :id" . [':id' => $id])->fetch();


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

        $j = 0;

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
        $return = $this->returnGymInfo($row);

        return $return;
    }
}
