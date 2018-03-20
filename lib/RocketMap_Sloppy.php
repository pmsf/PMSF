<?php

namespace Scanner;

class RocketMap_Sloppy extends RocketMap
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
        foreach ($weathers as $weather) {
            $weather['s2_cell_id'] = sprintf("%u", $weather['s2_cell_id']);
            $data["weather_" . $weather['s2_cell_id']] = $weather;
            $data["weather_" . $weather['s2_cell_id']]['condition'] = $data["weather_" . $weather['s2_cell_id']]['gameplay_weather'];
            unset($data["weather_" . $weather['s2_cell_id']]['gameplay_weather']);
        }
        return $data;
    }

    public function query_gyms($conds, $params)
    {
        global $db;

        $query = "SELECT gym.gym_id, 
        latitude, 
        longitude, 
        guard_pokemon_id, 
        slots_available, 
        total_cp, 
        Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified, 
        Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned, 
        team_id, 
        name,
        level AS raid_level, 
        pokemon_id AS raid_pokemon_id, 
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
            $gym["slots_available"] = intval($gym["slots_available"]);
            $data[] = $gym;

            unset($gyms[$i]);
            $i++;
        }
        return $data;
    }
}
