<?php

namespace Search;

class Search
{
    // Common functions for both RM and Monocle
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
    }
}
