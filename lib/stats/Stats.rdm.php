<?php

namespace Stats;

class RDM extends Stats
{   
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
          $geofenceSQL = " AND (ST_WITHIN(point(lat, lon), ST_GEOMFROMTEXT('POLYGON(( " . $boundaries . " ))')))";
      }

      $rewards = $db->query("
        SELECT 
          COUNT(*) as count,
          quest_item_id, 
          quest_pokemon_id,
          json_extract(json_extract(`quest_rewards`,'$[*].info.pokemon_id'),'$[0]') AS quest_energy_pokemon_id,
          json_extract(json_extract(`quest_rewards`,'$[*].info.form_id'),'$[0]') AS quest_pokemon_form,
          json_extract(json_extract(`quest_rewards`,'$[*].info.amount'),'$[0]') AS quest_reward_amount,
          quest_reward_type
        FROM pokestop
        WHERE quest_reward_type IS NOT NULL $geofenceSQL
        GROUP BY quest_reward_type, quest_item_id, quest_reward_amount, quest_pokemon_id, quest_pokemon_form"
      );
      $total = $db->query("SELECT COUNT(*) AS total FROM pokestop WHERE quest_reward_type IS NOT NULL $geofenceSQL")->fetch();

      $data = array();
      foreach ($rewards as $reward) {
        $questReward["quest_pokemon_id"] = $reward["quest_pokemon_id"];
        $questReward["quest_energy_pokemon_id"] = $reward["quest_energy_pokemon_id"];
        $questReward["quest_pokemon_form"] = $reward["quest_pokemon_form"];
        $questReward["quest_item_id"] = $reward["quest_item_id"];
        $questReward["quest_reward_amount"] = $reward["quest_reward_amount"];
        $questReward["quest_reward_type"] = intval($reward["quest_reward_type"]);
        $questReward["count"] = $reward["count"];
        $questReward["percentage"] = round(100 / $total["total"] * $reward["count"], 3) . '%';
        if ($reward["quest_reward_type"] == 12 && $reward["quest_energy_pokemon_id"] > 0) {
          $questReward["name"] = i8ln($this->data[$reward['quest_energy_pokemon_id']]["name"]);
        } elseif ($reward["quest_reward_type"] == 7 && $reward["quest_pokemon_id"] > 0) {
          $questReward["name"] = i8ln($this->data[$reward['quest_pokemon_id']]["name"]);
        } elseif ($reward["quest_reward_type"] == 2 && $reward["quest_item_id"] > 0) {
          $questReward["name"] = i8ln($this->items[$reward['quest_item_id']]["name"]);
        } elseif ($reward["quest_reward_type"] == 3) {
          $questReward["name"] = i8ln('Stardust');
        }
        $data[] = $questReward;
      }
      return $data;
    }
}