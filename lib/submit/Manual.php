<?php

namespace Submit;

class Manual extends Submit
{
    public function mark_portal($portalId, $loggedUser)
    {
        global $manualdb, $noPortals, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noPortals === true) {
            http_response_code(401);
            die();
        }
        $portalName = $manualdb->get("ingress_portals", [ 'name' ], [ 'external_id' => $portalId ]);
        if (! empty($portalId)) {
            $cols     = [
                'updated'      => time(),
                'checked'      => 1
            ];
            $where    = [
                'external_id' => $portalId
            ];
            $manualdb->update("ingress_portals", $cols, $where);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Marked portal with id "' . $portalId . '." As no Pokestop or Gym. PortalName: "' . $portalName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function delete_portal($portalId, $loggedUser)
    {
        global $manualdb, $noPortals, $noDeletePortal, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noPortals === true || $noDeletePortal === true) {
            http_response_code(401);
            die();
        }
        $portalName = $manualdb->get("ingress_portals", [ 'name' ], [ 'external_id' => $portalId ]);
        if (! empty($portalId)) {
            $manualdb->delete('ingress_portals', [
                "AND" => [
                    'external_id' => $portalId
                ]
            ]);
        }
        if ($noDiscordSubmitLogChannel === false) {
            $data = array("content" => '```Deleted portal with id "' . $portalId . '" and name: "' . $portalName['name'] . '" . ```', "username" => $loggedUser);
            sendToWebhook($discordSubmitLogChannelUrl, ($data));
        }
    }

