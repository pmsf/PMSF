<?php

namespace Submit;

class RDM extends Submit
{
    public function submit_raid($pokemonId, $gymId, $eggTime, $monTime, $loggedUser)
    {
        global $db, $noManualRaids, $noRaids, $sendWebhook, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noManualRaids === true || $noRaids === true ) {
            http_response_code( 401 );
            die();
        }
        $raidBosses = json_decode( file_get_contents( "static/dist/data/pokemon.min.json" ), true );
        if ( $eggTime > 60 ) {
            $eggTime = 60;
        }
        if ( $monTime > 45 ) {
            $monTime = 45;
        }
        if ( $eggTime < 0 ) {
            $eggTime = 0;
        }
        if ( $monTime < 0 ) {
            $monTime = 45;
        }
        $forty_five = 45 * 60;
        $hour       = 3600;

        $gym         = $db->get( "gym", [ 'id', 'name', 'lat', 'lon', 'team_id' ], [ 'id' => $gymId ] );
        $gymId       = $gym['id'];
        $add_seconds = ( $monTime * 60 );
        $time_spawn  = time() - $forty_five;
        $level       = 0;
        if ( strpos( $pokemonId, 'egg_' ) !== false ) {
            $add_seconds = ( $eggTime * 60 );
            $level       = (int) substr( $pokemonId, 4, 1 );
            $time_spawn  = time() + $add_seconds;
        }
        $time_battle = time() + $add_seconds;
        $time_end    = $time_battle + $forty_five;
        $cols = [
            'raid_level'              => $level,
            'raid_spawn_timestamp'    => $time_spawn,
            'raid_battle_timestamp'   => $time_battle,
            'raid_end_timestamp'      => $time_end,
            'raid_pokemon_cp'         => 0,
            'raid_pokemon_id'         => 0,
            'raid_pokemon_move_1'     => 0,
            'raid_pokemon_move_2'     => 0,
        ];
        $where = [
            'id'    => $gymId
        ];
        if ( array_key_exists( $pokemonId, $raidBosses ) ) {
            $time_end = time() + $add_seconds;
            // fake the battle start and spawn times cuz rip hashing :(
            $time_battle                       = $time_end - $forty_five;
            $time_spawn                        = $time_battle - $hour;
            $cols['raid_pokemon_id']           = $pokemonId;
            $cols['raid_pokemon_move_1']       = null;
            $cols['raid_pokemon_move_2']       = null;
            $cols['raid_level']                = array_key_exists('level',$raidBosses[ $pokemonId ]) ? $raidBosses[ $pokemonId ]['level'] : 1;
            $cols['raid_pokemon_cp']           = array_key_exists('cp',$raidBosses[ $pokemonId ]) ? $raidBosses[ $pokemonId ]['cp'] : 1;
            $cols['raid_spawn_timestamp']      = $time_spawn;
            $cols['raid_battle_timestamp']     = $time_battle;
            $cols['raid_end_timestamp']        = $time_end;
        } elseif ( $cols['raid_level'] === 0 ) {
            // no boss or egg matched
            http_response_code( 500 );
        }
        $db->update( 'gym', $cols, $where );

