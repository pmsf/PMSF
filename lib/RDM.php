<?php

namespace Scanner;

class RDM extends Scanner
{
    public function get_active($eids, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $bigKarp, $tinyRat, $zeroIv, $hundoIv, $independantPvpAndStats, $despawnTimeType, $gender, $missingIvOnly, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $encId = 0)
    {
        $conds = array();
        $params = array();

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
        global $noBoundaries, $boundaries, $showPokemonsOutsideBoundaries;
        if (!$noBoundaries && !$showPokemonsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat, lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        $tmpSQL = '';
        if ($tinyRat === true && ($key = array_search("19", $eids)) === false) {
            $tmpSQL .= ' OR (pokemon_id = 19 AND weight < 2.41)';
            $eids[] = "19";
        }
        if ($bigKarp === true && ($key = array_search("129", $eids)) === false) {
            $tmpSQL .= ' OR (pokemon_id = 129 AND weight > 13.13)';
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
               $conds[] = 'expire_timestamp_verified = 1';
            } elseif ($despawnTimeType == 2) {
               $conds[] = '(expire_timestamp_verified = 0 AND spawn_id IS NOT NULL)';
            } elseif ($despawnTimeType == 3) {
               $conds[] = 'expire_timestamp_verified = 0';
            }
        }
        if (!empty($gender) && ($gender == 1 || $gender == 2)) {
           $conds[] = 'gender = ' . $gender;
        }
        if ($missingIvOnly) {
            $conds[] = '(atk_iv IS NULL OR def_iv IS NULL OR sta_iv IS NULL)';
        } else if (($minLLRank === 0 && $minGLRank === 0 && $minULRank === 0) || !$independantPvpAndStats) {
            $zeroIvSql = ($zeroIv) ? ' OR (atk_iv = 0 AND def_iv = 0 AND sta_iv = 0)' : '';
            $hundoIvSql = ($hundoIv) ? ' OR (atk_iv = 15 AND def_iv = 15 AND sta_iv = 15)' : '';
            $exMinIvSql = (!empty($exMinIv)) ? ' OR pokemon_id IN(' . $exMinIv . ')' : '';
            if ($minIv !== 0) {
                $conds[] = '(iv >= ' . $minIv . $zeroIvSql . $hundoIvSql . $exMinIvSql . ')';
            }
            if ($minLevel !== 0) {
                $conds[] = '(level >= ' . $minLevel . $zeroIvSql . $hundoIvSql . $exMinIvSql . ')';
            }
        }
        $encSql = '';
        if ($encId != 0) {
            $encSql = " OR (id = " . $encId . " AND lat > '" . $swLat . "' AND lon > '" . $swLng . "' AND lat < '" . $neLat . "' AND lon < '" . $neLng . "' AND expire_timestamp > '" . $params[':time'] . "')";
        }
        return $this->query_active($conds, $params, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $zeroIv, $hundoIv, $independantPvpAndStats, $missingIvOnly, $encSql);
    }

