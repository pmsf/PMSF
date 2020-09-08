<?php

namespace Submit;

class RocketMap_MAD extends Submit
{
    public function delete_gym($gymId, $loggedUser)
    {
        global $db, $noDeleteGyms, $noGyms, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noDeleteGyms === true || $noGyms === true) {
            http_response_code(401);
            die();
        }
        if (! empty($gymId)) {
            $fortName = $db->get("gymdetails", [ 'name' ], [ 'gym_id' => $gymId ]);
            $db->delete('raid', [
                "AND" => [
                    'gym_id' => $gymId
                ]
            ]);
            $db->delete('gymdetails', [
                "AND" => [
                    'gym_id' => $gymId
                ]
            ]);
            $db->delete('gym', [
                "AND" => [
                    'gym_id' => $gymId
                ]
            ]);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Deleted gym with id "' . $gymId . '" and name: "' . $fortName['name'] . '"```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }

    public function delete_pokestop($pokestopId, $loggedUser)
    {
        global $db, $noDeletePokestops, $noPokestops, $noDiscordSubmitLogChannel, $discordSubmitLogChannelUrl;
        if ($noDeletePokestops === true || $noPokestops === true) {
            http_response_code(401);
            die();
        }
        $pokestopName = $db->get("pokestop", [ 'name' ], [ 'pokestop_id' => $pokestopId ]);
        if (! empty($pokestopId)) {
            $db->delete('trs_quest', [
                "AND" => [
                    'GUID' => $pokestopId
                ]
            ]);
            $db->delete('pokestop', [
                "AND" => [
                    'pokestop_id' => $pokestopId
                ]
            ]);
            if ($noDiscordSubmitLogChannel === false) {
                $data = array("content" => '```Deleted pokestop with id "' . $pokestopId . '" and name: "' . $pokestopName['name'] . '"```', "username" => $loggedUser);
                sendToWebhook($discordSubmitLogChannelUrl, ($data));
            }
        }
    }
}