    public function modify_nest($nestId, $pokemonId, $loggedUser)
    {
        global $manualdb, $noManualNests, $noNests, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noManualNests === true || $noNests === true) {
            http_response_code(401);
            die();
        }
        if (! empty($pokemonId) && ! empty($nestId)) {
            $cols  = [
                'pokemon_id' => $pokemonId,
                'nest_submitted_by' => $loggedUser
            ];
            $where = [
                'nest_id' => $nestId
            ];
            $manualdb->update("nests", $cols, $where);
        }
    }

    public function submit_nest($lat, $lon, $pokemonId, $loggedUser)
    {
        global $manualdb, $noAddNewNests, $noNests, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noAddNewNests === true || $noNests === true) {
            http_response_code(401);
            die();
        }
        if (! empty($lat) && ! empty($lon) && ! empty($pokemonId)) {
            $cols = [
                'pokemon_id'     => $pokemonId,
                'lat'            => $lat,
                'lon'            => $lon,
                'type'           => 0,
                'updated'        => time(),
                'nest_submitted_by'    => $loggedUser
            ];
            $manualdb->insert("nests", $cols);
        }
    }

    public function delete_nest($nestId)
    {
        global $manualdb, $noDeleteNests, $noNests, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noDeleteNests === true || $noNests === true) {
            http_response_code(401);
            die();
        }
        if (! empty($nestId)) {
            $manualdb->delete('nests', [
                "AND" => [
                    'nest_id' => $nestId
                ]
            ]);
        }
    }

    public function submit_community($lat, $lon, $communityName, $communityDescription, $communityInvite, $loggedUser)
    {
        global $manualdb, $noCommunity, $noAddNewCommunity, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl, $submitMapUrl;
        if ($noCommunity === true || $noAddNewCommunity === true) {
            http_response_code(401);
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
        } elseif (strpos($communityInvite, 'https://silph.gg/t') !== false) {
            $communityType = 9;
        } else {
            http_response_code(401);
            die();
        }
        if (! empty($lat) && ! empty($lon) && ! empty($communityName) && ! empty($communityDescription) && ! empty($communityInvite)) {
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
            $manualdb->insert("communities", $cols);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Added community with id "' . $communityId . '" and gave it the new name: "' . $communityName . '"```' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18 ', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function modify_community($communityId, $communityName, $communityDescription, $communityInvite, $loggedUser)
    {
        global $manualdb, $noCommunity, $noEditCommunity, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noCommunity === true || $noEditCommunity === true) {
            http_response_code(401);
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
        } elseif (strpos($communityInvite, 'https://silph.gg/t') !== false) {
            $communityType = 9;
        } else {
            http_response_code(401);
            die();
        }
        if (! empty($communityId) && ! empty($communityName) && ! empty($communityDescription) && ! empty($communityInvite)) {
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
            $manualdb->update("communities", $cols, $where);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Updated community with id "' . $communityId . '" and gave it the new name: "' . $communityName . '" . ```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function delete_community($communityId, $loggedUser)
    {
        global $manualdb, $noCommunity, $noDeleteCommunity, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noCommunity === true || $noDeleteCommunity === true) {
            http_response_code(401);
            die();
        }
        if (! empty($communityId)) {
            $manualdb->delete('communities', [
                "AND" => [
                    'community_id' => $communityId
                ]
            ]);
        }
        if ($noDiscordSubmitLogChannel === false) {
            $data = array("content" => '```Deleted community with id "' . $communityId . '" and name: "' . $communityName['title'] . '" . ```', "username" => $loggedUser);
            sendToWebhook($discordSubmitLogChannelUrl, ($data));
        }
    }

    public function submit_poi($lat, $lon, $poiName, $poiDescription, $poiNotes, $poiImage, $poiSurrounding, $loggedUser)
    {
        global $manualdb, $noPoi, $noAddPoi, $noDiscordSubmitLogChannel, $discordPOISubmitLogChannelUrl, $submitMapUrl, $imgurCID;
        if ($noPoi === true || $noAddPoi === true) {
            http_response_code(401);
            die();
        }
        $poiImageUrl = null;
        $poiImageDeleteHash = null;
        $poiSurroundingUrl = null;
        $poiSurroundingDeleteHash = null;
        if (! empty($poiImage)) {
            $payload    = [
                'image'     => $poiImage,
                'name'      => 'POI-' . $poiName
            ];
            $response = uploadImage($imgurCID, $payload);
            $info = json_decode($response, true);
            $poiImageUrl = $info['data']['link'];
            $poiImageDeleteHash = $info['data']['deletehash'];
        };
        if (! empty($poiSurrounding)) {
            $payload    = [
                'image'     => $poiSurrounding,
                'name'      => 'Surrounding-' . $poiName
            ];
            $response = uploadImage($imgurCID, $payload);
            $info = json_decode($response, true);
            $poiSurroundingUrl = $info['data']['link'];
            $poiSurroundingDeleteHash = $info['data']['deletehash'];
        };
        if (! empty($lat) && ! empty($lon) && ! empty($poiName) && ! empty($poiDescription)) {
            $poiId = randomNum();
            $cols       = [
                'poi_id'                    => $poiId,
                'name'                      => $poiName,
                'description'               => $poiDescription,
        'notes'                     => $poiNotes,
        'poiimageurl'               => $poiImageUrl,
        'poiimagedeletehash'         => $poiImageDeleteHash,
        'poisurroundingurl'         => $poiSurroundingUrl,
        'poisurroundingdeletehash'   => $poiSurroundingDeleteHash,
                'lat'                       => $lat,
                'lon'                       => $lon,
                'status'                    => 1,
                'updated'                   => time(),
                'submitted_by'              => $loggedUser
            ];
            $manualdb->insert("poi", $cols);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array(
            "username" => $loggedUser,
            "embeds" => array(array(
                "color" => 65280,
                "image" => array(
                    "url" => $poiImageUrl
                ),
                "thumbnail" => array(
                    "url" => $poiSurroundingUrl
                ),
                "fields" => array(
                    array(
                        "name" => 'Manual Action:',
                        "value" => 'Submit POI'
                    ),
                    array(
                        "name" => 'POI Title:',
                        "value" => $poiName
                    ),
                    array(
                        "name" => 'POI Description:',
                        "value" => $poiDescription
                    ),
                    array(
                        "name" => 'POI ID:',
                        "value" => $poiId
                    ),
                    array(
                        "name" => 'Map link',
                        "value" => '[View POI on Map](' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18)'
                    )
                )
            ))
        );
                sendToWebhook($discordPOISubmitLogChannelUrl, ($data));
            }
        }
    }
  
    public function modify_poi($poiId, $poiName, $poiDescription, $poiNotes, $poiImage, $poiSurrounding, $loggedUser)
    {
        global $manualdb, $noPoi, $noEditPoi, $noDiscordSubmitLogChannel, $discordPOISubmitLogChannelUrl, $submitMapUrl, $imgurCID;
        if ($noPoi === true || $noEditPoi === true) {
            http_response_code(401);
            die();
        }
        $CPoi = $manualdb->get("poi", [ "poiimageurl", "poiimagedeletehash", "poisurroundingurl", "poisurroundingdeletehash" ], [ 'poi_id' => $poiId ]);
        if (! empty($poiImage)) {
            if (! empty($Cpoi['poiimagedeletehash'])) {
                deleteImage($imgurCID, $Cpoi['poiimagedeletehash']);
            };
            $payload    = [
                'image'     => $poiImage,
                'name'      => 'POI-' . $poiName
            ];
            $response = uploadImage($imgurCID, $payload);
            $info = json_decode($response, true);
            $poiImageUrl = $info['data']['link'];
            $poiImageDeleteHash = $info['data']['deletehash'];
        };
        if (! empty($poiSurrounding)) {
            if (! empty($Cpoi['poisurroundingdeletehash'])) {
                deleteImage($imgurCID, $Cpoi['poisurroundingdeletehash']);
            };
            $payload    = [
                'image'     => $poiSurrounding,
                'name'      => 'Surrounding-' . $poiName
            ];
            $response = uploadImage($imgurCID, $payload);
            $info = json_decode($response, true);
            $poiSurroundingUrl = $info['data']['link'];
            $poiSurroundingDeleteHash = $info['data']['deletehash'];
        };
        $poiImageUrl			= ! empty($poiImageUrl) ? $poiImageUrl : $Cpoi['poiimageurl'];
        $poiImageDeleteHash		= ! empty($poiImageDeleteHash) ? $poiImageDeleteHash : $Cpoi['poiimagedeletehash'];
        $poiSurroundingUrl		= ! empty($poiSurroundingUrl) ? $poiSurroundingUrl : $Cpoi['poisurroundingurl'];
        $poiSurroundingDeleteHash	= ! empty($poiSurroundingDeleteHash) ? $poiSurroundingDeleteHash : $Cpoi['poisurroundingdeletehash'];
        if (! empty($poiId) && ! empty($poiName) && ! empty($poiDescription)) {
            $cols       = [
                'name'                     => $poiName,
                'description'              => $poiDescription,
                'notes'                    => $poiNotes,
                'poiimageurl'              => $poiImageUrl,
                'poiimagedeletehash'       => $poiImageDeleteHash,
                'poisurroundingurl'        => $poiSurroundingUrl,
                'poisurroundingdeletehash' => $poiSurroundingDeleteHash,
                'updated'                  => time(),
                'edited_by'                => $loggedUser
            ];
            $where    = [
                    'poi_id' => $poiId
            ];
            $manualdb->update("poi", $cols, $where);
            ;
            if ($noDiscordSubmitLogChannel === false) {
                $data = array(
            "username" => $loggedUser,
            "embeds" => array(array(
                "color" => 15105570,
                "image" => array(
                    "url" => $poiImageUrl
                ),
                "thumbnail" => array(
                    "url" => $poiSurroundingUrl
                ),
                "fields" => array(
                    array(
                        "name" => 'Manual Action:',
                        "value" => 'Edit POI'
                    ),
                    array(
                        "name" => 'POI Title:',
                        "value" => $poiName
                    ),
                    array(
                        "name" => 'POI Description:',
                        "value" => $poiDescription
                    ),
                    array(
                        "name" => 'POI ID:',
                        "value" => $poiId
                    ),
                    array(
                        "name" => 'Map link',
                        "value" => '[View POI on Map](' . $submitMapUrl . '/?lat=' . $lat . '&lon=' . $lon . '&zoom=18)'
                    )
                )
            ))
        );
                sendToWebhook($discordPOISubmitLogChannelUrl, ($data));
            }
        }
    }

    public function delete_poi($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDeletePoi, $noDiscordSubmitLogChannel, $discordPOISubmitLogChannelUrl, $submitMapUrl, $imgurCID;
        if ($noPoi === true || $noDeletePoi === true) {
            http_response_code(401);
            die();
        }
        $poi = $manualdb->get("poi", [ "poiimagedeletehash", "poisurroundingdeletehash", "poiimageurl", "poisurroundingurl", "name", "description", "notes", "lat", "lon", "submitted_by" ], [ 'poi_id' => $poiId ]);
        if (! empty($poiId)) {
            $manualdb->delete('poi', [
                "AND" => [
                    'poi_id' => $poiId
                ]
            ]);
            if (! empty($poi['poiimagedeletehash'])) {
                deleteImage($imgurCID, $poi['poiimagedeletehash']);
            };
            if (! empty($poi['poisurroundingdeletehash'])) {
                deleteImage($imgurCID, $poi['surroundingdeletehash']);
            };
        }
        if ($noDiscordSubmitLogChannel === false) {
            $data = array(
        "username" => $loggedUser,
        "embeds" => array(array(
            "color" => 15158332,
            "fields" => array(
                array(
                    "name" => 'Manual Action:',
                    "value" => 'Delete POI'
                ),
                array(
                    "name" => 'POI Title:',
                    "value" => $poi['name']
                ),
                array(
                    "name" => 'POI Description:',
                    "value" => $poi['description']
                ),
                array(
                    "name" => 'POI ID:',
                    "value" => $poiId
                ),
                array(
                    "name" => 'Map link',
                    "value" => '[View POI on Map](' . $submitMapUrl . '/?lat=' . $poi['lat'] . '&lon=' . $poi['lon'] . '&zoom=18)'
                )
            )
        ))
            );
            sendToWebhook($discordPOISubmitLogChannelUrl, ($data));
        }
    }

    public function mark_poi_submitted($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDiscordSubmitLogChannel, $discordPOISubmitLogChannelUrl;
        if ($noPoi === true) {
            http_response_code(401);
            die();
        }
        $poiName = $manualdb->get("poi", [ 'name' ], [ 'poi_id' => $poiId ]);
        if (! empty($poiId)) {
            $cols     = [
                'updated'      => time(),
                'status'       => 2
            ];
            $where    = [
                'poi_id' => $poiId
            ];
            $manualdb->update("poi", $cols, $where);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Marked poi with id "' . $poiId . '." As submitted. PoiName: "' . $poiName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordPOISubmitLogChannelUrl, ($data));
            }
        }
    }

    public function mark_poi_declined($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDiscordSubmitLogChannel, $discordPOISubmitLogChannelUrl;
        if ($noPoi === true) {
            http_response_code(401);
            die();
        }
        $poiName = $manualdb->get("poi", [ 'name' ], [ 'poi_id' => $poiId ]);
        if (! empty($poiId)) {
            $cols     = [
                'updated'      => time(),
                'status'       => 3
            ];
            $where    = [
                'poi_id' => $poiId
            ];
            $manualdb->update("poi", $cols, $where);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Marked poi with id "' . $poiId . '." As declined. PoiName: "' . $poiName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordPOISubmitLogChannelUrl, ($data));
            }
        }
    }

    public function mark_poi_resubmit($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDiscordSubmitLogChannel, $discordPOISubmitLogChannelUrl;
        if ($noPoi === true) {
            http_response_code(401);
            die();
        }
        $poiName = $manualdb->get("poi", [ 'name' ], [ 'poi_id' => $poiId ]);
        if (! empty($poiId)) {
            $cols     = [
                'updated'      => time(),
                'status'       => 4
            ];
            $where    = [
                'poi_id' => $poiId
            ];
            $manualdb->update("poi", $cols, $where);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Marked poi with id "' . $poiId . '." As declined but eligible to be resubmitted. PoiName: "' . $poiName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordPOISubmitLogChannelUrl, ($data));
            }
        }
    }

    public function mark_not_candidate($poiId, $loggedUser)
    {
        global $manualdb, $noPoi, $noDiscordSubmitLogChannel, $discordPOISubmitLogChannelUrl;
        if ($noPoi === true) {
            http_response_code(401);
            die();
        }
        $poiName = $manualdb->get("poi", [ 'name' ], [ 'poi_id' => $poiId ]);
        if (! empty($poiId)) {
            $cols     = [
                'updated'      => time(),
                'status'       => 5
            ];
            $where    = [
                'poi_id' => $poiId
            ];
            $manualdb->update("poi", $cols, $where);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Marked poi with id "' . $poiId . '." As non eligible candidate. PoiName: "' . $poiName['name'] . '". ```', "username" => $loggedUser);
                sendToWebhook($discordPOISubmitLogChannelUrl, ($data));
            }
        }
    }
}