    public function get_active_by_id($ids, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $bigKarp, $tinyRat, $zeroIv, $hundoIv, $independantPvpAndStats, $despawnTimeType, $gender, $missingIvOnly, $swLat, $swLng, $neLat, $neLng)
    {
        $conds = array();
        $params = array();

        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng AND expire_timestamp > :time";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        $params[':time'] = time();

        global $noBoundaries, $boundaries, $showPokemonsOutsideBoundaries;
        if (!$noBoundaries && !$showPokemonsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat, lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if (count($ids)) {
            $tmpSQL = '';
            if ($tinyRat === true && ($key = array_search("19", $ids)) !== false) {
                $tmpSQL .= ' OR (pokemon_id = 19 AND weight < 2.41)';
                unset($ids[$key]);
            }
            if ($bigKarp === true && ($key = array_search("129", $ids)) !== false) {
                $tmpSQL .= ' OR (pokemon_id = 129 AND weight > 13.13)';
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
               $conds[] = 'expire_timestamp_verified = 1';
            } elseif ($despawnTimeType == 2) {
               $conds[] = '(expire_timestamp_verified = 0 AND spawn_id IS NOT NULL)';
            } elseif ($despawnTimeType == 3) {
               $conds[] = 'expire_timestamp_verified = 0';
            }
        }
        if (!empty($gender) && ($gender == 1 || $gender == 2)) {
           $conds[] = 'gender = ' . $gender;
        }
        if ($missingIvOnly) {
            $conds[] = '(atk_iv IS NULL OR def_iv IS NULL OR sta_iv IS NULL)';
        } else if (($minLLRank === 0 && $minGLRank === 0 && $minULRank === 0) || !$independantPvpAndStats) {
            $zeroIvSql = ($zeroIv) ? ' OR (atk_iv = 0 AND def_iv = 0 AND sta_iv = 0)' : '';
            $hundoIvSql = ($hundoIv) ? ' OR (atk_iv = 15 AND def_iv = 15 AND sta_iv = 15)' : '';
            $exMinIvSql = (!empty($exMinIv)) ? ' OR pokemon_id IN(' . $exMinIv . ')' : '';
            if ($minIv !== 0) {
                $conds[] = '(iv >= ' . $minIv . $zeroIvSql . $hundoIvSql . $exMinIvSql . ')';
            }
            if ($minLevel !== 0) {
                $conds[] = '(level >= ' . $minLevel . $zeroIvSql . $hundoIvSql . $exMinIvSql . ')';
            }
        }
        return $this->query_active($conds, $params, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $zeroIv, $hundoIv, $independantPvpAndStats, $missingIvOnly, '');
    }

    private function getValidPvpRanks_UpdateBestRank($json, $minCp, $maxRank, &$bestRank)
    {
        $best = 9999;
        $rankings = json_decode($json, true);

        foreach ($rankings as $key => $rank) {
            if (isset($rank["rank"]) && isset($rank["cp"]) && (int)$rank["rank"] <= $maxRank && (int)$rank["cp"] >= $minCp) {
                if ((int)$rank["rank"] < $best) {
                    $best = intval($rank["rank"]);
                }
            } else {
                unset($rankings[$key]);
            }
        }
        if ($best === 9999) {
            $bestRank = null;
            return null;
        } else {
            $bestRank = $best;
            return json_encode($rankings);
        }
    }

    public function query_active($conds, $params, $minIv, $minLevel, $minLLRank, $minGLRank, $minULRank, $exMinIv, $zeroIv, $hundoIv, $independantPvpAndStats, $missingIvOnly, $encSql = '')
    {
        global $db, $noHighLevelData, $noPvp, $globalRankLimitLL, $globalRankLimitGL, $globalRankLimitUL, $globalCpLimitLL, $globalCpLimitGL, $globalCpLimitUL;

        $select = "pokemon_id,
        expire_timestamp AS disappear_time,
        id AS encounter_id,
        spawn_id,
        seen_type,
        lat AS latitude,
        lon AS longitude,
        gender,
        form,
        weather AS weather_boosted_condition,
        costume,
        first_seen_timestamp,
        expire_timestamp_verified";

        if (!$noHighLevelData) {
            if ($this->columnExists("pokemon","pvp")) {
                $rdmPvP = ",
                json_extract(`pvp`,'$.little') AS pvp_rankings_little_league,
                json_extract(`pvp`,'$.great') AS pvp_rankings_great_league,
                json_extract(`pvp`,'$.ultra') AS pvp_rankings_ultra_league";
            } else {
                $rdmPvP = ",
                pvp_rankings_great_league,
                pvp_rankings_ultra_league";
            }

            $select .= ",
            weight,
            size AS height,
            atk_iv AS individual_attack,
            def_iv AS individual_defense,
            sta_iv AS individual_stamina,
            move_1,
            move_2,
            cp,
            level,
            iv,
            capture_1 AS catch_rate_1,
            capture_2 AS catch_rate_2,
            capture_3 AS catch_rate_3
            $rdmPvP
            ";
        }

        $query = "SELECT :select
        FROM pokemon
        WHERE :conditions ORDER BY lat, lon ";

        $query = str_replace(":select", $select, $query);
        $query = str_replace(":conditions", '(' . join(" AND ", $conds) . ')' . $encSql, $query);
        $pokemons = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;
        $lastlat = 0;
        $lastlon = 0;
        $lasti = 0;

        $keepDefault = ($noHighLevelData || ($minIv === 0 && $minLevel === 0 && $minLLRank === 0 && $minGLRank === 0 && $minULRank === 0));
        $thisRankLimitLL = ($globalRankLimitLL === 0) ? $minLLRank : $globalRankLimitLL;
        $thisRankLimitGL = ($globalRankLimitGL === 0) ? $minGLRank : $globalRankLimitGL;
        $thisRankLimitUL = ($globalRankLimitUL === 0) ? $minULRank : $globalRankLimitUL;
        $exMinIvArray = array();
        if (!empty($exMinIv)) {
            $tmpArray = array_map('intval', explode(",", $exMinIv));
            $exMinIvArray = array_combine($tmpArray, $tmpArray);
            unset($tmpArray);
        }

        foreach ($pokemons as $pokemon) {
            $keepMons = $keepDefault;
            $bestLLRank = null;
            $bestGLRank = null;
            $bestULRank = null;

            if ($missingIvOnly) {
                $keepMons = ($pokemon["individual_attack"] === null || $pokemon["individual_defense"] === null || $pokemon["individual_stamina"] === null);
            } else if (!$noHighLevelData) {
                if (!$noPvp) {
                    $pokemon["pvp_rankings_little_league"] = (isset($pokemon["pvp_rankings_little_league"])) ? $this->getValidPvpRanks_UpdateBestRank($pokemon["pvp_rankings_little_league"], $globalCpLimitLL, $thisRankLimitLL, $bestLLRank) : null;
                    $pokemon["pvp_rankings_great_league"] = (isset($pokemon["pvp_rankings_great_league"])) ? $this->getValidPvpRanks_UpdateBestRank($pokemon["pvp_rankings_great_league"], $globalCpLimitGL, $thisRankLimitGL, $bestGLRank) : null;
                    $pokemon["pvp_rankings_ultra_league"] = (isset($pokemon["pvp_rankings_ultra_league"])) ? $this->getValidPvpRanks_UpdateBestRank($pokemon["pvp_rankings_ultra_league"], $globalCpLimitUL, $thisRankLimitUL, $bestULRank) : null;
                }

                if (!$keepMons) {
                    if (isset($pokemon["iv"])) {
                        if ($pokemon["iv"] == 100.0 && $hundoIv === true) {
                            $keepMons = true;
                        } else if ($pokemon["iv"] == 0.0 && $zeroIv === true) {
                            $keepMons = true;
                        }
                    }

                    if (!$keepMons) {
                        $keepPvp = true;
                        if ($minLLRank > 0 || $minGLRank > 0 || $minULRank > 0) {
                            $keepPvp = false;
                            if ($minLLRank > 0 && $bestLLRank !== null && $bestLLRank <= $minLLRank) {
                                $keepPvp = true;
                            } else if ($minGLRank > 0 && $bestGLRank !== null && $bestGLRank <= $minGLRank) {
                                $keepPvp = true;
                            } else if ($minULRank > 0 && $bestULRank !== null && $bestULRank <= $minULRank) {
                                $keepPvp = true;
                            }
                            $keepMons = ($independantPvpAndStats && $keepPvp);
                        }

                        if (!$keepMons) {
                            $keepMinIvLevel = ((!empty($exMinIv) && isset($exMinIvArray[intval($pokemon["pokemon_id"])])) || (($minIv === 0 || (isset($pokemon["iv"]) && $pokemon["iv"] >= $minIv)) && ($minLevel === 0 || (isset($pokemon["level"]) && intval($pokemon["level"]) >= $minLevel))));
                            $keepMons = (($independantPvpAndStats && $keepMinIvLevel) || (!$independantPvpAndStats && $keepMinIvLevel && $keepPvp));
                        }
                    }
                }
            }

            if ($keepMons) {
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
                $pokemon["expire_timestamp_verified"] = intval($pokemon["expire_timestamp_verified"]);
                $pokemon["first_seen_timestamp"] = intval($pokemon["first_seen_timestamp"] * 1000);
                $pokemon["disappear_time"] = $pokemon["disappear_time"] * 1000;

                $pokemon["weight"] = isset($pokemon["weight"]) ? floatval($pokemon["weight"]) : null;
                $pokemon["height"] = isset($pokemon["height"]) ? floatval($pokemon["height"]) : null;

                $pokemon["individual_attack"] = isset($pokemon["individual_attack"]) ? intval($pokemon["individual_attack"]) : null;
                $pokemon["individual_defense"] = isset($pokemon["individual_defense"]) ? intval($pokemon["individual_defense"]) : null;
                $pokemon["individual_stamina"] = isset($pokemon["individual_stamina"]) ? intval($pokemon["individual_stamina"]) : null;

                $pokemon["iv"] = isset($pokemon["iv"]) ? floatval($pokemon["iv"]) : null;
                $pokemon["level"] = isset($pokemon["level"]) ? intval($pokemon["level"]) : null;

                $pokemon["pvp_rankings_little_league"] = isset($pokemon["pvp_rankings_little_league"]) ? $pokemon["pvp_rankings_little_league"] : null;
                $pokemon["pvp_rankings_great_league"] = isset($pokemon["pvp_rankings_great_league"]) ? $pokemon["pvp_rankings_great_league"] : null;
                $pokemon["pvp_rankings_ultra_league"] = isset($pokemon["pvp_rankings_ultra_league"]) ? $pokemon["pvp_rankings_ultra_league"] : null;
                $pokemon["pvp_rankings_little_league_best"] = isset($bestLLRank) ? intval($bestLLRank) : null;
                $pokemon["pvp_rankings_great_league_best"] = isset($bestGLRank) ? intval($bestGLRank) : null;
                $pokemon["pvp_rankings_ultra_league_best"] = isset($bestULRank) ? intval($bestULRank) : null;

                $pokemon["weather_boosted_condition"] = isset($pokemon["weather_boosted_condition"]) ? intval($pokemon["weather_boosted_condition"]) : 0;

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
            }
            unset($pokemons[$i]);
            $i++;
        }
        return $data;
    }

    public function get_stops($geids, $qpeids, $qeeids, $qceids, $qieids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $rocket, $quests, $dustamount, $xpamount, $quests_with_ar)
    {
        $conds = array();
        $params = array();
        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;
        global $noBoundaries, $boundaries, $hideDeleted, $showStopsOutsideBoundaries;
        $ar_string = ($quests_with_ar === true) ? "" : "alternative_";
        if (!$noBoundaries && !$showStopsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat, lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($hideDeleted) {
            $conds[] = "deleted = 0";
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
                $pokemonSQL .= $ar_string."quest_pokemon_id NOT IN ( $pkmn_in ) AND ".$ar_string."quest_reward_type = 7";
            } else {
                $pokemonSQL .= $ar_string."quest_reward_type = 7";
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
                $energySQL .= $ar_string."quest_pokemon_id NOT IN ( $pkmn_in ) AND ".$ar_string."quest_reward_type = 12";
            } else {
                $energySQL .= $ar_string."quest_reward_type = 12";
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
                $candySQL .= $ar_string."quest_pokemon_id NOT IN ( $pkmn_in ) AND ".$ar_string."quest_reward_type = 4";
            } else {
                $candySQL .= $ar_string."quest_reward_type = 4";
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
                $itemSQL .= $ar_string."quest_item_id NOT IN ( $item_in )";
            } else {
                $itemSQL .= $ar_string."quest_item_id IS NOT NULL";
            }
            $dustSQL = '';
            if (!empty($dustamount) && !is_nan((float)$dustamount) && $dustamount > 0) {
                $dustSQL .= " OR (".$ar_string."quest_reward_type = 3 AND ".$ar_string."quest_reward_amount >= :dustamount)";
                $params[':dustamount'] = intval($dustamount);
            }
            $xpSQL = '';
            if (!empty($xpamount) && !is_nan((float)$xpamount) && $xpamount > 0) {
                $xpSQL .= " OR (".$ar_string."quest_reward_type = 1 AND ".$ar_string."quest_reward_amount >= :xpamount)";
                $params[':xpamount'] = intval($xpamount);
            }
            $conds[] = "((" . $pokemonSQL . ") OR (" . $itemSQL . ") OR (" . $energySQL . ") OR (" . $candySQL . ")" . $dustSQL . $xpSQL . ")";
        }
        if (!empty($rocket) && $rocket === 'true') {
            $rocketSQL = '';
            if (count($geids)) {
                $rocket_in = '';
                $r = 1;
                foreach ($geids as $geid) {
                    $params[':rqry_' . $r . "_"] = $geid;
                    $rocket_in .= ':rqry_' . $r . "_,";
                    $r++;
                }
                $rocket_in = substr($rocket_in, 0, -1);
                $rocketSQL .= "grunt_type NOT IN ( $rocket_in )";
            } else {
                $rocketSQL .= "grunt_type IS NOT NULL";
            }
            $conds[] = "" . $rocketSQL . "";
        }
        if ($oSwLat != 0) {
            $conds[] = "NOT (lat > :oswLat AND lon > :oswLng AND lat < :oneLat AND lon < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        if (!empty($lures) && $lures === 'true') {
            $conds[] = "lure_expire_timestamp > :time";
            $params[':time'] = time();
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        return $this->query_stops($conds, $params, $quests_with_ar);
    }

    public function get_stops_quest($greids, $qpreids, $qereids, $qcreids, $qireids, $swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $lures, $rocket, $quests, $dustamount, $reloaddustamount, $xpamount, $reloadxpamount, $quests_with_ar)
    {
        $conds = array();
        $params = array();
        $conds[] = "lat > :swLat AND lon > :swLng AND lat < :neLat AND lon < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        global $noBoundaries, $boundaries, $hideDeleted;
        $ar_string = ($quests_with_ar === true) ? "" : "alternative_";
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat, lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($hideDeleted) {
            $conds[] = "deleted = 0";
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
                $tmpSQL .= $ar_string."quest_pokemon_id IN ( $pkmn_in ) AND ".$ar_string."quest_reward_type = 7";
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
                $tmpSQL .= $ar_string."quest_pokemon_id IN ( $pkmn_in ) AND ".$ar_string."quest_reward_type = 12";
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
                $tmpSQL .= $ar_string."quest_pokemon_id IN ( $pkmn_in ) AND ".$ar_string."quest_reward_type = 4";
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
                $tmpSQL .= $ar_string."quest_item_id IN ( $item_in )";
            }
            if ($reloaddustamount == "true") {
                $tmpSQL .= "(".$ar_string."quest_reward_type = 3 AND ".$ar_string."quest_reward_amount >= :dustamount)";
                $params[':dustamount'] = intval($dustamount);
            }
            if ($reloadxpamount == "true") {
                $tmpSQL .= "(".$ar_string."quest_reward_type = 1 AND ".$ar_string."quest_reward_amount >= :xpamount)";
                $params[':xpamount'] = intval($xpamount);
            }
            $conds[] = $tmpSQL;
        }
        if (!empty($rocket) && $rocket === 'true') {
            $tmpSQL = '';
            if (count($greids)) {
                $rocket_in = '';
                $r = 1;
                foreach ($greids as $greid) {
                    $params[':rqry_' . $r . "_"] = $greid;
                    $rocket_in .= ':rqry_' . $r . "_,";
                    $r++;
                }
                $rocket_in = substr($rocket_in, 0, -1);
                $tmpSQL .= "grunt_type IN ( $rocket_in )";
            }
            $conds[] = $tmpSQL;
        }
        return $this->query_stops($conds, $params, $quests_with_ar);
    }

    public function query_stops($conds, $params, $quests_with_ar)
    {
        global $db, $noQuests, $noQuestsPokemon, $noQuestsItems, $noQuestsEnergy, $noQuestsCandy, $noQuestsStardust, $noQuestsXP;
        $rdmGrunts = ($this->columnExists("incident","pokestop_id")) ? " LEFT JOIN (SELECT `pokestop_id` AS pokestop_id_incident, MIN(`character`) AS grunt_type, `expiration` AS incident_expire_timestamp FROM incident WHERE `expiration` > UNIX_TIMESTAMP() GROUP BY `pokestop_id_incident`) AS i ON i.`pokestop_id_incident` = p.`id` " : "";

        $ar_string = ($quests_with_ar === true) ? "" : "alternative_";
        $query = "SELECT id AS pokestop_id,
        lat AS latitude,
        lon AS longitude,
        name AS pokestop_name,
        url,
        updated AS last_seen,
        lure_expire_timestamp AS lure_expiration,
        incident_expire_timestamp AS incident_expiration,
        lure_id,
        grunt_type,
        ".$ar_string."quest_type AS quest_type,
        ".$ar_string."quest_timestamp AS quest_timestamp,
        ".$ar_string."quest_target AS quest_target,
        ".$ar_string."quest_rewards AS quest_rewards,
        ".$ar_string."quest_item_id AS reward_item_id,
        json_extract(json_extract(`".$ar_string."quest_conditions`,'$[*].type'),'$[0]') AS quest_condition_type,
        json_extract(json_extract(`".$ar_string."quest_conditions`,'$[*].type'),'$[1]') AS quest_condition_type_1,
        json_extract(json_extract(`".$ar_string."quest_conditions`,'$[*].info'),'$[0]') AS quest_condition_info,
        ".$ar_string."quest_reward_type AS quest_reward_type,
        ".$ar_string."quest_reward_amount AS reward_amount,
        ".$ar_string."quest_pokemon_id AS reward_pokemon_id,
        json_extract(json_extract(`".$ar_string."quest_rewards`,'$[*].info.form_id'),'$[0]') AS reward_pokemon_formid,
        json_extract(json_extract(`".$ar_string."quest_rewards`,'$[*].info.costume_id'),'$[0]') AS reward_pokemon_costumeid,
        json_extract(json_extract(`".$ar_string."quest_rewards`,'$[*].info.gender_id'),'$[0]') AS reward_pokemon_genderid,
        json_extract(json_extract(`".$ar_string."quest_rewards`,'$[*].info.shiny'),'$[0]') AS reward_pokemon_shiny
        FROM pokestop p
        $rdmGrunts
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $pokestops = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;

        foreach ($pokestops as $pokestop) {
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
            $grunttype_pid = $pokestop["grunt_type"];
            if ($grunttype_pid == "0") {
                $grunttype_pid = null;
                $pokestop["grunt_type"] = null;
            }
            $pokestop["latitude"] = floatval($pokestop["latitude"]);
            $pokestop["longitude"] = floatval($pokestop["longitude"]);
            if ($noQuests ||
            ($noQuestsEnergy && intval($pokestop["quest_reward_type"]) === 12) ||
            ($noQuestsPokemon && intval($pokestop["quest_reward_type"]) === 7) ||
            ($noQuestsCandy && intval($pokestop["quest_reward_type"]) === 4) ||
            ($noQuestsStardust && intval($pokestop["quest_reward_type"]) === 3) ||
            ($noQuestsItems && intval($pokestop["quest_reward_type"]) === 2) ||
            ($noQuestsXP && intval($pokestop["quest_reward_type"]) === 1)) {
                $pokestop["quest_type"] = 0;
                $pokestop["quest_reward_type"] = 0;
            } else {
                $pokestop["quest_type"] = intval($pokestop["quest_type"]);
                $pokestop["quest_reward_type"] = intval($pokestop["quest_reward_type"]);
            }
            $pokestop["quest_condition_type"] = intval($pokestop["quest_condition_type"]);
            $pokestop["quest_condition_type_1"] = intval($pokestop["quest_condition_type_1"]);
            $pokestop["quest_target"] = intval($pokestop["quest_target"]);
            $pokestop["reward_pokemon_id"] = intval($pokestop["reward_pokemon_id"]);
            $pokestop["reward_pokemon_name"] = empty($mon_pid) ? null : i8ln($this->data[$mon_pid]["name"]);
            $pokestop["reward_pokemon_formid"] = intval($pokestop["reward_pokemon_formid"]);
            $pokestop["reward_pokemon_costumeid"] = intval($pokestop["reward_pokemon_costumeid"]);
            $pokestop["reward_pokemon_genderid"] = intval($pokestop["reward_pokemon_genderid"]);
            $pokestop["reward_pokemon_shiny"] = intval($pokestop["reward_pokemon_shiny"]);
            $pokestop["reward_item_id"] = intval($pokestop["reward_item_id"]);
            $pokestop["reward_item_name"] = empty($item_pid) ? null : i8ln($this->items[$item_pid]["name"]);
            $pokestop["reward_amount"] = intval($pokestop["reward_amount"]);
            $pokestop["url"] = ! empty($pokestop["url"]) ? preg_replace("/^http:/i", "https:", $pokestop["url"]) : null;
            $pokestop["lure_expiration"] = $pokestop["lure_expiration"] * 1000;
            $pokestop["incident_expiration"] = $pokestop["incident_expiration"] * 1000;
            $pokestop["lure_id"] = intval($pokestop["lure_id"]);
            $pokestop["grunt_type_name"] = empty($grunttype_pid) ? null : i8ln($this->grunttype[$grunttype_pid]["type"]);
            $pokestop["grunt_type_gender"] = empty($grunttype_pid) ? null : i8ln($this->grunttype[$grunttype_pid]["grunt"]);
            $pokestop["encounters"] = empty($this->grunttype[$grunttype_pid]["encounters"]) ? null : $this->grunttype[$grunttype_pid]["encounters"];
            $pokestop["second_reward"] = empty($this->grunttype[$grunttype_pid]["second_reward"]) ? null : $this->grunttype[$grunttype_pid]["second_reward"];
            $pokestop["last_seen"] = $pokestop["last_seen"] * 1000;

            $data[] = $pokestop;
            unset($pokestops[$i]);
            $i++;
        }
        return $data;
    }

    public function get_gyms($rbeids, $reeids, $swLat, $swLng, $neLat, $neLng, $exEligible = false, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0, $raids, $gyms)
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
        global $noBoundaries, $boundaries, $hideDeleted, $showGymsOutsideBoundaries;
        if (!$noBoundaries && !$showGymsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat, lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
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
                $raidSQL .= "raid_pokemon_id NOT IN ( $raid_in )";
            } else {
                $raidSQL .= "raid_pokemon_id IS NOT NULL";
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
                $eggSQL .= "raid_pokemon_id = 0 AND raid_level NOT IN ( $egg_in )";
            } else {
                $eggSQL .= "raid_pokemon_id = 0 AND raid_level IS NOT NULL";
            }
            $conds[] = "(" . $raidSQL . " OR " . $eggSQL . ")";
        }
        if ($hideDeleted) {
            $conds[] = "deleted = 0";
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        if ($exEligible === "true") {
            $conds[] = "(ex_raid_eligible = 1)";
        }

        return $this->query_gyms($conds, $params, $raids, $gyms, $rbeids, $reeids);
    }

