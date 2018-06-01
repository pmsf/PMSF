<?php

namespace NovaBot;

class NovaBot
{
	function addLobbies(&$gyms) {
		global $novabotDb;

		$query = "SELECT rl.gym_id, rl.lobby_id, rlm.count as count
					FROM raidlobby rl
					JOIN (
						SELECT SUM(count) AS count, lobby_id
					    FROM raidlobby_members
					    GROUP BY lobby_id
					) AS rlm ON rlm.lobby_id = rl.lobby_id";

		$lobbies = $novabotDb->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($gyms as &$gym) {
			$key = array_search($gym["gym_id"], array_column($lobbies, 'gym_id'));
			if (is_int($key)) {
				$gym["lobby_count"] = $lobbies[$key]["count"];
				$gym["lobby_id"] = $lobbies[$key]["lobby_id"];
			} else {
				$gym["lobby_count"] = 0;
				$gym["lobby_id"] = null;
			}
		}
	}

	function getLobbyInfo($lobbyId) {
		global $novabotDb;

		$query = "SELECT time, SUM(count) AS count
					FROM raidlobby_members
					WHERE lobby_id = ".$lobbyId."
					GROUP BY time";

		return $novabotDb->query($query)->fetchAll(\PDO::FETCH_ASSOC);
	}
}