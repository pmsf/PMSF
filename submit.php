<?php
$timing['start'] = microtime( true );
include( 'config/config.php' );
global $map, $fork, $db, $raidBosses, $webhookUrl, $sendWebhook, $noManualRaids, $noRaids, $noManualPokemon, $noPokemon, $noPokestops, $noManualPokestops, $noGyms, $noManualGyms, $noManualQuests, $noManualNests, $noNests, $noAddNewNests, $pokemonTimer, $noRenamePokestops;
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
        'move_2'      => 0

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
            'weather_boosted_condition' => 0
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
    if ( ! empty( $lat ) && ! empty( $lng ) && ! empty( $gymName ) ) {
        $gymId = randomGymId();
        $cols  = [
            'external_id' => $gymId,
            'lat'         => $lat,
            'lon'         => $lng,
            'name'        => $gymName
        ];
        $db->insert( "forts", $cols );
    }
} elseif ( $action === "quest" ) {
    if ( $noManualQuests === true || $noPokestops === true ) {
        http_response_code( 401 );
        die();
    }
    $pokestopId = ! empty( $_POST['pokestopId'] ) ? $_POST['pokestopId'] : '';
    $questId    = $_POST['questId'] == "NULL" ? 0 : $_POST['questId'];
    $reward     = $_POST['reward'] == "NULL" ? 0 : $_POST['reward'];
    if ( ! empty( $pokestopId ) && ! empty( $questId ) && ! empty( $reward ) ) {
        $cols  = [
            'quest_id' => $questId,
            'reward'   => $reward,
        ];
        $where = [
            'external_id' => $pokestopId
        ];
        $db->update( "pokestops", $cols, $where );
    }
} elseif ( $action === "nest" ) {
    if ( $noManualNests === true || $noNests === true ) {
        http_response_code( 401 );
        die();
    }
    $pokemonId = ! empty( $_POST['pokemonId'] ) ? $_POST['pokemonId'] : '';
    $nestId    = ! empty( $_POST['nestId'] ) ? $_POST['nestId'] : '';
    if ( ! empty( $pokemonId ) && ! empty( $nestId ) ) {
        $cols  = [
            'pokemon_id' => $pokemonId,
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
    if ( ! empty( $pokestopName ) && ! empty( $pokestopId ) ) {
        $cols     = [
            'name'        => $pokestopName,
            'updated'     => time()
        ];
        $where    = [
            'external_id' => $pokestopId
        ];
	$db->update( "pokestops", $cols, $where );
    }
	echo $pokestopName;
	echo $pokestopId;
 } elseif ( $action === "pokestop" ) {
    if ( $noManualPokestops === true || $noPokestops === true ) {
        http_response_code( 401 );
        die();
    }
    $pokestopName = ! empty( $_POST['pokestop'] ) ? $_POST['pokestop'] : '';
    if ( ! empty( $lat ) && ! empty( $lng ) && ! empty( $pokestopName ) ) {
        $pokestopId = randomGymId();
        $cols       = [
            'external_id' => $pokestopId,
            'lat'         => $lat,
            'lon'         => $lng,
            'name'        => $pokestopName,
            'updated'     => time()
        ];
        $db->insert( "pokestops", $cols );
    }
} elseif ( $action === "new-nest" ) {
    if ( $noAddNewNests === true || $noNests === true ) {
        http_response_code( 401 );
        die();
    }
    $id = ! empty( $_POST['id'] ) ? $_POST['id'] : 0;
    if ( ! empty( $lat ) && ! empty( $lng ) && ! empty( $id ) ) {
        $cols = [
            'pokemon_id' => $id,
            'lat'        => $lat,
            'lon'        => $lng,
            'type'       => 0,
            'updated'    => time()
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
        $fortid = $db->get( "forts", [ 'id' ], [ 'external_id' => $gymId ] );
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
        }
    }
} elseif ( $action === "delete-pokestop" ) {
    if ( $noDeletePokestops === true || $noDeletePokestops === true ) {
        http_response_code( 401 );
        die();
    }
    $pokestopId = ! empty( $_POST['id'] ) ? $_POST['id'] : '';
    if ( ! empty( $pokestopId ) ) {
        $db->delete( 'pokestops', [
            "AND" => [
                'external_id' => $pokestopId
            ]
        ] );
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