    public function query_gyms($conds, $params, $raids, $gyms, $rbeids, $reeids)
    {
        global $db, $noTeams, $noExEligible, $noInBattle;

        $rdmAvailableSlots = ($this->columnExists("gym","available_slots")) ? "available_slots" : "availble_slots";

        $query = "SELECT id AS gym_id,
        lat AS latitude,
        lon AS longitude,
        name,
        url,
        last_modified_timestamp AS last_modified,
        raid_end_timestamp AS raid_end,
        raid_battle_timestamp AS raid_start,
        updated AS last_scanned,
        raid_pokemon_id,
        $rdmAvailableSlots AS slots_available,
        team_id,
        raid_level,
        raid_pokemon_move_1,
        raid_pokemon_move_2,
        raid_pokemon_form,
        raid_pokemon_costume,
        raid_pokemon_cp,
        raid_pokemon_gender,
        raid_pokemon_evolution,
        ex_raid_eligible AS park,
        in_battle
        FROM gym
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
            $gym["url"] = ! empty($gym["url"]) ? preg_replace("/^http:/i", "https:", $gym["url"]) : null;
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
        global $noBoundaries, $boundaries, $showSpawnsOutsideBoundaries;
        if (!$noBoundaries && !$showSpawnsOutsideBoundaries) {
            $conds[] = "(ST_WITHIN(point(lat, lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        if ($tstamp > 0) {
            $conds[] = "updated > :lastUpdated";
            $params[':lastUpdated'] = $tstamp;
        }
        return $this->query_spawnpoints($conds, $params);
    }

