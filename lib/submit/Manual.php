<?php

namespace Submit;

class Manual extends Submit
{
    public function mark_portal($portalId, $loggedUser)
    {
        global $manualdb, $noPortals, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
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
        global $manualdb, $noPortals, $noDeletePortal, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
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
            $data = array("content" => '```Deleted portal with id "' . $portalId . '" and name: "' . $portalName['name'] . '" . ```', "username" => $loggedUser);
            sendToWebhook($discordSubmitLogChannelUrl, ($data));
        }
    }

    public function modify_nest($nestId, $pokemonId, $loggedUser)
    {
        global $manualdb, $noManualNests, $noNests, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
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
        global $manualdb, $noAddNewNests, $noNests, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noAddNewNests === true || $noNests === true ) {
            http_response_code( 401 );
            die();
        }
        if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $pokemonId ) ) {
            $cols = [
                'pokemon_id'     => $pokemonId,
                'lat'            => $lat,
                'lon'            => $lon,
                'type'           => 0,
                'updated'        => time(),
                'nest_submitted_by'    => $loggedUser
            ];
            $manualdb->insert( "nests", $cols );
        }
    }

    public function delete_nest($nestId)
    {
        global $manualdb, $noDeleteNests, $noNests, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
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
        global $manualdb, $noCommunity, $noAddNewCommunity, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
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
        } else if (strpos($communityInvite, 'https://silph.gg/t') !== false) {
            $communityType = 9;
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
                $data = array("content" => '```Added community with id "' . $communityId . '" and gave it the new name: "' . $communityName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function modify_community($communityId, $communityName, $communityDescription, $communityInvite, $loggedUser)
    {
        global $manualdb, $noCommunity, $noEditCommunity, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
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
        } else if (strpos($communityInvite, 'https://silph.gg/t') !== false) {
            $communityType = 9;
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
        global $manualdb, $noCommunity, $noDeleteCommunity, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
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

    public function submit_poi($lat, $lon, $poiName, $poiDescription, $poiNotes, $loggedUser)
    {
        global $manualdb, $noPoi, $noAddPoi, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ( $noPoi === true || $noAddPoi === true ) {
            http_response_code( 401 );
            die();
        }
        if ( ! empty( $lat ) && ! empty( $lon ) && ! empty( $poiName ) && ! empty( $poiDescription ) ) {
            $poiId = randomNum();
            $cols       = [
                'poi_id'              => $poiId,
                'name'                => $poiName,
                'description'         => $poiDescription,
                'notes'               => $poiNotes,
                'lat'                 => $lat,
                'lon'                 => $lon,
                'status'              => 1,
                'updated'             => time(),
                'submitted_by'        => $loggedUser 
            ];
            $manualdb->insert( "poi", $cols );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Added poi with id "' . $poiId . '" and gave it the new name: "' . $poiName . '".\nDescription: "' . $poiDescription . '".```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }
  
  	public function modify_poi($poiId, $poiName, $poiDescription, $poiNotes, $loggedUser)
		{
		    global $manualdb, $noPoi, $noEditPoi, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
			  if ( $noPoi === true || $noEditPoi === true ) {
				    http_response_code( 401 );
				    die();
			  }
			  if ( ! empty( $poiId ) && ! empty( $poiName ) && ! empty( $poiDescription ) ) {
				    $cols       = [
					      'name'                => $poiName,
					      'description'         => $poiDescription,
					      'notes'               => $poiNotes,
					      'status'	            => 1,
					      'updated'             => time(),
					      'edited_by'           => $loggedUser 
				    ];
				    $where    = [
					      'poi_id' => $poiId
				    ];
				    $manualdb->update( "poi", $cols, $where );
				    if ( $noDiscordSubmitLogChannel === false ) {
					      $data = array("content" => '```Updated poi with id "' . $poiId . '" and gave it the new name: "' . $poiName . '".\nDescription: "' . $poiDescription . '".```' . $submitMapUrl . '&zoom=18 ', "username" => $loggedUser);
					      sendToWebhook($discordSubmitLogChannelUrl, ($data));
				    }
			  }
		}

    public function delete_poi($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDeletePoi, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noPoi === true || $noDeletePoi === true) {
            http_response_code( 401 );
            die();
        }
        $poiName = $manualdb->get( "poi", [ 'name' ], [ 'poi_id' => $poiId ] );
        if ( ! empty( $poiId ) ) {
            $manualdb->delete( 'poi', [
                "AND" => [
                    'poi_id' => $poiId
                ]
            ] );
        }
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Deleted POI with id "' . $poiId . '" and name: "' . $poiName['name'] . '" . ```', "username" => $loggedUser);
            sendToWebhook($discordSubmitLogChannelUrl, ($data));
        }
    }

    public function mark_poi_submitted($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noPoi === true ) {
            http_response_code( 401 );
            die();
        }
        $poiName = $manualdb->get( "poi", [ 'name' ], [ 'poi_id' => $poiId ] );
        if ( ! empty( $poiId ) ) {
            $cols     = [
                'updated'      => time(),
                'status'       => 2
            ];
            $where    = [
                'poi_id' => $poiId
            ];
            $manualdb->update( "poi", $cols, $where );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Marked poi with id "' . $poiId . '." As submitted. PoiName: "' . $poiName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function mark_poi_declined($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noPoi === true ) {
            http_response_code( 401 );
            die();
        }
        $poiName = $manualdb->get( "poi", [ 'name' ], [ 'poi_id' => $poiId ] );
        if ( ! empty( $poiId ) ) {
            $cols     = [
                'updated'      => time(),
                'status'       => 3
            ];
            $where    = [
                'poi_id' => $poiId
            ];
            $manualdb->update( "poi", $cols, $where );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Marked poi with id "' . $poiId . '." As declined. PoiName: "' . $poiName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function mark_poi_resubmit($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noPoi === true ) {
            http_response_code( 401 );
            die();
        }
        $poiName = $manualdb->get( "poi", [ 'name' ], [ 'poi_id' => $poiId ] );
        if ( ! empty( $poiId ) ) {
            $cols     = [
                'updated'      => time(),
                'status'       => 4
            ];
            $where    = [
                'poi_id' => $poiId
            ];
            $manualdb->update( "poi", $cols, $where );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Marked poi with id "' . $poiId . '." As declined but eligible to be resubmitted. PoiName: "' . $poiName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function mark_not_candidate($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noPoi === true ) {
            http_response_code( 401 );
            die();
        }
        $poiName = $manualdb->get( "poi", [ 'name' ], [ 'poi_id' => $poiId ] );
        if ( ! empty( $poiId ) ) {
            $cols     = [
                'updated'      => time(),
                'status'       => 5
            ];
            $where    = [
                'poi_id' => $poiId
            ];
            $manualdb->update( "poi", $cols, $where );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Marked poi with id "' . $poiId . '." As non eligible candidate. PoiName: "' . $poiName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function convert_portal_inn($portalId, $loggedUser)
    {
        global $db, $manualdb, $noPortals, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ( $noPortals === true ) {
            http_response_code( 401 );
            die();
        }
        $portal = $manualdb->get( "ingress_portals", [ 'lat', 'lon', 'name', 'url' ], [ 'external_id' => $portalId ] );
        if ( ! empty( $portalId ) ) {
            $cols     = [
                'id'           => $portalId,
                'lat'          => $portal['lat'],
                'lon'          => $portal['lon'],
                'name'         => $portal['name'],
                'url'          => $portal['url'],
                'updated'      => time(),
                'submitted_by' => $loggedUser
            ];
            $manualdb->insert( "inn", $cols );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Converted portal with id "' . $portalId . '." New Inn: "' . $portal['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portal['lat'] . '&lon=' . $portal['lon'] . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function convert_portal_fortress($portalId, $loggedUser)
    {
        global $db, $manualdb, $noPortals, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ( $noPortals === true ) {
            http_response_code( 401 );
            die();
        }
        $portal = $manualdb->get( "ingress_portals", [ 'lat', 'lon', 'name', 'url' ], [ 'external_id' => $portalId ] );
        if ( ! empty( $portalId ) ) {
            $cols     = [
                'id'           => $portalId,
                'lat'          => $portal['lat'],
                'lon'          => $portal['lon'],
                'name'         => $portal['name'],
                'url'          => $portal['url'],
                'updated'      => time(),
                'submitted_by' => $loggedUser
            ];
            $manualdb->insert( "fortress", $cols );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Converted portal with id "' . $portalId . '." New Fortress: "' . $portal['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portal['lat'] . '&lon=' . $portal['lon'] . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function convert_portal_greenhouse($portalId, $loggedUser)
    {
        global $db, $manualdb, $noPortals, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ( $noPortals === true ) {
            http_response_code( 401 );
            die();
        }
        $portal = $manualdb->get( "ingress_portals", [ 'lat', 'lon', 'name', 'url' ], [ 'external_id' => $portalId ] );
        if ( ! empty( $portalId ) ) {
            $cols     = [
                'id'           => $portalId,
                'lat'          => $portal['lat'],
                'lon'          => $portal['lon'],
                'name'         => $portal['name'],
                'url'          => $portal['url'],
                'updated'      => time(),
                'submitted_by' => $loggedUser
            ];
            $manualdb->insert( "greenhouse", $cols );
            if ( $noDiscordSubmitLogChannel === false ) {
                $data = array("content" => '```Converted portal with id "' . $portalId . '." New Greenhouse: "' . $portal['name'] . '". ```' . $submitMapUrl . '/?lat=' . $portal['lat'] . '&lon=' . $portal['lon'] . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function delete_inn($innId, $loggedUser)
    {
        global $manualdb, $noInn, $noDeleteInn, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noInn === true || $noDeleteInn === true) {
            http_response_code( 401 );
            die();
        }
        $innName = $manualdb->get( "inn", [ 'name' ], [ 'id' => $innId ] );
        if ( ! empty( $innId ) ) {
            $manualdb->delete( 'inn', [
                "AND" => [
                    'id' => $innId
                ]
            ] );
        }
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Deleted inn with id "' . $innId . '" and name: "' . $innName['name'] . '" . ```', "username" => $loggedUser);
            sendToWebhook($discordSubmitLogChannelUrl, ($data));
        }
    }

    public function delete_fortress($fortressId, $loggedUser)
    {
        global $manualdb, $noFortress, $noDeleteFortress, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noFortress === true || $noDeleteFortress === true) {
            http_response_code( 401 );
            die();
        }
        $fortressName = $manualdb->get( "fortress", [ 'name' ], [ 'id' => $fortressId ] );
        if ( ! empty( $fortressId ) ) {
            $manualdb->delete( 'fortress', [
                "AND" => [
                    'id' => $fortressId
                ]
            ] );
        }
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Deleted fortress with id "' . $fortressId . '" and name: "' . $fortressName['name'] . '" . ```', "username" => $loggedUser);
            sendToWebhook($discordSubmitLogChannelUrl, ($data));
        }
    }

    public function delete_greenhouse($greenhouseId, $loggedUser)
    {
        global $manualdb, $noGreenhouse, $noDeleteGreenhouse, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ( $noGreenhouse === true || $noDeleteGreenhouse === true) {
            http_response_code( 401 );
            die();
        }
        $innName = $manualdb->get( "greenhouse", [ 'name' ], [ 'id' => $greenhouseId ] );
        if ( ! empty( $greenhouseId ) ) {
            $manualdb->delete( 'greenhouse', [
                "AND" => [
                    'id' => $greenhouseId
                ]
            ] );
        }
        if ( $noDiscordSubmitLogChannel === false ) {
            $data = array("content" => '```Deleted greenhouse with id "' . $greenhouseId . '" and name: "' . $greenhouseName['name'] . '" . ```', "username" => $loggedUser);
            sendToWebhook($discordSubmitLogChannelUrl, ($data));
        }
    }
}