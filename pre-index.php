<?php
if (! file_exists('config/config.php')) {
    http_response_code(500);
    die("<h1>Config file missing</h1><p>Please ensure you have created your config file (<code>config/config.php</code>).</p>");
}
include('config/config.php');
if ($noNativeLogin === false || $noDiscordLogin === false) {
    if (isset($_COOKIE["LoginCookie"])) {
        if (validateCookie($_COOKIE["LoginCookie"]) === false) {
            header("Location: .");
        }
    }
    if (!empty($_SESSION['user']->updatePwd) && $_SESSION['user']->updatePwd === 1) {
        header("Location: ./user");
        die();
    }
    if (empty($_SESSION['user']->id) && $forcedLogin === true) {
        header("Location: ./user");
    }
}
$zoom        = ! empty($_GET['zoom']) ? $_GET['zoom'] : null;
$encounterId = ! empty($_GET['encId']) ? $_GET['encId'] : null;
if (!empty($_GET['lang'])) {
    setcookie("LocaleCookie", $_GET['lang'], time() + 60 * 60 * 24 * 31);
    header("Location: .");
}
if (!empty($_COOKIE["LocaleCookie"])) {
    $locale = $_COOKIE["LocaleCookie"];
}
if (! empty($_GET['lat']) && ! empty($_GET['lon'])) {
    $startingLat = $_GET['lat'];
    $startingLng = $_GET['lon'];
    $locationSet = 1;
} else {
    $locationSet = 0;
}
if ($blockIframe) {
    header('X-Frame-Options: DENY');
}
if (strtolower($map) === "rdm") {
    if (strtolower($fork) === "beta") {
        $getList = new \Scanner\RDM_beta();
    }
} elseif (strtolower($map) === "rocketmap") {
    if (strtolower($fork) === "mad") {
        $getList = new \Scanner\RocketMap_MAD();
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="PokeMap">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#3b3b3b">
    <!-- Fav- & Apple-Touch-Icons -->
    <!-- Favicon -->
    <?php
    if ($faviconPath != "") {
        echo '<link rel="shortcut icon" href="' . $faviconPath . '"
             type="image/x-icon">';
    } else {
        echo '<link rel="shortcut icon" href="static/appicons/favicon.ico"
             type="image/x-icon">';
    }
    ?>
    <!-- non-retina iPhone pre iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/114x114.png"
          sizes="57x57">
    <!-- non-retina iPad pre iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/144x144.png"
          sizes="72x72">
    <!-- non-retina iPad iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/152x152.png"
          sizes="76x76">
    <!-- retina iPhone pre iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/114x114.png"
          sizes="114x114">
    <!-- retina iPhone iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/120x120.png"
          sizes="120x120">
    <!-- retina iPad pre iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/144x144.png"
          sizes="144x144">
    <!-- retina iPad iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/152x152.png"
          sizes="152x152">
    <!-- retina iPhone 6 iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/180x180.png"
          sizes="180x180">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.js"></script>
    <?php
    function pokemonFilterImages($noPokemonNumbers, $onClick = '', $pokemonToExclude = array(), $num = 0)
    {
        global $mons, $copyrightSafe, $iconRepository, $numberOfPokemon;
        if (empty($mons)) {
            $json = file_get_contents('static/dist/data/pokemon.min.json');
            $mons = json_decode($json, true);
        }
        echo '<div class="pokemon-list-cont" id="pokemon-list-cont-' . $num . '"><input type="hidden" class="search-number" value="' . $num . '" /><input class="search search-input" placeholder="' . i8ln("Search Name, ID & Type") . '" /><div class="pokemon-list list">';
        $i = 0;
        $z = 0;
        foreach ($mons as $k => $pokemon) {
            $type = '';
            $name = $pokemon['name'];
            foreach ($pokemon['types'] as $t) {
                $type .= $t['type'];
            }

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
                if (! $copyrightSafe) {
                    echo '<span class="pokemon-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '"><span style="display:none" class="types">' . i8ln($type) . '</span><span style="display:none" class="name">' . i8ln($name) . '</span><span style="display:none" class="id">' . $k . '</span><img src="' . $iconRepository . 'pokemon_icon_' . $id . '_00.png" style="width:48px;height:48px;"/>';
                } else {
                    echo '<span class="pokemon-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '"><span style="display:none" class="types">' . i8ln($type) . '</span><span style="display:none" class="name">' . i8ln($name) . '</span><span style="display:none" class="id">' . $k . '</span><img src="static/icons-safe/pokemon_icon_' . $id . '_00.png" style="width:48px;height:48px;"/>';
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
            var monList = new List('pokemon-list-cont-<?php echo $num; ?>', options);
        </script>
        <?php
    }

    function itemFilterImages($noItemNumbers, $onClick = '', $itemsToExclude = array(), $num = 0)
    {
        global $items, $copyrightSafe, $iconRepository;
        if (empty($items)) {
            $json = file_get_contents('static/dist/data/items.min.json');
            $items = json_decode($json, true);
        }
        echo '<div class="item-list-cont" id="item-list-cont-' . $num . '"><input type="hidden" class="search-number" value="' . $num . '" /><input class="search search-input" placeholder="' . i8ln("Search Name & ID") . '" /><div class="item-list list">';
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
        <?php
    }
    function gruntFilterImages($noGruntNumbers, $onClick = '', $gruntsToExclude = array(), $num = 0)
    {
        global $grunts;
        if (empty($grunts)) {
            $json = file_get_contents('static/dist/data/grunttype.min.json');
            $grunts = json_decode($json, true);
        }
        echo '<div class="grunt-list-cont" id="grunt-list-cont-' . $num . '"><input type="hidden" class="search-number" value="' . $num . '" /><input class="search search-input" placeholder="' . i8ln("Search Name & ID") . '" /><div class="grunt-list list">';
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
        <?php
    }
    function raidEggFilterImages($noRaideggNumbers, $onClick = '', $raideggToExclude = array(), $num = 0)
    {
        global $raids, $copyrightSafe, $iconRepository;
        if (empty($raids)) {
            $json = file_get_contents('static/dist/data/raidegg.min.json');
            $egg = json_decode($json, true);
        }
        echo '<div class="raidegg-list-cont" id="raidegg-list-cont-' . $num . '"><input type="hidden" class="search-number" value="' . $num . '" /><input class="search search-input" placeholder="' . i8ln("Search Level") . '" /><div class="raidegg-list list">';
        $i = 0;
        $z = 0;
        foreach ($egg as $e => $egg) {
            $eggImage = $egg['image_name'];
            $eggLevel = $egg['level'];
            $eggType = $egg['type'];
            if (! in_array($e, $raideggToExclude)) {
                echo '<span class="raidegg-icon-sprite" data-value="' . $e . '" onclick="' . $onClick . '"><span style="display:none" class="level">' . $eggLevel . '</span><img src="static/raids/egg_' . $eggImage . '.png" style="width:48px;height:56px;"/>';
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
        <?php
    }
    ?>

    <?php
    if ($gAnalyticsId != "") {
        echo '<!-- Google Analytics -->
            <script>
                window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
                ga("create", "' . $gAnalyticsId . '", "auto");
                ga("send", "pageview");
            </script>
            <script async src="https://www.google-analytics.com/analytics.js"></script>
            <!-- End Google Analytics -->';
    }
    ?>
    <?php
    if ($piwikUrl != "" && $piwikSiteId != "") {
        echo '<!-- Piwik -->
            <script type="text/javascript">
              var _paq = _paq || [];
              _paq.push(["trackPageView"]);
              _paq.push(["enableLinkTracking"]);
              (function() {
                var u="//' . $piwikUrl . '/";
                _paq.push(["setTrackerUrl", u+"piwik.php"]);
                _paq.push(["setSiteId", "' . $piwikSiteId . '"]);
                var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];
                g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
              })();
            </script>
            <!-- End Piwik Code -->';
    }
    ?>
    <!-- Cookie Disclamer -->
    <?php
    if (! $noCookie) {
        echo '<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
            <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
            <script>
            window.addEventListener("load", function(){
                window.cookieconsent.initialise({
                "palette": {
                    "popup": {
                    "background": "#3b3b3b"
                    },
                    "button": {
                    "background": "#d6d6d6"
                    }
        },
        "content": {
            "message": "' . i8ln('This website uses cookies to ensure you get the best experience on our website.') . '",
            "dismiss": "' . i8ln('Allow') . '",
            "link": "' . i8ln('Learn more') . '",
            "href": "https://www.cookiesandyou.com/"
        }
            })});
        </script>';
    }
    ?>

    <script>
        var token = '<?php echo (! empty($_SESSION['token'])) ? $_SESSION['token'] : ""; ?>';
    </script>
    <link href="node_modules/leaflet-geosearch/assets/css/leaflet.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
    <link rel="stylesheet" href="node_modules/datatables/media/css/jquery.dataTables.min.css">
    <script src="static/js/vendor/modernizr.custom.js"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Leaflet -->
    <link rel="stylesheet" href="node_modules/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="static/dist/css/app.min.css">
    <?php if (file_exists('static/css/custom.css')) {
        echo '<link rel="stylesheet" href="static/css/custom.css?' . time() . '">';
    } ?>
    <link rel="stylesheet" href="node_modules/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <link href='static/css/leaflet.fullscreen.css' rel='stylesheet' />
</head>
<?php
if (!$noLoadingScreen) {
    echo '<app-root><p class="spinner" VALIGN="CENTER">';
    if ($loadingStyle == '') {
        $loadingStyle = '<i class="fa fas fa-cog fa-spin fa-2x" aria-hidden="true"></i>';
    }
    echo $loadingStyle . '&nbsp;' . i8ln('Loading') . '...</p></app-root>';
} ?>
<body id="top">
<div class="wrapper">
    <!-- Header -->
    <header id="header">
        <a href="#nav" title="<?php echo i8ln('Options') ?>"></a>

        <h1><a href="#"><?= $headerTitle ?><img src="<?= $raidmapLogo ?>" height="35" width="auto" border="0" style="float: right; margin-left: 5px; margin-top: 10px;"></a></h1>

        <?php
        if (! $noStatsToggle) {
            echo '<a href="#stats" id="statsToggle" class="statsNav" title="' . i8ln('Stats') . '" style="float: right;"></a>';
        }
        if ($paypalUrl != "") {
            echo '<a href="' . $paypalUrl . '" target="_blank" style="float:right;padding:0 5px;">
                 <i class="fab fa-paypal" title="' . i8ln('PayPal') . '" style="position:relative;vertical-align:middle;color:white;margin-left:10px;font-size:20px;"></i>
                 </a>';
        }
        if ($telegramUrl != "") {
            echo '<a href="' . $telegramUrl . '" target="_blank" style="float:right;padding:0 5px;">
                 <i class="fab fa-telegram" title="' . i8ln('Telegram') . '" style="position:relative;vertical-align: middle;color:white;margin-left:10px;font-size:20px;"></i>
                 </a>';
        }
        if ($whatsAppUrl != "") {
            echo '<a href="' . $whatsAppUrl . '" target="_blank" style="float:right;padding:0 5px;">
                 <i class="fab fa-whatsapp" title="' . i8ln('WhatsApp') . '" style="position:relative;vertical-align:middle;color:white;margin-left:10px;font-size:20px;"></i>
                 </a>';
        }
        if ($discordUrl != "") {
            echo '<a href="' . $discordUrl . '" target="_blank" style="float:right;padding:0 5px;">
                 <i class="fab fa-discord" title="' . i8ln('Discord') . '" style="position:relative;vertical-align:middle;color:white;margin-left:10px;font-size:20px;"></i>
                 </a>';
        }
        if ($customUrl != "") {
            echo '<a href="' . $customUrl . '" target="_blank" style="float:right;padding:0 5px;">
                 <i class="' . $customUrlFontIcon . '" style="position:relative;vertical-align:middle;color:white;margin-left:10px;font-size:20px;"></i>
                 </a>';
        }
        ?>
        <?php if (! $noWeatherOverlay) {
            ?>
            <div id="currentWeather"></div>
            <?php
        } ?>
        
        <?php
        if ($noNativeLogin === false || $noDiscordLogin === false) {
            if (!empty($_SESSION['user']->id)) {
                if ($_SESSION['user']->expire_timestamp < time() && $manualAccessLevel === true) {
                    echo '<i class="fas fa-user-times" title="' . i8ln('User Expired') . '" style="color: red;font-size: 20px;position: relative;float: right;padding: 0 5px;top: 17px;"></i>';
                } else {
                    echo '<i class="fas fa-user-check" title="' . i8ln('User Logged in') . '" style="color: green;font-size: 20px;position: relative;float: right;padding: 0 5px;top: 17px;"></i>';
                }
            } else {
                echo "<a href='./user' style='float:right;padding:0 5px;' title='" . i8ln('Login') . "'><i class='fas fa-user' style='color:white;font-size:20px;vertical-align:middle;'></i></a>";
            }
        }
        ?>
    </header>
    <!-- NAV -->
    <nav id="nav">
        <div id="nav-accordion">
            <?php
            if (! $noPokemon || ! $noNests) {
                if (! $noNests) {
                    ?>
                <h3><?php echo i8ln('Pokémon &amp; Nests') ?></h3>
                <?php
                } else {
                    ?>
                <h3><?php echo i8ln('Pokémon') ?></h3>
                <?php
                } ?>
                <div>
                <?php
                if (! $noPokemon) {
                    echo '<div class=" form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln('Pokémon') . '</h3>
                    <div class="onoffswitch">
                        <input id="pokemon-switch" type="checkbox" name="pokemon-switch" class="onoffswitch-checkbox"
                               checked>
                        <label class="onoffswitch-label" for="pokemon-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
        </div>';
                } ?>
                <?php
                if (! $noNests) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln('Nests') . '</h3>
                    <div class="onoffswitch">
                        <input id="nests-switch" type="checkbox" name="nests-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="nests-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <div id="nest-filter-wrapper" style="display:none">
                    <?php
                    if (! $noNestPolygon && ! $noNests) {
                        echo '<div class="form-control switch-container">
                        <h3>' . i8ln('Nest Polygon') . '</h3>
                        <div class="onoffswitch">
                            <input id="nest-polygon-switch" type="checkbox" name="nest-polygon-switch" class="onoffswitch-checkbox">
                            <label class="onoffswitch-label" for="nest-polygon-switch">
                                <span class="switch-label" data-on="On" data-off="Off"></span>
                                <span class="switch-handle"></span>
                            </label>
                        </div>
                    </div>';
                    } ?>
                </div>
                    <div id="pokemon-filter-wrapper" style="display:none">
                        <?php
                        if (!$noTinyRat) {
                            ?>
                            <div class="form-control switch-container">
                                <h3><?php echo i8ln('Tiny Rats') ?></h3>
                                <div class="onoffswitch">
                                    <input id="tiny-rat-switch" type="checkbox" name="tiny-rat-switch"
                                           class="onoffswitch-checkbox" checked>
                                    <label class="onoffswitch-label" for="tiny-rat-switch">
                                        <span class="switch-label" data-on="On" data-off="Off"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <?php
                        } ?>
                        <?php
                        if (!$noBigKarp) {
                            ?>
                            <div class="form-control switch-container">
                                <h3><?php echo i8ln('Big Karp') ?></h3>
                                <div class="onoffswitch">
                                    <input id="big-karp-switch" type="checkbox" name="big-karp-switch"
                                           class="onoffswitch-checkbox" checked>
                                    <label class="onoffswitch-label" for="big-karp-switch">
                                        <span class="switch-label" data-on="On" data-off="Off"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <?php
                        } ?>
                        <div class="form-row min-stats-row">
                            <?php
                            if (! $noMinIV) {
                                echo '<div class="form-control" >
                            <label for="min-iv">
                                <h3>' . i8ln('Min IV') . '</h3>
                                <input id="min-iv" type="number" min="0" max="100" name="min-iv" placeholder="' . i8ln('Min IV') . '"/>
                            </label>
                        </div>';
                            } ?>
                            <?php
                            if (! $noMinLevel) {
                                echo '<div class="form-control">
                            <label for="min-level">
                                <h3>' . i8ln('Min Lvl') . '</h3>
                                <input id="min-level" type="number" min="0" max="100" name="min-level" placeholder="' . i8ln('Min Lvl') . '"/>
                            </label>
                        </div>';
                            } ?>
                        </div>
                        <div id="tabs">
                            <ul>
                                <?php
                                if (! $noHidePokemon) {
                                    ?>
                                    <li><a href="#tabs-1"><?php echo i8ln('Hide Pokémon') ?></a></li>
                                    <?php
                                } ?>
                                <?php
                                if (! $noExcludeMinIV) {
                                    ?>
                                    <li><a href="#tabs-2"><?php echo i8ln('Excl. Min IV/Lvl') ?></a></li>
                                    <?php
                                } ?>
                            </ul>
                            <?php
                            if (! $noHidePokemon) {
                                ?>
                                <div id="tabs-1">
                                    <div class="form-control hide-select-2">
                                        <label for="exclude-pokemon">
                                            <div class="pokemon-container">
                                                <input id="exclude-pokemon" type="text" readonly="true">
                                                <?php
                                                pokemonFilterImages($noPokemonNumbers, '', [], 2); ?>
                                            </div>
                                            <a href="#" class="select-all"><?php echo i8ln('All') ?>
                                                <div>
                                            </a><a href="#" class="hide-all"><?php echo i8ln('None') ?></a>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <?php
                            if (! $noExcludeMinIV) {
                                ?>
                                <div id="tabs-2">
                                    <div class="form-control hide-select-2">
                                        <label for="exclude-min-iv">
                                            <div class="pokemon-container">
                                                <input id="exclude-min-iv" type="text" readonly="true">
                                                <?php
                                                pokemonFilterImages($noPokemonNumbers, '', [], 3); ?>
                                            </div>
                                            <a href="#" class="select-all"><?php echo i8ln('All') ?>
                                                <div>
                                            </a><a href="#" class="hide-all"><?php echo i8ln('None') ?></a>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            } ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
            if (! $noPokestops) {
                if (! $noQuests) {
                    ?>
        <h3><?php echo i8ln('Pokéstops &amp; Quests'); ?></h3>
                <?php
                } else {
                    ?>
        <h3><?php echo i8ln('Pokéstops'); ?></h3>
                <?php
                } ?>
        <div>
                <?php
                if (! $noPokestops) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln('Pokéstops') . '</h3>
                    <div class="onoffswitch">
                        <input id="pokestops-switch" type="checkbox" name="pokestops-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="pokestops-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                    <div id="pokestops-filter-wrapper" style="display:none">
                <?php
                if (! $noAllPokestops) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln('All Pokéstops') . '</h3>
                    <div class="onoffswitch">
                        <input id="allPokestops-switch" type="checkbox" name="allPokestops-switch" class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="allPokestops-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noLures) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln('Lures only') . '</h3>
                    <div class="onoffswitch">
                        <input id="lures-switch" type="checkbox" name="lures-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="lures-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noTeamRocket) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln('Team Rocket only') . '</h3>
                    <div class="onoffswitch">
                        <input id="rocket-switch" type="checkbox" name="rocket-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="rocket-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <div id="rocket-wrapper" style="display:none">
                    <?php
                    if (! $noTeamRocketTimer && ! $noTeamRocket) {
                        echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                        <h3>' . i8ln('Team Rocket Timer') . '</h3>
                        <div class="onoffswitch">
                        <input id="rocket-timer-switch" type="checkbox" name="rocket-timer-switch" class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="rocket-timer-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                    </div>';
                    } ?>
                    <div id="grunt-tabs">
                        <ul>
                            <li><a href="#tabs-1"><?php echo i8ln('Hide Team Rocket') ?></a></li>
                        </ul>
                        <div id="tabs-1">
                            <div class="form-control-rocket hide-select-2">
                                <label for="exclude-grunts">
                                    <div class="grunts-container">
                                        <input id="exclude-grunts" type="text" readonly="true">
                                        <?php
                                        if ($generateExcludeGrunts === true) {
                                            gruntFilterImages($noGruntNumbers, '', array_diff(range(1, $numberOfGrunt), $getList->generated_exclude_list('gruntlist')), 10);
                                        } else {
                                            gruntFilterImages($noGruntNumbers, '', $excludeGrunts, 10);
                                        } ?>
                                    </div>
                                    <a href="#" class="select-all-grunt"><?php echo i8ln('All') ?>
                                        <div>
                                    </a><a href="#" style="margin-bottom:20px;" class="hide-all-grunt"><?php echo i8ln('None') ?> </a>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (! $noQuests) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln('Quests only') . '</h3>
                    <div class="onoffswitch">
                        <input id="quests-switch" type="checkbox" name="quests-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="quests-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                    </div>'; ?>
                    <div id="quests-filter-wrapper" style="display:none">
                        <div id="quests-tabs">
                            <ul>
                                <?php
                                if (! $noQuestsPokemon) {
                                    ?>
                                    <li><a href="#tabs-1"><?php echo i8ln('Hide Pokémon') ?></a></li>
                                    <?php
                                } ?>
                                <?php
                                if (! $noQuestsItems) {
                                    ?>
                                    <li><a href="#tabs-2"><?php echo i8ln('Hide Items') ?></a></li>
                                    <?php
                                } ?>
                            </ul>
                            <?php
                            if (! $noQuestsPokemon) {
                                ?>
                                <div id="tabs-1">
                                    <div class="form-control hide-select-2">
                                        <label for="exclude-quests-pokemon">
                                            <div class="quest-pokemon-container">
                                                <input id="exclude-quests-pokemon" type="text" readonly="true">
                                                <?php
                                                    if ($generateExcludeQuestsPokemon === true) {
                                                        pokemonFilterImages($noPokemonNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('pokemonlist')), 8);
                                                    } else {
                                                        pokemonFilterImages($noPokemonNumbers, '', $excludeQuestsPokemon, 8);
                                                    } ?>
                                            </div>
                                            <a href="#" class="select-all"><?php echo i8ln('All') ?>
                                                <div>
                                            </a><a href="#" class="hide-all"><?php echo i8ln('None') ?> </a>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <?php
                            if (! $noQuestsItems) {
                                ?>
                                <div id="tabs-2">
                                    <div class="form-control hide-select-2">
                                        <label for="exclude-quests-item">
                                            <div class="quest-item-container">
                                                <input id="exclude-quests-item" type="text" readonly="true">
                                                <?php
                                                    if ($generateExcludeQuestsItem === true) {
                                                        itemFilterImages($noItemNumbers, '', array_diff(range(1, $numberOfItem), $getList->generated_exclude_list('itemlist')), 9);
                                                    } else {
                                                        itemFilterImages($noItemNumbers, '', $excludeQuestsItem, 9);
                                                    } ?>
                                            </div>
                                            <a href="#" class="select-all-item"><?php echo i8ln('All') ?>
                                                <div>
                                            </a><a href="#" class="hide-all-item"><?php echo i8ln('None') ?> </a>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            } ?>
                        </div>
                        <div class="dustslider">
                <input type="range" min="0" max="2500" value="500" class="slider" id="dustrange">
                <p><?php echo i8ln('Show stardust ') ?><span id="dustvalue"></span></p>
                        </div>
                    </div>
                <?php
                } ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
            if (! $noRaids || ! $noGyms) {
                if (! $noRaids) {
                    echo '<h3>' . i8ln('Gym &amp; Raid') . '</h3>';
                } else {
                    echo '<h3>' . i8ln('Gym') . '</h3>';
                } ?>
                <div>
                    <?php
                    if (! $noRaids) {
                        echo '<div class="form-control switch-container" id="raids-wrapper" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln('Raids') . '</h3>
                    <div class="onoffswitch">
                        <input id="raids-switch" type="checkbox" name="raids-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="raids-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                    </div>';
                    } ?>
                    <div id="raids-filter-wrapper" style="display:none">
                    <?php
                    if (! $noRaidTimer && ! $noRaids) {
                        echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                        <h3>' . i8ln('Raids Timer') . '</h3>
                        <div class="onoffswitch">
                        <input id="raid-timer-switch" type="checkbox" name="raid-timer-switch" class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="raid-timer-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                    </div>';
                    } ?>
                    <?php
                    if (! $noActiveRaids) {
                        ?>
                        <div class="form-control switch-container" id="active-raids-wrapper" style="float:none;height:35px;margin-bottom:0px;">
                            <h3><?php echo i8ln('Only Active Raids') ?></h3>
                            <div class="onoffswitch">
                                <input id="active-raids-switch" type="checkbox" name="active-raids-switch"
                                       class="onoffswitch-checkbox" checked>
                                <label class="onoffswitch-label" for="active-raids-switch">
                                    <span class="switch-label" data-on="On" data-off="Off"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                        </div>
                        <?php
                    } ?>
                    <?php
                    if (! $noMinMaxRaidLevel) {
                        ?>
                        <div class="form-control switch-container" id="min-level-raids-filter-wrapper" style="float:none;height:50px;margin-bottom:0px;">
                            <h3><?php echo i8ln('Minimum Raid Level') ?></h3>
                            <select name="min-level-raids-filter-switch" id="min-level-raids-filter-switch">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-control switch-container" id="max-level-raids-filter-wrapper" style="float:none;height:50px;margin-bottom:5px;">
                            <h3><?php echo i8ln('Maximum Raid Level') ?></h3>
                            <select name="max-level-raids-filter-switch" id="max-level-raids-filter-switch">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <?php
                    } ?>
                        <div id="raid-tabs">
                            <ul>
                                <li><a href="#tabs-1"><?php echo i8ln('Hide Raidboss') ?></a></li>
                                <li><a href="#tabs-2"><?php echo i8ln('Hide Raidegg') ?></a></li>
                            </ul>
                            <div id="tabs-1">
                                <div class="form-control-raids hide-select-2">
                                    <label for="exclude-raidboss">
                                        <div class="raidboss-container">
                                            <input id="exclude-raidboss" type="text" readonly="true">
                                            <?php
                                                if ($generateExcludeRaidboss === true) {
                                                    pokemonFilterImages($noRaidbossNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('raidbosslist')), 11);
                                                } else {
                                                    pokemonFilterImages($noRaidbossNumbers, '', $excludeRaidboss, 11);
                                                } ?>
                                        </div>
                                        <a href="#" class="select-all"><?php echo i8ln('All') ?>
                                            <div>
                                        </a><a href="#" style="margin-bottom:20px;" class="hide-all"><?php echo i8ln('None') ?></a>
                                    </label>
                                </div>
                            </div>
                            <div id="tabs-2">
                                <div class="form-control-raids hide-select-2">
                                    <label for="exclude-raidegg">
                                        <div class="raidegg-container">
                                            <input id="exclude-raidegg" type="text" readonly="true">
                                            <?php
                                                raideggFilterImages($noRaideggNumbers, '', $excludeRaidegg, 12); ?>
                                        </div>
                                        <a href="#" class="select-all-egg"><?php echo i8ln('All') ?>
                                            <div>
                                        </a><a href="#" style="margin-bottom:20px;" class="hide-all-egg"><?php echo i8ln('None') ?></a>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if (! $noGyms) {
                        echo '<div class="form-control switch-container">
                            <h3>' . i8ln('Gyms') . '</h3>
                            <div class="onoffswitch">
                                <input id="gyms-switch" type="checkbox" name="gyms-switch" class="onoffswitch-checkbox" checked>
                                <label class="onoffswitch-label" for="gyms-switch">
                                    <span class="switch-label" data-on="On" data-off="Off"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                        </div>';
                    } ?>
                    <div id="gyms-filter-wrapper" style="display:none">
                        <?php
                        if (! $noTeams) {
                            echo '<div class="form-control switch-container" id="team-gyms-only-wrapper">
                                <h3>' . i8ln('Team') . '</h3>
                                <select name="team-gyms-filter-switch" id="team-gyms-only-switch">
                                    <option value="0">' . i8ln('All') . '</option>
                                    <option value="1">' . i8ln('Mystic') . '</option>
                                    <option value="2">' . i8ln('Valor') . '</option>
                                    <option value="3">' . i8ln('Instinct') . '</option>
                                </select>
                            </div>';
                        } ?>
                        <?php
                        if (! $noOpenSpot) {
                            echo '<div class="form-control switch-container" id="open-gyms-only-wrapper">
                                <h3>' . i8ln('Open Spot') . '</h3>
                                <div class="onoffswitch">
                                    <input id="open-gyms-only-switch" type="checkbox" name="open-gyms-only-switch"
                                           class="onoffswitch-checkbox" checked>
                                    <label class="onoffswitch-label" for="open-gyms-only-switch">
                                        <span class="switch-label" data-on="On" data-off="Off"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>';
                        } ?>
                        <?php
                        if (! $noMinMaxFreeSlots) {
                            echo '<div class="form-control switch-container" id="min-level-gyms-filter-wrapper">
                                <h3>' . i8ln('Minimum Free Slots') . '</h3>
                                <select name="min-level-gyms-filter-switch" id="min-level-gyms-filter-switch">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>
                            <div class="form-control switch-container" id="max-level-gyms-filter-wrapper">
                                <h3>' . i8ln('Maximum Free Slots') . '</h3>
                                <select name="max-level-gyms-filter-switch" id="max-level-gyms-filter-switch">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>';
                        } ?>
                        <?php
                        if (! $noLastScan) {
                            echo '<div class="form-control switch-container" id="last-update-gyms-wrapper">
                                <h3>' . i8ln('Last Scan') . '</h3>
                                <select name="last-update-gyms-switch" id="last-update-gyms-switch">
                                    <option value="0">' . i8ln('All') . '</option>
                                    <option value="1">' . i8ln('Last Hour') . '</option>
                                    <option value="6">' . i8ln('Last 6 Hours') . '</option>
                                    <option value="12">' . i8ln('Last 12 Hours') . '</option>
                                    <option value="24">' . i8ln('Last 24 Hours') . '</option>
                                    <option value="168">' . i8ln('Last Week') . '</option>
                                </select>
                            </div>';
                        } ?>
                    </div>
                    <div id="gyms-raid-filter-wrapper" style="display:none">
                        <?php
                        if (! $noExEligible) {
                            echo '<div class="form-control switch-container" id="ex-eligible-wrapper">
                                <h3>' . i8ln('EX Eligible Only') . '</h3>
                                <div class="onoffswitch">
                                    <input id="ex-eligible-switch" type="checkbox" name="ex-eligible-switch"
                                           class="onoffswitch-checkbox" checked>
                                    <label class="onoffswitch-label" for="ex-eligible-switch">
                                        <span class="switch-label" data-on="On" data-off="Off"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>';
                        } ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
            if (! $noCommunity) {
                ?>
                <h3><?php echo i8ln('Communities'); ?></h3>
                <div>
                <?php
                if (! $noCommunity) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln('Communities') . '</h3>
                    <div class="onoffswitch">
                        <input id="communities-switch" type="checkbox" name="communities-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="communities-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                </div>
                <?php
            }
            ?>
            <?php
            if (! $noPortals || ! $noS2Cells) {
                ?>
                <h3><?php echo i8ln('Ingress / S2Cell'); ?></h3>
                <div>
                <?php
                if (! $noPortals) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln('Portals') . '</h3>
                    <div class="onoffswitch">
                        <input id="portals-switch" type="checkbox" name="portals-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="portals-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>
                <div class="form-control switch-container" id = "new-portals-only-wrapper" style = "display:none">
                    <select name = "new-portals-only-switch" id = "new-portals-only-switch">
                        <option value = "0"> ' . i8ln('All') . '</option>
                        <option value = "1"> ' . i8ln('Only new') . ' </option>
                    </select>
                </div>';
                } ?>
                <?php
                if (! $noPoi) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln('POI') . '</h3>
                    <div class="onoffswitch">
                        <input id="poi-switch" type="checkbox" name="poi-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="poi-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noS2Cells) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln('Show S2 Cells') . '</h3>
                    <div class="onoffswitch">
                        <input id="s2-switch" type="checkbox" name="s2-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="s2-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
        </div>
                <div class="form-control switch-container" id = "s2-switch-wrapper" style = "display:none">
                    <div class="form-control switch-container">
                        <h3>' . i8ln('EX trigger Cells') . '</h3>
                        <div class="onoffswitch">
                            <input id="s2-level13-switch" type="checkbox" name="s2-level13-switch"
                                   class="onoffswitch-checkbox" checked>
                            <label class="onoffswitch-label" for="s2-level13-switch">
                                <span class="switch-label" data-on="On" data-off="Off"></span>
                                <span class="switch-handle"></span>
                            </label>
            </div>
                    </div>
                    <div class="form-control switch-container">
                        <h3>' . i8ln('Gym placement Cells') . '</h3>
                        <div class="onoffswitch">
                            <input id="s2-level14-switch" type="checkbox" name="s2-level14-switch"
                                   class="onoffswitch-checkbox" checked>
                            <label class="onoffswitch-label" for="s2-level14-switch">
                                <span class="switch-label" data-on="On" data-off="Off"></span>
                                <span class="switch-handle"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-control switch-container">
                        <h3>' . i8ln('Pokéstop placement Cells') . '</h3>
                        <div class="onoffswitch">
                            <input id="s2-level17-switch" type="checkbox" name="s2-level17-switch"
                                   class="onoffswitch-checkbox" checked>
                            <label class="onoffswitch-label" for="s2-level17-switch">
                                <span class="switch-label" data-on="On" data-off="Off"></span>
                                <span class="switch-handle"></span>
                            </label>
                        </div>
                    </div>
                </div>';
                } ?>
                </div>
                <?php
            }
            ?>
            <?php
            if (! $noSearchLocation || ! $noNests || ! $noStartMe || ! $noStartLast || ! $noFollowMe || ! $noPokestops || ! $noSpawnPoints || ! $noRanges || ! $noWeatherOverlay || ! $noSpawnArea) {
                if (! $noSearchLocation) {
                    echo '<h3>' . i8ln('Location &amp; Search') . '</h3>
                    <div>';
                } else {
                    echo '<h3>' . i8ln('Location') . '</h3>
                    <div>';
                } ?>
                <?php
                if (! $noWeatherOverlay) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln('Weather Conditions') . ' </h3>
                    <div class="onoffswitch">
                        <input id="weather-switch" type="checkbox" name="weather-switch"
                               class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="weather-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noSpawnPoints) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln('Spawn Points') . ' </h3>
                    <div class="onoffswitch">
                        <input id="spawnpoints-switch" type="checkbox" name="spawnpoints-switch"
                               class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="spawnpoints-switch">
                            <span class="switch-label" data - on="On" data - off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noRanges) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln('Ranges') . '</h3>
                    <div class="onoffswitch">
                        <input id="ranges-switch" type="checkbox" name="ranges-switch" class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="ranges-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noScanPolygon) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln('Scan Areas') . '</h3>
                    <div class="onoffswitch">
                        <input id="scan-area-switch" type="checkbox" name="scan-area-switch" class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="scan-area-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noLiveScanLocation) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln('Live scanner location') . '</h3>
                    <div class="onoffswitch">
                        <input id="scan-location-switch" type="checkbox" name="scan-location-switch" class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="scan-location-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noSearchLocation) {
                    echo '<div class="form-control switch-container" style="display:{{is_fixed}}">
                <label for="next-location">
            <h3>' . i8ln('Change search location') . '</h3>
                    <form id ="search-places">
            <input id="next-location" type="text" name="next-location" placeholder="' . i8ln('Change search location') . '">
                    <ul id="search-places-results" class="search-results places-results"></ul>
                    </form>
                </label>
            </div>';
                } ?>
                <?php
                if (! $noStartMe) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln('Start map at my position') . ' </h3>
                    <div class="onoffswitch">
                        <input id = "start-at-user-location-switch" type = "checkbox" name = "start-at-user-location-switch"
                               class="onoffswitch-checkbox"/>
                        <label class="onoffswitch-label" for="start-at-user-location-switch">
                            <span class="switch-label" data - on = "On" data - off = "Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noStartLast) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln('Start map at last position') . ' </h3>
                    <div class="onoffswitch">
                        <input id = "start-at-last-location-switch" type = "checkbox" name = "start-at-last-location-switch"
                               class="onoffswitch-checkbox"/>
                        <label class="onoffswitch-label" for="start-at-last-location-switch">
                            <span class="switch-label" data - on = "On" data - off = "Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noFollowMe) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln('Follow me') . ' </h3>
                    <div class="onoffswitch">
                        <input id = "follow-my-location-switch" type = "checkbox" name = "follow-my-location-switch"
                               class="onoffswitch-checkbox"/>
                        <label class="onoffswitch-label" for="follow-my-location-switch">
                            <span class="switch-label" data - on = "On" data - off = "Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if (! $noSpawnArea) {
                    echo '<div id="spawn-area-wrapper" class="form-control switch-container">
                <h3> ' . i8ln('Spawn area') . ' </h3>
                <div class="onoffswitch">
                    <input id = "spawn-area-switch" type = "checkbox" name = "spawn-area-switch"
                           class="onoffswitch-checkbox"/>
                    <label class="onoffswitch-label" for="spawn-area-switch">
                        <span class="switch-label" data - on = "On" data - off = "Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>';
                }
                echo '</div>';
            }
            ?>
            <?php
            if (! $noNotifyPokemon || ! $noNotifyRarity || ! $noNotifyIv || ! $noNotifyLevel || ! $noNotifySound || ! $noNotifyRaid || ! $noNotifyBounce || ! $noNotifyNotification) {
                echo '<h3>' . i8ln('Notification') . '</h3>
            <div>';
            }
            ?>
            <?php
            if (! $noNotifyPokemon) {
                ?>
                <div class="form-control hide-select-2">
                    <label for="notify-pokemon">
                        <h3 class="notify-pokemon-tab"><?php echo i8ln('Notify of Pokémon'); ?></h3>
                        <div style="max-height:165px;overflow-y:auto;">
                            <input id="notify-pokemon" type="text" readonly="true"/>
                            <?php pokemonFilterImages($noPokemonNumbers, '', [], 4); ?>
                        </div>
                        <a href="#" class="select-all notify-pokemon-button"><?php echo i8ln('All'); ?></a>
                        <a href="#" class="hide-all notify-pokemon-button"><?php echo i8ln('None'); ?></a>
                    </label>
                </div>
                <?php
            }
            ?>
            <?php
            if (! $noNotifyRarity) {
                echo '<div class="form-control">
                <label for="notify-rarity">
                    <h3>' . i8ln('Notify of Rarity') . '</h3>
                    <div style="max-height:165px;overflow-y:auto">
                        <select id="notify-rarity" multiple="multiple"></select>
                    </div>
                </label>
            </div>';
            }
            ?>
            <?php
            if (! $noNotifyIv) {
                echo '<div class="form-control">
                <label for="notify-perfection">
                    <h3>' . i8ln('Notify of IV') . '</h3>
                    <input id="notify-perfection" type="text" name="notify-perfection"
                           placeholder="' . i8ln('Min IV') . '%" style="float: right;width: 75px;text-align:center"/>
                </label>
            </div>';
            }
            ?>
            <?php
            if (! $noNotifyLevel) {
                echo '<div class="form-control">
                <label for="notify-level">
                    <h3 style="float:left;">' . i8ln('Notify of Level') . '</h3>
                    <input id="notify-level" min="1" max="35" type="number" name="notify-level"
                           placeholder="' . i8ln('Level') . '" style="float: right;width: 75px;text-align:center"/>
                </label>
            </div>';
            }
            ?>
            <?php
            if (! $noNotifyRaid) {
                echo '<div class="form-control switch-container" id="notify-raid-wrapper">
                        <h3>' . i8ln('Notify of Minimum Raid Level') . '</h3>
                        <select name="notify-raid" id="notify-raid">
                            <option value="0">' . i8ln('Disable') . '</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>';
            }
            ?>
            <?php
            if (! $noNotifySound) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Notify with sound') . '</h3>
                <div class="onoffswitch">
                    <input id="sound-switch" type="checkbox" name="sound-switch" class="onoffswitch-checkbox"
                           checked>
                    <label class="onoffswitch-label" for="sound-switch">
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>';
            }
            ?>
            <?php
            if (! $noCriesSound) {
                echo '<div class="form-control switch-container" id="cries-switch-wrapper">
                <h3>' . i8ln('Use Pokémon cries') . '</h3>
                <div class="onoffswitch">
                    <input id="cries-switch" type="checkbox" name="cries-switch" class="onoffswitch-checkbox"
                           checked>
                    <label class="onoffswitch-label" for="cries-switch">
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>';
            }
            ?>
            <?php
            if (! $noNotifySound) {
                echo '</div>';
            }
            ?>
            <?php
            if (! $noNotifyBounce) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Bounce') . '</h3>
                <div class="onoffswitch">
                    <input id="bounce-switch" type="checkbox" name="bounce-switch" class="onoffswitch-checkbox"
                           checked>
                    <label class="onoffswitch-label" for="bounce-switch">
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>';
            }
            ?>
            <?php
            if (! $noNotifyNotification) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Push Notifications') . '</h3>
                <div class="onoffswitch">
                    <input id="notification-switch" type="checkbox" name="notification-switch" class="onoffswitch-checkbox"
                           checked>
                    <label class="onoffswitch-label" for="notification-switch">
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>';
            }
            ?>
            <?php
            if (! $noNotifyPokemon || ! $noNotifyRarity || ! $noNotifyIv || ! $noNotifyLevel || ! $noNotifySound || ! $noNotifyRaid || ! $noNotifyBounce || ! $noNotifyNotification) {
                echo '</div>';
            }
            ?>

            <?php
            if (! $noMapStyle || ! $noDirectionProvider || ! $noIconSize || ! $noIconNotifySizeModifier || ! $noGymStyle || ! $noLocationStyle) {
                echo '<h3>' . i8ln('Style') . '</h3>
            <div>';
            }
            ?>
            <?php
            if (! $noDarkMode) {
                echo '<div class="form-control switch-container">
                <h3> ' . i8ln('Dark Mode') . ' </h3>
                <div class="onoffswitch">
                    <input id="dark-mode-switch" type="checkbox" name="dark-mode-switch" class="onoffswitch-checkbox"/>
                    <label class="onoffswitch-label" for="dark-mode-switch">
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>';
            } ?>
            <?php
            if (! $noMapStyle && !$forcedTileServer) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Map Style') . '</h3>
                <select id="map-style"></select>
            </div>';
            }
            ?>
            <?php
            if (! $noDirectionProvider) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Direction Provider') . '</h3>
                <select name="direction-provider" id="direction-provider">
                    <option value="apple">' . i8ln('Apple') . '</option>
                    <option value="google">' . i8ln('Google (Directions)') . '</option>
                    <option value="google_pin">' . i8ln('Google (Pin)') . '</option>
                    <option value="waze">' . i8ln('Waze') . '</option>
                    <option value="bing">' . i8ln('Bing') . '</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if (! $noMultipleRepos && ! $copyrightSafe) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Icon Style') . '</h3>';
                $count = sizeof($iconRepos);
                if ($count > 0) {
                    echo '<div><select name="icon-style" id="icon-style">';
                    for ($i = 0; $i <= $count - 1; $i ++) {
                        echo '<option value="' . $iconRepos[$i][1] . '">' . $iconRepos[$i][0] . '</option>';
                    }
                    echo '</select></div></div>';
                } else {
                    echo '</div>';
                    echo '<div><p>404 No Icon Packs found</p></div>';
                }
            }
            ?>
            <?php
            if (! $noIconSize) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Icon Size') . '</h3>
                <select name="pokemon-icon-size" id="pokemon-icon-size">
                    <option value="-8">' . i8ln('Small') . '</option>
                    <option value="0">' . i8ln('Normal') . '</option>
                    <option value="10">' . i8ln('Large') . '</option>
                    <option value="20">' . i8ln('X-Large') . '</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if (! $noIconNotifySizeModifier) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Increase Notified Icon Size') . '</h3>
                <select name="pokemon-icon-notify-size" id="pokemon-icon-notify-size">
                    <option value="0">' . i8ln('Disable') . '</option>
                    <option value="15">' . i8ln('Large') . '</option>
                    <option value="30">' . i8ln('X-Large') . '</option>
                    <option value="45">' . i8ln('XX-Large') . '</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if (! $noGymStyle) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Gym Marker Style') . '</h3>
                <select name="gym-marker-style" id="gym-marker-style">
                    <option value="ingame">' . i8ln('In-Game') . '</option>
                    <option value="shield">' . i8ln('Shield') . '</option>
                    <option value="rocketmap">' . i8ln('Rocketmap') . '</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if (! $noLocationStyle) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln('Location Icon Marker') . '</h3>
                <select name="locationmarker-style" id="locationmarker-style"></select>
            </div>';
            }
            ?>
            <?php
            if (! $noMapStyle || ! $noDirectionProvider || ! $noIconSize || ! $noIconNotifySizeModifier || ! $noGymStyle || ! $noLocationStyle) {
                echo '</div>';
            }
            ?>
            <?php
            if (! $noAreas) {
                echo '<h3>' . i8ln('Areas') . '</h3>';
                $count = sizeof($areas);
                if ($count > 0) {
                    echo '<div class="form-control switch-container area-container"><ul>';
                    for ($i = 0; $i <= $count - 1; $i ++) {
                        echo '<li><a href="" data-lat="' . $areas[ $i ][0] . '" data-lng="' . $areas[ $i ][1] . '" data-zoom="' . $areas[ $i ][2] . '" class="area-go-to">' . $areas[ $i ][3] . '</a></li>';
                    }
                    echo '</ul></div>';
                }
            }
            ?>
        </div>
        <div>
            <center>
                <button class="settings"
                        onclick="confirm('<?php echo i8ln('Are you sure you want to reset settings to default values?') ?>') ? (localStorage.clear(), window.location.reload()) : false">
                    <i class="fas fa-sync-alt" aria-hidden="true"></i> <?php echo i8ln('Reset Settings') ?>
                </button>
            </center>
        </div>
        <div>
            <center>
                <button class="settings"
                        onclick="download('<?= addslashes($title) ?>', JSON.stringify(JSON.stringify(localStorage)))">
                    <i class="fas fa-upload" aria-hidden="true"></i> <?php echo i8ln('Export Settings') ?>
                </button>
            </center>
        </div>
        <div>
            <center>
                <input id="fileInput" type="file" style="display:none;" onchange="openFile(event)"/>
                <button class="settings"
                        onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-download" aria-hidden="true"></i> <?php echo i8ln('Import Settings') ?>
                </button>
            </center>
        </div>
        <?php
        if (($noNativeLogin === false || $noDiscordLogin === false) && !empty($_SESSION['user']->id)) {
            ?>
            <div><center>
                <button class="settings" onclick="document.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i> <?php echo i8ln('Logout'); ?>
                </button>
            </center></div>
            <?php
        } ?>
        <?php
        if (!$noLocaleSelection) {
            ?>
            <div class="form-control switch-container" style="width:40%;left:32%;top:10px;position:relative;">
                <select name="language-switch" onchange="location = this.value;">
                    <option selected><?php echo i8ln('select language'); ?></option>
                    <option value="?lang=en"><?php echo i8ln('English'); ?></option>
                    <option value="?lang=de"><?php echo i8ln('German'); ?></option>
                    <option value="?lang=fr"><?php echo i8ln('French'); ?></option>
                    <option value="?lang=it"><?php echo i8ln('Italian'); ?></option>
                    <option value="?lang=pl"><?php echo i8ln('Polish'); ?></option>
                    <option value="?lang=sp"><?php echo i8ln('Spanish'); ?></option>
                </select>
            </div>
            <br><br>
            <?php
        }?>
        <?php
        if (($noNativeLogin === false || $noDiscordLogin === false) && !empty($_SESSION['user']->id)) {
            if ($manualAccessLevel && $noDiscordLogin) {
                $time = date("Y-m-d", $_SESSION['user']->expire_timestamp);
                echo '<div><center><p>';
                if ($_SESSION['user']->expire_timestamp > time()) {
                    echo "<span style='color: green;'>" . i8ln('Membership expires on') . " {$time}</span>";
                } else {
                    echo "<span style='color: red;'>" . i8ln('Membership expired on') . " {$time}</span>";
                }
                echo '</p></center></div>';
            }
            echo '<div><center><p>' . i8ln('Logged in as') . ': ' . $_SESSION['user']->user . '</p></center></div><br>';
        }
        ?>
    </nav>
    <nav id="stats">
        <div class="switch-container">
            <?php
            if ($worldopoleUrl !== "") {
                ?>
                <div class="switch-container">
                    <div>
                        <center><a class="button" href="<?= $worldopoleUrl ?>"><i class="far fa-chart-bar"></i><?php echo i8ln(' Full Stats') ?></a></center>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="switch-container">
                <center><h1 id="stats-ldg-label"><?php echo i8ln('Loading') ?>...</h1></center>
            </div>
            <div class="stats-label-container">
                <center><h1 id="stats-pkmn-label"></h1></center>
            </div>
            <div id="pokemonList" style="color: black;">
                <table id="pokemonList_table" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?php echo i8ln('Icon') ?></th>
                        <th><?php echo i8ln('Name') ?></th>
                        <th><?php echo i8ln('Count') ?></th>
                        <th>%</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div id="pokeStatStatus" style="color: black;"></div>
            </div>
            <div class="stats-label-container">
                <center><h1 id="stats-gym-label"></h1></center>
            </div>
            <div id="arenaList" style="color: black;"></div>

            <div class="stats-label-container">
                <center><h1 id="stats-raid-label"></h1></center>
            </div>
            <div id="raidList" style="color: black;"></div>

            <div class="stats-label-container">
                <center><h1 id="stats-pkstop-label"></h1></center>
            </div>
            <div id="pokestopList" style="color: black;"></div>

            <div class="stats-label-container">
                <center><h1 id="stats-spawnpoint-label"></h1></center>
            </div>
            <div id="spawnpointList" style="color: black;"></div>
        </div>
    </nav>
    <nav id="gym-details">
        <center><h1><?php echo i8ln('Loading') ?>...</h1></center>
    </nav>

    <div id="motd" title=""></div>

    <div id="map"></div>
    <div class="loader" style="display:none;"></div>
    <div class="global-raid-modal">

    </div>
    <?php if (! $noManualNests) { ?>
        <div class="global-nest-modal" style="display:none;">
            <input type="hidden" name="pokemonID" class="pokemonID"/>
            <?php pokemonFilterImages($noPokemonNumbers, 'pokemonSubmitFilter(event)', $excludeNestMons, 5); ?>
            <div class="button-container">
                <button type="button" onclick="manualNestData(event);" class="submitting-nests"><i
                        class="fas fa-binoculars"></i> <?php echo i8ln('Submit Nest'); ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <?php if (! $noRenamePokestops) { ?>
        <div class="renamepokestop-modal" style="display: none;">
            <input type="text" id="pokestop-name" name="pokestop-name" 
                placeholder="<?php echo i8ln('Enter New Pokéstop Name'); ?>" data-type="pokestop" class="search-input">
            <div class="button-container">
                <button type="button" onclick="renamePokestopData(event);" class="renamepokestopid"><i class="fas fa-edit"></i> <?php echo i8ln('Rename Pokéstop'); ?></button>
            </div>
        </div>
    <?php } ?>
    <?php if (! $noRenameGyms) { ?>
        <div class="renamegym-modal" style="display: none;">
            <input type="text" id="gym-name" name="gym-name" 
                placeholder="<?php echo i8ln('Enter New Gym Name'); ?>" data-type="gym" class="search-input">
            <div class="button-container">
                <button type="button" onclick="renameGymData(event);" class="renamegymid"><i class="fas fa-edit"></i> <?php echo i8ln('Rename Gym'); ?></button>
            </div>
        </div>
    <?php } ?>
    <?php if (! $noConvertPokestops) { ?>
        <div class="convert-modal" style="display: none;">
             <div class="button-container">
                <button type="button" onclick="convertPokestopData(event);" class="convertpokestopid"><i class="fas fa-sync-alt"></i> <?php echo i8ln('Convert to gym'); ?></button>
            </div>
        </div>
    <?php } ?>
    <?php if (! $noEditCommunity) { ?>
        <div class="editcommunity-modal" style="display: none;">
            <input type="text" id="community-name" name="community-name" placeholder="<?php echo i8ln('Enter New community Name'); ?>" data-type="community-name" class="search-input">
            <input type="text" id="community-description" name="community-description" placeholder="<?php echo i8ln('Enter New community Description'); ?>" data-type="community-description" class="search-input">
            <input type="text" id="community-invite" name="community-invite" placeholder="<?php echo i8ln('Enter New community Invite link'); ?>" data-type="community-invite" class="search-input">
            <div class="button-container">
                <button type="button" onclick="editCommunityData(event);" class="editcommunityid">
                    <i class="fas fa-edit"></i> <?php echo i8ln('Save Changes'); ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <?php if (! $noEditPoi) { ?>
        <div class="editpoi-modal" style="display: none;">
            <input type="text" id="poi-name" name="poi-name" placeholder="<?php echo i8ln('Enter New POI Name'); ?>" data-type="poi-name" class="search-input">
            <input type="text" id="poi-description" name="poi-description" placeholder="<?php echo i8ln('Enter New POI Description'); ?>" data-type="poi-description" class="search-input">
            <input type="text" id="poi-notes" name="poi-notes"placeholder="<?php echo i8ln('Enter New POI Notes'); ?>" data-type="poi-notes" class="search-input">
                <?php if (! empty($imgurCID)) {
                ?>
                    <div class="upload-button-container">
                         <button type="button"><i class="fas fa-upload"></i> <?php echo i8ln('Upload POI Image') ?></button>
                         <input type="file" id="poi-image" name="poi-image" accept="image/*" class="poi-image" data-type="poi-image" class="search-input" onchange='previewPoiImage(event)' >
                    </div>
                    <center><img id='preview-poi-image' name='preview-poi-image' width="50px" height="auto"></center>
                    <div class="upload-button-container">
                         <button type="button"><i class="fas fa-upload"></i> <?php echo i8ln('Upload POI Surrounding') ?></button>
                         <input type="file" id="poi-surrounding" name="poi-surrounding" accept="image/*" class="poi-surrounding" data-type="poi-surrounding" class="search-input" onchange='previewPoiSurrounding(event)'>
                    </div>
                    <center><img id='preview-poi-surrounding' name='preview-poi-surrounding' width="50px" height="auto"></center>
                <?php
            } ?>
            <div class="button-container">
                <button type="button" onclick="editPoiData(event);" class="editpoiid"><i class="fas fa-save"></i> <?php echo i8ln('Save Changes'); ?></button>
            </div>
        </div>
    <?php } ?>
    <?php if (! $noPortals) { ?>
        <div class="convert-portal-modal" style="display: none;">
            <div class="button-container">
                <button type="button" onclick="convertPortalToPokestopData(event);" class="convertportalid">
                    <i class="fas fa-sync-alt"></i> <?php echo i8ln('Convert to Pokéstop'); ?></button>
                <button type="button" onclick="convertPortalToGymData(event);" class="convertportalid">
                    <i class="fas fa-sync-alt"></i> <?php echo i8ln('Convert to Gym'); ?></button>
                <button type="button" onclick="markPortalChecked(event);" class="convertportalid">
                    <i class="fas fa-times"></i> <?php echo i8ln('No Pokéstop or Gym'); ?></button>
            </div>
        </div>
    <?php } ?>
    <?php if (! $noPoi) { ?>
        <div class="mark-poi-modal" style="display: none;">
            <div class="button-container">
                <button type="button" onclick="markPoiSubmitted(event);" class="markpoiid"><i class="fas fa-sync-alt"></i> <?php echo i8ln('Submitted'); ?></button>
                <button type="button" onclick="markPoiDeclined(event);" class="markpoiid"><i class="fas fa-times"></i> <?php echo i8ln('Declined'); ?></button>
                <button type="button" onclick="markPoiResubmit(event);" class="markpoiid"><i class="fas fa-times"></i> <?php echo i8ln('Resubmit'); ?></button>
                <button type="button" onclick="markNotCandidate(event);" class="markpoiid"><i class="fas fa-times"></i> <?php echo i8ln('Not a candidate'); ?></button>
            </div>
        </div>
    <?php } ?>
    <?php if (! $noDiscordLogin) { ?>
        <div class="accessdenied-modal" style="display: none;">
            <?php if ($copyrightSafe === false) { ?>
                <img src="static/images/accessdenied.png" alt="PikaSquad" width="250">
            <?php } ?>
            <center><?php echo i8ln('Your access has been denied.'); ?></center>
            <br>
            <?php echo i8ln('You might not be a member of our Discord or you joined a server which is on our blacklist. Click') . ' <a href="' . $discordUrl . '">' . i8ln('here') . '</a> ' . i8ln('to join!'); ?>
        </div>
    <?php } ?>
    <div id="fullscreenModal" class="modal">
        <span class="close" onclick="closeFullscreenModal();">&times;</span>
        <img class="modal-content" id="fullscreenimg">
    </div>
    <?php if (! $noManualQuests) { ?>
        <div class="quest-modal" style="display: none;">
            <input type="hidden" value="" name="questPokestop" class="questPokestop"/>
            <?php
                $json   = file_get_contents('static/dist/data/questtype.min.json');
                $questtypes  = json_decode($json, true);
                
                $json    = file_get_contents('static/dist/data/rewardtype.min.json');
                $rewardtypes   = json_decode($json, true);
                
                $json    = file_get_contents('static/dist/data/conditiontype.min.json');
                $conditiontypes   = json_decode($json, true);
                
                $json    = file_get_contents('static/dist/data/pokemon.min.json');
                $encounters = json_decode($json, true);
                
                $json    = file_get_contents('static/dist/data/items.min.json');
                $items = json_decode($json, true);
            ?>
            <label for="questTypeList"><?php echo i8ln('Quest'); ?>
            <select id="questTypeList" name="questTypeList" class="questTypeList">
                <option />
                <?php
                foreach ($questtypes as $key => $value) {
                    if (! in_array($key, $hideQuestTypes)) {
                        ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln($value['text']); ?></option>
                    <?php
                    }
                }
                ?>
            </select>
            <select id="questAmountList" name="questAmountList" class="questAmountList">
                <option />
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
            </label>
            <label for="conditionTypeList"><?php echo i8ln('Conditions'); ?>
            <select id="conditionTypeList" name="conditionTypeList" class="conditionTypeList">
                <option />
                <?php
                foreach ($conditiontypes as $key => $value) {
                    if (! in_array($key, $hideConditionTypes)) {
                        ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln($value['text']); ?></option>
                    <?php
                    }
                }
                ?>
            </select>
            <select id="pokeCatchList" name="pokeCatchList" class="pokeCatchList" multiple></select>
            <select id="typeCatchList" name="typeCatchList" class="typeCatchList" multiple>
                <option value="1"><?php echo i8ln('Normal'); ?></option>
                <option value="2"><?php echo i8ln('Fighting'); ?></option>
                <option value="3"><?php echo i8ln('Flying'); ?></option>
                <option value="4"><?php echo i8ln('Poison'); ?></option>
                <option value="5"><?php echo i8ln('Ground'); ?></option>
                <option value="6"><?php echo i8ln('Rock'); ?></option>
                <option value="7"><?php echo i8ln('Bug'); ?></option>
                <option value="8"><?php echo i8ln('Ghost'); ?></option>
                <option value="9"><?php echo i8ln('Steel'); ?></option>
                <option value="10"><?php echo i8ln('Fire'); ?></option>
                <option value="11"><?php echo i8ln('Water'); ?></option>
                <option value="12"><?php echo i8ln('Grass'); ?></option>
                <option value="13"><?php echo i8ln('Electric'); ?></option>
                <option value="14"><?php echo i8ln('Psychic'); ?></option>
                <option value="15"><?php echo i8ln('Ice'); ?></option>
                <option value="16"><?php echo i8ln('Dragon'); ?></option>
                <option value="17"><?php echo i8ln('Dark'); ?></option>
                <option value="18"><?php echo i8ln('Fairy'); ?></option>
            </select>
            <select id="raidLevelList" name="raidLevelList" class="raidLevelList">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <select id="throwTypeList" name="throwTypeList" class="throwTypeList">
                <option />
                <option value="10"><?php echo i8ln('Nice'); ?></option>
                <option value="11"><?php echo i8ln('Great'); ?></option>
                <option value="12"><?php echo i8ln('Excellent'); ?></option>
            </select>
            <select id="curveThrow" class="curveThrow" class="curveThrow">
                <option />
                <option value="0"><?php echo i8ln('Without curve throw'); ?></option>
                <option value="1"><?php echo i8ln('With curve throw'); ?></option>
            </select>
            </label>
            <label for="rewardTypeList"><?php echo i8ln('Reward'); ?>
            <select id="rewardTypeList" name="rewardTypeList" class="rewardTypeList">
                <option />
                <?php
                foreach ($rewardtypes as $key => $value) {
                    if (! in_array($key, $hideRewardTypes)) {
                        ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln($value['text']); ?></option>
                    <?php
                    }
                }
                ?>
            </select>
            <select id="pokeQuestList" name="pokeQuestList" class="pokeQuestList">
                <option />
                <?php
                foreach ($encounters as $key => $value) {
                    if (in_array($key, $showEncounters)) {
                        ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln($value['name']); ?></option>
                    <?php
                    }
                }
                ?>
            </select>
            <select id="itemQuestList" name="itemQuestList" class="itemQuestList">
                <option />
                <?php
                foreach ($items as $key => $value) {
                    if (in_array($key, $showItems)) {
                        ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln($value['name']); ?></option>
                    <?php
                    }
                }
                ?>
            </select>
            <select id="itemAmountList" name="itemAmountList" class="itemAmountList">
                <option />
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
            <select id="dustQuestList" name="dustQuestList" class="dustQuestList">
                <option />
                <option value="200">200</option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
            </select>
            </label>
            <div class="button-container">
                <button type="button" onclick="manualQuestData(event);" class="submitting-quest"><i
                        class="fas fa-binoculars"></i> <?php echo i8ln('Submit Quest'); ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <div class="fullscreen-toggle">
        <button class="map-toggle-button" onClick="toggleFullscreenMap();"><i class="fa fa-expand" aria-hidden="true"></i></button>
    </div>
    <?php if ((! $noGyms || ! $noPokestops) && ! $noSearch) { ?>
        <div class="search-container">
            <button class="search-modal-button" onClick="openSearchModal(event);"><i class="fas fa-search"
                                                                                     aria-hidden="true"></i></button>
            <div class="search-modal" style="display:none;">
                <div id="search-tabs">
                    <ul>
                        <?php if (! $noQuests && ! $noSearchManualQuests) { ?>
                            <li><a href="#tab-rewards"><img src="static/images/reward.png"/></a></li>
                        <?php }
                        if (! $noSearchNests) { ?>
                            <li><a href="#tab-nests"><img src="static/images/nest.png"/></a></li>
                        <?php }
                        if (! $noSearchGyms) { ?>
                            <li><a href="#tab-gym"><img src="static/forts/ingame/Uncontested.png"/></a></li>
                        <?php }
                        if (! $noSearchPokestops) { ?>
                            <li><a href="#tab-pokestop"><img src="static/forts/Pstop.png"/></a></li>
                        <?php }
                        if (! $noSearchPortals) { ?>
                            <li><a href="#tab-portals"><img src="static/images/portal.png"/></a></li>
            <?php } ?>
                    </ul>
                    <?php if (! $noQuests && ! $noSearchManualQuests) { ?>
                        <div id="tab-rewards">
                            <input type="search" id="reward-search" name="reward-search"
                                   placeholder="<?php echo i8ln('Enter Reward Name'); ?>"
                                   data-type="reward" class="search-input"/>
                            <ul id="reward-search-results" class="search-results reward-results"></ul>
                        </div>
                    <?php } ?>
                    <?php if (! $noSearchNests) { ?>
                        <div id="tab-nests">
                            <input type="search" id="nest-search" name="nest-search"
                                   placeholder="<?php echo i8ln('Enter Pokémon or Type'); ?>"
                                   data-type="nests" class="search-input"/>
                            <ul id="nest-search-results" class="search-results nest-results"></ul>
                        </div>
                    <?php } ?>
                    <?php if (! $noSearchGyms) { ?>
                        <div id="tab-gym">
                            <input type="search" id="gym-search" name="gym-search"
                                   placeholder="<?php echo i8ln('Enter Gym Name'); ?>"
                                   data-type="forts" class="search-input"/>
                            <ul id="gym-search-results" class="search-results gym-results"></ul>
                        </div>
            <?php } ?>
            <?php if (! $noSearchPokestops) { ?>
                        <div id="tab-pokestop">
                            <input type="search" id="pokestop-search" name="pokestop-search"
                                   placeholder="<?php echo i8ln('Enter Pokéstop Name'); ?>" data-type="pokestops"
                                   class="search-input"/>
                            <ul id="pokestop-search-results" class="search-results pokestop-results"></ul>
                        </div>
            <?php } ?>
            <?php if (! $noSearchPortals) { ?>
                        <div id="tab-portals">
                            <input type="search" id="portals-search" name="portals-search"
                                   placeholder="<?php echo i8ln('Enter Portal Name'); ?>" data-type="portals"
                                   class="search-input"/>
                            <ul id="portals-search-results" class="search-results portals-results"></ul>
                        </div>
            <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php
    if ((! $noPokemon && ! $noManualPokemon) || (! $noGyms && ! $noManualGyms) || (! $noPokestops && ! $noManualPokestops) || (! $noAddNewNests && ! $noNests) || (!$noAddNewCommunity && ! $noCommunity) || (!$noAddPoi && ! $noPoi)) {
        ?>
        <button class="submit-on-off-button" onclick="$('.submit-on-off-button').toggleClass('on');">
            <i class="fas fa-map-marker-alt submit-to-map" aria-hidden="true"></i>
        </button>
        <div class="submit-modal" style="display:none;">
            <input type="hidden" value="" name="submitLatitude" class="submitLatitude"/>
            <input type="hidden" value="" name="submitLongitude" class="submitLongitude"/>
            <div id="submit-tabs">
                <ul>
                    <?php if (! $noManualPokemon && !$noPokemon) {
            ?>
                        <li><a href="#tab-pokemon"><img src="static/images/pokeball.png"/></a></li>
                    <?php
        } ?>
                    <?php if (! $noManualGyms && !$noGyms) {
            ?>
                        <li><a href="#tab-gym"><img src="static/forts/ingame/Uncontested.png"/></a></li>
                    <?php
        } ?>
                    <?php if (! $noManualPokestops && !$noPokestops) {
            ?>
                        <li><a href="#tab-pokestop"><img src="static/forts/Pstop.png"/></a></li>
                    <?php
        } ?>
                    <?php if (! $noAddNewNests && !$noNests) {
            ?>
                        <li><a href="#tab-nests"><img src="static/images/nest.png"/></a></li>
            <?php
        } ?>
                    <?php if (! $noAddNewCommunity && !$noCommunity) {
            ?>
                        <li><a href="#tab-communities"><img src="static/images/community.png"/></a></li>
                    <?php
        } ?>
                    <?php if (! $noAddPoi && !$noPoi) {
            ?>
                        <li><a href="#tab-poi"><img src="static/images/playground.png"/></a></li>
                    <?php
        } ?>
                </ul>
                <?php if (! $noManualPokemon && !$noPokemon) {
            ?>
                    <div id="tab-pokemon">
                        <input type="hidden" name="pokemonID" class="pokemonID"/>
                        <?php pokemonFilterImages($noPokemonNumbers, 'pokemonSubmitFilter(event)', $pokemonToExclude, 6); ?>
                        <div class="button-container">
                            <button type="button" onclick="manualPokemonData(event);" class="submitting-pokemon">
                                <i class="fas fa-binoculars"></i> <?php echo i8ln('Submit Pokémon'); ?>
                            </button>
                        </div>
                    </div>
                <?php
        } ?>
                <?php if (! $noManualGyms && !$noGyms) {
            ?>
                    <div id="tab-gym">
                        <input type="text" id="gym-name" name="gym-name"
                               placeholder="<?php echo i8ln('Enter Gym Name'); ?>" data-type="forts"
                               class="search-input">
                        <div class="button-container">
                            <button type="button" onclick="manualGymData(event);" class="submitting-gym"><i
                                    class="fas fa-binoculars"></i> <?php echo i8ln('Submit Gym'); ?>
                            </button>
                        </div>
                    </div>
                <?php
        } ?>
                <?php if (! $noManualPokestops && !$noPokestops) {
            ?>
                    <div id="tab-pokestop">
                        <input type="text" id="pokestop-name" name="pokestop-name"
                               placeholder="<?php echo i8ln('Enter Pokéstop Name'); ?>" data-type="pokestop"
                               class="search-input">
                        <div class="button-container">
                            <button type="button" onclick="manualPokestopData(event);" class="submitting-pokestop"><i
                                    class="fas fa-binoculars"></i> <?php echo i8ln('Submit Pokéstop'); ?>
                            </button>
                        </div>
                    </div>
                <?php
        } ?>
                <?php if (! $noAddNewNests && !$noNests) {
            ?>
                    <div id="tab-nests">
                        <input type="hidden" name="pokemonID" class="pokemonID"/>
                        <?php pokemonFilterImages($noPokemonNumbers, 'pokemonSubmitFilter(event)', $excludeNestMons, 7); ?>
                        <div class="button-container">
                            <button type="button" onclick="submitNewNest(event);" class="submitting-nest"><i
                                    class="fas fa-binoculars"></i> <?php echo i8ln('Submit Nest'); ?>
                            </button>
                        </div>
                    </div>
                <?php
        } ?>
                <?php if (! $noAddNewCommunity && !$noCommunity) {
            ?>
                    <div id="tab-communities">
                        <input type="text" name="community-name" class="community-name"
                               placeholder="<?php echo i8ln('Enter Community Name'); ?>" data-type="name"
                   class="search-input">
                        <input type="text" name="community-description" class="community-description"
                               placeholder="<?php echo i8ln('Enter description'); ?>" data-type="description"
                   class="search-input">
                        <input type="text" name="community-invite" class="community-invite"
                               placeholder="<?php echo i8ln('Whatsapp, Telegram, Discord Link'); ?>" data-type="invite-link"
                   class="search-input">
            <h6><center><?php echo i8ln('Link must be valid and start with https://'); ?></center></h6>
                        <div class="button-container">
                            <button type="button" onclick="submitNewCommunity(event);" class="submitting-community">
                                <i class="fas fa-comments"></i> <?php echo i8ln('Submit Community'); ?>
                            </button>
                        </div>
                    </div>
                <?php
        } ?>
                <?php if (! $noAddPoi && !$noPoi) {
            ?>
                    <div id="tab-poi">
                        <input type="text" name="poi-name" class="poi-name"placeholder="<?php echo i8ln('Enter candidate Name'); ?>" data-type="name" class="search-input">
                        <input type="text" name="poi-description" class="poi-description" placeholder="<?php echo i8ln('Enter candidate description'); ?>" data-type="description" class="search-input">
                        <input type="text" name="poi-notes" class="poi-notes" placeholder="<?php echo i8ln('Enter field notes'); ?>" data-type="description" class="search-input">
                        <?php if (! empty($imgurCID)) {
                ?>
                            <div class="upload-button-container">
                                <button type="button"><i class="fas fa-upload"></i> <?php echo i8ln('Upload POI Image') ?></button>
                                <input type="file" id="poi-image" name="poi-image" accept="image/*" class="poi-image" data-type="poi-image" class="search-input" onchange='previewPoiImage(event)'>
                            </div>
                            <center><img id='preview-poi-image' name='preview-poi-image' width="50px" height="auto"></center>
                            <div class="upload-button-container">
                                <button type="button"><i class="fas fa-upload"></i> <?php echo i8ln('Upload Surrounding Image') ?></button>
                                <input type="file" id="poi-surrounding" name="poi-surrounding" accept="image/*" class="poi-surrounding" data-type="poi-surrounding" class="search-input" onchange='previewPoiSurrounding(event)'>
                            </div>
                            <center><img id='preview-poi-surrounding' name='preview-poi-surrounding' width="50px" height="auto" ></center>
                        <?php
            } ?>
                        <div class="button-container">
                            <h6><center><?php echo i8ln('If you submit a POI candidate you agree that your discord username will be shown in the marker label'); ?></center></h6>
                            <button type="button" onclick="submitPoi(event);" class="submitting-poi"><i class="fas fa-comments"></i> <?php echo i8ln('Submit POI candidate'); ?></button>
                        </div>
                    </div>
                <?php
        } ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.9.1/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.full.min.js"></script>
<script src="node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="node_modules/moment/min/moment-with-locales.min.js"></script>
<script src="https://code.createjs.com/soundjs-0.6.2.min.js"></script>
<script src="node_modules/push.js/bin/push.min.js"></script>
<script src="node_modules/long/src/long.js"></script>
<script src="node_modules/leaflet/dist/leaflet.js"></script>
<script src="node_modules/leaflet-geosearch/dist/bundle.min.js"></script>
<script src="static/js/vendor/s2geometry.js"></script>
<script src="static/dist/js/app.min.js"></script>
<script src="static/js/vendor/classie.js"></script>
<script src="node_modules/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script src='static/js/vendor/Leaflet.fullscreen.min.js'></script>
<script src="static/js/vendor/smoothmarkerbouncing.js"></script>
<script src='https://maps.googleapis.com/maps/api/js?key=<?= $gmapsKey ?> ' async defer></script>
<script src="static/js/vendor/Leaflet.GoogleMutant.js"></script>
<script src="static/js/vendor/turf.min.js"></script>
<script>
    var centerLat = <?= $startingLat; ?>;
    var centerLng = <?= $startingLng; ?>;
    var locationSet = <?= $locationSet; ?>;
    var motd = <?php echo $noMotd ? 'false' : 'true' ?>;
    var motdContent = <?php echo json_encode($motdContent) ?>;
    var showMotdOnlyOnce = <?php echo $showMotdOnlyOnce === true ? 'true' : 'false' ?>;
    var zoom<?php echo $zoom ? " = " . $zoom : null; ?>;
    var encounterId<?php echo $encounterId ? " = '" . $encounterId . "'" : null; ?>;
    var defaultZoom = <?= $defaultZoom; ?>;
    var maxZoom = <?= $maxZoomIn; ?>;
    var minZoom = <?= $maxZoomOut; ?>;
    var maxLatLng = <?= $maxLatLng; ?>;
    var disableClusteringAtZoom = <?= $disableClusteringAtZoom; ?>;
    var zoomToBoundsOnClick = <?= $zoomToBoundsOnClick; ?>;
    var maxClusterRadius = <?= $maxClusterRadius; ?>;
    var spiderfyOnMaxZoom = <?= $spiderfyOnMaxZoom; ?>;
    var mapStyle = '<?php echo $mapStyle ?>';
    var gmapsKey = '<?php echo $gmapsKey ?>';
    var mBoxKey = '<?php echo $mBoxKey ?>';
    var noCustomTileServer = <?php echo $noCustomTileServer === true ? 'true' : 'false' ?>;
    var customTileServerAddress = '<?php echo $customTileServerAddress ?>';
    var hidePokemon = <?php echo $noHidePokemon ? '[]' : $hidePokemon ?>;
    var excludeMinIV = <?php echo $noExcludeMinIV ? '[]' : $excludeMinIV ?>;
    var minIV = <?php echo $noMinIV ? '""' : $minIV ?>;
    var minLevel = <?php echo $noMinLevel ? '""' : $minLevel ?>;
    var notifyPokemon = <?php echo $noNotifyPokemon ? '[]' : $notifyPokemon ?>;
    var notifyRarity = <?php echo $noNotifyRarity ? '[]' : $notifyRarity ?>;
    var notifyIv = <?php echo $noNotifyIv ? '""' : $notifyIv ?>;
    var notifyLevel = <?php echo $noNotifyLevel ? '""' : $notifyLevel ?>;
    var notifyRaid = <?php echo $noNotifyRaid ? 0 : $notifyRaid ?>;
    var notifyBounce = <?php echo $notifyBounce ?>;
    var notifyNotification = <?php echo $notifyNotification ?>;
    var enableRaids = <?php echo $noRaids ? 'false' : $enableRaids ?>;
    var activeRaids = <?php echo $activeRaids ?>;
    var noActiveRaids = <?php echo $noActiveRaids === true ? 'true' : 'false' ?>;
    var noMinMaxRaidLevel = <?php echo $noMinMaxRaidLevel === true ? 'true' : 'false' ?>;
    var noTeams = <?php echo $noTeams === true ? 'true' : 'false' ?>;
    var noOpenSpot = <?php echo $noOpenSpot === true ? 'true' : 'false' ?>;
    var noMinMaxFreeSlots = <?php echo $noMinMaxFreeSlots === true ? 'true' : 'false' ?>;
    var noLastScan = <?php echo $noLastScan === true ? 'true' : 'false' ?>;
    var minRaidLevel = <?php echo $minRaidLevel ?>;
    var maxRaidLevel = <?php echo $maxRaidLevel ?>;
    var hideRaidboss = <?php echo $noRaids ? '[]' : $hideRaidboss ?>;
    var hideRaidegg = <?php echo $noRaids ? '[]' : $hideRaidegg ?>;
    var enableGyms = <?php echo $noGyms ? 'false' : $enableGyms ?>;
    var enableNests = <?php echo $noNests ? 'false' : $enableNests ?>;
    var enableCommunities = <?php echo $noCommunity ? 'false' : $enableCommunities ?>;
    var enablePokemon = <?php echo $noPokemon ? 'false' : $enablePokemon ?>;
    var enablePokestops = <?php echo $noPokestops ? 'false' : $enablePokestops ?>;
    var enableLured = <?php echo $noLures ? 'false' : $enableLured ?>;
    var enableRocket = <?php echo $noTeamRocket ? 'false' : $enableTeamRocket ?>;
    var noQuests = <?php echo $noQuests === true ? 'true' : 'false' ?>;
    var noLures = <?php echo $noLures === true ? 'true' : 'false' ?>;
    var noTeamRocket = <?php echo $noTeamRocket === true ? 'true' : 'false' ?>;
    var hideGrunts = <?php echo $noTeamRocket ? '[]' : $hideGrunts ?>;
    var noAllPokestops = <?php echo $noAllPokestops === true ? 'true' : 'false' ?>;
    var enableAllPokestops = <?php echo $noAllPokestops ? 'false' : $enableAllPokestops ?>;
    var enableQuests = <?php echo $noQuests ? 'false' : $enableQuests ?>;
    var hideQuestsPokemon = <?php echo $noQuestsPokemon ? '[]' : $hideQuestsPokemon ?>;
    var hideQuestsItem = <?php echo $noQuestsItems ? '[]' : $hideQuestsItem ?>;
    var enableNewPortals = <?php echo (($map != "monocle") || ($fork == "alternate")) ? $enableNewPortals : 0 ?>;
    var enableWeatherOverlay = <?php echo ! $noWeatherOverlay ? $enableWeatherOverlay : 'false' ?>;
    var enableSpawnpoints = <?php echo $noSpawnPoints ? 'false' : $enableSpawnPoints ?>;
    var enableLiveScan = <?php echo $noLiveScanLocation ? 'false' : $enableLiveScan ?>;
    var deviceOfflineAfterSeconds = <?php echo $deviceOfflineAfterSeconds ?>;
    var enableRanges = <?php echo $noRanges ? 'false' : $enableRanges ?>;
    var enableScanPolygon = <?php echo $noScanPolygon ? 'false' : $enableScanPolygon ?>;
    var geoJSONfile = '<?php echo $noScanPolygon ? '' : $geoJSONfile ?>';
    var notifySound = <?php echo $noNotifySound ? 'false' : $notifySound ?>;
    var criesSound = <?php echo $noCriesSound ? 'false' : $criesSound ?>;
    var enableStartMe = <?php echo $noStartMe ? 'false' : $enableStartMe ?>;
    var enableStartLast = <?php echo (! $noStartLast && $enableStartMe === 'false') ? $enableStartLast : 'false' ?>;
    var enableFollowMe = <?php echo $noFollowMe ? 'false' : $enableFollowMe ?>;
    var enableSpawnArea = <?php echo $noSpawnArea ? 'false' : $enableSpawnArea ?>;
    var iconSize = <?php echo $iconSize ?>;
    var iconNotifySizeModifier = <?php echo $iconNotifySizeModifier ?>;
    var locationStyle = '<?php echo $locationStyle ?>';
    var gymStyle = '<?php echo $gymStyle ?>';
    var spriteFileLarge = '<?php echo $copyrightSafe ? 'static/icons-safe-1-bigger.png' : 'static/icons-im-1-bigger.png' ?>';
    var weatherSpritesSrc = '<?php echo $copyrightSafe ? 'static/sprites-safe/' : 'static/sprites-pokemon/' ?>';
    var icons = '<?php echo $copyrightSafe ? 'static/icons-safe/' : $iconRepository ?>';
    var weatherColors = <?php echo json_encode($weatherColors); ?>;
    var s2Colors = <?php echo json_encode($s2Colors); ?>;
    var mapType = '<?php echo strtolower($map); ?>';
    var mapFork = '<?php echo strtolower($fork); ?>';
    var triggerGyms = <?php echo $triggerGyms ?>;
    var noExGyms = <?php echo $noExGyms === true ? 'true' : 'false' ?>;
    var onlyTriggerGyms = <?php echo $onlyTriggerGyms === true ? 'true' : 'false' ?>;
    var showBigKarp = <?php echo $noBigKarp === true ? 'true' : 'false' ?>;
    var showTinyRat = <?php echo $noTinyRat === true ? 'true' : 'false' ?>;
    var hidePokemonCoords = <?php echo $hidePokemonCoords === true ? 'true' : 'false' ?>;
    var hidePokestopCoords = <?php echo $hidePokestopCoords === true ? 'true' : 'false' ?>;
    var hideGymCoords = <?php echo $hideGymCoords === true ? 'true' : 'false' ?>;
    var hideNestCoords = <?php echo $hideNestCoords === true ? 'true' : 'false' ?>;
    var directionProvider = '<?php echo $noDirectionProvider === true ? $directionProvider : 'google' ?>';
    var exEligible = <?php echo $noExEligible === true ? 'false' : $exEligible  ?>;
    var raidBossActive = <?php echo json_encode($raidBosses); ?>;
    var manualRaids = <?php echo $noManualRaids === true ? 'false' : 'true' ?>;
    var pokemonReportTime = <?php echo $pokemonReportTime === true ? 'true' : 'false' ?>;
    var noDeleteGyms = <?php echo $noDeleteGyms === true ? 'true' : 'false' ?>;
    var noToggleExGyms = <?php echo $noToggleExGyms === true ? 'true' : 'false' ?>;
    var defaultUnit = '<?php echo $defaultUnit ?>';
    var noDeletePokestops = <?php echo $noDeletePokestops === true ? 'true' : 'false' ?>;
    var noDeleteNests = <?php echo $noDeleteNests === true ? 'true' : 'false' ?>;
    var noManualNests = <?php echo $noManualNests === true ? 'true' : 'false' ?>;
    var noManualQuests = <?php echo $noManualQuests === true ? 'true' : 'false' ?>;
    var noAddNewCommunity = <?php echo $noAddNewCommunity === true ? 'true' : 'false' ?>;
    var noDeleteCommunity = <?php echo $noDeleteCommunity === true ? 'true' : 'false' ?>;
    var noEditCommunity = <?php echo $noEditCommunity === true ? 'true' : 'false' ?>;
    var login = <?php echo $noNativeLogin === false || $noDiscordLogin === false  ? 'true' : 'false' ?>;
    var expireTimestamp = <?php echo isset($_SESSION['user']->expire_timestamp) ? $_SESSION['user']->expire_timestamp : 0 ?>;
    var timestamp = <?php echo time() ?>;
    var noRenamePokestops = <?php echo $noRenamePokestops === true ? 'true' : 'false' ?>;
    var noRenameGyms = <?php echo $noRenameGyms === true ? 'true' : 'false' ?>;
    var noConvertPokestops = <?php echo $noConvertPokestops === true ? 'true' : 'false' ?>;
    var noWhatsappLink = <?php echo $noWhatsappLink === true ? 'true' : 'false' ?>;
    var enablePoi = <?php echo $noPoi ? 'false' : $enablePoi ?>;
    var enablePortals = <?php echo $noPortals ? 'false' : $enablePortals ?>;
    var noDeletePoi = <?php echo $noDeletePoi === true ? 'true' : 'false' ?>;
    var noEditPoi = <?php echo $noEditPoi === true ? 'true' : 'false' ?>;
    var noMarkPoi = <?php echo $noMarkPoi === true ? 'true' : 'false' ?>;
    var noPortals = <?php echo $noPortals === true ? 'true' : 'false' ?>;
    var noPoi = <?php echo $noPoi === true ? 'true' : 'false' ?>;
    var enableS2Cells = <?php echo $noS2Cells ? 'false' : $enableS2Cells ?>;
    var enableLevel13Cells = <?php echo $noS2Cells ? 'false' : $enableLevel13Cells ?>;
    var enableLevel14Cells = <?php echo $noS2Cells ? 'false' : $enableLevel14Cells ?>;
    var enableLevel17Cells = <?php echo $noS2Cells ? 'false' : $enableLevel17Cells ?>;
    var noDeletePortal = <?php echo $noDeletePortal === true ? 'true' : 'false' ?>;
    var noConvertPortal = <?php echo $noConvertPortal === true ? 'true' : 'false' ?>;
    var markPortalsAsNew = <?php echo $markPortalsAsNew ?>;
    var copyrightSafe = <?php echo $copyrightSafe === true ? 'true' : 'false' ?>;
    var forcedTileServer = <?php echo $forcedTileServer === true ? 'true' : 'false' ?>;
    var noRarityDisplay = <?php echo $noRarityDisplay === true ? 'true' : 'false' ?>;
    var noWeatherIcons = <?php echo $noWeatherIcons === true ? 'true' : 'false' ?>;
    var noIvShadow = <?php echo $no100IvShadow === true ? 'true' : 'false' ?>;
    var noRaidTimer = <?php echo $noRaidTimer === true ? 'true' : 'false' ?>;
    var enableRaidTimer = <?php echo $noRaidTimer ? 'false' : $enableRaidTimer ?>;
    var noRocketTimer = <?php echo $noTeamRocketTimer === true ? 'true' : 'false' ?>;
    var enableRocketTimer = <?php echo $noTeamRocketTimer ? 'false' : $enableTeamRocketTimer ?>;
    var enableNestPolygon = <?php echo $noNestPolygon ? 'false' : $enableNestPolygon ?>;
    var nestGeoJSONfile = '<?php echo $noNestPolygon ? '' : $nestGeoJSONfile ?>';
    var noCostumeIcons = <?php echo $noCostumeIcons === true ? 'true' : 'false' ?>;
    var queryInterval = <?php echo $queryInterval ?>;
    var noInvasionEncounterData = <?php echo $noTeamRocketEncounterData === true ? 'true' : 'false' ?>;
    var numberOfPokemon = <?php echo $numberOfPokemon; ?>;
    var numberOfItem = <?php echo $numberOfItem; ?>;
    var numberOfGrunt = <?php echo $numberOfGrunt; ?>;
    var numberOfEgg = <?php echo $numberOfEgg; ?>;
    var noRaids = <?php echo $noRaids === true ? 'true' : 'false' ?>;
    var letItSnow = <?php echo $letItSnow === true ? 'true' : 'false' ?>;
    var makeItBang = <?php echo $makeItBang === true ? 'true' : 'false' ?>;
    var defaultDustAmount = <?php echo $defaultDustAmount; ?>;
    var noDarkMode = <?php echo $noDarkMode === true ? 'true' : 'false' ?>;
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="static/dist/js/map.common.min.js"></script>
<script src="static/dist/js/map.min.js"></script>
<script src="static/dist/js/stats.min.js"></script>
<script>
$( document ).ready(function() {
    initMap()
})
</script>
</body>
</html>