    private function query_spawnpoints($conds, $params)
    {
        global $db;
        $query = "SELECT lat AS latitude,
        lon AS longitude,
        id AS spawnpoint_id,
        despawn_sec
        FROM spawnpoint
        WHERE :conditions";
        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $spawnpoints = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        $i = 0;
        foreach ($spawnpoints as $spawnpoint) {
            $spawnpoint["latitude"] = floatval($spawnpoint["latitude"]);
            $spawnpoint["longitude"] = floatval($spawnpoint["longitude"]);
            $spawnpoint["time"] = is_null($spawnpoint["despawn_sec"]) ? null : intval($spawnpoint["despawn_sec"]);
            $data[] = $spawnpoint;
            unset($spawnpoints[$i]);
            $i++;
        }
        return $data;
    }

    public function get_weather_by_cell_id($cell_id)
    {
        global $db;
        $query = "SELECT id AS s2_cell_id, gameplay_condition AS gameplay_weather, updated FROM weather WHERE id = :cell_id";
        $params = [':cell_id' => intval((float)$cell_id)]; // use float to intval because RDM is signed int
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
        $query = "SELECT id AS s2_cell_id, gameplay_condition AS gameplay_weather, updated FROM weather";
        $weathers = $db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $data = array();
        foreach ($weathers as $weather) {
            $data["weather_" . $weather['s2_cell_id']] = $weather;
            $data["weather_" . $weather['s2_cell_id']]['condition'] = $data["weather_" . $weather['s2_cell_id']]['gameplay_weather'];
            unset($data["weather_" . $weather['s2_cell_id']]['gameplay_weather']);
        }
        return $data;
    }

