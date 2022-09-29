<?php

namespace Stats;

class RocketMap_MAD extends Stats
{
    public function get_overview_stats()
    {
      global $db, $noBoundaries, $boundaries;

      $whereGeofenceSQL = '';
      $andGeofenceSQL = '';
      if (!$noBoundaries) {
          $andGeofenceSQL = " AND (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
          $whereGeofenceSQL = " WHERE (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $pokemon = $db->query("SELECT count(*) AS pokemon_count FROM pokemon WHERE disappear_time > UTC_TIMESTAMP() $andGeofenceSQL")->fetch();
      $gym = $db->query("SELECT COUNT(*) AS gym_count FROM gym $whereGeofenceSQL")->fetch();
      $pokestop = $db->query("SELECT COUNT(*) AS pokestop_count FROM pokestop $whereGeofenceSQL")->fetch();

      $raid = $db->query("
        SELECT
          COUNT(*) AS raid_count
        FROM raid
        LEFT JOIN gym ON raid.gym_id = gym.gym_id
        WHERE raid.end > UTC_TIMESTAMP() $andGeofenceSQL
      ")->fetch();

      $data = array();
    
      $overview['pokemon_count'] = $pokemon['pokemon_count'];
      $overview['gym_count'] = $gym['gym_count'];
      $overview['raid_count'] = $raid['raid_count'];
      $overview['pokestop_count'] = $pokestop['pokestop_count'];

      $data[] = $overview;
      return $data;
    }

    public function get_team_stats()
    {
      global $db, $noBoundaries, $boundaries;

      $geofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " WHERE (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
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
      global $db, $noBoundaries, $boundaries;

      $whereGeofenceSQL = '';
      $andGeofenceSQL = '';
      if (!$noBoundaries) {
          $andGeofenceSQL = " AND (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
          $whereGeofenceSQL = " WHERE (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $pokestops = $db->query("
        SELECT
          SUM(incident_expiration > UTC_TIMESTAMP()) AS rocket,
          SUM(lure_expiration > UTC_TIMESTAMP() AND active_fort_modifier = 501) AS normal_lure,
          SUM(lure_expiration > UTC_TIMESTAMP() AND active_fort_modifier = 502) AS glacial_lure,
          SUM(lure_expiration > UTC_TIMESTAMP() AND active_fort_modifier = 503) AS mossy_lure,
          SUM(lure_expiration > UTC_TIMESTAMP() AND active_fort_modifier = 504) AS magnetic_lure,
          SUM(lure_expiration > UTC_TIMESTAMP() AND active_fort_modifier = 505) AS rainy_lure
        FROM pokestop
        $whereGeofenceSQL"
      )->fetch();

      $quests = $db->query("
        SELECT
          COUNT(*) AS count
        FROM trs_quest tq
        LEFT JOIN pokestop p ON p.pokestop_id = tq.GUID
        WHERE tq.quest_timestamp >= UNIX_TIMESTAMP(CURDATE()) $andGeofenceSQL"
      )->fetch();

      $data = array();

      $pokestop['quest'] = $quests['count'];
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
          $geofenceSQL = " WHERE (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $spawnpoints = $db->query("
        SELECT
          COUNT(*) AS total,
          SUM(last_scanned IS NOT NULL) AS found,
          SUM(last_scanned IS NULL) AS missing
        FROM trs_spawn
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
          $geofenceSQL = " AND (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $mons = $db->query("
        SELECT
          pokemon_id,
          form,
          costume,
          count(*) AS count
        FROM pokemon
        WHERE disappear_time > UTC_TIMESTAMP() $geofenceSQL
        GROUP BY pokemon_id, form, costume"
      );
      $total = $db->query("SELECT COUNT(*) AS total FROM pokemon WHERE disappear_time > UTC_TIMESTAMP() $geofenceSQL")->fetch();

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
      global $db, $noBoundaries, $boundaries;

      $geofenceSQL = '';
      if (!$noBoundaries) {
          $geofenceSQL = " AND (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $rewards = $db->query("
        SELECT 
          COUNT(GUID) as count,
          tq.quest_item_id, 
          tq.quest_pokemon_id, 
          tq.quest_pokemon_id AS quest_energy_pokemon_id,
          tq.quest_pokemon_form_id AS quest_pokemon_form,
          tq.quest_pokemon_costume_id AS quest_pokemon_costume,
          tq.quest_item_amount AS quest_item_amount,
          tq.quest_stardust AS quest_dust_amount,
          tq.quest_reward_type AS quest_reward_type
        FROM trs_quest tq
        LEFT JOIN pokestop p ON p.pokestop_id = tq.GUID
        WHERE tq.quest_timestamp >= UNIX_TIMESTAMP(CURDATE()) $geofenceSQL
        GROUP BY tq.quest_reward_type, tq.quest_item_id, tq.quest_stardust, tq.quest_item_amount, tq.quest_pokemon_id, tq.quest_pokemon_form_id, tq.quest_pokemon_costume_id"
      );

      $total = $db->query("
        SELECT
          COUNT(*) AS total
        FROM trs_quest tq
        LEFT JOIN pokestop p ON p.pokestop_id = tq.GUID
        WHERE tq.quest_timestamp >= UNIX_TIMESTAMP(CURDATE()) $geofenceSQL"
      )->fetch();

      $data = array();
      foreach ($rewards as $reward) {
        $questReward["quest_pokemon_id"] = intval($reward["quest_pokemon_id"]);
        $questReward["quest_energy_pokemon_id"] = intval($reward["quest_energy_pokemon_id"]);
        $questReward["quest_pokemon_form"] = intval($reward["quest_pokemon_form"]);
        $questReward["quest_pokemon_costume"] = intval($reward["quest_pokemon_costume"]);
        $questReward["quest_item_id"] = intval($reward["quest_item_id"]);
        $questReward["count"] = $reward["count"];
        $questReward["percentage"] = round(100 / $total["total"] * $reward["count"], 3) . '%';
        $questReward["quest_reward_type"] = intval($reward["quest_reward_type"]);
        
        if ($reward["quest_reward_type"] == 12) {
          $questReward["name"] = i8ln($this->data[$reward['quest_energy_pokemon_id']]["name"]);
          $questReward["quest_reward_amount"] = $reward["quest_item_amount"];
        } elseif ($reward["quest_reward_type"] == 7) {
          $questReward["name"] = i8ln($this->data[$reward['quest_pokemon_id']]["name"]);
          $questReward["quest_reward_amount"] = null;
        } elseif ($reward["quest_reward_type"] == 2) {
          $questReward["name"] = i8ln($this->items[$reward['quest_item_id']]["name"]);
          $questReward["quest_reward_amount"] = $reward["quest_item_amount"];
        } elseif ($reward["quest_reward_type"] == 3) {
          $questReward["name"] = i8ln('Stardust');
          $questReward["quest_reward_amount"] = $reward["quest_dust_amount"];
        } elseif ($reward["quest_reward_type"] == 1) {
          $questReward["name"] = i8ln('XP');
          $questReward["quest_reward_amount"] = 0;
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
          $geofenceSQL = " AND (ST_WITHIN(point(latitude, longitude), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $shinys = $db->query("
        SELECT
          SUM(stats.is_shiny) AS shiny_count,
          p.pokemon_id,
          p.form,
          p.costume,
          COUNT(*) AS sample_size
        FROM pokemon p
        JOIN trs_stats_detect_mon_raw stats ON stats.encounter_id = p.encounter_id
        WHERE 
          p.individual_attack IS NOT NULL AND
          p.disappear_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)
          $geofenceSQL
        GROUP BY p.pokemon_id, p.form, p.costume
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