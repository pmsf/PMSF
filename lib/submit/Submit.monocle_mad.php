<?php

namespace Submit;

class Monocle_MAD extends Submit
{
	public function submit_raid($pokemonId, $gymId, $eggTime, $monTime, $loggedUser)
		{
			global $db, $noManualRaids, $noRaids, $sendWebhook, $noDiscordSubmitLogChannel;
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

			$gym         = $db->get( "forts", [ 'id', 'name', 'lat', 'lon' ], [ 'external_id' => $gymId ] );
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
				'fort_id'		   => $gymId,
				'level'		       	   => $level,
				'time_spawn'	 	   => $time_spawn,
				'time_battle'		   => $time_battle,
				'time_end' 	  	   => $time_end,
				'cp'		  	   => 0,
				'pokemon_id' 	 	   => 0,
				'move_1'		   => 0,
				'move_2'		   => 0,
				'submitted_by'		   => $loggedUser
			];
			if ( array_key_exists( $pokemonId, $raidBosses ) ) {
				$time_end = time() + $add_seconds;
				// fake the battle start and spawn times cuz rip hashing :(
				$time_battle         	= $time_end - $forty_five;
				$time_spawn         	= $time_battle - $hour;
				$cols['pokemon_id']  	= $pokemonId;
				$cols['move_1']    	= null;
				$cols['move_2']    	= null;
				$cols['level']       	= array_key_exists('level',$raidBosses[ $pokemonId ]) ? $raidBosses[ $pokemonId ]['level'] : 1;
				$cols['cp']        	= array_key_exists('cp',$raidBosses[ $pokemonId ]) ? $raidBosses[ $pokemonId ]['cp'] : 1;
				$cols['time_spawn']  	= $time_spawn;
				$cols['time_battle'] 	= $time_battle;
				$cols['time_end']    	= $time_end;
			} elseif ( $cols['level'] === 0 ) {
				// no boss or egg matched
				http_response_code( 500 );
			}
			$db->query( 'DELETE FROM raids WHERE fort_id = :gymId', [ ':gymId' => $gymId ] );
			$db->insert( 'raids', $cols );
			$db->query( "UPDATE fort_sightings SET updated = :updated WHERE fort_id = :gymId", [
				'updated' => time(),
				':gymId'  => $gymId
			] );