    public function get_scanlocation($swLat, $swLng, $neLat, $neLng, $tstamp = 0, $oSwLat = 0, $oSwLng = 0, $oNeLat = 0, $oNeLng = 0)
    {
        $conds = array();
        $params = array();

//        $conds[] = "last_lat > :swLat AND last_lon > :swLng AND last_lat < :neLat AND last_lon < :neLng";
//        $params[':swLat'] = $swLat;
//        $params[':swLng'] = $swLng;
//        $params[':neLat'] = $neLat;
//        $params[':neLng'] = $neLng;
        if ($oSwLat != 0) {
            $conds[] = "NOT (last_lat > :oswLat AND last_lon > :oswLng AND last_lat < :oneLat AND last_lon < :oneLng)";
            $params[':oswLat'] = $oSwLat;
            $params[':oswLng'] = $oSwLng;
            $params[':oneLat'] = $oNeLat;
            $params[':oneLng'] = $oNeLng;
        }
        global $noBoundaries, $boundaries;
        if (!$noBoundaries) {
            $conds[] = "(ST_WITHIN(point(last_lat,last_lon),ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
        }
        global $hideDeviceAfterMinutes;
        if ($hideDeviceAfterMinutes > 0) {
            $conds[] = "last_seen > UNIX_TIMESTAMP( NOW() - INTERVAL " . $hideDeviceAfterMinutes . " MINUTE)";
        }
        return $this->query_scanlocation($conds, $params);
    }

