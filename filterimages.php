<?php
    function pokemonFilterImages($noPokemonNumbers, $onClick = '', $pokemonToExclude = array(), $num = 0)
    {
        global $mons, $copyrightSafe, $iconRepository, $numberOfPokemon;
        if (empty($mons)) {
            $json = file_get_contents('static/dist/data/pokemon.min.json');
            $mons = json_decode($json, true);
        }
        echo '<div class="pokemon-list-cont" id="pokemon-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Name, ID & Type") . '" />
        <div class="pokemon-list list">';
        $i = 0;
        $z = 0;
        foreach ($mons as $k => $pokemon) {
            $type = '';
            $form = '';
            $formId = '';
            $name = $pokemon['name'];
            foreach ($pokemon['types'] as $t) {
                $type .= i8ln($t['type']);
            }
            if (!empty($pokemon['forms'])) {
                foreach ($pokemon['forms'] as $f) {
                    $form .= i8ln($f['nameform']);
                    $formId .= $f['protoform'];
                }
            }
            $genId = ($k <= 151) ? '1' : (($k <= 251) ? '2' : (($k <= 386) ? '3' : (($k <= 493) ? '4' : (($k <= 649) ? '5' : (($k <= 721) ? '6' : (($k <= 809) ? '7' : (($k <= 898) ? '8' : '')))))));
            if (! in_array($k, $pokemonToExclude)) {
                if ($k > $numberOfPokemon) {
                    break;
                }
                if ($k <= 9) {
                    $id = "00$k";
                } elseif ($k <= 99) {
                    $id = "0$k";
                } else {
                    $id = $k;
                }
                echo '<span class="pokemon-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '">
                <span style="display:none" class="types">' . $type . '</span>
                <span style="display:none" class="name">' . i8ln($name) . '</span>
                <span style="display:none" class="id">' . $k . '</span>
                <span style="display:none" class="genid">' . i8ln('generation') . $genId . '</span>
                <span style="display:none" class="forms">' . $form . '</span>
                <span style="display:none" class="formid">' . $formId . '</span>';
                if (! $copyrightSafe) {
                    echo "<img src='" . $iconRepository . "pokemon_icon_" . $id . "_00.png' style='width:48px;height:48px;'/>";
                } else {
                    echo "<img src='static/icons-safe/pokemon_icon_" . $id . "_00.png' style='width:48px;height:48px;'/>";
                }
                if (! $noPokemonNumbers) {
                    echo "<span class='pokemon-number'>" . $k . "</span>";
                }
                echo "</span>";
            }
        }
        echo '</div></div>'; ?>
        <script>
            var options = {
                valueNames: ['name', 'types', 'id', 'genid', 'forms', 'formid']
            };
            var monList = new List('pokemon-list-cont-<?php echo $num; ?>', options);
        </script>
    <?php }

    function energyFilterImages($noPokemonNumbers, $onClick = '', $energyToExclude = array(), $num = 0)
    {
        global $mons, $copyrightSafe, $iconRepository, $numberOfPokemon;
        if (empty($mons)) {
            $json = file_get_contents('static/dist/data/pokemon.min.json');
            $mons = json_decode($json, true);
        }
        echo '<div class="energy-list-cont" id="energy-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Name, ID & Type") . '" />
        <div class="energy-list list">';
        $i = 0;
        $z = 0;
        foreach ($mons as $k => $pokemon) {
            $type = '';
            $name = $pokemon['name'];
            foreach ($pokemon['types'] as $t) {
                $type .= i8ln($t['type']);
            }
            if (! in_array($k, $energyToExclude)) {
                if ($k > $numberOfPokemon) {
                    break;
                }
                echo '<span class="energy-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '">
                <span style="display:none" class="types">' . $type . '</span>
                <span style="display:none" class="name">' . i8ln($name) . '</span>
                <span style="display:none" class="id">' . $k . '</span>';
                if (! $copyrightSafe) {
                    echo '<img src="' . $iconRepository . 'rewards/reward_mega_energy_' . $k . '.png" style="width:48px;height:48px;"/>';
                } else {
                    echo '<img src="static/icons-safe/pokemon_icon_' . $k . '_00.png" style="width:48px;height:48px;"/>';
                }
                if (! $noPokemonNumbers) {
                    echo "<span class='pokemon-number'>" . $k . "</span>";
                }
                echo "</span>";
            }
        }
        echo '</div></div>'; ?>
        <script>
            var options = {
                valueNames: ['name', 'types', 'id']
            };
            var energyList = new List('energy-list-cont-<?php echo $num; ?>', options);
        </script>
    <?php }

    function itemFilterImages($noItemNumbers, $onClick = '', $itemsToExclude = array(), $num = 0)
    {
        global $items, $copyrightSafe, $iconRepository;
        if (empty($items)) {
            $json = file_get_contents('static/dist/data/items.min.json');
            $items = json_decode($json, true);
        }
        echo '<div class="item-list-cont" id="item-list-cont-' . $num . '"><input type="hidden" class="search-number" value="' . $num . '" /><input type="text" class="search search-input" placeholder="' . i8ln("Search Name & ID") . '" /><div class="item-list list">';
        $i = 0;
        $z = 0;
        foreach ($items as $k => $item) {
            $name = $item['name'];

            if (! in_array($k, $itemsToExclude)) {
                if (! $copyrightSafe) {
                    echo '<span class="item-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '"><span style="display:none" class="name">' . i8ln($name) . '</span><span style="display:none" class="id">' . $k . '</span><img src="' . $iconRepository . 'rewards/reward_' . $k . '_1.png" style="width:48px;height:48px;"/>';
                } else {
                    echo '<span class="item-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '"><span style="display:none" class="name">' . i8ln($name) . '</span><span style="display:none" class="id">' . $k . '</span><img src="static/icons-safe/rewards/reward_' . $k . '_1.png" style="width:48px;height:48px;"/>';
                }
                if (! $noItemNumbers) {
                    echo '<span class="item-number">' . $k . '</span>';
                }
                echo "</span>";
            }
        }
        echo '</div></div>'; ?>
        <script>
            var options = {
                valueNames: ['name', 'id']
            };
            var itemList = new List('item-list-cont-<?php echo $num; ?>', options);
        </script>
    <?php }

    function gruntFilterImages($noGruntNumbers, $onClick = '', $gruntsToExclude = array(), $num = 0)
    {
        global $grunts;
        if (empty($grunts)) {
            $json = file_get_contents('static/dist/data/grunttype.min.json');
            $grunts = json_decode($json, true);
        }
        echo '<div class="grunt-list-cont" id="grunt-list-cont-' . $num . '"><input type="hidden" class="search-number" value="' . $num . '" /><input type="text" class="search search-input" placeholder="' . i8ln("Search Name & ID") . '" /><div class="grunt-list list">';
        $i = 0;
        $z = 0;
        foreach ($grunts as $g => $grunt) {
            $type = $grunt['type'];
            $gender = $grunt['grunt'];

            if (! in_array($g, $gruntsToExclude)) {
                echo '<span class="grunt-icon-sprite" data-value="' . $g . '" onclick="' . $onClick . '"><span style="display:none" class="gender">' . i8ln($gender) . '</span><span style="display:none" class="type">' . i8ln($type) . '</span><span style="display:none" class="id">' . $g . '</span><img src="static/grunttype/' . $g . '.png" style="width:48px;height:48px;"/>';
                if (! $noGruntNumbers) {
                    echo '<span class="grunt-number">' . $g . '</span>';
                }
                echo "</span>";
            }
        }
        echo '</div></div>'; ?>
        <script>
            var options = {
                valueNames: ['type', 'gender', 'id']
            };
            var gruntList = new List('grunt-list-cont-<?php echo $num; ?>', options);
        </script>
    <?php }

    function raidEggFilterImages($noRaideggNumbers, $onClick = '', $raideggToExclude = array(), $num = 0)
    {
        global $raids, $copyrightSafe, $iconRepository;
        if (empty($raids)) {
            $json = file_get_contents('static/dist/data/raidegg.min.json');
            $egg = json_decode($json, true);
        }
        echo '<div class="raidegg-list-cont" id="raidegg-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Level") . '" />
        <div class="raidegg-list list">';
        $i = 0;
        $z = 0;
        foreach ($egg as $e => $egg) {
            $eggImage = $egg['image_name'];
            $eggLevel = $egg['level'];
            $eggType = $egg['type'];
            if (! in_array($e, $raideggToExclude)) {
                echo '<span class="raidegg-icon-sprite" data-value="' . $e . '" onclick="' . $onClick . '">
                <span style="display:none" class="level">' . $eggLevel . '</span>
                <img src="static/raids/egg_' . $eggImage . '.png" style="width:48px;"/>';
                if (! $noRaideggNumbers) {
                    echo '<span class="raidegg-number">' . $eggLevel . '</span>';
                }
                echo "</span>";
            }
        }
        echo '</div></div>'; ?>
        <script>
            var options = {
                valueNames: ['level']
            };
            var raideggsList = new List('raidegg-list-cont-<?php echo $num; ?>', options);
        </script>
    <?php } ?>