			if ( $sendWebhook === true ) {
				$webhook = [
					'message' => [
						'gym_id'     => $gym['external_id'],
						'pokemon_id' => $cols['pokemon_id'],
						'cp'         => $cols['cp'],
						'move_1'     => 133,
						'move_2'     => 133,
						'level'      => $cols['level'],
						'latitude'   => $gym['lat'],
						'longitude'  => $gym['lon'],
						'start'      => $time_battle,
						'end'        => $time_end,
						'team_id'    => 0,
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
			global $db, $noManualPokemon, $noPokemon, $pokemonTimer, $sendWebhook, $noDiscordSubmitLogChannel;
			if ( $noManualPokemon === true || $noPokemon === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $pokemonId ) ) {
				$spawnID = randomNum();
				$pokecols    = [
					'encounter_id'                  => $spawnID,
					'spawn_id'              	=> $spawnID,
					'lon'                   	=> $lon,
					'lat'                   	=> $lat,
					'pokemon_id'            	=> $pokemonId,
					'expire_timestamp'      	=> time() + $pokemonTimer,
					'updated'               	=> time(),
					'weather_boosted_condition' 	=> 0,
				];
				$db->insert( "sightings", $pokecols );
			}
			if ( $sendWebhook === true ) {
				$webhook = [
					'message' => [
						'cp'                                => null,
						'cp_multiplier'                     => null,
						'disappear_time'                    => $pokecols['expire_timestamp'],
						'encounter_id'                      => $pokecols['encounter_id'],
						'form'                              => 0,
						'gender'                            => null,
						'height'                            => null,
						'weight'                            => null,
						'individual_attack'                 => null,
						'individual_defense'                => null,
						'individual_stamina'                => null,
						'latitude'                          => $pokecols['lat'],
						'longitude'                         => $pokecols['lon'],
						'move_1'                            => null,
						'move_2'                            => null,
						'pokemon_id'                        => $pokecols['pokemon_id'],
						'pokemon_level'                     => null,
						'seconds_until_despawn'             => $pokemonTimer,
						'verified'                          => true,
						'weather_boosted_condition'         => $pokecols['weather_boosted_condition']
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
			global $db, $noManualGyms, $noGyms, $noDiscordSubmitLogChannel;
			if ( $noManualGyms === true || $noGyms === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $gymName ) ) {
				$gymId = randomGymId();
				$cols  = [
					'external_id' => $gymId,
					'lat'         => $lat,
					'lon'         => $lon,
					'name'        => $gymName,
					'edited_by'   => $loggedUser
				];
				$db->insert( 'forts', $cols );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Added gym with id "' . $gymId . '" and name: "' . $gymName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function toggle_ex($gymId, $loggedUser)
		{
			global $db, $noToggleExGyms, $noGyms, $noDiscordSubmitLogChannel;
			if ( $noToggleExGyms === true || $noGyms === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $gymId ) ) {
				$fortName = $db->get( "forts", [ 'name' ], [ 'external_id' => $gymId ] );
				$fortid = $db->get( "forts", [ 'id' ], [ 'external_id' => $gymId ] );
				$park = $db->get( "forts", [ 'park' ], [ 'external_id' => $gymId ] );
				if ( empty($park['park'])) {
					$cols = [
						'park'       => 'Park'
					];
					$where    = [
						'external_id' => $gymId
					];
					$db->update( "forts", $cols, $where );
					if ( $noDiscordSubmitLogChannel === false ) {
						$data = array("content" => '```Marked gym with id "' . $gymId . '" and name: "' . $fortName['name'] . '" as EX eligible```', "username" => $loggedUser);
						sendToWebhook($discordSubmitLogChannelUrl, ($data));
					}
				} else {
					$cols = [
						'park'       => null
					];
					$where    = [
						'external_id' => $gymId
					];
					$db->update( "forts", $cols, $where );
					if ( $noDiscordSubmitLogChannel === false ) {
						$data = array("content" => '```Marked gym with id "' . $gymId . '" and name: "' . $fortName['name'] . '" as non EX eligible```', "username" => $loggedUser);
						sendToWebhook($discordSubmitLogChannelUrl, ($data));
					}
				}
			}
		}
	public function delete_gym($gymId, $loggedUser)
		{
			global $db, $noDeleteGyms, $noGyms, $noDiscordSubmitLogChannel;
			if ( $noDeleteGyms === true || $noGyms === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $gymId ) ) {
				$fortid = $db->get( "forts", [ 'id' ], [ 'external_id' => $gymId ] );
				$fortName = $db->get( "forts", [ 'name' ], [ 'external_id' => $gymId ] );
				if ( $fortid ) {
					$db->delete( 'fort_sightings', [
						"AND" => [
							'fort_id' => $fortid['id']
						]
					]);
					$db->delete( 'raids', [
						"AND" => [
							'fort_id' => $fortid['id']
						]
					]);
					$db->delete( 'forts', [
						"AND" => [
							'external_id' => $gymId
						]
					]);
					if ( $noDiscordSubmitLogChannel === false ) {
						$data = array("content" => '```Deleted gym with id "' . $gymId . '" and name: "' . $fortName['name'] . '"```', "username" => $loggedUser);
						sendToWebhook($discordSubmitLogChannelUrl, ($data));
					}
				}
			}
		}
	public function submit_pokestop($lat, $lon, $pokestopName, $loggedUser)
		{
			global $db, $noManualPokestops, $noPokestops, $noDiscordSubmitLogChannel;
			if ( $noManualPokestops === true || $noPokestops === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $pokestopName ) ) {
				$pokestopId = randomGymId();
				$cols       = [
					'external_id'  			=> $pokestopId,
					'lat'         			=> $lat,
					'lon'         			=> $lon,
					'name'        			=> $pokestopName,
					'updated'     			=> time(),
					'edited_by'			=> $loggedUser
				];
				$db->insert( "pokestops", $cols );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Added pokestop with id "' . $pokestopId . '" and gave it the new name: "' . $pokestopName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18 ', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function modify_pokestop($pokestopId, $pokestopName, $loggedUser)
		{
			global $db, $noRenamePokestops, $noPokestops, $noDiscordSubmitLogChannel;
			if ( $noRenamePokestops === true || $noPokestops === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $pokestopName ) && ! empty( $pokestopId ) ) {
				$cols     = [
					'name'        => $pokestopName,
					'updated'     => time(),
					'edited_by'   => $loggedUser
				];
				$where    = [
					'external_id' => $pokestopId
				];
				$db->update( "pokestops", $cols, $where );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Updated pokestop with id "' . $pokestopId . '" and gave it the new name: "' . $pokestopName . '" . ```', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function delete_pokestop($pokestopId, $loggedUser)
		{
			global $db, $noDeletePokestops, $noPokestops, $noDiscordSubmitLogChannel;
			if ( $noDeletePokestops === true || $noPokestops === true ) {
				http_response_code( 401 );
				die();
			}
			$pokestopName = $db->get( "pokestops", [ 'name' ], [ 'external_id' => $pokestopId ] );
			if ( ! empty( $pokestopId ) ) {
				$db->delete( 'pokestops', [
					"AND" => [
						'external_id' => $pokestopId
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
			global $db, $noConvertPokestops, $noPokestops, $noDiscordSubmitLogChannel;
			if ( $noConvertPokestops === true || $noPokestops === true ) {
				http_response_code( 401 );
				die();
			}
			$gym = $db->get( "pokestops", [ 'lat', 'lon', 'name', 'url' ], [ 'external_id' => $pokestopId ] );
			if ( ! empty( $pokestopId ) ) {
				$cols     = [
					'id'  => $pokestopId,
					'lat'          => $gym['lat'],
					'lon'          => $gym['lon'],
					'name'         => $gym['name'],
					'url'          => $gym['url']
				];
				$db->insert( "forts", $cols );
				$db->delete( 'pokestops', [
					"AND" => [
						'external_id' => $pokestopId
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
			global $db, $noManualQuests, $noPokestops, $noDiscordSubmitLogChannel;
			if ( $noManualQuests === true || $noPokestops === true ) {
				http_response_code( 401 );
				die();
			}
			$pokestopName = $db->get( "pokestops", [ 'name', 'lat', 'lon', 'url', 'external_id' ], [ 'GUID' => $pokestopId ] );
			$pokestopQuest = $db->get( "trs_quest", [ 'GUID' ], [ 'GUID' => $pokestopId ] );
			if ( empty( $pokestopQuest ) ) {
				$cols = [
					'GUID' => $pokestopId
				];
				$db->insert( "trs_quest", $cols );
			}
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
						'type' => intval($rewardType),
						'item' => array(
							'amount' => intval($itemAmount),
							'item' => intval($item)
						),
						'stardust' => intval('0'),
						'candy' => array(
							'pokemon_id' => intval('0'),
							'amount' => intval('0')
						),
						'avatar_template_id' => '',
						'quest_template_id' => '',
						'pokemon_encounter' => array(
							'pokemon_id' => intval('0'),
							'use_quest_pokemon_encounter_distribuition' => 'False',
							'pokemon_display' => array(
								'is_shiny' => 'False',
								'weather_boosted_value' => intval('0'),
								'weather_boosted_description' => 'NONE',
								'gender_value' => intval('0'),
								'form_value' => intval('0'),
								'costume_value' => intval('0')
							),
							'is_hidden_ditto' => 'False',
							'ditto_display' => array(
								'is_shiny' => 'False',
								'weather_boosted_value' => intval('0'),
								'weather_boosted_description' => 'NONE',
								'gender_value' => intval('0'),
								'form_value' => intval('0'),
								'costume_value' => intval('0')
							)
						)
					));
				} else if ($rewardType === '3') {
					$jsonRewards = json_encode(array(
						'type' => intval($rewardType),
						'item' => array(
							'amount' => intval('0'),
							'item' => intval('0')
						),
						'stardust' => intval($dust),
						'candy' => array(
							'pokemon_id' => intval('0'),
							'amount' => intval('0')
						),
						'avatar_template_id' => '',
						'quest_template_id' => '',
						'pokemon_encounter' => array(
							'pokemon_id' => intval('0'),
							'use_quest_pokemon_encounter_distribuition' => 'False',
							'pokemon_display' => array(
								'is_shiny' => 'False',
								'weather_boosted_value' => intval('0'),
								'weather_boosted_description' => 'NONE',
								'gender_value' => intval('0'),
								'form_value' => intval('0'),
								'costume_value' => intval('0')
							),
							'is_hidden_ditto' => 'False',
							'ditto_display' => array(
								'is_shiny' => 'False',
								'weather_boosted_value' => intval('0'),
								'weather_boosted_description' => 'NONE',
								'gender_value' => intval('0'),
								'form_value' => intval('0'),
								'costume_value' => intval('0')
							)
						)
					));
				} else if ($rewardType === '7') {
					$jsonRewards = json_encode(array(
						'type' => intval($rewardType),
						'item' => array(
							'amount' => intval('0'),
							'item' => intval('0')
						),
						'stardust' => intval('0'),
						'candy' => array(
							'pokemon_id' => intval('0'),
							'amount' => intval('0')
						),
						'avatar_template_id' => '',
						'quest_template_id' => '',
						'pokemon_encounter' => array(
							'pokemon_id' => intval($encounter),
							'use_quest_pokemon_encounter_distribuition' => 'False',
							'pokemon_display' => array(
								'is_shiny' => 'False',
								'weather_boosted_value' => intval('0'),
								'weather_boosted_description' => 'NONE',
								'gender_value' => intval('0'),
								'form_value' => intval('0'),
								'costume_value' => intval('0')
							),
							'is_hidden_ditto' => 'False',
							'ditto_display' => array(
								'is_shiny' => 'False',
								'weather_boosted_value' => intval('0'),
								'weather_boosted_description' => 'NONE',
								'gender_value' => intval('0'),
								'form_value' => intval('0'),
								'costume_value' => intval('0')
							)
						)
					));
				}
				$cols = [
					'quest_type'		=> $questType,
					'quest_timestamp'	=> time(),
					'quest_stardust'	=> ! empty($dust) ? $dust : '0',
					'quest_pokemon_id'	=> ! empty($encounter) ? $encounter : '0',
					'quest_reward_type'	=> $rewardType,
					'quest_item_id'		=> ! empty($item) ? $item : '0',
					'quest_item_amount'	=> ! empty($itemAmount) ? $itemAmount : '0',
					'quest_target'		=> $questTarget,
					'quest_condition'	=> ! empty($jsonCondition) ? '[' . $jsonCondition . ']' : '[]',
					'quest_reward'		=> '[' . $jsonRewards . ']'
				];
				$where = [
					'GUID'		=> $pokestopId
				];
				$db->update( "trs_quest", $cols, $where );

			}
		}
	public function convert_portal_pokestop($portalId, $loggedUser)
		{
			global $db, $manualdb, $noPortals, $noDiscordSubmitLogChannel;
			if ( $noPortals === true ) {
				http_response_code( 401 );
				die();
			}
			$portal = $manualdb->get( "ingress_portals", [ 'lat', 'lon', 'name', 'url' ], [ 'external_id' => $portalId ] );
			if ( ! empty( $portalId ) ) {
				$cols     = [
					'external_id'  => $portalId,
					'lat'          => $portal['lat'],
					'lon'          => $portal['lon'],
					'name'         => $portal['name'],
					'url'          => $portal['url'],
					'updated'      => time()
				];
				$db->insert( "pokestops", $cols );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Converted portal with id "' . $portalId . '." New Pokestop: "' . $portal['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portal['lat'] . '&lon=' . $portal['lon'] . '&zoom=18 ', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function convert_portal_gym($portalId, $loggedUser)
		{
			global $db, $manualdb, $noPortals, $noDiscordSubmitLogChannel;
			if ( $noPortals === true ) {
				http_response_code( 401 );
				die();
			}
			$portal = $manualdb->get( "ingress_portals", [ 'lat', 'lon', 'name', 'url' ], [ 'external_id' => $portalId ] );
			if ( ! empty( $portalId ) ) {
				$cols     = [
					'external_id'  => $portalId,
					'lat'          => $portal['lat'],
					'lon'          => $portal['lon'],
					'name'         => $portal['name'],
					'url'          => $portal['url']
				];
				$db->insert( "forts", $cols );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Converted portal with id "' . $portalId . '." New Gym: "' . $portal['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portal['lat'] . '&lon=' . $portal['lon'] . '&zoom=18 ', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function mark_portal($portalId, $loggedUser)
		{
			global $manualdb, $noPortals, $noDiscordSubmitLogChannel;
			if ( $noPortals === true ) {
				http_response_code( 401 );
				die();
			}
			$portalName = $manualdb->get( "ingress_portals", [ 'name' ], [ 'external_id' => $portalId ] );
			if ( ! empty( $portalId ) ) {
				$cols     = [
					'updated'      => time(),
					'checked'      => 1
				];
				$where    = [
					'external_id' => $portalId
				];
				$manualdb->update( "ingress_portals", $cols, $where );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Marked portal with id "' . $portalId . '." As no Pokestop or Gym. PortalName: "' . $portalName['name'] . '". ```', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function delete_portal($portalId, $loggedUser)
		{
			global $manualdb, $noPortals, $noDeletePortal, $noDiscordSubmitLogChannel;
			if ( $noPortals === true || $noDeletePortal === true) {
				http_response_code( 401 );
				die();
			}
			$portalName = $manualdb->get( "ingress_portals", [ 'name' ], [ 'external_id' => $portalId ] );
			if ( ! empty( $portalId ) ) {
				$manualdb->delete( 'ingress_portals', [
					"AND" => [
						'external_id' => $portalId
					]
				] );
			}
			if ( $noDiscordSubmitLogChannel === false ) {
				$data = array("content" => '```Deleted portal with id "' . $portalId . '" and name: "' . $portalName['title'] . '" . ```', "username" => $loggedUser);
				sendToWebhook($discordSubmitLogChannelUrl, ($data));
			}
		}
	public function modify_nest($nestId, $pokemonId, $loggedUser)
		{
			global $manualdb, $noManualNests, $noNests, $noDiscordSubmitLogChannel;
			if ( $noManualNests === true || $noNests === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $pokemonId ) && ! empty( $nestId ) ) {
				$cols  = [
					'pokemon_id' => $pokemonId,
					'nest_submitted_by' => $loggedUser
				];
				$where = [
					'nest_id' => $nestId
				];
				$manualdb->update( "nests", $cols, $where );
			}
		}
	public function submit_nest($lat, $lon, $pokemonId, $loggedUser)
		{
			global $manualdb, $noAddNewNests, $noNests, $noDiscordSubmitLogChannel;
			if ( $noAddNewNests === true || $noNests === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $pokemonId ) ) {
				$cols = [
					'pokemon_id' 	=> $pokemonId,
					'lat'        	=> $lat,
					'lon'        	=> $lon,
					'type'       	=> 0,
					'updated'    	=> time(),
					'nest_submitted_by'	=> $loggedUser
				];
				$manualdb->insert( "nests", $cols );
			}
		}
	public function delete_nest($nestId)
		{
			global $manualdb, $noDeleteNests, $noNests, $noDiscordSubmitLogChannel;
			if ( $noDeleteNests === true || $noNests === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $nestId ) ) {
				$manualdb->delete( 'nests', [
					"AND" => [
						'nest_id' => $nestId
					]
				] );
			}
		}
	public function submit_community($lat, $lon, $communityName, $communityDescription, $communityInvite, $loggedUser)
		{
			global $manualdb, $noCommunity, $noAddNewCommunity, $noDiscordSubmitLogChannel;
			if ( $noCommunity === true || $noAddNewCommunity === true ) {
				http_response_code( 401 );
				die();
			}
			if (strpos($communityInvite, 'https://discord.gg') !== false) {
				$communityType = 3;
			} elseif (strpos($communityInvite, 'https://t.me') !== false) {
				$communityType = 4;
			} elseif (strpos($communityInvite, 'https://chat.whatsapp.com') !== false) {
				$communityType = 5;
			} elseif (strpos($communityInvite, 'https://m.me/join') !== false) {
				$communityType = 6;
			} elseif (strpos($communityInvite, 'https://facebook.com/groups') !== false) {
				$communityType = 7;
			} elseif (strpos($communityInvite, 'https://groupme.com/join_group') !== false) {
				$communityType = 8;
			} else {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $communityName ) && ! empty( $communityDescription ) && ! empty( $communityInvite ) ) {
				$communityId = randomNum();
				$cols       = [
					'community_id'        => $communityId,
					'title'               => $communityName,
					'description'         => $communityDescription,
					'type'                => $communityType,
					'image_url'           => null,
					'team_instinct'       => 1,
					'team_mystic'         => 1,
					'team_valor'          => 1,
					'has_invite_url'      => 1,
					'invite_url'          => $communityInvite,
					'lat'                 => $lat,
					'lon'                 => $lon,
					'updated'             => time(),
					'source'              => 1,
					'submitted_by'        => $loggedUser 
				];
				$manualdb->insert( "communities", $cols );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Added community with id "' . $communityId . '" and gave it the new name: "' . $communityName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lng . '&zoom=18 ', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function modify_community($communityId, $communityName, $communityDescription, $communityInvite, $loggedUser)
		{
			global $manualdb, $noCommunity, $noEditCommunity, $noDiscordSubmitLogChannel;
			if ( $noCommunity === true || $noEditCommunity === true ) {
				http_response_code( 401 );
				die();
			}
			if (strpos($communityInvite, 'https://discord.gg') !== false) {
				$communityType = 3;
			} elseif (strpos($communityInvite, 'https://t.me') !== false) {
				$communityType = 4;
			} elseif (strpos($communityInvite, 'https://chat.whatsapp.com') !== false) {
				$communityType = 5;
			} elseif (strpos($communityInvite, 'https://m.me/join') !== false) {
				$communityType = 6;
			} elseif (strpos($communityInvite, 'https://facebook.com/groups') !== false) {
				$communityType = 7;
			} elseif (strpos($communityInvite, 'https://groupme.com/join_group') !== false) {
				$communityType = 8;
			} else {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $communityId ) && ! empty( $communityName ) && ! empty( $communityDescription ) && ! empty( $communityInvite ) ) {
				$cols       = [
					'title'               => $communityName,
					'description'         => $communityDescription,
					'type'                => $communityType,
					'team_instinct'       => 1,
					'team_mystic'         => 1,
					'team_valor'          => 1,
					'has_invite_url'      => 1,
					'invite_url'          => $communityInvite,
					'updated'             => time(),
					'source'              => 1,
					'submitted_by'        => $loggedUser 
				];
				$where    = [
					'community_id' => $communityId
				];
				$manualdb->update( "communities", $cols, $where );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Updated community with id "' . $communityId . '" and gave it the new name: "' . $communityName . '" . ```', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function delete_community($communityId, $loggedUser)
		{
			global $manualdb, $noCommunity, $noDeleteCommunity, $noDiscordSubmitLogChannel;
			if ( $noCommunity === true || $noDeleteCommunity === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $communityId ) ) {
				$manualdb->delete( 'communities', [
					"AND" => [
						'community_id' => $communityId
					]
				] );
			}
			if ( $noDiscordSubmitLogChannel === false ) {
				$data = array("content" => '```Deleted community with id "' . $communityId . '" and name: "' . $communityName['title'] . '" . ```', "username" => $loggedUser);
				sendToWebhook($discordSubmitLogChannelUrl, ($data));
			}
		}
}
