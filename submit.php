<?php
$timing['start'] = microtime( true );
include( 'config/config.php' );
global $map, $fork, $db, $raidBosses, $webhookUrl, $sendWebhook, $sendQuestWebhook, $noManualRaids, $noRaids, $noManualPokemon, $noPokemon, $noPokestops, $noManualPokestops, $noGyms, $noManualGyms, $noManualQuests, $noManualNests, $noNests, $noAddNewNests, $pokemonTimer, $noRenamePokestops, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
$action = ! empty( $_POST['action'] ) ? $_POST['action'] : '';
$lat    = ! empty( $_POST['lat'] ) ? $_POST['lat'] : '';
$lng    = ! empty( $_POST['lng'] ) ? $_POST['lng'] : '';

// set content type
header( 'Content-Type: application/json' );

$now = new DateTime();
$now->sub( new DateInterval( 'PT20S' ) );

$d           = array();
$d['status'] = "ok";

$d["timestamp"] = $now->getTimestamp();

if ( $action === "raid" ) {

    if ( $noManualRaids === true || $noRaids === true ) {
        http_response_code( 401 );
        die();
    }
    $raidBosses = json_decode( file_get_contents( "static/dist/data/pokemon.min.json" ), true );
    $pokemonId  = ! empty( $_POST['pokemonId'] ) ? $_POST['pokemonId'] : 0;
    $gymId      = ! empty( $_POST['gymId'] ) ? $_POST['gymId'] : 0;
    $eggTime    = ! empty( $_POST['eggTime'] ) ? $_POST['eggTime'] : 0;
    $monTime    = ! empty( $_POST['monTime'] ) ? $_POST['monTime'] : 0;
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

// brimful of asha on the:
    $forty_five = 45 * 60;
    $hour       = 3600;

//$db->debug();
// fetch fort_id
    $gym         = $db->get( "forts", [ 'id', 'name', 'lat', 'lon', 'external_id' ], [ 'external_id' => $gymId ] );
    $gymId       = $gym['id'];
    $add_seconds = ( $monTime * 60 );
    $time_spawn  = time() - $forty_five;
    $level       = 0;
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( strpos( $pokemonId, 'egg_' ) !== false ) {
        $add_seconds = ( $eggTime * 60 );
        $level       = (int) substr( $pokemonId, 4, 1 );
        $time_spawn  = time() + $add_seconds;
    }

    $time_battle = time() + $add_seconds;
    $time_end    = $time_battle + $forty_five;
    $extId       = rand( 0, 65535 ) . rand( 0, 65535 );

    $cols = [
        'external_id' => $gymId,
        'fort_id'     => $gymId,
        'level'       => $level,
        'time_spawn'  => $time_spawn,
        'time_battle' => $time_battle,
        'time_end'    => $time_end,
        'cp'          => 0,
        'pokemon_id'  => 0,
        'move_1'      => 0,
        'move_2'      => 0,
        'submitted_by'=> $loggedUser

    ];
    if ( array_key_exists( $pokemonId, $raidBosses ) ) {
        $time_end = time() + $add_seconds;
        // fake the battle start and spawn times cuz rip hashing :(
        $time_battle         = $time_end - $forty_five;
        $time_spawn          = $time_battle - $hour;
        $cols['pokemon_id']  = $pokemonId;
        $cols['move_1']      = null;
        $cols['move_2']      = null;
        $cols['level']       = array_key_exists('level',$raidBosses[ $pokemonId ]) ? $raidBosses[ $pokemonId ]['level'] : 1;
        $cols['cp']          = array_key_exists('cp',$raidBosses[ $pokemonId ]) ? $raidBosses[ $pokemonId ]['cp'] : 1;
        $cols['time_spawn']  = $time_spawn;
        $cols['time_battle'] = $time_battle;
        $cols['time_end']    = $time_end;
    } elseif ( $cols['level'] === 0 ) {
        // no boss or egg matched
        http_response_code( 500 );
    }
    $db->query( 'DELETE FROM raids WHERE fort_id = :gymId', [ ':gymId' => $gymId ] );
    $db->insert( "raids", $cols );

// also update fort_sightings so PMSF knows the gym has changed
// todo: put team stuff in here too
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
                'start' => $time_battle,
                'end'   => $time_end,
                'team_id'       => 0,
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
} elseif ( $action === "pokemon" ) {
    if ( $noManualPokemon === true || $noPokemon === true ) {
        http_response_code( 401 );
        die();
    }
    $id = ! empty( $_POST['id'] ) ? $_POST['id'] : 0;
    if ( ! empty( $lat ) && ! empty( $lng ) && ! empty( $id ) ) {
        $spawnID = randomNum();
        $cols    = [
            'spawn_id'                  => $spawnID,
            'encounter_id'              => $spawnID,
            'lon'                       => $lng,
            'lat'                       => $lat,
            'pokemon_id'                => $id,
            'expire_timestamp'          => time() + $pokemonTimer,
            'updated'                   => time(),
            'weather_boosted_condition' => 0,
        ];
        $db->insert( "sightings", $cols );
    }
    if ( $sendWebhook === true ) {
	$webhook = [
	    'message' => [
		'cp'                                => null,
		'cp_multiplier'                     => null,
		'disappear_time'                    => $cols['expire_timestamp'],
		'encounter_id'                      => $cols['encounter_id'],
		'form'                              => 0,
		'gender'                            => null,
		'height'                            => null,
		'weight'                            => null,
		'individual_attack'                 => null,
		'individual_defense'                => null,
		'individual_stamina'                => null,
		'latitude'                          => $cols['lat'],
		'longitude'                         => $cols['lon'],
		'move_1'                            => null,
		'move_2'                            => null,
		'pokemon_id'                        => $cols['pokemon_id'],
		'pokemon_level'                     => null,
		'seconds_until_despawn'             => $pokemonTimer,
		'verified'                          => true,
		'weather_boosted_condition'         => $cols['weather_boosted_condition']
	    ],
	    'type'    => 'pokemon'
	];
	foreach ( $webhookUrl as $url ) {
            sendToWebhook($url, array($webhook));
	}
    }
} elseif ( $action === "gym" ) {
    if ( $noManualGyms === true || $noGyms === true ) {
        http_response_code( 401 );
        die();
    }
    $gymName = ! empty( $_POST['gymName'] ) ? $_POST['gymName'] : '';
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( ! empty( $lat ) && ! empty( $lng ) && ! empty( $gymName ) ) {
        $gymId = randomGymId();
        $cols  = [
            'external_id' => $gymId,
            'lat'         => $lat,
            'lon'         => $lng,
            'name'        => $gymName,
            'edited_by'   => $loggedUser
        ];
        $db->insert( "forts", $cols );
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Added gym with id "' . $gymId . '" and name: "' . $gymName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lng . '&zoom=18', "username" => $loggedUser);
            $curl = curl_init($discordSubmitLogChannelUrl);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
        }
    }
} elseif ( $action === "quest" ) {
    if ( $noManualQuests === true || $noPokestops === true ) {
        http_response_code( 401 );
        die();
    }
    $pokestopId = ! empty( $_POST['pokestopId'] ) ? $_POST['pokestopId'] : '';
    $questId    = $_POST['questId'] == "NULL" ? 0 : $_POST['questId'];
    $rewardId   = $_POST['rewardId'] == "NULL" ? 0 : $_POST['rewardId'];
    $pokestop         = $db->get( "pokestops", [ 'name', 'lat', 'lon', 'external_id' ], [ 'external_id' => $pokestopId ] );
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( ! empty( $pokestopId ) && ! empty( $questId ) && ! empty( $rewardId ) ) {
        $cols  = [
            'quest_id' => $questId,
            'reward_id'   => $rewardId,
            'quest_submitted_by'  => $loggedUser
        ];
        $where = [
            'external_id' => $pokestopId
        ];
        $db->update( "pokestops", $cols, $where );
    }
    if ( $sendQuestWebhook === true ) {
	$questwebhook = [
	    'message' => [
		'latitude'                          => $pokestop['lat'],
		'longitude'                         => $pokestop['lon'],
		'pokestop_id'                       => $pokestopId,
                'name'                              => $pokestop['name'],
		'quest_id'                          => $cols['quest_id'],
		'reward_id'                         => $cols['reward_id'],
	    ],
	    'type'    => 'quest'
	];
	foreach ( $questWebhookUrl as $url ) {
            sendToWebhook($url, array($questwebhook));
	}
    }

} elseif ( $action === "nest" ) {
    if ( $noManualNests === true || $noNests === true ) {
        http_response_code( 401 );
        die();
    }
    $pokemonId = ! empty( $_POST['pokemonId'] ) ? $_POST['pokemonId'] : '';
    $nestId    = ! empty( $_POST['nestId'] ) ? $_POST['nestId'] : '';
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( ! empty( $pokemonId ) && ! empty( $nestId ) ) {
        $cols  = [
            'pokemon_id' => $pokemonId,
            'nest_submitted_by' => $loggedUser
        ];
        $where = [
            'nest_id' => $nestId
        ];
        $db->update( "nests", $cols, $where );
    }
} elseif ( $action === "renamepokestop" ) {
    if ( $noRenamePokestops === true || $noPokestops === true ) {
        http_response_code( 401 );
        die();
    }
    $pokestopName = ! empty( $_POST['pokestop'] ) ? $_POST['pokestop'] : '';
    $pokestopId   = ! empty( $_POST['pokestopid'] ) ? $_POST['pokestopid'] : '';
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( ! empty( $pokestopName ) && ! empty( $pokestopId ) ) {
        $cols     = [
            'name'        => $pokestopName,
            'updated'     => time(),
            'edited_by'    => $loggedUser 
        ];
        $where    = [
            'external_id' => $pokestopId
        ];
	$db->update( "pokestops", $cols, $where );
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Updated pokestop with id "' . $pokestopId . '" and gave it the new name: "' . $pokestopName . '" . ```', "username" => $loggedUser);
            $curl = curl_init($discordSubmitLogChannelUrl);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
        }
    }
} elseif ( $action === "convertpokestop" ) {
    if ( $noConvertPokestops === true || $noPokestops === true ) {
        http_response_code( 401 );
        die();
    }
    $pokestopId   = ! empty( $_POST['pokestopid'] ) ? $_POST['pokestopid'] : '';
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    $gymId = randomGymId();
    $gymLat = $db->get( "pokestops", [ 'lat' ], [ 'external_id' => $pokestopId ] );
    $gymLon= $db->get( "pokestops", [ 'lon' ], [ 'external_id' => $pokestopId ] );
    $gymName = $db->get( "pokestops", [ 'name' ], [ 'external_id' => $pokestopId ] );
    $gymUrl = $db->get( "pokestops", [ 'url' ], [ 'external_id' => $pokestopId ] );
    if ( ! empty( $pokestopId ) ) {
        $cols     = [
            'external_id'  => $gymId,
            'lat'          => $gymLat['lat'],
            'lon'          => $gymLon['lon'],
            'name'         => $gymName['name'],
            'url'          => $gymUrl['url'],
            'edited_by'    => $loggedUser
        ];
	$db->insert( "forts", $cols );
        $db->delete( 'pokestops', [
            "AND" => [
                'external_id' => $pokestopId
            ]
        ] );
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Converted pokestop with id "' . $pokestopId . '." New Gym: "' . $gymName['name'] . '". ```', "username" => $loggedUser);
            $curl = curl_init($discordSubmitLogChannelUrl);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
        }
    }
 } elseif ( $action === "pokestop" ) {
    if ( $noManualPokestops === true || $noPokestops === true ) {
        http_response_code( 401 );
        die();
    }
    $pokestopName = ! empty( $_POST['pokestop'] ) ? $_POST['pokestop'] : '';
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( ! empty( $lat ) && ! empty( $lng ) && ! empty( $pokestopName ) ) {
        $pokestopId = randomGymId();
        $cols       = [
            'external_id' => $pokestopId,
            'lat'         => $lat,
            'lon'         => $lng,
            'name'        => $pokestopName,
            'updated'     => time(),
            'edited_by'    => $loggedUser 
        ];
        $db->insert( "pokestops", $cols );
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Added pokestop with id "' . $pokestopId . '" and gave it the new name: "' . $pokestopName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lng . '&zoom=18 ', "username" => $loggedUser);
            $curl = curl_init($discordSubmitLogChannelUrl);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
        }
    }
} elseif ( $action === "new-nest" ) {
    if ( $noAddNewNests === true || $noNests === true ) {
        http_response_code( 401 );
        die();
    }
    $id = ! empty( $_POST['id'] ) ? $_POST['id'] : 0;
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( ! empty( $lat ) && ! empty( $lng ) && ! empty( $id ) ) {
        $cols = [
            'pokemon_id' 	=> $id,
            'lat'        	=> $lat,
            'lon'        	=> $lng,
            'type'       	=> 0,
            'updated'    	=> time(),
            'nest_submitted_by'	=> $loggedUser
        ];
        $db->insert( "nests", $cols );
    }
} elseif ( $action === "delete-gym" ) {
    if ( $noDeleteGyms === true || $noGyms === true ) {
        http_response_code( 401 );
        die();
    }
    $gymId = ! empty( $_POST['id'] ) ? $_POST['id'] : '';
    if ( ! empty( $gymId ) ) {
	$fortName = $db->get( "forts", [ 'name' ], [ 'external_id' => $gymId ] );    
        $fortid = $db->get( "forts", [ 'id' ], [ 'external_id' => $gymId ] );
        $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
        if ( $fortid ) {
            $db->delete( 'fort_sightings', [
                "AND" => [
                    'fort_id' => $fortid['id']
                ]
            ] );
            $db->delete( 'raids', [
                "AND" => [
                    'fort_id' => $fortid['id']
                ]
            ] );
            $db->delete( 'forts', [
                "AND" => [
                    'external_id' => $gymId
                ]
            ] );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Deleted gym with id "' . $gymId . '" and name: "' . $fortName['name'] . '"```', "username" => $loggedUser);
                $curl = curl_init($discordSubmitLogChannelUrl);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
            }
        }
    }
} elseif ( $action === "delete-pokestop" ) {
    if ( $noDeletePokestops === true || $noDeletePokestops === true ) {
        http_response_code( 401 );
        die();
    }
    $pokestopId = ! empty( $_POST['id'] ) ? $_POST['id'] : '';
    $pokestopName = $db->get( "pokestops", [ 'name' ], [ 'external_id' => $pokestopId ] );
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( ! empty( $pokestopId ) ) {
        $db->delete( 'pokestops', [
            "AND" => [
                'external_id' => $pokestopId
            ]
        ] );
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Deleted pokestop with id "' . $pokestopId . '" and name: "' . $pokestopName['name'] . '"```', "username" => $loggedUser);
            $curl = curl_init($discordSubmitLogChannelUrl);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
        }
    }
} elseif ( $action === "delete-nest" ) {
    if ( $noManualNests === true || $noNests === true ) {
        http_response_code( 401 );
        die();
    }
    $nestId = ! empty( $_POST['nestId'] ) ? $_POST['nestId'] : '';
    if ( ! empty( $nestId ) ) {
        $db->delete( 'nests', [
            "AND" => [
                'nest_id' => $nestId
            ]
        ] );
    }
} elseif ( $action === "community-add" ) {
    if ( $noCommunity === true || $noAddNewCommunity === true ) {
	http_response_code( 401 );
	die();
    }
    $communityName = ! empty( $_POST['communityName'] ) ? $_POST['communityName'] : '';
    $communityDescription = ! empty( $_POST['communityDescription'] ) ? $_POST['communityDescription'] : '';
    $communityInvite = ! empty( $_POST['communityInvite'] ) ? $_POST['communityInvite'] : '';
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
    } else {
        http_response_code( 401 );
	die();
    }
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    if ( ! empty( $lat ) && ! empty( $lng ) && ! empty( $communityName ) && ! empty( $communityDescription ) && ! empty( $communityInvite ) ) {
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
            'lon'                 => $lng,
            'updated'             => time(),
            'source'              => 1,
            'submitted_by'        => $loggedUser 
        ];
        $db->insert( "communities", $cols );
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Added community with id "' . $communityId . '" and gave it the new name: "' . $communityName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lng . '&zoom=18 ', "username" => $loggedUser);
            $curl = curl_init($discordSubmitLogChannelUrl);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
        }
    }
} elseif ( $action === "editcommunity" ) {
    if ( $noCommunity === true || $noEditCommunity === true ) {
        http_response_code( 401 );
        die();
    }
    $communityName = ! empty( $_POST['communityname'] ) ? $_POST['communityname'] : '';
    $communityDescription = ! empty( $_POST['communitydescription'] ) ? $_POST['communitydescription'] : '';
    $communityInvite = ! empty( $_POST['communityinvite'] ) ? $_POST['communityinvite'] : '';
    $communityId   = ! empty( $_POST['communityid'] ) ? $_POST['communityid'] : '';
    if (strpos($communityInvite, 'https://discord.gg') !== false) {
	    $communityType = 3;
    } elseif (strpos($communityInvite, 'https://t.me') !== false) {
	    $communityType = 4;
    } elseif (strpos($communityInvite, 'https://chat.whatsapp.com') !== false) {
	    $communityType = 5;
    } else {
        http_response_code( 401 );
	die();
    }
    $loggedUser = ! empty( $_SESSION['user']->user ) ? $_SESSION['user']->user : 'NOLOGIN';
    $cols     = [
        'title'        => $communityName,
        'description'  => $communityDescription,
        'invite_url'   => $communityInvite,
        'type'         => $communityType,
        'updated'      => time(),
        'source'       => 1,
        'submitted_by' => $loggedUser 
    ];
    $where    = [
        'community_id' => $communityId
    ];
    $db->update( "communities", $cols, $where );
    if ( $noDiscordSubmitLogChannel === false ) {
        $data = array("content" => '```Updated community with id "' . $communityId . '" and gave it the new name: "' . $communityName . '" . ```', "username" => $loggedUser);
        $curl = curl_init($discordSubmitLogChannelUrl);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);
    }

} elseif ( $action === "delete-community" ) {
    if ( $noCommunity === true || $noDeleteCommunity === true ) {
        http_response_code( 401 );
        die();
    }
    $communityId = ! empty( $_POST['communityId'] ) ? $_POST['communityId'] : '';
    $communityName = $db->get( "communities", [ 'title' ], [ 'community_id' => $communityId ] );
    if ( ! empty( $communityId ) ) {
        $db->delete( 'communities', [
            "AND" => [
                'community_id' => $communityId
            ]
        ] );
    }
    if ( $noDiscordSubmitLogChannel === false ) {
        $data = array("content" => '```Deleted community with id "' . $communityId . '" and name: "' . $communityName['title'] . '" . ```', "username" => $loggedUser);
        $curl = curl_init($discordSubmitLogChannelUrl);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);
    }

}
function randomGymId() {
    $alphabet    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass        = array(); //remember to declare $pass as an array
    $alphaLength = strlen( $alphabet ) - 1; //put the length -1 in cache
    for ( $i = 0; $i < 12; $i ++ ) {
        $n      = rand( 0, $alphaLength );
        $pass[] = $alphabet[ $n ];
    }

    return implode( $pass ); //turn the array into a string
}

function randomNum() {
    $alphabet    = '1234567890';
    $pass        = array(); //remember to declare $pass as an array
    $alphaLength = strlen( $alphabet ) - 1; //put the length -1 in cache
    for ( $i = 0; $i < 15; $i ++ ) {
        $n      = rand( 0, $alphaLength );
        $pass[] = $alphabet[ $n ];
    }

    return implode( $pass ); //turn the array into a string
}

$jaysson = json_encode( $d );
echo $jaysson;
