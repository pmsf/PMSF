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
}