    private function query_scanlocation($conds, $params)
    {
        global $db;
        if (empty($conds)) {
            $query = "SELECT last_lat AS latitude,
            last_lon AS longitude,
            last_seen,
            uuid,
            instance_name
            FROM device";
        } else {
            $query = "SELECT last_lat AS latitude,
            last_lon AS longitude,
            last_seen,
            uuid,
            instance_name
            FROM device
            WHERE :conditions";
            $query = str_replace(":conditions", join(" AND ", $conds), $query);
        }
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

    public function generated_exclude_list($type)
    {
        global $db, $userTimezone, $noQuestsARTaskToggle;
        $curdate = new \DateTime(null, new \DateTimeZone($userTimezone) );
        if ($type === 'pokemonlist') {
            if ($noQuestsARTaskToggle) {
                $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE quest_pokemon_id > 0 AND DATE(FROM_UNIXTIME(quest_timestamp)) = '" . $curdate->format('Y-m-d') . "' AND quest_reward_type = 7 order by quest_pokemon_id;")->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE quest_pokemon_id > 0 AND DATE(FROM_UNIXTIME(quest_timestamp)) = '" . $curdate->format('Y-m-d') . "' AND quest_reward_type = 7 UNION SELECT distinct alternative_quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE alternative_quest_pokemon_id > 0 AND DATE(FROM_UNIXTIME(alternative_quest_timestamp)) = '" . $curdate->format('Y-m-d') . "' AND alternative_quest_reward_type = 7 ORDER BY reward_pokemon_id;")->fetchAll(\PDO::FETCH_ASSOC);
            }
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['reward_pokemon_id'];
            }
        } elseif ($type === 'energylist') {
            if ($noQuestsARTaskToggle) {
                $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE quest_reward_type = 12;")->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE quest_reward_type = 12 UNION SELECT distinct alternative_quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE alternative_quest_reward_type = 12;")->fetchAll(\PDO::FETCH_ASSOC);
            }
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['reward_pokemon_id'];
            }
        } elseif ($type === 'candylist') {
            if ($noQuestsARTaskToggle) {
                $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE quest_reward_type = 4;")->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $pokestops = $db->query("SELECT distinct quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE quest_reward_type = 4 UNION SELECT distinct alternative_quest_pokemon_id AS reward_pokemon_id FROM pokestop WHERE alternative_quest_reward_type = 4;")->fetchAll(\PDO::FETCH_ASSOC);
            }
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['reward_pokemon_id'];
            }
        } elseif ($type === 'itemlist') {
            if ($noQuestsARTaskToggle) {
                $pokestops = $db->query("SELECT distinct quest_item_id AS reward_item_id FROM pokestop WHERE quest_item_id > 0 AND DATE(FROM_UNIXTIME(quest_timestamp)) = '" . $curdate->format('Y-m-d') . "' order by quest_item_id;")->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $pokestops = $db->query("SELECT distinct quest_item_id AS reward_item_id FROM pokestop WHERE quest_item_id > 0 AND DATE(FROM_UNIXTIME(quest_timestamp)) = '" . $curdate->format('Y-m-d') . "' UNION SELECT distinct alternative_quest_item_id AS reward_item_id FROM pokestop WHERE alternative_quest_item_id > 0 AND DATE(FROM_UNIXTIME(alternative_quest_timestamp)) = '" . $curdate->format('Y-m-d') . "' ORDER BY reward_item_id;")->fetchAll(\PDO::FETCH_ASSOC);
            }
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['reward_item_id'];
            }
        } elseif ($type === 'gruntlist') {
            if ($this->columnExists("incident","pokestop_id")) {
                $pokestops = $db->query("SELECT distinct `character` AS grunt_type FROM incident WHERE `expiration` > UNIX_TIMESTAMP() ORDER BY `character`;")->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $pokestops = $db->query("SELECT distinct grunt_type FROM pokestop WHERE grunt_type > 0 AND incident_expire_timestamp > UNIX_TIMESTAMP() order by grunt_type;")->fetchAll(\PDO::FETCH_ASSOC);
            }
            $data = array();
            foreach ($pokestops as $pokestop) {
                $data[] = $pokestop['grunt_type'];
            }
        } elseif ($type === 'raidbosslist') {
            $gyms = $db->query("SELECT distinct raid_pokemon_id FROM gym WHERE raid_pokemon_id > 0 AND raid_end_timestamp > UNIX_TIMESTAMP() order by raid_pokemon_id;")->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($gyms as $gym) {
                $data[] = $gym['raid_pokemon_id'];
            }
        }
        return $data;
    }
}
