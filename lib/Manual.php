<?php

namespace Manual;

class Manual
{
    // Common functions for both RM and Monocle
    /**
     * $data
     * Used for Pokemon data
     * @var array|mixed
     */
    public $data = [];
    /**
     * $moves
     * Used for Pokemon moves
     * @var array|mixed
     */
    public $moves = [];
    /**
     * Scanner constructor.
     * Loads in the JSON arrays for Pokemon and moves
     */
    public function __construct()
    {
        $json_poke = "static/data/pokemon.json";
        $json_contents = file_get_contents($json_poke);
        $this->data = json_decode($json_contents, true);
        $json_moves = "static/data/moves.json";
        $json_contents = file_get_contents($json_moves);
        $this->moves = json_decode($json_contents, true);
    }
public function get_nests($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
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
        return $this->query_nests($conds, $params);
    }
    public function query_nests($conds, $params)
    {
        global $manualdb;
        $query = "SELECT nest_id,
        lat,
        lon,
        pokemon_id,
        type
        FROM nests
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $nests = $manualdb->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($nests as $nest) {
            $nest["lat"] = floatval($nest["lat"]);
            $nest["lon"] = floatval($nest["lon"]);
            $nest["type"] = intval($nest["type"]);
            if($nest['pokemon_id'] > 0 ){
                $nest["pokemon_name"] = i8ln($this->data[$nest["pokemon_id"]]['name']);
                $types = $this->data[$nest["pokemon_id"]]["types"];
                $etypes = $this->data[$nest["pokemon_id"]]["types"];
                foreach ($types as $k => $v) {
                    $types[$k]['type'] = i8ln($v['type']);
                    $etypes[$k]['type'] = $v['type'];
                }
                $nest["pokemon_types"] = $types;
                $nest["english_pokemon_types"] = $etypes;
            }
            $data[] = $nest;
            unset($nests[$i]);
            $i++;
        }
        return $data;
    }
    public function get_communities($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
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
        return $this->query_communities($conds, $params);
    }
    public function query_communities($conds, $params)
    {
        global $manualdb;
        $query = "SELECT community_id,
        title,
        description,
        type,
        image_url,
        size,
        team_instinct,
        team_mystic,
        team_valor,
        has_invite_url,
        invite_url,
        lat,
        lon,
        source
        FROM communities
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $communities = $manualdb->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($communities as $community) {
            $community["type"] = intval($community["type"]);
            $community["size"] = intval($community["size"]);
            $community["team_instinct"] = intval($community["team_instinct"]);
            $community["team_mystic"] = intval($community["team_mystic"]);
            $community["team_valor"] = intval($community["team_valor"]);
            $community["has_invite_url"] = intval($community["has_invite_url"]);
            $community["lat"] = floatval($community["lat"]);
            $community["lon"] = floatval($community["lon"]);
            $community["source"] = intval($community["source"]);
            $data[] = $community;
            unset($communities[$i]);
            $i++;
        }
        return $data;
    }
    public function get_portals($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
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
        return $this->query_portals($conds, $params);
    }
    public function query_portals($conds, $params)
    {
        global $manualdb;
        $query = "SELECT external_id,
        lat,
        lon,
        name,
	url,
	updated,
	imported
        FROM ingress_portals
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $portals = $manualdb->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($portals as $portal) {
            $portal["lat"] = floatval($portal["lat"]);
            $portal["lon"] = floatval($portal["lon"]);
            $portal["url"] = str_replace("http://", "https://images.weserv.nl/?url=", $portal["url"]);
            $data[] = $portal;
            unset($portals[$i]);
            $i++;
        }
        return $data;
    }
    public function get_poi($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
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
        return $this->query_poi($conds, $params);
    }
    public function query_poi($conds, $params)
    {
        global $manualdb;
        $query = "SELECT poi_id,
        lat,
        lon,
        name,
	description,
	updated,
	submitted_by,
	status
        FROM poi
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $pois = $manualdb->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($pois as $poi) {
            $poi["lat"] = floatval($poi["lat"]);
            $poi["lon"] = floatval($poi["lon"]);
            $data[] = $poi;
            unset($pois[$i]);
            $i++;
        }
        return $data;
    }
}
