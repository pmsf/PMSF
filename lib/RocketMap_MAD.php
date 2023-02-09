<?php

namespace Scanner;

class RocketMap_MAD extends RocketMap
{
    public function get_active($eids, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $bigKarp, $tinyRat, $zeroIv, $hundoIv, $independantPvpAndStats, $despawnTimeType, $gender, $missingIvOnly, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $encId = 0)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $conds[] = "p.latitude > :swLat AND p.longitude > :swLng AND p.latitude < :neLat AND p.longitude < :neLng AND disappear_time > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date->setTimestamp(time());
        $params[':time'] = date_format($date, 'Y-m-d H:i:s');

        if ($oSwLat != 0) {
            $conds[] = "NOT (p.latitude > :oswLat AND p.longitude > :oswLng AND p.latitude < :oneLat AND p.longitude < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries, $showPokemonsOutsideBoundaries;
        if (!$noBoundaries && !$showPokemonsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(p.latitude,p.longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
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
        if (!empty($despawnTimeType)) {
            if ($despawnTimeType == 1) {
                $conds[] = 'ts.calc_endminsec IS NOT NULL';
            } elseif ($despawnTimeType == 2) {
                $conds[] = '(ts.calc_endminsec IS NULL AND spawnpoint_id IS NOT NULL)';
            } elseif ($despawnTimeType == 3) {
                $conds[] = 'ts.calc_endminsec IS NULL';
            }
        }
        if (!empty($gender) && ($gender == 1 || $gender == 2)) {
            $conds[] = 'gender = ' . $gender;
        }
        if ($missingIvOnly) {
            $conds[] = '(individual_attack IS NULL OR individual_defense IS NULL OR individual_stamina IS NULL)';
        } else {
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
            $encSql = " OR (encounter_id = " . $encId . " AND p.latitude > '" . $swLat . "' AND p.longitude > '" . $swLng . "' AND p.latitude < '" . $neLat . "' AND p.longitude < '" . $neLng . "' AND disappear_time > '" . $params[':time'] . "')";
        }
        return $this->query_active($conds, $params, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $zeroIv, $hundoIv, $independantPvpAndStats, $missingIvOnly, $encSql);
    }

    public function get_active_by_id($ids, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $bigKarp, $tinyRat, $zeroIv, $hundoIv, $independantPvpAndStats, $despawnTimeType, $gender, $missingIvOnly, $swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        $conds = array();
        $params = array();
        $float = $db->info()['driver'] == 'pgsql' ? "::float" : "";

        $conds[] = "p.latitude > :swLat AND p.longitude > :swLng AND p.latitude < :neLat AND p.longitude < :neLng AND disappear_time > :time";
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
            $conds[] = "(ST_WITHIN(point(p.latitude,p.longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
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
        if (!empty($despawnTimeType)) {
            if ($despawnTimeType == 1) {
                $conds[] = 'ts.calc_endminsec IS NOT NULL';
            } elseif ($despawnTimeType == 2) {
                $conds[] = '(ts.calc_endminsec IS NULL AND spawnpoint_id IS NOT NULL)';
            } elseif ($despawnTimeType == 3) {
                $conds[] = 'ts.calc_endminsec IS NULL';
            }
        }
        if (!empty($gender) && ($gender == 1 || $gender == 2)) {
            $conds[] = 'gender = ' . $gender;
        }
        if ($missingIvOnly) {
            $conds[] = '(individual_attack IS NULL OR individual_defense IS NULL OR individual_stamina IS NULL)';
        } else {
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


        $select = "ts.calc_endminsec AS expire_timestamp_verified,
        pokemon_id,
        UNIX_TIMESTAMP(CONVERT_TZ(disappear_time, '+00:00', @@global.time_zone)) AS disappear_time,
        encounter_id,
        p.latitude,
        p.longitude,
        gender,
        form,
        weather_boosted_condition,
        costume,
        seen_type";

        $sqlSizeColumn = ($this->columnExists("pokemon","size") ? "size," : "");
        if (!$noHighLevelData) {
            $select .= ",
            weight,
            height,
            $sqlSizeColumn
            individual_attack,
            individual_defense,
            individual_stamina,
            move_1,
            move_2,
            cp,
            IFNULL(IF(cp_multiplier < 0.734, ROUND(58.35178527 * cp_multiplier * cp_multiplier - 2.838007664 * cp_multiplier + 0.8539209906), ROUND(171.0112688 * cp_multiplier - 95.20425243)), NULL) as level,
            IFNULL((individual_attack + individual_defense + individual_stamina) / 0.45, NULL) as iv,
            catch_prob_1 AS catch_rate_1,
            catch_prob_2 AS catch_rate_2,
            catch_prob_3 AS catch_rate_3";
        }

        $query = "SELECT :select
        FROM pokemon p
        LEFT JOIN trs_spawn ts ON ts.spawnpoint = p.spawnpoint_id
        WHERE :conditions ORDER BY p.latitude, p.longitude";

        $query = str_replace(":select", $select, $query);
        $query = str_replace(":conditions", '(' . join(" AND ", $conds) . ')' . $encSql, $query);
        $pokemons = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;
        $lastlat = 0;
        $lastlon = 0;
        $lasti = 0;

        foreach ($pokemons as $pokemon) {
            // Jitter for nearby that would otherwise stack on top of each other
            if ($pokemon['seen_type'] === 'nearby_cell' || $pokemon['seen_type'] === 'nearby_stop') {
                $pokemon["latitude"] = floatval($pokemon["latitude"]);
                $pokemon["longitude"] = floatval($pokemon["longitude"]);
                $lastlat = floatval($pokemon["latitude"]);
                $lastlon = floatval($pokemon["longitude"]);
                if (abs($pokemon["latitude"] - $lastlat) < 0.0001 && abs($pokemon["longitude"] - $lastlon) < 0.0001) {
                    $lasti = $lasti + 1;
                } else {
                    $lasti = 0;
                }
                $pokemon["latitude"] = $pokemon["latitude"] + 0.0003*cos(deg2rad($lasti*45));
                $pokemon["longitude"] = $pokemon["longitude"] + 0.0003*sin(deg2rad($lasti*45));
            } else {
                $pokemon["latitude"] = floatval($pokemon["latitude"]);
                $pokemon["longitude"] = floatval($pokemon["longitude"]);
            }
            $pokemon["expire_timestamp_verified"] = isset($pokemon["expire_timestamp_verified"]) ? 1 : 0;
            $pokemon["first_seen_timestamp"] = null;
            $pokemon["disappear_time"] = $pokemon["disappear_time"] * 1000;

            $pokemon["weight"] = isset($pokemon["weight"]) ? floatval($pokemon["weight"]) : null;
            $pokemon["height"] = isset($pokemon["height"]) ? floatval($pokemon["height"]) : null;
            $pokemon["size"] = (isset($pokemon["size"]) && isset($this->pokemonSize[$pokemon["size"]])) ? $this->pokemonSize[$pokemon["size"]] : null;

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

    // This is based on assumption from the last version I saw
    public function get_weather_by_cell_id($cell_id)
    {
        global $db;
        $query = "SELECT s2_cell_id, gameplay_weather, UNIX_TIMESTAMP(CONVERT_TZ(last_updated, '+00:00', @@global.time_zone)) AS updated FROM weather WHERE s2_cell_id = :cell_id";
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
        $query = "SELECT s2_cell_id, gameplay_weather, UNIX_TIMESTAMP(CONVERT_TZ(last_updated, '+00:00', @@global.time_zone)) AS updated FROM weather";
        $weathers = $db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        foreach ($weathers as $weather) {
            $data["weather_" . $weather['s2_cell_id']] = $weather;
            $data["weather_" . $weather['s2_cell_id']]['condition'] = $data["weather_" . $weather['s2_cell_id']]['gameplay_weather'];
            unset($data["weather_" . $weather['s2_cell_id']]['gameplay_weather']);
        }
        return $data;
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
        global $noBoundaries, $boundaries, $showGymsOutsideBoundaries;
        if (!$noBoundaries && !$showGymsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ((!empty($raids) && $raids === 'true') && (!empty($gyms) && $gyms === 'false')) {
            $raidSQL = '';
            if (count($rbeids)) {
                $raid_in = '';
                $r = 1;
                foreach ($rbeids as $rbeid) {
                    $params[':rbqry_' . $r . "_"] = $rbeid;
                    $raid_in .= ':rbqry_' . $r . "_,";
                    $r++;
                }
                $raid_in = substr($raid_in, 0, -1);
                $raidSQL .= "raid.pokemon_id NOT IN ( $raid_in )";
            } else {
                $raidSQL .= "raid.pokemon_id IS NOT NULL";
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
                $eggSQL .= "raid.pokemon_id IS NULL AND raid.level NOT IN ( $egg_in )";
            } else {
                $eggSQL .= "raid.pokemon_id IS NULL AND raid.level IS NOT NULL";
            }
            $conds[] = "(" . $raidSQL . " OR " . $eggSQL . ")";
        }
        if ($tstamp > 0) {
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($tstamp);
            $conds[] = "gym.last_scanned > :lastUpdated";
            $params[':lastUpdated'] = date_format($date, 'Y-m-d H:i:s');
        }
        if ($exEligible === "true") {
            $conds[] = "(is_ex_raid_eligible = 1)";
        }

        return $this->query_gyms($conds, $params, $raids, $gyms, $rbeids, $reeids);
    }

    public function query_gyms($conds, $params, $raids, $gyms, $rbeids, $reeids)
    {
        global $db, $noTeams, $noExEligible, $noInBattle;

        $query = "SELECT gym.gym_id,
        latitude,
        longitude,
        slots_available,
        UNIX_TIMESTAMP(CONVERT_TZ(last_modified, '+00:00', @@global.time_zone)) AS last_modified,
        UNIX_TIMESTAMP(CONVERT_TZ(gym.last_scanned, '+00:00', @@global.time_zone)) AS last_scanned,
        team_id,
        name,
        url,
        is_in_battle as in_battle,
        is_ex_raid_eligible AS park,
        raid.level AS raid_level,
        raid.pokemon_id AS raid_pokemon_id,
        raid.form AS raid_pokemon_form,
        raid.costume AS raid_pokemon_costume,
        raid.evolution AS raid_pokemon_evolution,
        raid.gender AS raid_pokemon_gender,
        raid.cp AS raid_pokemon_cp,
        raid.move_1 AS raid_pokemon_move_1,
        raid.move_2 AS raid_pokemon_move_2,
        UNIX_TIMESTAMP(CONVERT_TZ(start, '+00:00', @@global.time_zone)) AS raid_start,
        UNIX_TIMESTAMP(CONVERT_TZ(end, '+00:00', @@global.time_zone)) AS raid_end
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
            $gym["raid_pokemon_form"] = intval($gym["raid_pokemon_form"]);
            $gym["raid_pokemon_costume"] = intval($gym["raid_pokemon_costume"]);
            $gym["raid_pokemon_evolution"] = intval($gym["raid_pokemon_evolution"]);
            $gym["raid_pokemon_gender"] = intval($gym["raid_pokemon_gender"]);
            $gym["latitude"] = floatval($gym["latitude"]);
            $gym["longitude"] = floatval($gym["longitude"]);
            $gym["slots_available"] = $noTeams ? 0 : intval($gym["slots_available"]);
            $gym["in_battle"] = $noInBattle ? 0 : intval($gym["in_battle"]);
            $gym["last_modified"] = $gym["last_modified"] * 1000;
            $gym["last_scanned"] = $gym["last_scanned"] * 1000;
            $gym["raid_start"] = $gym["raid_start"] * 1000;
            $gym["raid_end"] = $gym["raid_end"] * 1000;
            $gym["url"] = !empty($gym["url"]) ? preg_replace("/^http:/i", "https:", $gym["url"]) : null;
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
        global $noBoundaries, $boundaries, $showSpawnsOutsideBoundaries;
        if (!$noBoundaries && !$showSpawnsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $conds[] = "last_scanned > from_unixtime(:lastUpdated)";
            $params[':lastUpdated'] = $tstamp;
        }
        return $this->query_spawnpoints($conds, $params);
    }

    private function query_spawnpoints($conds, $params)
    {
        global $db;
        $query = "SELECT latitude,
        longitude,
        spawnpoint AS spawnpoint_id,
        calc_endminsec AS calc_endminsec,
        (SUBSTRING_INDEX(SUBSTRING_INDEX(calc_endminsec, ':', 1), ' ', -1)*60) + (SUBSTRING_INDEX(SUBSTRING_INDEX(calc_endminsec, ':', -1), ' ', -1)) AS time
        FROM trs_spawn
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $spawnpoints = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($spawnpoints as $spawnpoint) {
            $spawnpoint["latitude"] = floatval($spawnpoint["latitude"]);
            $spawnpoint["longitude"] = floatval($spawnpoint["longitude"]);
            $spawnpoint["time"] = is_null($spawnpoint["calc_endminsec"]) ? null : intval($spawnpoint["time"]);
            $data[] = $spawnpoint;
            unset($spawnpoints[$i]);
            $i++;
        }
        return $data;
    }

    public function get_stops($geids, $qpeids, $qeeids, $qceids, $qieids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lured = false, $eventstops = false, $rocket = false, $quests, $dustamount, $xpamount, $quests_with_ar)
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
        if (!empty($quests) && $quests === 'true') {
            $pokemonSQL = '';
            if (count($qpeids)) {
                $pkmn_in = '';
                $p = 1;
                foreach ($qpeids as $qpeid) {
                    $params[':pqry_' . $p . "_"] = $qpeid;
                    $pkmn_in .= ':pqry_' . $p . "_,";
                    $p++;
                }
                $pkmn_in = substr($pkmn_in, 0, -1);
                $pokemonSQL .= "tq.quest_pokemon_id NOT IN ( $pkmn_in ) AND quest_reward_type = 7";
            } else {
                $pokemonSQL .= "tq.quest_pokemon_id IS NOT NULL AND quest_reward_type = 7";
            }
            $energySQL = '';
            if (count($qeeids)) {
                $pkmn_in = '';
                $p = 1;
                foreach ($qeeids as $qeeid) {
                    $params[':eqry_' . $p . "_"] = $qeeid;
                    $pkmn_in .= ':eqry_' . $p . "_,";
                    $p++;
                }
                $pkmn_in = substr($pkmn_in, 0, -1);
                $energySQL .= "tq.quest_pokemon_id NOT IN ( $pkmn_in ) AND tq.quest_reward_type = 12";
            } else {
                $energySQL .= "tq.quest_reward_type = 12";
            }
            $candySQL = '';
            if (count($qceids)) {
                $pkmn_in = '';
                $p = 1;
                foreach ($qceids as $qceid) {
                    $params[':cqry_' . $p . "_"] = $qceid;
                    $pkmn_in .= ':cqry_' . $p . "_,";
                    $p++;
                }
                $pkmn_in = substr($pkmn_in, 0, -1);
                $candySQL .= "tq.quest_pokemon_id NOT IN ( $pkmn_in ) AND tq.quest_reward_type = 4";
            } else {
                $candySQL .= "tq.quest_reward_type = 4";
            }
            $itemSQL = '';
            if (count($qieids)) {
                $item_in = '';
                $i = 1;
                foreach ($qieids as $qieid) {
                    $params[':iqry_' . $i . "_"] = $qieid;
                    $item_in .= ':iqry_' . $i . "_,";
                    $i++;
                }
                $item_in = substr($item_in, 0, -1);
                $itemSQL .= "tq.quest_item_id NOT IN ( $item_in )";
            } else {
                $itemSQL .= "tq.quest_item_id IS NOT NULL";
            }
            $dustSQL = '';
            if (!empty($dustamount) && !is_nan((float)$dustamount) && $dustamount > 0) {
                $dustSQL .= " OR (tq.quest_reward_type = 3 AND tq.quest_stardust >= :dustamount)";
                $params[':dustamount'] = intval($dustamount);
            }
            $xpSQL = '';
            if (!empty($xpamount) && !is_nan((float)$xpamount) && $xpamount > 0) {
                $xpSQL .= " OR (tq.quest_reward_type = 1 AND tq.quest_stardust >= :xpamount)";
                $params[':xpamount'] = intval($xpamount);
            }
            $conds[] = "((" . $pokemonSQL . ") OR (" . $itemSQL . ") OR (" . $energySQL . ") OR (" . $candySQL . ")" . $dustSQL . $xpSQL . ")";
        }
        if (!empty($rocket) && $rocket === 'true') {
            $rocketSQL = '';
            $legacyIncidents = ($this->columnExists("pokestop_incident","pokestop_id")) ? false : true;
            if (count($geids)) {
                $rocket_in = '';
                $r = 1;
                foreach ($geids as $geid) {
                    $params[':rqry_' . $r . "_"] = $geid;
                    $rocket_in .= ':rqry_' . $r . "_,";
                    $r++;
                }
                $rocket_in = substr($rocket_in, 0, -1);
                if ($legacyIncidents) {
                    $rocketSQL .= "incident_grunt_type NOT IN ( $rocket_in )";
                } else {
                    $rocketSQL .= "i.`character_display` NOT IN ( $rocket_in )";
                }
            } else {
                if ($legacyIncidents) {
                    $rocketSQL .= "incident_grunt_type > 0";
                } else {
                    $rocketSQL .= "i.`incident_display_type` > 0";
                }
            }
            $conds[] = "" . $rocketSQL . "";
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

    public function get_stops_quest($greids, $qpreids, $qereids, $qcreids, $qireids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $eventstops, $rocket, $quests, $dustamount, $reloaddustamount, $xpamount, $reloadxpamount, $quests_with_ar)
    {
        $conds = array();
        $params = array();
        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(latitude,longitude),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if (!empty($quests) && $quests === 'true') {
            $tmpSQL = '';
            if (count($qpreids)) {
                $pkmn_in = '';
                $p = 1;
                foreach ($qpreids as $qpreid) {
                    $params[':pqry_' . $p . "_"] = $qpreid;
                    $pkmn_in .= ':pqry_' . $p . "_,";
                    $p++;
                }
                $pkmn_in = substr($pkmn_in, 0, -1);
                $tmpSQL .= "tq.quest_pokemon_id IN ( $pkmn_in ) AND tq.quest_reward_type = 7";
            }
            if (count($qereids)) {
                $pkmn_in = '';
                $p = 1;
                foreach ($qereids as $qereid) {
                    $params[':eqry_' . $p . "_"] = $qereid;
                    $pkmn_in .= ':eqry_' . $p . "_,";
                    $p++;
                }
                $pkmn_in = substr($pkmn_in, 0, -1);
                $tmpSQL .= "tq.quest_pokemon_id IN ( $pkmn_in ) AND tq.quest_reward_type = 12";
            }
            if (count($qcreids)) {
                $pkmn_in = '';
                $p = 1;
                foreach ($qcreids as $qcreid) {
                    $params[':cqry_' . $p . "_"] = $qcreid;
                    $pkmn_in .= ':cqry_' . $p . "_,";
                    $p++;
                }
                $pkmn_in = substr($pkmn_in, 0, -1);
                $tmpSQL .= "tq.quest_pokemon_id IN ( $pkmn_in ) AND tq.quest_reward_type = 4";
            }
            if (count($qireids)) {
                $item_in = '';
                $i = 1;
                foreach ($qireids as $qireid) {
                    $params[':iqry_' . $i . "_"] = $qireid;
                    $item_in .= ':iqry_' . $i . "_,";
                    $i++;
                }
                $item_in = substr($item_in, 0, -1);
                $tmpSQL .= "tq.quest_item_id IN ( $item_in )";
            }
            if ($reloaddustamount == "true") {
                $tmpSQL .= "(tq.quest_reward_type = 3 AND tq.quest_stardust > :dustamount)";
                $params[':dustamount'] = intval($dustamount);
            }
            if ($reloadxpamount == "true") {
                $tmpSQL .= "(tq.quest_reward_type = 1 AND tq.quest_stardust > :xpamount)";
                $params[':xpamount'] = intval($xpamount);
            }
            $conds[] = $tmpSQL;
        }
        if (!empty($rocket) && $rocket === 'true') {
            $rocketSQL = '';
            $gruntCol = ($this->columnExists("pokestop_incident","pokestop_id")) ? "i.`character_display`" : "incident_grunt_type";
            if (count($greids)) {
                $rocket_in = '';
                $r = 1;
                foreach ($greids as $greid) {
                    $params[':rqry_' . $r . "_"] = $greid;
                    $rocket_in .= ':rqry_' . $r . "_,";
                    $r++;
                }
                $rocket_in = substr($rocket_in, 0, -1);
                $rocketSQL .= "{$gruntCol} IN ( $rocket_in )";
            }
            $conds[] = "" . $rocketSQL . "";
        }
        return $this->query_stops($conds, $params, $quests_with_ar);
    }

    public function query_stops($conds, $params, $quests_with_ar)
    {
        global $db, $noQuests, $noQuestsPokemon, $noQuestsItems, $noQuestsEnergy, $noQuestsCandy, $noQuestsStardust, $noQuestsXP, $noEventStops, $noLures, $noTeamRocket;

        if ($this->columnExists("trs_quest","layer")) {
            if ($quests_with_ar) {
                $sqlQuestLayer = " AND tq.layer = 1 ";
            } else {
                $sqlQuestLayer = " AND tq.layer = 0 ";
            }
        } else {
            $sqlQuestLayer = "";
        }

        if ($this->columnExists("pokestop_incident","pokestop_id")) {
            $query = "
            SELECT
                p.pokestop_id,
                p.latitude,
                p.longitude,
                p.name AS pokestop_name,
                p.image AS url,
                UNIX_TIMESTAMP(CONVERT_TZ(p.last_updated, '+00:00', @@global.time_zone)) AS last_seen,
                p.active_fort_modifier AS lure_id,
                UNIX_TIMESTAMP(CONVERT_TZ(p.lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
                i.character_display AS grunt_type,
                UNIX_TIMESTAMP(CONVERT_TZ(i.incident_expiration, '+00:00', @@global.time_zone)) AS incident_expiration,
                i.incident_display_type AS incident_display_type,
                tq.quest_type,
                tq.quest_timestamp,
                tq.quest_reward,
                tq.quest_pokemon_id AS reward_pokemon_id,
                tq.quest_item_id AS reward_item_id,
                tq.quest_task,
                tq.quest_reward_type,
                tq.quest_pokemon_form_id AS reward_pokemon_formid,
                json_extract(json_extract(tq.`quest_reward`,'$[*].pokemon_encounter.pokemon_display.is_shiny'),'$[0]') AS reward_pokemon_shiny,
                json_extract(json_extract(tq.`quest_reward`,'$[*].pokemon_encounter.pokemon_display.costume_value'),'$[0]') AS reward_pokemon_costumeid,
                json_extract(json_extract(tq.`quest_reward`,'$[*].pokemon_encounter.pokemon_display.gender_value'),'$[0]') AS reward_pokemon_genderid,
                tq.quest_item_amount AS reward_item_amount,
                tq.quest_stardust AS reward_dust_amount,
                json_extract(json_extract(tq.`quest_reward`,'$[*].candy.amount'),'$[0]') AS reward_candy_amount,
                json_extract(json_extract(tq.`quest_reward`,'$[*].candy.pokemon_id'),'$[0]') AS reward_candy_pokemon_id
            FROM pokestop p
            LEFT JOIN trs_quest tq ON tq.GUID = p.pokestop_id $sqlQuestLayer
            LEFT JOIN pokestop_incident i ON i.pokestop_id = p.pokestop_id AND i.incident_expiration > UTC_TIMESTAMP()
            WHERE :conditions";
        } else {
            $query = "
            SELECT
                pokestop_id,
                latitude,
                longitude,
                name AS pokestop_name,
                image AS url,
                UNIX_TIMESTAMP(CONVERT_TZ(last_updated, '+00:00', @@global.time_zone)) AS last_seen,
                active_fort_modifier AS lure_id,
                UNIX_TIMESTAMP(CONVERT_TZ(lure_expiration, '+00:00', @@global.time_zone)) AS lure_expiration,
                incident_grunt_type AS grunt_type,
                UNIX_TIMESTAMP(CONVERT_TZ(incident_expiration, '+00:00', @@global.time_zone)) AS incident_expiration,
                tq.quest_type,
                tq.quest_timestamp,
                tq.quest_reward,
                tq.quest_pokemon_id AS reward_pokemon_id,
                tq.quest_item_id AS reward_item_id,
                tq.quest_task,
                tq.quest_reward_type,
                tq.quest_pokemon_form_id AS reward_pokemon_formid,
                json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.is_shiny'),'$[0]') AS reward_pokemon_shiny,
                json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.costume_value'),'$[0]') AS reward_pokemon_costumeid,
                json_extract(json_extract(`quest_reward`,'$[*].pokemon_encounter.pokemon_display.gender_value'),'$[0]') AS reward_pokemon_genderid,
                tq.quest_item_amount AS reward_item_amount,
                tq.quest_stardust AS reward_dust_amount,
                json_extract(json_extract(`quest_reward`,'$[*].candy.amount'),'$[0]') AS reward_candy_amount,
                json_extract(json_extract(`quest_reward`,'$[*].candy.pokemon_id'),'$[0]') AS reward_candy_pokemon_id
            FROM pokestop p
            LEFT JOIN trs_quest tq ON tq.GUID = p.pokestop_id $sqlQuestLayer
            WHERE :conditions";
        }

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $pokestops = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($pokestops as $pokestop) {
            if (isset($data[$pokestop["pokestop_id"]])) {
                // duplicate stop due to multiple incidents. everything already processed but need to make sure same grunt is always returned to frontend until multiple invasion support.
                if (!$noTeamRocket && isset($pokestop["grunt_type"]) && intval($pokestop["grunt_type"]) > 0) {
                    $updateGrunt = false;
                    if (!isset($data[$pokestop['pokestop_id']]['grunt_type'])) {
                        $updateGrunt = true;
                    } elseif (intval($data[$pokestop["pokestop_id"]]["grunt_type"] === 44) && (intval($pokestop["grunt_type"]) < 44 || intval($pokestop["grunt_type"]) > 44)) {
                        $updateGrunt = true;
                    } elseif (in_array(intval($data[$pokestop["pokestop_id"]]["grunt_type"]), [41,42,43]) && (intval($pokestop["grunt_type"]) < 41 || intval($pokestop["grunt_type"]) > 44)) {
                        $updateGrunt = true;
                    } elseif (intval($pokestop["grunt_type"]) < intval($data[$pokestop["pokestop_id"]]["grunt_type"])) {
                        $updateGrunt = true;
                    }
                    if ($updateGrunt) {
                        $data[$pokestop["pokestop_id"]]["grunt_type"] = $pokestop["grunt_type"];
                        $data[$pokestop["pokestop_id"]]["grunt_type_name"] = i8ln($this->grunttype[$pokestop["grunt_type"]]["type"]);
                        $data[$pokestop["pokestop_id"]]["grunt_type_gender"] = i8ln($this->grunttype[$pokestop["grunt_type"]]["grunt"]);
                        $data[$pokestop["pokestop_id"]]["encounters"] = empty($this->grunttype[$pokestop["grunt_type"]]["encounters"]) ? null : $this->grunttype[$pokestop["grunt_type"]]["encounters"];
                        $data[$pokestop["pokestop_id"]]["second_reward"] = empty($this->grunttype[$pokestop["grunt_type"]]["second_reward"]) ? null : $this->grunttype[$pokestop["grunt_type"]]["second_reward"];
                        $data[$pokestop["pokestop_id"]]["incident_expiration"] = $pokestop["incident_expiration"] * 1000;
                    }
                } elseif (!$noEventStops && isset($pokestop["incident_display_type"]) && intval($pokestop["incident_display_type"] >= 7)) {
                    $data[$pokestop["pokestop_id"]]["eventstops_id"] = intval($pokestop["incident_display_type"]);
                    $data[$pokestop["pokestop_id"]]["eventstops_expiration"] = !empty($pokestop["incident_expiration"]) ? $pokestop["incident_expiration"] * 1000 : null;
                }
            } else {
                $pokestop["url"] = !empty($pokestop["url"]) ? preg_replace("/^http:/i", "https:", $pokestop["url"]) : null;
                $pokestop["last_seen"] = $pokestop["last_seen"] * 1000;
                $pokestop["latitude"] = floatval($pokestop["latitude"]);
                $pokestop["longitude"] = floatval($pokestop["longitude"]);

                if (
                    $noQuests ||
                    ($noQuestsEnergy && intval($pokestop["quest_reward_type"]) === 12) ||
                    ($noQuestsPokemon && intval($pokestop["quest_reward_type"]) === 7) ||
                    ($noQuestsCandy && intval($pokestop["quest_reward_type"]) === 4) ||
                    ($noQuestsStardust && intval($pokestop["quest_reward_type"]) === 3) ||
                    ($noQuestsItems && intval($pokestop["quest_reward_type"]) === 2) ||
                    ($noQuestsXP && intval($pokestop["quest_reward_type"]) === 1)
                ) {
                    $pokestop["quest_type"] = 0;
                    $pokestop["quest_with_artask"] = null;
                    $pokestop["quest_reward_type"] = 0;
                    $pokestop["quest_condition_type"] = 0;
                    $pokestop["quest_condition_type_1"] = 0;
                    $pokestop["quest_target"] = 0;
                    $pokestop["reward_pokemon_id"] = 0;
                    $pokestop["reward_pokemon_name"] = null;
                    $pokestop["reward_pokemon_formid"] = 0;
                    $pokestop["reward_pokemon_costumeid"] = 0;
                    $pokestop["reward_pokemon_genderid"] = 0;
                    $pokestop["reward_pokemon_shiny"] = 0;
                    $pokestop["reward_item_id"] = 0;
                    $pokestop["reward_item_name"] = null;
                    $pokestop["reward_amount"] = 0;
                    $pokestop["quest_condition_info"] = null;
                } else {
                    $item_pid = $pokestop["reward_item_id"];
                    if ($item_pid == "0") {
                        $item_pid = null;
                        $pokestop["reward_item_id"] = null;
                    }
                    $mon_pid = $pokestop["reward_pokemon_id"];
                    if ($mon_pid == "0") {
                        $mon_pid = null;
                        $pokestop["reward_pokemon_id"] = null;
                    }

                    switch ($pokestop["quest_reward_type"]) {
                        case 1:
                            $pokestop["reward_amount"] = intval($pokestop["reward_dust_amount"]);
                            break;
                        case 2:
                            $pokestop["reward_amount"] = intval($pokestop["reward_item_amount"]);
                            break;
                        case 3:
                            $pokestop["reward_amount"] = intval($pokestop["reward_dust_amount"]);
                            break;
                        case 4:
                            $pokestop["reward_pokemon_id"] = intval($pokestop["reward_candy_pokemon_id"]);
                            $pokestop["reward_amount"] = intval($pokestop["reward_candy_amount"]);
                            break;
                        case 7:
                            $pokestop["reward_pokemon_id"] = intval($pokestop["reward_pokemon_id"]);
                            break;
                        case 12:
                            $pokestop["reward_pokemon_id"] = intval($pokestop["reward_pokemon_id"]);
                            $pokestop["reward_amount"] = intval($pokestop["reward_item_amount"]);
                            break;
                        default:
                            $pokestop["reward_pokemon_id"] = null;
                            $pokestop["reward_amount"] = null;
                    }
                    $pokestop["quest_type"] = intval($pokestop["quest_type"]);
                    $pokestop["quest_with_artask"] = $quests_with_ar;
                    $pokestop["quest_reward_type"] = intval($pokestop["quest_reward_type"]);
                    $pokestop["quest_condition_type"] = 0;
                    $pokestop["quest_condition_type_1"] = 0;
                    $pokestop["quest_condition_info"] = null;
                    $pokestop["quest_target"] = 0;
                    $pokestop["reward_pokemon_name"] = empty($mon_pid) ? null : i8ln($this->data[$mon_pid]["name"]);
                    $pokestop["reward_pokemon_formid"] = intval($pokestop["reward_pokemon_formid"]);
                    $pokestop["reward_pokemon_costumeid"] = intval($pokestop["reward_pokemon_costumeid"]);
                    $pokestop["reward_pokemon_genderid"] = intval($pokestop["reward_pokemon_genderid"]);
                    $pokestop["reward_pokemon_shiny"] = intval($pokestop["reward_pokemon_shiny"]);
                    $pokestop["reward_item_id"] = intval($pokestop["reward_item_id"]);
                    $pokestop["reward_item_name"] = empty($item_pid) ? null : i8ln($this->items[$item_pid]["name"]);
                }

                if ($noLures) {
                    $pokestop["lure_id"] = 0;
                    $pokestop["lure_expiration"] = 0;
                } else {
                    $pokestop["lure_id"] = intval($pokestop["lure_id"]);
                    $pokestop["lure_expiration"] = !empty($pokestop["lure_expiration"]) ? $pokestop["lure_expiration"] * 1000 : null;
                }

                if (!$noEventStops && isset($pokestop["incident_display_type"]) && intval($pokestop["incident_display_type"] >= 7)) {
                    $pokestop["eventstops_id"] = intval($pokestop["incident_display_type"]);
                    $pokestop["eventstops_expiration"] = !empty($pokestop["incident_expiration"]) ? $pokestop["incident_expiration"] * 1000 : null;
                    $pokestop["grunt_type"] = null;
                }
                elseif (!$noEventStops && isset($pokestop["grunt_type"]) && intval($pokestop["grunt_type"]) === 352) {
                    // required for MAD master hackery
                    $pokestop["eventstops_id"] = 8;
                    $pokestop["eventstops_expiration"] = !empty($pokestop["incident_expiration"]) ? $pokestop["incident_expiration"] * 1000 : null;
                    $pokestop["grunt_type"] = null;
                } else {
                    $pokestop["eventstops_id"] = 0;
                    $pokestop["eventstops_expiration"] = 0;
                }

                if ($noTeamRocket || !isset($pokestop["grunt_type"])) {
                    $pokestop["grunt_type"] = null;
                    $pokestop["grunt_type_name"] = null;
                    $pokestop["grunt_type_gender"] = null;
                    $pokestop["encounters"] = null;
                    $pokestop["second_reward"] = null;
                    $pokestop["incident_expiration"] = null;
                } else {
                    $grunttype_pid = $pokestop["grunt_type"];
                    if ($grunttype_pid == "0") {
                        $grunttype_pid = null;
                        $pokestop["grunt_type"] = null;
                    }
                    $pokestop["grunt_type_name"] = empty($grunttype_pid) ? null : i8ln($this->grunttype[$grunttype_pid]["type"]);
                    $pokestop["grunt_type_gender"] = empty($grunttype_pid) ? null : i8ln($this->grunttype[$grunttype_pid]["grunt"]);
                    $pokestop["encounters"] = empty($this->grunttype[$grunttype_pid]["encounters"]) ? null : $this->grunttype[$grunttype_pid]["encounters"];
                    $pokestop["second_reward"] = empty($this->grunttype[$grunttype_pid]["second_reward"]) ? null : $this->grunttype[$grunttype_pid]["second_reward"];
                    $pokestop["incident_expiration"] = !empty($pokestop["incident_expiration"]) ? $pokestop["incident_expiration"] * 1000 : null;
                }
                $data[$pokestop["pokestop_id"]] = $pokestop;
            }

            unset($pokestops[$i]);
            $i++;
        }
        return $data;
    }

    public function generated_exclude_list($type)
    {
        global $db, $userTimezone;
        $curdate = new \DateTime('', new \DateTimeZone($userTimezone));
        if ($type === 'pokemonlist') {
            $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM trs_quest WHERE quest_pokemon_id > 0 AND DATE(FROM_UNIXTIME(quest_timestamp)) = '" . $curdate->format('Y-m-d') . "' AND quest_reward_type = 7 order by quest_pokemon_id;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['reward_pokemon_id'];
            }
        } elseif ($type === 'energylist') {
            $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM trs_quest WHERE quest_reward_type = 12;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['reward_pokemon_id'];
            }
        } elseif ($type === 'candylist') {
            $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM trs_quest WHERE quest_reward_type = 4;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['reward_pokemon_id'];
            }
        } elseif ($type === 'itemlist') {
            $pokestops = $db->query("SELECT distinct quest_item_id AS reward_item_id FROM trs_quest WHERE quest_item_id > 0 AND DATE(FROM_UNIXTIME(quest_timestamp)) = '" . $curdate->format('Y-m-d') . "' order by quest_item_id;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['reward_item_id'];
            }
        } elseif ($type === 'gruntlist') {
            if ($this->columnExists("pokestop_incident","pokestop_id")) {
                $pokestops = $db->query("SELECT distinct character_display AS incident_grunt_type FROM pokestop_incident WHERE character_display > 0 AND incident_expiration > UTC_TIMESTAMP() ORDER BY character_display;")->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $pokestops = $db->query("SELECT distinct incident_grunt_type FROM pokestop WHERE incident_grunt_type > 0 AND incident_expiration > UTC_TIMESTAMP() order by incident_grunt_type;")->fetchAll(\PDO::FETCH_ASSOC);
            }
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['incident_grunt_type'];
            }
        } elseif ($type === 'raidbosslist') {
            $gyms = $db->query("SELECT distinct pokemon_id FROM raid WHERE pokemon_id > 0 AND end > UTC_TIMESTAMP() order by pokemon_id;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($gyms as $gym) {
                $data[] = $gym['pokemon_id'];
            }
        }
        return $data;
    }

    public function get_scanlocation($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        $conds = array();
        $params = array();

        $conds[] = "ST_X(currentPos_raw) > :swLat AND ST_Y(currentPos_raw) > :swLng AND ST_X(currentPos_raw) < :neLat AND ST_Y(currentPos_raw) < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        if ($oSwLat != 0) {

            $conds[] = "NOT (ST_X(currentPos_raw) > :oswLat AND ST_Y(currentPos_raw) > :oswLng AND ST_X(currentPos_raw) < :oneLat AND ST_Y(currentPos_raw) < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(currentPos_raw,ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        global $hideDeviceAfterMinutes;
        if ($hideDeviceAfterMinutes > 0) {
            $conds[] = "lastProtoDateTime > UNIX_TIMESTAMP( NOW() - INTERVAL " . $hideDeviceAfterMinutes . " MINUTE)";
        }
        return $this->query_scanlocation($conds, $params);
    }

    private function query_scanlocation($conds, $params)
    {
        global $db;

        $query = "SELECT ST_X(currentPos_raw) AS latitude,
        ST_Y(currentPos_raw) AS longitude,
        lastProtoDateTime AS last_seen,
        name AS uuid,
        rmname AS instance_name
        FROM v_trs_status
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $scanlocations = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($scanlocations as $scanlocation) {
            $scanlocation["latitude"] = floatval($scanlocation["latitude"]);
            $scanlocation["longitude"] = floatval($scanlocation["longitude"]);
            $data[] = $scanlocation;
            unset($scanlocations[$i]);
            $i++;
        }
        return $data;
    }
}
