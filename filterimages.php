<?php
    function pokemonFilterImages($noPokemonNames, $noPokemonNumbers, $onClick = '', $pokemonToExclude = array(), $num = 0)
    {
        global $mons, $copyrightSafe, $iconFolderArray, $numberOfPokemon, $pokemonGenSearchString;
        if (empty($mons)) {
            $json = file_get_contents('static/dist/data/pokemon.min.json');
            $mons = json_decode($json, true);
        }
        echo '<div class="pokemon-list-cont" id="pokemon-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Name, ID & Type") . '" />
        <div class="pokemon-list list d-flex flex-wrap align-items-center text-center">';
        foreach ($mons as $k => $pokemon) {
            $type = '';
            $form = '';
            $name = $pokemon['name'];
            foreach ($pokemon['types'] as $t) {
                $type .= i8ln($t['type']);
            }
            if (!empty($pokemon['forms'])) {
                foreach ($pokemon['forms'] as $f) {
                    $form .= i8ln($f['nameform']);
                }
            }
            $genId = ($k <= 151) ? '1' : (($k <= 251) ? '2' : (($k <= 386) ? '3' : (($k <= 493) ? '4' : (($k <= 649) ? '5' : (($k <= 721) ? '6' : (($k <= 809) ? '7' : (($k <= 905) ? '8' : '')))))));
            $genName = ($k <= 151) ? i8ln('Kanto') : (($k <= 251) ? i8ln('Johto') : (($k <= 386) ? i8ln('Hoenn') : (($k <= 493) ? i8ln('Sinnoh') : (($k <= 649) ? i8ln('Unova') : (($k <= 721) ? i8ln('Kalos') : (($k <= 809) ? i8ln('Alola') : (($k <= 905) ? i8ln('Galar') : '')))))));
            if (! in_array($k, $pokemonToExclude)) {
                if ($k > $numberOfPokemon) {
                    break;
                }
                echo '<span class="pokemon-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '">
                <span style="display:none" class="types">' . $type . '</span>
                <span style="display:none" class="name">' . i8ln($name) . '</span>
                <span style="display:none" class="id">' . $k . '</span>
                <span style="display:none" class="genid">' . i8ln($pokemonGenSearchString) . $genId . '</span>
                <span style="display:none" class="genname">' . $genName . '</span>
                <span style="display:none" class="forms">' . $form . '</span>';
                if (! $copyrightSafe) {
                    echo '<img class="pkmnfilter" data-pkmnid="' . $k . '" loading="lazy" src="' . getIcon($iconFolderArray['pokemon'], 'pokemon/', '.png', $k) . '"/>';
                } else {
                    echo '<img loading="lazy" src="static/sprites/pokemon/' . $k . '.png"/>';
                }
                if (! $noPokemonNames) {
                    echo '<span style="font-size:.55rem;white-space:nowrap;">' . i8ln($name) . '</span>';
                } elseif (! $noPokemonNumbers) {
                    echo "<span class='pokemon-number'>" . $k . "</span>";
                }
                echo "</span>";
            }
        }
        echo '</div></div>'; ?>
        <script>
            var options = {
                valueNames: ['name', 'types', 'id', 'genid', 'genname', 'forms']
            };
            var monList = new List('pokemon-list-cont-<?php echo $num; ?>', options);
        </script>
    <?php }

    function energyFilterImages($noPokemonNames, $noPokemonNumbers, $onClick = '', $energyToExclude = array(), $num = 0)
    {
        global $mons, $copyrightSafe, $iconFolderArray, $numberOfPokemon;
        if (empty($mons)) {
            $json = file_get_contents('static/dist/data/pokemon.min.json');
            $mons = json_decode($json, true);
        }
        echo '<div class="energy-list-cont" id="energy-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Name, ID & Type") . '" />
        <div class="energy-list list d-flex flex-wrap align-items-center text-center">';
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
                    echo "<img class='rewardfilter' data-megaid='" . $k . "' data-type='mega_resource' loading='lazy' src='" . getIcon($iconFolderArray['reward'], 'reward/mega_resource/', '.png', $k) . "'/>";
                } else {
                    echo '<img loading="lazy" src="static/sprites/pokemon/' . $k . '.png"/>';
                }
                if (! $noPokemonNames) {
                    echo '<span style="font-size:.55rem;white-space:nowrap;">' . i8ln($name) . '</span>';
                } elseif (! $noPokemonNumbers) {
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

    function candyFilterImages($noPokemonNames, $noPokemonNumbers, $onClick = '', $candyToExclude = array(), $num = 0)
    {
        global $mons, $copyrightSafe, $iconFolderArray, $numberOfPokemon;
        if (empty($mons)) {
            $json = file_get_contents('static/dist/data/pokemon.min.json');
            $mons = json_decode($json, true);
        }
        echo '<div class="candy-list-cont" id="candy-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Name, ID & Type") . '" />
        <div class="candy-list list d-flex flex-wrap align-items-center text-center">';
        foreach ($mons as $k => $pokemon) {
            $type = '';
            $name = $pokemon['name'];
            foreach ($pokemon['types'] as $t) {
                $type .= i8ln($t['type']);
            }
            if (! in_array($k, $candyToExclude)) {
                if ($k > $numberOfPokemon) {
                    break;
                }
                echo '<span class="candy-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '">
                <span style="display:none" class="types">' . $type . '</span>
                <span style="display:none" class="name">' . i8ln($name) . '</span>
                <span style="display:none" class="id">' . $k . '</span>';
                if (! $copyrightSafe) {
                    echo '<img class="rewardfilter" data-type="candy" data-candyid="' . $k . '" loading="lazy" src="' . getIcon($iconFolderArray['reward'], 'reward/candy/', '.png', $k) . '"/>';
                    echo '<img class="pkmnfilter" data-pkmnid="' . $k . '" loading="lazy" src="' . getIcon($iconFolderArray['pokemon'], 'pokemon/', '.png', $k) . '"/>';
                } else {
                    echo '<img loading="lazy" src="static/sprites/pokemon/' . $k . '.png"/>';
                }
                if (! $noPokemonNames) {
                    echo '<span style="font-size:.55rem;white-space:nowrap;">' . i8ln($name) . '</span>';
                } elseif (! $noPokemonNumbers) {
                    echo "<span class='pokemon-number'>" . $k . "</span>";
                }
                echo '</span>';
            }
        }
        echo '</div></div>'; ?>
        <script>
            var options = {
                valueNames: ['name', 'types', 'id']
            };
            var candyList = new List('candy-list-cont-<?php echo $num; ?>', options);
        </script>
    <?php }

    function itemFilterImages($noItemNames, $noItemNumbers, $onClick = '', $itemsToExclude = array(), $num = 0)
    {
        global $items, $copyrightSafe, $iconFolderArray;
        if (empty($items)) {
            $json = file_get_contents('static/dist/data/items.min.json');
            $items = json_decode($json, true);
        }
        echo '<div class="item-list-cont" id="item-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Name & ID") . '" />
        <div class="item-list list d-flex flex-wrap align-items-center text-center">';
        foreach ($items as $k => $item) {
            $name = $item['name'];

            if (! in_array($k, $itemsToExclude)) {
                if (! $copyrightSafe) {
                    echo '<span class="item-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '">
                    <span style="display:none" class="name">' . i8ln($name) . '</span>
                    <span style="display:none" class="id">' . $k . '</span>
                    <img class="rewardfilter" data-itemid="' . $k . '" data-type="item" loading="lazy" src="' . getIcon($iconFolderArray['reward'], 'reward/item/', '.png', $k) . '"/>';
                } else {
                    echo '<span class="item-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '">
                    <span style="display:none" class="name">' . i8ln($name) . '</span>
                    <span style="display:none" class="id">' . $k . '</span>
                    <img loading="lazy" src="static/icons-safe/rewards/reward_' . $k . '_1.png"/>';
                }
                if (! $noItemNames) {
                    echo '<span style="font-size:.55rem;white-space:nowrap;">' . i8ln($name) . '</span>';
                } elseif (! $noItemNumbers) {
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
        global $grunts, $iconFolderArray;
        if (empty($grunts)) {
            $json = file_get_contents('static/dist/data/grunttype.min.json');
            $grunts = json_decode($json, true);
        }
        echo '<div class="grunt-list-cont" id="grunt-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Name & ID") . '" />
        <div class="grunt-list list d-flex flex-wrap align-items-center text-center">';
        foreach ($grunts as $g => $grunt) {
            $type = $grunt['type'];
            $gender = $grunt['grunt'];

            if (! in_array($g, $gruntsToExclude)) {
                echo '<span class="grunt-icon-sprite" data-value="' . $g . '" onclick="' . $onClick . '">
                <span style="display:none" class="gender">' . i8ln($gender) . '</span>
                <span style="display:none" class="type">' . i8ln($type) . '</span>
                <span style="display:none" class="id">' . $g . '</span>
                <img loading="lazy" src="' . getIcon($iconFolderArray['invasion'], 'invasion/', '.png', $g) . '"/>';
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
        global $raids, $copyrightSafe, $iconFolderArray;
        if (empty($raids)) {
            $json = file_get_contents('static/dist/data/raidegg.min.json');
            $egg = json_decode($json, true);
        }
        echo '<div class="raidegg-list-cont" id="raidegg-list-cont-' . $num . '">
        <input type="hidden" class="search-number" value="' . $num . '" />
        <input type="text" class="search search-input" placeholder="' . i8ln("Search Level") . '" />
        <div class="raidegg-list list d-flex flex-wrap align-items-center text-center">';
        foreach ($egg as $e => $egg) {
            $eggLevel = $egg['level'];
            $eggHatched = $egg['type'] === '2' ? true : false;
            if (! in_array($e, $raideggToExclude)) {
                echo '<span class="raidegg-icon-sprite" data-value="' . $e . '" onclick="' . $onClick . '">
                <span style="display:none" class="level">' . $eggLevel . '</span>
                <img loading="lazy" src="' . getIcon($iconFolderArray['raid'], 'raid/egg/', '.png', $eggLevel, $eggHatched) . '"/>';
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

