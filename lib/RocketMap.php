<?php

namespace Scanner;

class RocketMap extends Scanner
{
    public $cpMultiplier;

    public function __construct()
    {
        parent::__construct();
        $this->setCpMultiplier();
    }

    public function get_active($eids, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $bigKarp, $tinyRat, $zeroIv, $hundoIv, $independantPvpAndStats, $despawnTimeType, $gender, $missingIvOnly, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $encId = 0)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng AND disappear_time > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date->setTimestamp(time());
        $params[':time'] = date_format($date, 'Y-m-d H:i:s');

        if ($oSwLat != 0) {
            $conds[] = "NOT (latitude > :oswLat AND longitude > :oswLng AND latitude < :oneLat AND longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries, $showPokemonsOutsideBoundaries;
        if (!$noBoundaries && !$showPokemonsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $date->setTimestamp($tstamp);
            $conds[] = "last_modified > :lastUpdated";
            $params[':lastUpdated'] = date_format($date, 'Y-m-d H:i:s');
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
        if (!empty($gender) && ($gender == 1 || $gender == 2)) {
           $conds[] = 'gender = ' . $gender;
        }
        if ($missingIvOnly) {
            $conds[] = '(individual_attack IS NULL OR individual_defense IS NULL OR individual_stamina IS NULL)';
        } else  {
            $zeroIvSql = ($zeroIv) ? ' OR (individual_attack = 0 AND individual_defense = 0 AND individual_stamina = 0)' : '';
            $hundoIvSql = ($hundoIv) ? ' OR (individual_attack = 15 AND individual_defense = 15 AND individual_stamina = 15)' : '';
            $exMinIvSql = (!empty($exMinIv)) ? ' OR pokemon_id IN(' . $exMinIv . ')' : '';
            if ($minIv !== 0) {
                $conds[] = '((individual_attack' . $float . ' + individual_defense' . $float . ' + individual_stamina' . $float . ') >= ' . ($minIv * .45) . $zeroIvSql . $hundoIvSql . $exMinIvSql . ')';
            }
            if ($minLevel !== 0) {
                $conds[] = '(cp_multiplier >= ' . $this->cpMultiplier[$minLevel] . $zeroIvSql . $hundoIvSql . $exMinIvSql . ')';
            }
        }
        $encSql = '';
        if ($encId != 0) {
            $encSql = " OR (encounter_id = " . $encId . " AND latitude > '" . $swLat . "' AND longitude > '" . $swLng . "' AND latitude < '" . $neLat . "' AND longitude < '" . $neLng . "' AND disappear_time > '" . $params[':time'] . "')";
        }
        return $this->query_active($conds, $params, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $zeroIv, $hundoIv, $independantPvpAndStats, $missingIvOnly, $encSql);
    }

    public function get_active_by_id($ids, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $bigKarp, $tinyRat, $zeroIv, $hundoIv, $independantPvpAndStats, $despawnTimeType, $gender, $missingIvOnly, $swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng AND disappear_time > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date->setTimestamp(time());
        $params[':time'] = date_format($date, 'Y-m-d H:i:s');

        global $noBoundaries, $boundaries, $showPokemonsOutsideBoundaries;
        if (!$noBoundaries && !$showPokemonsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }

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
        if (!empty($gender) && ($gender == 1 || $gender == 2)) {
           $conds[] = 'gender = ' . $gender;
        }
        if ($missingIvOnly) {
            $conds[] = '(individual_attack IS NULL OR individual_defense IS NULL OR individual_stamina IS NULL)';
        } else  {
            $zeroIvSql = ($zeroIv) ? ' OR (individual_attack = 0 AND individual_defense = 0 AND individual_stamina = 0)' : '';
            $hundoIvSql = ($hundoIv) ? ' OR (individual_attack = 15 AND individual_defense = 15 AND individual_stamina = 15)' : '';
            $exMinIvSql = (!empty($exMinIv)) ? ' OR pokemon_id IN(' . $exMinIv . ')' : '';
            if ($minIv !== 0) {
                $conds[] = '((individual_attack' . $float . ' + individual_defense' . $float . ' + individual_stamina' . $float . ') >= ' . ($minIv * .45) . $zeroIvSql . $hundoIvSql . $exMinIvSql . ')';
            }
            if ($minLevel !== 0) {
                $conds[] = '(cp_multiplier >= ' . $this->cpMultiplier[$minLevel] . $zeroIvSql . $hundoIvSql . $exMinIvSql . ')';
            }
        }
        return $this->query_active($conds, $params, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $zeroIv, $hundoIv, $independantPvpAndStats, $missingIvOnly, '');
    }

    public function query_active($conds, $params, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $zeroIv, $hundoIv, $independantPvpAndStats, $missingIvOnly, $encSql = '')
    {
        global $db, $noHighLevelData;

        $select = "pokemon_id, Unix_timestamp(Convert_tz(disappear_time, '+00:00', @@global.time_zone)) AS disappear_time, encounter_id, latitude, longitude, gender, form, weather_boosted_condition, costume";
        global $noHighLevelData;
        if (!$noHighLevelData) {
            $select .= ", weight, height, individual_attack, individual_defense, individual_stamina, move_1, move_2, cp, cp_multiplier, IFNULL(IF(cp_multiplier < 0.734, ROUND(58.35178527 * cp_multiplier * cp_multiplier - 2.838007664 * cp_multiplier + 0.8539209906), ROUND(171.0112688 * cp_multiplier - 95.20425243)), NULL) as level, IFNULL((individual_attack + individual_defense + individual_stamina) / 0.45, NULL) as iv,";
        }

        $query = "SELECT :select
        FROM pokemon 
        WHERE :conditions";

        $query = str_replace(":select", $select, $query);
        $query = str_replace(":conditions", '(' . join(" AND ", $conds) . ')' . $encSql, $query);
        $pokemons = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($pokemons as $pokemon) {
            $pokemon["latitude"] = floatval($pokemon["latitude"]);
            $pokemon["longitude"] = floatval($pokemon["longitude"]);
            $pokemon['expire_timestamp_verified'] = 0;
            $pokemon["first_seen_timestamp"] = null;
            $pokemon["disappear_time"] = $pokemon["disappear_time"] * 1000;

            $pokemon["weight"] = isset($pokemon["weight"]) ? floatval($pokemon["weight"]) : null;
            $pokemon["height"] = isset($pokemon["height"]) ? floatval($pokemon["height"]) : null;

            $pokemon["individual_attack"] = isset($pokemon["individual_attack"]) ? intval($pokemon["individual_attack"]) : null;
            $pokemon["individual_defense"] = isset($pokemon["individual_defense"]) ? intval($pokemon["individual_defense"]) : null;
            $pokemon["individual_stamina"] = isset($pokemon["individual_stamina"]) ? intval($pokemon["individual_stamina"]) : null;

            $pokemon["iv"] = isset($pokemon["iv"]) ? floatval($pokemon["iv"]) : null;
            $pokemon["level"] = isset($pokemon["level"]) ? intval($pokemon["level"]) : null;

            $pokemon["pvp_rankings_little_league"] = null;
            $pokemon["pvp_rankings_great_league"] = null;
            $pokemon["pvp_rankings_ultra_league"] = null;
            $pokemon["pvp_rankings_little_league_best"] = null;
            $pokemon["pvp_rankings_great_league_best"] = null;
            $pokemon["pvp_rankings_ultra_league_best"] = null;

            $pokemon["weather_boosted_condition"] = intval($pokemon["weather_boosted_condition"]);

            $pokemon["pokemon_id"] = intval($pokemon["pokemon_id"]);
            $pokemon["form"] = intval($pokemon["form"]);
            $pokemon["costume"] = intval($pokemon["costume"]);
            $pokemon["gender"] = intval($pokemon["gender"]);
            $pokemon["pokemon_name"] = i8ln($this->data[$pokemon["pokemon_id"]]['name']);
            $pokemon["pokemon_rarity"] = i8ln($this->data[$pokemon["pokemon_id"]]['rarity']);

            if (isset($pokemon["form"]) && $pokemon["form"] > 0) {
                $forms = $this->data[$pokemon["pokemon_id"]]["forms"];
                foreach ($forms as $f => $v) {
                    if ($pokemon["form"] === intval($v['protoform'])) {
                        $types = $v['formtypes'];
                        $pokemon["form_name"] = $v['nameform'];
                        foreach ($v['formtypes'] as $ft => $v) {
                            $types[$ft]['type'] = $v['type'];
                        }
                        $pokemon["pokemon_types"] = $types;
                    }
                }
            } else {
                $types = $this->data[$pokemon["pokemon_id"]]["types"];
                foreach ($types as $k => $v) {
                    $types[$k]['type'] = $v['type'];
                }
                $pokemon["pokemon_types"] = $types;
            }

            $data[] = $pokemon;
            unset($pokemons[$i]);
            $i++;
        }
        return $data;
    }

    public function get_stops($geids, $qpeids, $qeeids, $qceids, $qieids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lured = false, $rocket = false, $quests, $dustamount, $xpamount, $quests_with_ar)
    {
        $conds = array();
        $params = array();

        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        if ($oSwLat != 0) {
            $conds[] = "NOT (latitude > :oswLat AND longitude > :oswLng AND latitude < :oneLat AND longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }

        global $noBoundaries, $boundaries, $showStopsOutsideBoundaries;
        if (!$noBoundaries && !$showStopsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }

        if ($lured == "true") {
            $conds[] = "active_fort_modifier IS NOT NULL";
        }

        if ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $conds[] = "last_updated > :lastUpdated";
            $params[':lastUpdated'] = date_format($date, 'Y-m-d H:i:s');
        }

        return $this->query_stops($conds, $params, $quests_with_ar);
    }

    public function query_stops($conds, $params, $quests_with_ar)
    {
        global $db;

        $query = "SELECT Unix_timestamp(Convert_tz(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
        pokestop_id, 
        latitude, 
        longitude 
        FROM pokestop
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $pokestops = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($pokestops as $pokestop) {
            $pokestop["latitude"] = floatval($pokestop["latitude"]);
            $pokestop["longitude"] = floatval($pokestop["longitude"]);
            $pokestop["lure_expiration"] = !empty($pokestop["lure_expiration"]) ? $pokestop["lure_expiration"] * 1000 : null;
            $data[] = $pokestop;

            unset($pokestops[$i]);
            $i++;
        }
        return $data;
    }

    public function get_spawnpoints($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        $conds = array();
        $params = array();

        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        if ($oSwLat != 0) {
            $conds[] = "NOT (latitude > :oswLat AND longitude > :oswLng AND latitude < :oneLat AND longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        if ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $conds[] = "last_scanned > :lastUpdated";
            $params[':lastUpdated'] = date_format($date, 'Y-m-d H:i:s');
        }

        global $noBoundaries, $boundaries, $showSpawnsOutsideBoundaries;
        if (!$noBoundaries && !$showSpawnsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }

        return $this->query_spawnpoints($conds, $params);
    }

    private function query_spawnpoints($conds, $params)
    {
        global $db;

        $query = "SELECT latitude, 
        longitude, 
        id AS spawnpoint_id,
        latest_seen,
        earliest_unseen,
        links,
        kind
        FROM spawnpoint
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $spawnpoints = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($spawnpoints as $spawnpoint) {
            $spawnpoint["latitude"] = floatval($spawnpoint["latitude"]);
            $spawnpoint["longitude"] = floatval($spawnpoint["longitude"]);
            $spawnpoint["time"] = $this->get_disappear_time($spawnpoint);
            $data[] = $spawnpoint;

            unset($spawnpoints[$i]);
            $i++;
        }
        return $data;
    }

    private function get_disappear_time($spawnpoint)
    {
        $links = $spawnpoint["links"];

        if ($links == "????") {
            $links = str_replace("?", "s", $spawnpoint["kind"]);
        }
        if (substr_count($links, "-") == 0) {
            $links = substr($links, 0, -1) . '-';
        }
        $links = str_replace("+", "?", $links);
        $links = substr($links, 0, -1) . '-';

        $check = false && $spawnpoint["latest_seen"] != $spawnpoint["earliest_unseen"] ? true : false;
        $no_tth_adjust = $check ? 60 : 0;
        $end = $spawnpoint["latest_seen"] - (3 - strpos($links, "-")) * 900 + $no_tth_adjust;

        return $end % 3600;
    }

    public function get_gyms($rbeids, $reeids, $swLat, $swLng, $neLat, $neLng, $exEligible = false, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $raids, $gyms)
    {
        $conds = array();
        $params = array();

        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        if ($oSwLat != 0) {
            $conds[] = "NOT (latitude > :oswLat AND longitude > :oswLng AND latitude < :oneLat AND longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        if ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $conds[] = "gym.last_scanned > :lastUpdated";
            $params[':lastUpdated'] = date_format($date, 'Y-m-d H:i:s');
        }
        global $noBoundaries, $boundaries, $showGymsOutsideBoundaries;
        if (!$noBoundaries && !$showGymsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ((!empty($raids) && $raids === 'true') && (!empty($gyms) && $gyms === 'false')) {
            $raidsSQL = '';
            if (count($rbeids)) {
                $raid_in = '';
                $r = 1;
                foreach ($rbeids as $rbeid) {
                    $params[':rbqry_' . $r . '_'] = $rbeid;
                    $raid_in .= ':rbqry_' . $r . '_,';
                    $r++;
                }
                $raid_in = substr($raid_in, 0, -1);
                $raidsSQL .= "raid.pokemon_id NOT IN ( $raid_in )";
            } else {
                $raidsSQL .= "raid.pokemon_id IS NOT NULL";
            }
            $eggSQL = '';
            if (count($reeids)) {
                $egg_in = '';
                $e = 1;
                foreach ($reeids as $reeid) {
                    $params[':reqry_' . $e . '_'] = $reeid;
                    $egg_in .= ':reqry_' . $e . '_,';
                    $e++;
                }
                $egg_in = substr($egg_in, 0, -1);
                $eggSQL .= "level NOT IN ( $egg_in )";
            } else {
                $eggSQL .= "level IS NOT NULL";
            }
            $conds[] = "" . $eggSQL . "";
            $conds[] = "" . $raidsSQL . "";
        }
        if ($exEligible === "true") {
            $conds[] = "(park = 1)";
        }

        return $this->query_gyms($conds, $params, $raids, $gyms, $rbeids, $reeids);
    }

    public function query_gyms($conds, $params, $raids, $gyms, $rbeids, $reeids)
    {
        global $db, $noTeams, $noExEligible;

        $query = "SELECT gym.gym_id,
        latitude,
        longitude,
        slots_available,
        Unix_timestamp(Convert_tz(last_modified, '+00:00', @@global.time_zone)) AS last_modified,
        Unix_timestamp(Convert_tz(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
        team_id,
        name,
        park,
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
            $raid_pid = $gym["raid_pokemon_id"];
            if ($raid_pid == "0") {
                $raid_pid = null;
                $gym["raid_pokemon_id"] = null;
            }
            $gym["team_id"] = $noTeams ? 0 : intval($gym["team_id"]);
            $gym["pokemon"] = [];
            $gym["raid_pokemon_name"] = empty($raid_pid) ? null : i8ln($this->data[$raid_pid]["name"]);
            $gym["raid_pokemon_form"] = 0;
            $gym["raid_pokemon_costume"] = 0;
            $gym["raid_pokemon_evolution"] = 0;
            $gym["raid_pokemon_gender"] = 0;
            $gym["latitude"] = floatval($gym["latitude"]);
            $gym["longitude"] = floatval($gym["longitude"]);
            $gym["slots_available"] = $noTeams ? 0 : intval($gym["slots_available"]);
            $gym["last_modified"] = $gym["last_modified"] * 1000;
            $gym["in_battle"] = 0;
            $gym["last_scanned"] = $gym["last_scanned"] * 1000;
            $gym["raid_start"] = $gym["raid_start"] * 1000;
            $gym["raid_end"] = $gym["raid_end"] * 1000;
            $gym["park"] = $noExEligible ? 0 : intval($gym["park"]);
            if (isset($gym["raid_pokemon_form"]) && $gym["raid_pokemon_form"] > 0) {
                $forms = $this->data[$gym["raid_pokemon_id"]]["forms"];
                foreach ($forms as $f => $v) {
                    if ($gym["raid_pokemon_form"] === intval($v['protoform'])) {
                        $gym["raid_pokemon_form_name"] = $v['nameform'];
                    }
                }
            }
            if ((!empty($raids) && $raids === 'true') && (!empty($gyms) && $gyms === 'true')) {
                if (count($rbeids)) {
                    foreach ($rbeids as $rbeid) {
                        if ($rbeid == $gym["raid_pokemon_id"]) {
                            $gym["raid_pokemon_id"] = null;
                            $gym["raid_end"] = null;
                            $gym["raid_start"] = null;
                            $gym["raid_level"] = null;
                            $gym["raid_pokemon_move_1"] = null;
                            $gym["raid_pokemon_move_2"] = null;
                            $gym["raid_pokemon_form"] = null;
                            $gym["raid_pokemon_costume"] = null;
                            $gym["raid_pokemon_cp"] = null;
                            $gym["raid_pokemon_gender"] = null;
                            $gym["raid_pokemon_evolution"] = null;
                            break;
                        }
                    }
                }
                if (count($reeids)) {
                    foreach ($reeids as $reeid) {
                        if ($rbeid == $gym["raid_pokemon_id"]) {
                            $gym["raid_pokemon_id"] = null;
                            $gym["raid_end"] = null;
                            $gym["raid_start"] = null;
                            $gym["raid_level"] = null;
                            $gym["raid_pokemon_move_1"] = null;
                            $gym["raid_pokemon_move_2"] = null;
                            $gym["raid_pokemon_form"] = null;
                            $gym["raid_pokemon_costume"] = null;
                            $gym["raid_pokemon_cp"] = null;
                            $gym["raid_pokemon_gender"] = null;
                            $gym["raid_pokemon_evolution"] = null;
                            break;
                        }
                    }
                }
            }
            $data[] = $gym;
            unset($gyms[$i]);
            $i++;
        }
        return $data;
    }

    public function get_weather_by_cell_id($cell_id)
    {
        global $db;
        $query = "SELECT s2_cell_id, gameplay_weather FROM weather WHERE s2_cell_id = :cell_id";
        $params = [':cell_id' => $cell_id]; // use float to intval because RM is signed int
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
        $data = array();
        foreach ($weathers as $weather) {
            $data["weather_" . $weather['s2_cell_id']] = $weather;
            $data["weather_" . $weather['s2_cell_id']]['condition'] = $data["weather_" . $weather['s2_cell_id']]['gameplay_weather'];
            unset($data["weather_" . $weather['s2_cell_id']]['gameplay_weather']);
        }
        return $data;
    }

    private function setCpMultiplier()
    {
        $this->cpMultiplier = array(
            1 => 0.094,
            2 => 0.16639787,
            3 => 0.21573247,
            4 => 0.25572005,
            5 => 0.29024988,
            6 => 0.3210876,
            7 => 0.34921268,
            8 => 0.37523559,
            9 => 0.39956728,
            10 => 0.42250001,
            11 => 0.44310755,
            12 => 0.46279839,
            13 => 0.48168495,
            14 => 0.49985844,
            15 => 0.51739395,
            16 => 0.53435433,
            17 => 0.55079269,
            18 => 0.56675452,
            19 => 0.58227891,
            20 => 0.59740001,
            21 => 0.61215729,
            22 => 0.62656713,
            23 => 0.64065295,
            24 => 0.65443563,
            25 => 0.667934,
            26 => 0.68116492,
            27 => 0.69414365,
            28 => 0.70688421,
            29 => 0.71939909,
            30 => 0.7317,
            31 => 0.73776948,
            32 => 0.74378943,
            33 => 0.74976104,
            34 => 0.75568551,
            35 => 0.76156384
        );
    }
}
