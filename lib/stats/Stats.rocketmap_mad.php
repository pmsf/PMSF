<?php

namespace Stats;

class RocketMap_MAD extends Stats
{   
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
        $pokemon["pokemon_id"] = $mon["pokemon_id"];
        $pokemon["form"] = $mon["form"];
        $pokemon["costume"] = $mon["costume"];
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
          tq.quest_item_amount AS quest_item_amount,
          tq.quest_stardust AS quest_dust_amount,
          tq.quest_reward_type AS quest_reward_type
        FROM trs_quest tq
        LEFT JOIN pokestop p ON p.pokestop_id = tq.GUID
        WHERE tq.quest_timestamp >= UNIX_TIMESTAMP(CURDATE()) $geofenceSQL
        GROUP BY tq.quest_reward_type, tq.quest_item_id, tq.quest_stardust, tq.quest_item_amount, tq.quest_pokemon_id, tq.quest_pokemon_form_id"
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
        $questReward["quest_pokemon_id"] = $reward["quest_pokemon_id"];
        $questReward["quest_pokemon_form"] = $reward["quest_pokemon_form"];
        $questReward["quest_item_id"] = $reward["quest_item_id"];
        $questReward["count"] = $reward["count"];
        $questReward["percentage"] = round(100 / $total["total"] * $reward["count"], 3) . '%';
        $questReward["quest_reward_type"] = intval($reward["quest_reward_type"]);
        
        if ($reward["quest_reward_type"] == 12 && $reward["quest_energy_pokemon_id"] > 0) {
          $questReward["name"] = i8ln($this->data[$reward['quest_energy_pokemon_id']]["name"]);
          $questReward["quest_reward_amount"] = null;
        } elseif ($reward["quest_reward_type"] == 7 && $reward["quest_pokemon_id"] > 0) {
          $questReward["name"] = i8ln($this->data[$reward['quest_pokemon_id']]["name"]);
          $questReward["quest_reward_amount"] = null;
        } elseif ($reward["quest_reward_type"] == 2 && $reward["quest_item_id"] > 0) {
          $questReward["name"] = i8ln($this->items[$reward['quest_item_id']]["name"]);
          $questReward["quest_reward_amount"] = $reward["quest_item_amount"];
        } elseif ($reward["quest_reward_type"] == 3) {
          $questReward["name"] = i8ln('Stardust');
          $questReward["quest_reward_amount"] = $reward["quest_dust_amount"];
        }
        
        $data[] = $questReward;
      }
      return $data;
    }
}