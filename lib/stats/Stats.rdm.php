<?php

namespace Stats;

class RDM extends Stats
{
    public function get_overview_stats()
    {
      global $db, $noBoundaries, $boundaries;

      $geofenceSQL = '';
      $pokemonGeofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " WHERE (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
          $pokemonGeofenceSQL = " AND (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $pokemon = $db->query("
        SELECT COUNT(*) AS pokemon_count
        FROM pokemon
        WHERE expire_timestamp > UNIX_TIMESTAMP()
        $pokemonGeofenceSQL"
      )->fetch();
      
      $gym = $db->query("
        SELECT
          COUNT(*) AS gym_count,
          SUM(raid_end_timestamp > UNIX_TIMESTAMP()) AS raid_count
        FROM gym
        $geofenceSQL"
      )->fetch();
      
      $pokestop = $db->query("
        SELECT COUNT(*) AS pokestop_count
        FROM pokestop
        $geofenceSQL"
      )->fetch();

      $data = array();
    
      $overview['pokemon_count'] = $pokemon['pokemon_count'];
      $overview['gym_count'] = $gym['gym_count'];
      $overview['raid_count'] = $gym['raid_count'];
      $overview['pokestop_count'] = $pokestop['pokestop_count'];

      $data[] = $overview;
      return $data;
    }

    public function get_team_stats()
    {
      global $db, $noBoundaries, $boundaries;

      $geofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " WHERE (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $teams = $db->query("
        SELECT 
          SUM(team_id = 0) AS neutral_count,
          SUM(team_id = 1) AS mystic_count,
          SUM(team_id = 2) AS valor_count,
          SUM(team_id = 3) AS instinct_count
        FROM gym
        $geofenceSQL"
      )->fetch();

      $data = array();

      $team['neutral_count'] = $teams['neutral_count'];
      $team['mystic_count'] = $teams['mystic_count'];
      $team['valor_count'] = $teams['valor_count'];
      $team['instinct_count'] = $teams['instinct_count'];

      $data[] = $team;
      return $data;
    }

    public function get_pokestop_stats()
    {
      global $db, $noBoundaries, $boundaries, $noQuestsARTaskToggle;

      $rdmGrunts = ($this->columnExists("incident","pokestop_id")) ? " LEFT JOIN (SELECT `pokestop_id` AS pokestop_id_incident, MIN(`character`) AS grunt_type, `expiration` AS incident_expire_timestamp FROM incident WHERE `expiration` > UNIX_TIMESTAMP() GROUP BY `pokestop_id_incident`) AS i ON i.`pokestop_id_incident` = p.`id` " : "";
      $alternative_quests = ($noQuestsARTaskToggle) ? "" : " SUM(alternative_quest_type IS NOT NULL) AS alternative_quest, ";

      $geofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " WHERE (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $pokestops = $db->query("
        SELECT
          SUM(quest_type IS NOT NULL) AS quest,
          $alternative_quests
          SUM(incident_expire_timestamp > UNIX_TIMESTAMP()) AS rocket,
          SUM(lure_expire_timestamp > UNIX_TIMESTAMP() AND lure_id = 501) AS normal_lure,
          SUM(lure_expire_timestamp > UNIX_TIMESTAMP() AND lure_id = 502) AS glacial_lure,
          SUM(lure_expire_timestamp > UNIX_TIMESTAMP() AND lure_id = 503) AS mossy_lure,
          SUM(lure_expire_timestamp > UNIX_TIMESTAMP() AND lure_id = 504) AS magnetic_lure,
          SUM(lure_expire_timestamp > UNIX_TIMESTAMP() AND lure_id = 505) AS rainy_lure
        FROM pokestop p
        $rdmGrunts
        $geofenceSQL"
      )->fetch();

      $data = array();

      $pokestop['quest'] = ($noQuestsARTaskToggle) ? $pokestops['quest'] : ($pokestops['quest'] + $pokestops['alternative_quest']);
      $pokestop['rocket'] = $pokestops['rocket'];
      $pokestop['normal_lure'] = $pokestops['normal_lure'];
      $pokestop['glacial_lure'] = $pokestops['glacial_lure'];
      $pokestop['mossy_lure'] = $pokestops['mossy_lure'];
      $pokestop['magnetic_lure'] = $pokestops['magnetic_lure'];
      $pokestop['rainy_lure'] = $pokestops['rainy_lure'];

      $data[] = $pokestop;
      return $data;
    }

    public function get_spawnpoint_stats()
    {
      global $db, $noBoundaries, $boundaries;

      $geofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " WHERE (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $spawnpoints = $db->query("
        SELECT
          COUNT(*) AS total,
          SUM(despawn_sec IS NOT NULL) AS found,
          SUM(despawn_sec IS NULL) AS missing
        FROM spawnpoint
        $geofenceSQL"
      )->fetch();

      $data = array();

      $spawnpoint['total'] = $spawnpoints['total'];
      $spawnpoint['found'] = $spawnpoints['found'];
      $spawnpoint['missing'] = $spawnpoints['missing'];

      $data[] = $spawnpoint;
      return $data;
    }

    public function get_pokemon_stats()
    {
      global $db, $noBoundaries, $boundaries;

      $geofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " AND (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $mons = $db->query("
        SELECT
          pokemon_id,
          form,
          costume,
          count(*) AS count
        FROM pokemon
        WHERE expire_timestamp > UNIX_TIMESTAMP() $geofenceSQL
        GROUP BY pokemon_id, form, costume"
      );
      $total = $db->query("SELECT COUNT(*) AS total FROM pokemon WHERE expire_timestamp > UNIX_TIMESTAMP() $geofenceSQL")->fetch();

      $data = array();
      foreach ($mons as $mon) {
        $pokemon["name"] = i8ln($this->data[$mon['pokemon_id']]["name"]);
        $pokemon["pokemon_id"] = intval($mon["pokemon_id"]);
        $pokemon["form"] = intval($mon["form"]);
        $pokemon["costume"] = intval($mon["costume"]);
        $pokemon["count"] = $mon["count"];
        $pokemon["percentage"] = round(100 / $total["total"] * $mon["count"], 3) . '%';
        if (isset($mon["form"]) && $mon["form"] > 0) {
            $forms = $this->data[$mon["pokemon_id"]]["forms"];
              foreach ($forms as $f => $v) {
                if ($mon["form"] === $v['protoform']) {
                    $types = $v['formtypes'];
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
      return $data;
    }

    public function get_reward_stats()
    {
      global $db, $noBoundaries, $boundaries, $noQuestsARTaskToggle;

      $geofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " AND (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      if ($noQuestsARTaskToggle) {
          $rewards = $db->query("
            SELECT
              COUNT(*) as count,
              quest_item_id,
              quest_pokemon_id,
              quest_pokemon_id AS quest_energy_pokemon_id,
              json_extract(json_extract(`quest_rewards`,'$[*].info.form_id'),'$[0]') AS quest_pokemon_form,
              json_extract(json_extract(`quest_rewards`,'$[*].info.costume_id'),'$[0]') AS quest_pokemon_costume,
              quest_reward_amount AS quest_reward_amount,
              quest_reward_type
            FROM pokestop
            WHERE quest_reward_type IS NOT NULL $geofenceSQL
            GROUP BY quest_reward_type, quest_item_id, quest_reward_amount, quest_pokemon_id, quest_pokemon_form, quest_pokemon_costume
            ORDER BY quest_reward_type, quest_item_id, CAST(quest_reward_amount AS UNSIGNED), quest_pokemon_id, quest_pokemon_form, quest_pokemon_costume;");
            $total = $db->query("SELECT COUNT(quest_reward_type) AS total FROM pokestop WHERE quest_reward_type IS NOT NULL $geofenceSQL")->fetch();
      } else {
          $rewards = $db->query("
          SELECT COUNT(*) as count, quest_item_id, quest_pokemon_id,quest_energy_pokemon_id, quest_pokemon_form, quest_pokemon_costume, quest_reward_amount, quest_reward_type
          FROM
          (
            SELECT
              quest_item_id,
              quest_pokemon_id,
              quest_pokemon_id AS quest_energy_pokemon_id,
              json_extract(json_extract(`quest_rewards`,'$[*].info.form_id'),'$[0]') AS quest_pokemon_form,
              json_extract(json_extract(`quest_rewards`,'$[*].info.costume_id'),'$[0]') AS quest_pokemon_costume,
              quest_reward_amount AS quest_reward_amount,
              quest_reward_type
            FROM pokestop
            WHERE quest_reward_type IS NOT NULL $geofenceSQL
          UNION ALL
            SELECT
              alternative_quest_item_id AS quest_item_id,
              alternative_quest_pokemon_id AS quest_pokemon_id,
              alternative_quest_pokemon_id AS quest_energy_pokemon_id,
              json_extract(json_extract(`alternative_quest_rewards`,'$[*].info.form_id'),'$[0]') AS quest_pokemon_form,
              json_extract(json_extract(`alternative_quest_rewards`,'$[*].info.costume_id'),'$[0]') AS quest_pokemon_costume,
              alternative_quest_reward_amount AS quest_reward_amount,
              alternative_quest_reward_type AS quest_reward_type
            FROM pokestop
            WHERE alternative_quest_reward_type IS NOT NULL $geofenceSQL
          ) combined
          GROUP BY quest_reward_type, quest_item_id, quest_reward_amount, quest_pokemon_id, quest_pokemon_form, quest_pokemon_costume
          ORDER BY quest_reward_type, quest_item_id, CAST(quest_reward_amount AS UNSIGNED), quest_pokemon_id, quest_pokemon_form, quest_pokemon_costume;");
          $total = $db->query("SELECT (COUNT(quest_reward_type)+COUNT(alternative_quest_reward_type)) AS total FROM pokestop WHERE quest_reward_type IS NOT NULL OR alternative_quest_reward_type IS NOT NULL $geofenceSQL")->fetch();
      }

      $data = array();
      foreach ($rewards as $reward) {
        $questReward["quest_pokemon_id"] = intval($reward["quest_pokemon_id"]);
        $questReward["quest_energy_pokemon_id"] = intval($reward["quest_energy_pokemon_id"]);
        $questReward["quest_pokemon_form"] = intval($reward["quest_pokemon_form"]);
        $questReward["quest_pokemon_costume"] = intval($reward["quest_pokemon_costume"]);
        $questReward["quest_item_id"] = intval($reward["quest_item_id"]);
        $questReward["quest_reward_amount"] = intval($reward["quest_reward_amount"]);
        $questReward["quest_reward_type"] = intval($reward["quest_reward_type"]);
        $questReward["count"] = $reward["count"];
        $questReward["percentage"] = round(100 / $total["total"] * $reward["count"], 3) . '%';
        if ($reward["quest_reward_type"] == 12) {
          $questReward["name"] = i8ln($this->data[$reward['quest_energy_pokemon_id']]["name"]);
        } elseif ($reward["quest_reward_type"] == 7) {
          $questReward["name"] = i8ln($this->data[$reward['quest_pokemon_id']]["name"]);
        } elseif ($reward["quest_reward_type"] == 2) {
          $questReward["name"] = i8ln($this->items[$reward['quest_item_id']]["name"]);
        } elseif ($reward["quest_reward_type"] == 3) {
          $questReward["name"] = i8ln('Stardust');
        } elseif ($reward["quest_reward_type"] == 1) {
          $questReward["name"] = i8ln('XP');
        }
        $data[] = $questReward;
      }
      return $data;
    }

   public function get_shiny_stats()
    {
      global $db, $noBoundaries, $boundaries;

      $geofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " AND (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $shinys = $db->query("
        SELECT
          SUM(shiny) AS shiny_count,
          pokemon_id,
          form,
          costume,
          COUNT(*) AS sample_size
        FROM pokemon
        WHERE expire_timestamp > UNIX_TIMESTAMP() - 86400 AND iv IS NOT NULL $geofenceSQL
        GROUP BY pokemon_id, form, costume
        HAVING shiny_count >= 1"
      );

      $data = array();
      foreach ($shinys as $shiny) {
        $pokemon["name"] = i8ln($this->data[$shiny['pokemon_id']]["name"]);
        $pokemon["shiny_count"] = $shiny["shiny_count"];
        $pokemon["pokemon_id"] = intval($shiny["pokemon_id"]);
        $pokemon["form"] = intval($shiny["form"]);
        $pokemon["costume"] = intval($shiny["costume"]);
        $pokemon["rate"] = '1/' . round($shiny["sample_size"] / $shiny['shiny_count']);
        $pokemon["percentage"] = round(100 / $shiny["sample_size"] * $shiny["shiny_count"], 3) . '%';
        $pokemon["sample_size"] = $shiny['sample_size'];
        $data[] = $pokemon;
      }
      return $data;
    }
}