        if ( $sendWebhook === true ) {
            $webhook = [
                'message' => [
                    'gym_id'     => $gym['id'],
                    'pokemon_id' => $cols['raid_pokemon_id'],
                    'cp'         => $cols['raid_pokemon_cp'],
                    'move_1'     => 133,
                    'move_2'     => 133,
                    'level'      => $cols['raid_level'],
                    'latitude'   => $gym['lat'],
                    'longitude'  => $gym['lon'],
                    'start'      => $time_battle,
                    'end'        => $time_end,
                    'team_id'    => $gym['team_id'],
                    'name'       => $gym['name']
                ],
                'type'    => 'raid'
            ];
            if ( strpos( $pokemonId, 'egg_' ) !== false ) {
                $webhook['message']['raid_begin'] = $time_spawn;
            }
            foreach ( $webhookUrl as $url ) {
                sendToWebhook($url, array($webhook));
            }
        }
    }

    public function submit_pokemon($lat, $lon, $pokemonId)
    {
        global $db, $noManualPokemon, $noPokemon, $pokemonTimer, $sendWebhook, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noManualPokemon === true || $noPokemon === true ) {
            http_response_code( 401 );
            die();
        }
        if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $pokemonId ) ) {
            $spawnID = randomNum();
            $pokecols    = [
                'id'                 => $spawnID,
                'spawn_id'           => $spawnID,
                'lon'                => $lon,
                'lat'                => $lat,
                'pokemon_id'         => $pokemonId,
                'expire_timestamp'   => time() + $pokemonTimer,
                'updated'            => time(),
                'weather'            => 0,
            ];
            $pointcols   = [
                'id'        => $spawnID,
                'lat'        => $lat,
                'lon'        => $lon,
                'updated'    => time()
            ];
            $db->insert( "spawnpoint", $pointcols );
            $db->insert( "pokemon", $pokecols );
        }
        if ( $sendWebhook === true ) {
            $webhook = [
                'message' => [
                    'cp'                            => null,
                    'cp_multiplier'                 => null,
                    'disappear_time'                => $pokecols['expire_timestamp'],
                    'encounter_id'                  => $pokecols['encounter_id'],
                    'form'                          => 0,
                    'gender'                        => null,
                    'height'                        => null,
                    'weight'                        => null,
                    'individual_attack'             => null,
                    'individual_defense'            => null,
                    'individual_stamina'            => null,
                    'latitude'                      => $pokecols['lat'],
                    'longitude'                     => $pokecols['lon'],
                    'move_1'                        => null,
                    'move_2'                        => null,
                    'pokemon_id'                    => $pokecols['pokemon_id'],
                    'pokemon_level'                 => null,
                    'seconds_until_despawn'         => $pokemonTimer,
                    'verified'                      => true,
                    'weather_boosted_condition'     => $pokecols['weather']
                ],
                'type'    => 'pokemon'
            ];
            foreach ( $webhookUrl as $url ) {
                sendToWebhook($url, array($webhook));
            }
        }
    }

    public function submit_gym($lat, $lon, $gymName, $loggedUser)
    {
        global $db, $noManualGyms, $noGyms, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ( $noManualGyms === true || $noGyms === true ) {
            http_response_code( 401 );
            die();
        }
        if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $gymName ) ) {
            $gymId = randomGymId();
            $cols  = [
                'id'          => $gymId,
                'lat'         => $lat,
                'lon'         => $lon,
                'name'        => $gymName
            ];
            $db->insert( 'gym', $cols );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Added gym with id "' . $gymId . '" and name: "' . $gymName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function toggle_ex($gymId, $loggedUser)
    {
        global $db, $noToggleExGyms, $noGyms, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noToggleExGyms === true || $noGyms === true ) {
            http_response_code( 401 );
            die();
        }
        if ( ! empty( $gymId ) ) {
            $fortName = $db->get( "gym", [ 'name' ], [ 'id' => $gymId ] );
            $park = $db->get( "gym", [ 'ex_raid_eligible' ], [ 'id' => $gymId ] );
            if ( intval($park['ex_raid_eligible']) === 0 ) {
                $cols = [
                    'ex_raid_eligible' => 1
                ];
                $where    = [
                    'id' => $gymId
                ];
                $db->update( "gym", $cols, $where );
                if ( $noDiscordSubmitLogChannel === false ) {
                    $data = array("content" => '```Marked gym with id "' . $gymId . '" and name: "' . $fortName['name'] . '" as EX eligible```', "username" => $loggedUser);
                    sendToWebhook($discordSubmitLogChannelUrl, ($data));
                }
            } else {
                $cols = [
                    'ex_raid_eligible' => 0
                ];
                $where    = [
                    'id' => $gymId
                ];
                $db->update( "gym", $cols, $where );
                if ( $noDiscordSubmitLogChannel === false ) {
                    $data = array("content" => '```Marked gym with id "' . $gymId . '" and name: "' . $fortName['name'] . '" as non EX eligible```', "username" => $loggedUser);
                    sendToWebhook($discordSubmitLogChannelUrl, ($data));
                }
            }
        }
    }

    public function delete_gym($gymId, $loggedUser)
    {
        global $db, $noDeleteGyms, $noGyms, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noDeleteGyms === true || $noGyms === true ) {
            http_response_code( 401 );
            die();
        }
        if ( ! empty( $gymId ) ) {
            $fortName = $db->get( "forts", [ 'name' ], [ 'external_id' => $gymId ] );    
            $db->delete( 'gym', [
                "AND" => [
                    'id' => $gymId
                ]
            ] );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Deleted gym with id "' . $gymId . '" and name: "' . $fortName['name'] . '"```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function submit_pokestop($lat, $lon, $pokestopName, $loggedUser)
    {
        global $db, $noManualPokestops, $noPokestops, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ( $noManualPokestops === true || $noPokestops === true ) {
            http_response_code( 401 );
            die();
        }
        if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $pokestopName ) ) {
            $pokestopId = randomGymId();
            $cols       = [
                'id'                       => $pokestopId,
                'lat'                      => $lat,
                'lon'                      => $lon,
                'lure_expire_timestamp'    => 0,
                'last_modified_timestamp'  => time(),
                'enabled'                  => 0,
                'name'                     => $pokestopName,
                'updated'                  => time()
            ];
            $db->insert( "pokestop", $cols );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Added pokestop with id "' . $pokestopId . '" and gave it the new name: "' . $pokestopName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function modify_pokestop($pokestopId, $pokestopName, $loggedUser)
    {
        global $db, $noRenamePokestops, $noPokestops, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noRenamePokestops === true || $noPokestops === true ) {
            http_response_code( 401 );
            die();
        }
        if ( ! empty( $pokestopName ) && ! empty( $pokestopId ) ) {
            $cols     = [
                'name'        => $pokestopName,
                'updated'     => time()
            ];
            $where    = [
                'id' => $pokestopId
            ];
            $db->update( "pokestop", $cols, $where );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Updated pokestop with id "' . $pokestopId . '" and gave it the new name: "' . $pokestopName . '" . ```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function delete_pokestop($pokestopId, $loggedUser)
    {
        global $db, $noDeletePokestops, $noPokestops, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noDeletePokestops === true || $noPokestops === true ) {
            http_response_code( 401 );
            die();
        }
        $pokestopName = $db->get( "pokestop", [ 'name' ], [ 'id' => $pokestopId ] );
        if ( ! empty( $pokestopId ) ) {
            $db->delete( 'pokestop', [
                "AND" => [
                    'id' => $pokestopId
                ]
            ] );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Deleted pokestop with id "' . $pokestopId . '" and name: "' . $pokestopName['name'] . '"```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function convert_pokestop($pokestopId, $loggedUser)
    {
        global $db, $noConvertPokestops, $noPokestops, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noConvertPokestops === true || $noPokestops === true ) {
            http_response_code( 401 );
            die();
        }
        $gym = $db->get( "pokestop", [ 'lat', 'lon', 'name', 'url' ], [ 'id' => $pokestopId ] );
        if ( ! empty( $pokestopId ) ) {
            $cols     = [
                'id'           => $pokestopId,
                'lat'          => $gym['lat'],
                'lon'          => $gym['lon'],
                'name'         => $gym['name'],
                'url'          => $gym['url']
            ];
            $db->insert( "gym", $cols );
            $db->delete( 'pokestop', [
                "AND" => [
                    'id' => $pokestopId
                ]
            ] );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Converted pokestop with id "' . $pokestopId . '." New Gym: "' . $gym['name'] . '". ```' . $submitMapUrl . '/?lat=' . $gym['lat'] . '&lon=' . $gym['lon'] . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function submit_quest($pokestopId, $questType, $questTarget, $conditionType, $catchPokemonType, $catchPokemon, $raidLevel, $throwType, $curveThrow, $rewardType, $encounter, $item, $itemAmount, $dust, $loggedUser)
    {
        global $db, $noManualQuests, $noPokestops, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noManualQuests === true || $noPokestops === true ) {
            http_response_code( 401 );
            die();
        }
        $pokestopName = $db->get( "pokestop", [ 'name', 'lat', 'lon', 'url', 'id' ], [ 'id' => $pokestopId ] );
        if ( ! empty( $pokestopId ) && ! empty( $questType ) && ! empty( $rewardType ) ) {
            if ($conditionType === '1') {
                $jsonCondition = json_encode(array(
                    'info' => array(
                        'pokemon_type_ids' => $catchPokemonType
                    ),
                    'type' => intval($conditionType)
                    )
                );
            } else if ($conditionType === '2') {
                $jsonCondition = json_encode(array(
                    'info' => array(
                        'pokemon_ids' => $catchPokemon
                    ),
                    'type' => intval($conditionType)
                    )
                );
            } else if ($conditionType === '6' || $conditionType === '7') {
                $jsonCondition = json_encode(array(
                    'info' => array(
                        'raid_levels' => $raidLevel
                    ),
                    'type' => intval($conditionType)
                    )
                );
            } else if ($conditionType === '8') {
                $jsonCondition = json_encode(array(
                    'info' => array(
                        'throw_type_id' => intval($throwType),
                        'hit' => false
                    ),
                    'type' => intval($conditionType)
                    )
                );
                if ($curveThrow === '1') {
                    $jsonCondition .= ',' . json_encode(array(
                        'type' => 15
                        )
                    );
                }
            } else if ($conditionType === '14') {
                $jsonCondition = json_encode(array(
                    'info' => array(
                        'throw_type_id' => intval($throwType),
                        'hit' => false
                    ),
                    'type' => intval($conditionType)
                    )
                );
                if ($curveThrow === '1') {
                    $jsonCondition .= ',' . json_encode(array(
                        'type' => 15
                        )
                    );
                }
            } else if ( ! empty( $conditionType ) ) {
                $jsonCondition = json_encode(array(
                    'type' => intval($conditionType)
                    )
                );
            }

            if ($rewardType === '2') {
                $jsonRewards = json_encode(array(
                    'info' => array(
                        'amount' => intval($itemAmount),
                        'item_id' => intval($item)
                    ),
                    'type' => intval($rewardType)
                ));
            } else if ($rewardType === '3') {
                $jsonRewards = json_encode(array(
                    'info' => array(
                        'amount' => intval($dust)
                    ),
                    'type' => intval($rewardType)
                ));
            } else if ($rewardType === '7') {
                $jsonRewards = json_encode(array(
                    'info' => array(
                        'pokemon_id' => intval($encounter),
                        'form_id'    => 0,
                        'shiny'      => false,
                        'costume_id' => 0,
                        'gender_id'  => 0
                    ),
                    'type' => intval($rewardType)
                ));
            }
            $cols = [
                'updated'           => time(),
                'quest_type'        => $questType,
                'quest_timestamp'   => time(),
                'quest_target'      => $questTarget,
                'quest_conditions'  => ! empty($jsonCondition) ? '[' . $jsonCondition . ']' : '[]',
                'quest_rewards'     => '[' . $jsonRewards . ']',
                'quest_template'    => 'manual_added_challenge'
            ];
            $where = [
                'id'            => $pokestopId
            ];
            $db->update( "pokestop", $cols, $where );

        }
    }

    public function convert_portal_pokestop($portalId, $loggedUser)
    {
        global $db, $manualdb, $noPortals, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ( $noPortals === true ) {
            http_response_code( 401 );
            die();
        }
        $portal = $manualdb->get( "ingress_portals", [ 'lat', 'lon', 'name', 'url' ], [ 'external_id' => $portalId ] );
        if ( ! empty( $portalId ) ) {
            $cols     = [
                'id'       => $portalId,
                'lat'      => $portal['lat'],
                'lon'      => $portal['lon'],
                'name'     => $portal['name'],
                'url'      => $portal['url'],
                'updated'  => time(),
                'enabled'  => 1
            ];
            $db->insert( "pokestop", $cols );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Converted portal with id "' . $portalId . '." New Pokestop: "' . $portal['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portal['lat'] . '&lon=' . $portal['lon'] . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function convert_portal_gym($portalId, $loggedUser)
    {
        global $db, $manualdb, $noPortals, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ( $noPortals === true ) {
            http_response_code( 401 );
            die();
        }
        $portal = $manualdb->get( "ingress_portals", [ 'lat', 'lon', 'name', 'url' ], [ 'external_id' => $portalId ] );
        if ( ! empty( $portalId ) ) {
            $cols     = [
                'id'       => $portalId,
                'lat'      => $portal['lat'],
                'lon'      => $portal['lon'],
                'name'     => $portal['name'],
                'url'      => $portal['url'],
                'updated'  => time()
            ];
            $db->insert( "gym", $cols );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Converted portal with id "' . $portalId . '." New Gym: "' . $portal['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portal['lat'] . '&lon=' . $portal['lon'] . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }
}
