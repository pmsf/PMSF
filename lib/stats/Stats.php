<?php

namespace Stats;

class Stats
{
    // Common functions for both RDM and MAD
    /**
     * $data
     * Used for Pokemon data
     * @var array|mixed
     */
    public $data = [];
    /**
     * $moves
     * Used for Pokemon moves
     * @var array|mixed
     */
    public $moves = [];

    /**
     * columnExists()
     * Used for backward compatibility (backend agnostic)
     * Returns true if column exists in table
     */
    public function columnExists($table = '', $column = '')
    {
        global $db;
        if (empty($table) || empty($column)) {
            return false;
        }
        return (count($db->query("SHOW COLUMNS FROM `$table` WHERE LOWER(`Field`) = LOWER('$column');")->fetchAll(\PDO::FETCH_ASSOC)) > 0) ? true : false;
    }

    /**
     * Scanner constructor.
     * Loads in the JSON arrays for Pokemon and moves
     */
    public function __construct()
    {
        $json_poke = "static/data/pokemon.json";
        $json_contents = file_get_contents($json_poke);
        $this->data = json_decode($json_contents, true);

        $json_moves = "static/data/moves.json";
        $json_contents = file_get_contents($json_moves);
        $this->moves = json_decode($json_contents, true);

        $json_items = "static/data/items.json";
        $json_contents = file_get_contents($json_items);
        $this->items = json_decode($json_contents, true);

        $json_grunttype = "static/data/grunttype.json";
        $json_contents = file_get_contents($json_grunttype);
        $this->grunttype = json_decode($json_contents, true);
    }
}
