<?php
namespace Scanner;
class Scanner
{
    // Common functions for both RM and Monocle
    /**
     * $data
     * Used for Pokemon data
     * @var array|mixed
     */
    var $data = [];
    /**
     * $moves
     * Used for Pokemon moves
     * @var array|mixed
     */
    var $moves = [];

    /**
     * Scanner constructor.
     * Loads in the JSON arrays for Pokemon and moves
     */
    public function __construct()
    {
        $json_poke = "static/data/pokemon.json";
        $json_contents = file_get_contents($json_poke);
        $this->data = json_decode($json_contents, TRUE);

        $json_moves = "static/data/moves.json";
        $json_contents = file_get_contents($json_moves);
        $this->moves = json_decode($json_contents, TRUE);
    }

    /**
     * returnPokemon
     * Reads the database output from RM/Monocle and returns an array of Pokemon
     * @param $datas array
     * @return array
     */
    public function returnPokemon($datas)
    {
        $pokemons = array();

        $i = 0;

        /* fetch associative array */
        foreach ($datas as $row) {
            $p = array();

            $dissapear = $row["expire_timestamp"] * 1000;
            $lat = floatval($row["lat"]);
            $lon = floatval($row["lon"]);
            $pokeid = intval($row["pokemon_id"]);

            $atk = isset($row["atk_iv"]) ? intval($row["atk_iv"]) : null;
            $def = isset($row["def_iv"]) ? intval($row["def_iv"]) : null;
            $sta = isset($row["sta_iv"]) ? intval($row["sta_iv"]) : null;
            $mv1 = !empty($row["move_1"]) ? intval($row["move_1"]) : null;
            $mv2 = !empty($row["move_2"]) ? intval($row["move_2"]) : null;
            $weight = !empty($row["weight"]) ? floatval($row["weight"]) : null;
            $height = !empty($row["height"]) ? floatval($row["height"]) : null;
            $gender = isset($row["gender"]) ? intval($row["gender"]) : null;
            $form = isset($row["form"]) ? intval($row["form"]) : null;
            $cp = !empty($row["cp"]) ? intval($row["cp"]) : null;
            $cpm = !empty($row["cp_multiplier"]) ? floatval($row["cp_multiplier"]) : null;
            $level = !empty($row["level"]) ? intval($row["level"]) : null;

            $p["disappear_time"] = $dissapear; //done
            $p["encounter_id"] = $row["encounter_id"]; //done

            global $noHighLevelData;
            if (!$noHighLevelData) {
                $p["individual_attack"] = $atk; //done
                $p["individual_defense"] = $def; //done
                $p["individual_stamina"] = $sta; //done
                $p["move_1"] = $mv1; //done
                $p["move_2"] = $mv2;
                $p["weight"] = $weight;
                $p["height"] = $height;
                $p["cp"] = $cp;
                $p["cp_multiplier"] = $cpm;
                $p["level"] = $level;
            }

            $p["latitude"] = $lat; //done
            $p["longitude"] = $lon; //done
            $p["gender"] = $gender;
            $p["form"] = $form;
            $p["pokemon_id"] = $pokeid;
            $p["pokemon_name"] = i8ln($this->data[$pokeid]['name']);
            $p["pokemon_rarity"] = i8ln($this->data[$pokeid]['rarity']);

            $types = $this->data[$pokeid]["types"];
            foreach ($types as $k => $v) {
                $types[$k]['type'] = i8ln($v['type']);
            }
            $p["pokemon_types"] = $types;
            $p["spawnpoint_id"] = $row["spawn_id"];

            $pokemons[] = $p;

            unset($datas[$i]);

            $i++;
        }
        return $pokemons;
    }

    /**
     * returnPokestops
     * Reads the database output from RM/Monocle and returns an array of Pokestops
     * @param $datas
     * @return array
     */
    public function returnPokestops($datas)
    {
        $pokestops = array();
        $i = 0;
        /* fetch associative array */
        foreach ($datas as $row) {
            $p = array();

            $lat = floatval($row["lat"]);
            $lon = floatval($row["lon"]);

            $p["active_fort_modifier"] = !empty($row["active_fort_modifier"]) ? $row["active_fort_modifier"] : null;
            $p["enabled"] = isset($row["enabled"]) ? boolval($row["enabled"]) : true;
            $p["last_modified"] = !empty($row["last_modified"]) ? $row["last_modified"] * 1000 : 0;
            $p["latitude"] = $lat;
            $p["longitude"] = $lon;
            $p["lure_expiration"] = !empty($row["lure_expiration"]) ? $row["lure_expiration"] * 1000 : null;
            $p["pokestop_id"] = $row["external_id"];

            $pokestops[] = $p;

            unset($datas[$i]);

            $i++;
        }
        return $pokestops;
    }


    /**
     * returnGyms
     * Reads the database output from RM/Monocle and returns an array of Gyms
     * @param $datas
     * @return array
     */
    public function returnGyms($datas)
    {
        global $map;
        $gyms = array();
        $gym_ids = array();
        $i = 0;


        /* fetch associative array */
        foreach ($datas as $row) {
            $lat = floatval($row["lat"]);
            $lon = floatval($row["lon"]);
            $gpid = intval($row["guard_pokemon_id"]);
            $lm = $row["last_modified"] * 1000;
            $ls = !empty($row["last_scanned"]) ? $row["last_scanned"] * 1000 : null;
            $ti = isset($row["team"]) ? intval($row["team"]) : null;
            $tc = !empty($row["total_cp"]) ? intval($row["total_cp"]) : null;
            $sa = intval($row["slots_available"]);

            $p = array();

            $p["enabled"] = isset($row["enabled"]) ? boolval($row["enabled"]) : true;
            $p["guard_pokemon_id"] = $gpid;
            $p["gym_id"] = $row["external_id"];
            $p["slots_available"] = $sa;
            $p["last_modified"] = $lm;
            $p["last_scanned"] = $ls;
            $p["latitude"] = $lat;
            $p["longitude"] = $lon;
            $p["name"] = !empty($row["name"]) ? $row["name"] : null;
            $p["team_id"] = $ti;
            $p["pokemon"] = [];
            $p['total_gym_cp'] = $tc;

            if ($map != "monocle") {
                $rpid = intval($row['pokemon_id']);
                $p['raid_level'] = intval($row['level']);
                if ($rpid)
                    $p['raid_pokemon_id'] = $rpid;
                if ($rpid)
                    $p['raid_pokemon_name'] = i8ln($this->data[$rpid]['name']);
                $p['raid_pokemon_cp'] = !empty($row['cp']) ? intval($row['cp']) : null;
                $p['raid_pokemon_move_1'] = !empty($row['move_1']) ? intval($row['move_1']) : null;
                $p['raid_pokemon_move_2'] = !empty($row['move_2']) ? intval($row['move_2']) : null;
                $p['raid_start'] = $row["raid_start"] * 1000;
                $p['raid_end'] = $row["raid_end"] * 1000;
            }

            $gym_ids[] = $row["external_id"];

            $gyms[$row["external_id"]] = $p;

            unset($datas[$i]);

            $i++;
        }
        return ['gyms'=>$gyms, 'gym_ids'=>$gym_ids];
    }



}