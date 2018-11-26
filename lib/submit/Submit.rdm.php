<?php

namespace Submit;

class RDM extends Submit
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
				'raid_level'       	   => $level,
				'raid_spawn_timestamp' 	   => $time_spawn,
				'raid_battle_timestamp'	   => $time_battle,
				'raid_end_timestamp'   	   => $time_end,
				'raid_pokemon_cp'  	   => 0,
				'raid_pokemon_id'  	   => 0,
				'raid_pokemon_move_1'      => 0,
				'raid_pokemon_move_2'      => 0,
			];
			$where = [
				'id'	=> $gymId
			];
			if ( array_key_exists( $pokemonId, $raidBosses ) ) {
				$time_end = time() + $add_seconds;
				// fake the battle start and spawn times cuz rip hashing :(
				$time_battle         		= $time_end - $forty_five;
				$time_spawn         		= $time_battle - $hour;
				$cols['raid_pokemon_id']  	= $pokemonId;
				$cols['raid_pokemon_move_1']    = null;
				$cols['raid_pokemon_move_2']    = null;
				$cols['raid_level']       	= array_key_exists('level',$raidBosses[ $pokemonId ]) ? $raidBosses[ $pokemonId ]['level'] : 1;
				$cols['raid_pokemon_cp']        = array_key_exists('cp',$raidBosses[ $pokemonId ]) ? $raidBosses[ $pokemonId ]['cp'] : 1;
				$cols['raid_spawn_timestamp']  	= $time_spawn;
				$cols['raid_battle_timestamp'] 	= $time_battle;
				$cols['raid_end_timestamp']    	= $time_end;
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
			global $db, $noManualPokemon, $noPokemon, $pokemonTimer, $sendWebhook, $noDiscordSubmitLogChannel;
			if ( $noManualPokemon === true || $noPokemon === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $pokemonId ) ) {
				$spawnID = randomNum();
				$pokecols    = [
					'id'                    => $spawnID,
					'spawn_id'              => $spawnID,
					'lon'                   => $lon,
					'lat'                   => $lat,
					'pokemon_id'            => $pokemonId,
					'expire_timestamp'      => time() + $pokemonTimer,
					'updated'               => time(),
					'weather' 		=> 0,
				];
				$pointcols   = [
					'id'		=> $spawnID,
					'lat'		=> $lat,
					'lon'		=> $lon,
					'updated'	=> time()
				];
				$db->insert( "spawnpoint", $pointcols );
				$db->insert( "pokemon", $pokecols );
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
						'weather_boosted_condition'         => $pokecols['weather']
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
			file_put_contents('log.txt', print_r($lon, true));
			if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $gymName ) ) {
				$gymId = randomGymId();
				$cols  = [
					'id' 	      => $gymId,
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
			global $db, $noToggleExGyms, $noGyms, $noDiscordSubmitLogChannel;
			if ( $noToggleExGyms === true || $noGyms === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $gymId ) ) {
				$fortName = $db->get( "gym", [ 'name' ], [ 'id' => $gymId ] );
				$park = $db->get( "gym", [ 'ex_raid_eligible' ], [ 'id' => $gymId ] );
				if ( intval($park['ex_raid_eligible']) === 0 ) {
					$cols = [
						'ex_raid_eligible'       => 1
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
						'ex_raid_eligible'       => 0
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
			global $db, $noDeleteGyms, $noGyms, $noDiscordSubmitLogChannel;
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
			global $db, $noManualPokestops, $noPokestops, $noDiscordSubmitLogChannel;
			if ( $noManualPokestops === true || $noPokestops === true ) {
				http_response_code( 401 );
				die();
			}
			if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $pokestopName ) ) {
				$pokestopId = randomGymId();
				$cols       = [
					'id'          			=> $pokestopId,
					'lat'         			=> $lat,
					'lon'         			=> $lon,
					'lure_expire_timestamp'		=> 0,
					'last_modified_timestamp'	=> time(),
					'enabled'      			=> 0,
					'name'        			=> $pokestopName,
					'updated'     			=> time()
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
			global $db, $noRenamePokestops, $noPokestops, $noDiscordSubmitLogChannel;
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
			global $db, $noDeletePokestops, $noPokestops, $noDiscordSubmitLogChannel;
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
			global $db, $noConvertPokestops, $noPokestops, $noDiscordSubmitLogChannel;
			if ( $noConvertPokestops === true || $noPokestops === true ) {
				http_response_code( 401 );
				die();
			}
			$gymLat = $db->get( "pokestop", [ 'lat' ], [ 'id' => $pokestopId ] );
			$gymLon= $db->get( "pokestop", [ 'lon' ], [ 'id' => $pokestopId ] );
			$gymName = $db->get( "pokestop", [ 'name' ], [ 'id' => $pokestopId ] );
			$gymUrl = $db->get( "pokestop", [ 'url' ], [ 'id' => $pokestopId ] );
			if ( ! empty( $pokestopId ) ) {
				$cols     = [
					'id'  => $pokestopId,
					'lat'          => $gymLat['lat'],
					'lon'          => $gymLon['lon'],
					'name'         => $gymName['name'],
					'url'          => $gymUrl['url']
				];
				$db->insert( "gym", $cols );
				$db->delete( 'pokestop', [
					"AND" => [
						'id' => $pokestopId
					]
				] );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Converted pokestop with id "' . $pokestopId . '." New Gym: "' . $gymName['name'] . '". ```' . $submitMapUrl . '/?lat=' . $gymLat['lat'] . '&lon=' . $gymLon['lon'] . '&zoom=18 ', "username" => $loggedUser);
					sendToWebhook($discordSubmitLogChannelUrl, ($data));
				}
			}
		}
	public function submit_quest($pokestopId, $questId, $rewardId, $loggedUser)
		{
			global $noManualQuests, $noPokestops, $noDiscordSubmitLogChannel;
			if ( $noManualQuests === true || $noPokestops === true ) {
				http_response_code( 401 );
				die();
			}
			// Unfinished
			die();
		}
	public function convert_portal_pokestop($portalId, $loggedUser)
		{
			global $db, $manualdb, $noPortals, $noDiscordSubmitLogChannel;
			if ( $noPortals === true ) {
				http_response_code( 401 );
				die();
			}
			$portalLat = $manualdb->get( "ingress_portals", [ 'lat' ], [ 'external_id' => $portalId ] );
			$portalLon= $manualdb->get( "ingress_portals", [ 'lon' ], [ 'external_id' => $portalId ] );
			$portalName = $manualdb->get( "ingress_portals", [ 'name' ], [ 'external_id' => $portalId ] );
			$portalUrl = $manualdb->get( "ingress_portals", [ 'url' ], [ 'external_id' => $portalId ] );
			if ( ! empty( $portalId ) ) {
				$cols     = [
					'id'  	       => $portalId,
					'lat'          => $portalLat['lat'],
					'lon'          => $portalLon['lon'],
					'name'         => $portalName['name'],
					'url'          => $portalUrl['url'],
					'updated'      => time()
				];
				$db->insert( "pokestop", $cols );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Converted portal with id "' . $portalId . '." New Pokestop: "' . $portalName['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portalLat['lat'] . '&lon=' . $portalLon['lon'] . '&zoom=18 ', "username" => $loggedUser);
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
			$portalLat = $manualdb->get( "ingress_portals", [ 'lat' ], [ 'external_id' => $portalId ] );
			$portalLon= $manualdb->get( "ingress_portals", [ 'lon' ], [ 'external_id' => $portalId ] );
			$portalName = $manualdb->get( "ingress_portals", [ 'name' ], [ 'external_id' => $portalId ] );
			$portalUrl = $manualdb->get( "ingress_portals", [ 'url' ], [ 'external_id' => $portalId ] );
			if ( ! empty( $portalId ) ) {
				$cols     = [
					'id'  	       => $portalId,
					'lat'          => $portalLat['lat'],
					'lon'          => $portalLon['lon'],
					'name'         => $portalName['name'],
					'url'          => $portalUrl['url'],
					'updated'      => time()
				];
				$db->insert( "gym", $cols );
				if ( $noDiscordSubmitLogChannel === false ) {
					$data = array("content" => '```Converted portal with id "' . $portalId . '." New Gym: "' . $portalName['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portalLat['lat'] . '&lon=' . $portalLon['lon'] . '&zoom=18 ', "username" => $loggedUser);
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
