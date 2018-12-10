<?php
if ( ! file_exists( 'config/config.php' ) ) {
    http_response_code( 500 );
    die( "<h1>Config file missing</h1><p>Please ensure you have created your config file (<code>config/config.php</code>).</p>" );
}
include( 'config/config.php' );
$zoom        = ! empty( $_GET['zoom'] ) ? $_GET['zoom'] : null;
$encounterId = ! empty( $_GET['encId'] ) ? $_GET['encId'] : null;
if ( ! empty( $_GET['lat'] ) && ! empty( $_GET['lon'] ) ) {
    $startingLat = $_GET['lat'];
    $startingLng = $_GET['lon'];
    $locationSet = 1;
} else {
    $locationSet = 0;
}
if ( $blockIframe ) {
    header( 'X-Frame-Options: DENY' );
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
    if ( $faviconPath != "" ) {
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
    function pokemonFilterImages( $noPokemonNumbers, $onClick = '', $pokemonToExclude = array(), $num = 0 ) {
        global $mons, $copyrightSafe, $iconRepository;
        if ( empty( $mons ) ) {
            $json = file_get_contents( 'static/dist/data/pokemon.min.json' );
            $mons = json_decode( $json, true );
        }
        echo '<div class="pokemon-list-cont" id="pokemon-list-cont-' . $num . '"><input type="hidden" class="search-number" value="' . $num . '" /><input class="search search-input" placeholder="' . i8ln( "Search Name, ID & Type" ) . '" /><div class="pokemon-list list">';
        $i = 0;
        $z = 0;
        foreach ( $mons as $k => $pokemon ) {
            $type = '';
            $name = $pokemon['name'];
            foreach ( $pokemon['types'] as $t ) {
                $type .= $t['type'];
            }

            if ( ! in_array( $k, $pokemonToExclude ) ) {
                if ( $k > 493 ) {
                    break;
		}
		if ( $k <= 9 ) {
                    $id = "00$k";
                } else if ( $k <= 99 ) {
                    $id = "0$k";
                } else {
                    $id = $k;
		}
		if ( ! $copyrightSafe ) {
                    echo '<span class="pokemon-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '"><span style="display:none" class="types">' . i8ln( $type ) . '</span><span style="display:none" class="name">' . i8ln( $name ) . '</span><span style="display:none" class="id">$k</span><img src="' . $iconRepository . 'pokemon_icon_' . $id . '_00.png" style="width:48px;height:48px;"/>';
		} else {
                    echo '<span class="pokemon-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '"><span style="display:none" class="types">' . i8ln( $type ) . '</span><span style="display:none" class="name">' . i8ln( $name ) . '</span><span style="display:none" class="id">$k</span><img src="static/icons-safe/pokemon_icon_' . $id . '_00.png" style="width:48px;height:48px;"/>';
                }
                if ( ! $noPokemonNumbers ) {
                    echo "<span class='pokemon-number'>" . $k . "</span>";
                }
                echo "</span>";

            }
        }
        echo '</div></div>';
        ?>
        <script>
            var options = {
                valueNames: ['name', 'types', 'id']
            };
            var monList = new List('pokemon-list-cont-<?php echo $num;?>', options);
        </script>
        <?php
    }

    function itemFilterImages( $noItemNumbers, $onClick = '', $itemsToExclude = array(), $num = 0 ) {
        global $items, $copyrightSafe, $iconRepository;
        if ( empty( $items ) ) {
            $json = file_get_contents( 'static/dist/data/items.min.json' );
            $items = json_decode( $json, true );
        }
        echo '<div class="item-list-cont" id="item-list-cont-' . $num . '"><input type="hidden" class="search-number" value="' . $num . '" /><input class="search search-input" placeholder="' . i8ln( "Search Name & ID" ) . '" /><div class="item-list list">';
        $i = 0;
        $z = 0;
        foreach ( $items as $k => $item ) {
            $name = $item['name'];

            if ( ! in_array( $k, $itemsToExclude ) ) {
		if ( ! $copyrightSafe ) {
                    echo '<span class="item-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '"><span style="display:none" class="name">' . i8ln( $name ) . '</span><span style="display:none" class="id">$k</span><img src="' . $iconRepository . 'rewards/reward_' . $k . '_1.png" style="width:48px;height:48px;"/>';
		} else {
                    echo '<span class="item-icon-sprite" data-value="' . $k . '" onclick="' . $onClick . '"><span style="display:none" class="name">' . i8ln( $name ) . '</span><span style="display:none" class="id">$k</span><img src="static/icons-safe/rewards/reward_' . $k . '_1.png" style="width:48px;height:48px;"/>';
                }
                if ( ! $noItemNumbers ) {
                    echo '<span class="item-number">' . $k . '</span>';
                }
                echo "</span>";

            }
        }
        echo '</div></div>';
        ?>
        <script>
            var options = {
                valueNames: ['name', 'id']
            };
            var itemList = new List('item-list-cont-<?php echo $num;?>', options);
        </script>
        <?php
    }

    ?>

    <?php
    if ( $gAnalyticsId != "" ) {
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
    if ( $piwikUrl != "" && $piwikSiteId != "" ) {
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
    <script>
        var token = '<?php echo ( ! empty( $_SESSION['token'] ) ) ? $_SESSION['token'] : ""; ?>';
    </script>
    <link href="https://unpkg.com/leaflet-geosearch@2.7.0/assets/css/leaflet.css" rel="stylesheet" />
    <link rel="stylesheet" href="static/dist/css/app.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="static/js/vendor/modernizr.custom.js"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
</head>
<body id="top">
<div class="wrapper">
    <!-- Header -->
    <header id="header">
        <a href="#nav"><span class="label"><?php echo i8ln( 'Options' ) ?></span></a>

        <h1><a href="#"><?= $title ?><img src="<?= $raidmapLogo ?>" height="35" width="auto" border="0" style="float: right; margin-left: 5px; margin-top: 10px;"></a></h1>
        <?php
        if ( $discordUrl != "" ) {
            echo '<a href="' . $discordUrl . '" target="_blank" style="margin-bottom: 5px; vertical-align: middle;padding:0 5px;">
            <img src="static/images/discord.png" border="0" style="float: right;">
        </a>';
        }
        if ( $paypalUrl != "" ) {
            echo '<a href="' . $paypalUrl . '" target="_blank" style="margin-bottom: 5px; vertical-align: middle; padding:0 5px;">
            <img src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_donate_74x21.png" border="0" name="submit"
                 title="PayPal - The safer, easier way to pay online!" alt="Donate" style="float: right;">
        </a>';
        }
        ?>
        <?php if ( ! $noWeatherOverlay ) {
            ?>
            <div id="currentWeather"></div>
            <?php
        } ?>
        
        <?php
        if ($noNativeLogin === false || $noDiscordLogin === false) {
            if (isset($_COOKIE["LoginCookie"])) {
                if (validateCookie($_COOKIE["LoginCookie"]) === false) {
                    header("Location: .");
                }
            }
            if (!empty($_SESSION['user']->id)) {
                $info = $manualdb->query(
                    "SELECT expire_timestamp FROM users WHERE id = :id AND login_system = :login_system", [
                        ":id" => $_SESSION['user']->id,
                        ":login_system" => $_SESSION['user']->login_system
                    ]
                )->fetch();

                $_SESSION['user']->expire_timestamp = $info['expire_timestamp'];

                if (!empty($_SESSION['user']->updatePwd) && $_SESSION['user']->updatePwd === 1) {
                    header("Location: ./user");
                    die();
                }
                
                if ($info['expire_timestamp'] > time()) {
                    $color = "green";
                } else {
                    header('Location: ./logout.php');
                }

                echo "<span style='color: {$color};'>" . substr($_SESSION['user']->user, 0, 30) . "...</span>";
            } else {
                echo "<a href='./user'>" . i8ln('Login') . "</a>";
            }
        }
        ?>
        <?php if ( ! $noStatsToggle ) {
            ?>
        <a href="#stats" id="statsToggle" class="statsNav" style="float: right;"><span
                class="label"><?php echo i8ln( 'Stats' ) ?></span></a>
            <?php
        } ?>
    </header>
    <!-- NAV -->
    <nav id="nav">
        <div id="nav-accordion">
            <?php
            if ( ! $noPokemon || ! $noNests ) {
                if ( ! $noNests ) {
                ?>
                <h3><?php echo i8ln( 'Pokemon &amp; Nests' ) ?></h3>
                <?php
                } else {
                ?>
                <h3><?php echo i8ln( 'Pokemon' ) ?></h3>
                <?php
                } ?>
                <div>
                <?php
                if ( ! $noPokemon ) {
                    echo '<div class=" form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln( 'Pokemon' ) . '</h3>
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
                if ( ! $noNests ) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln( 'Nests' ) . '</h3>
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
                    <div id="pokemon-filter-wrapper" style="display:none">
                        <?php
                        if ( ! $noTinyRat ) {
                            ?>
                            <div class="form-control switch-container">
                                <h3><?php echo i8ln( 'Tiny Rats' ) ?></h3>
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
                        if ( ! $noBigKarp ) {
                            ?>
                            <div class="form-control switch-container">
                                <h3><?php echo i8ln( 'Big Karp' ) ?></h3>
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
                            if ( ! $noMinIV ) {
                                echo '<div class="form-control" >
                            <label for="min-iv">
                                <h3>' . i8ln( 'Min IV' ) . '</h3>
                                <input id="min-iv" type="number" min="0" max="100" name="min-iv" placeholder="' . i8ln( 'Min IV' ) . '"/>
                            </label>
                        </div>';
                            } ?>
                            <?php
                            if ( ! $noMinLevel ) {
                                echo '<div class="form-control">
                            <label for="min-level">
                                <h3>' . i8ln( 'Min Lvl' ) . '</h3>
                                <input id="min-level" type="number" min="0" max="100" name="min-level" placeholder="' . i8ln( 'Min Lvl' ) . '"/>
                            </label>
                        </div>';
                            } ?>
                        </div>
                        <div id="tabs">
                            <ul>
                                <?php
                                if ( ! $noHidePokemon ) {
                                    ?>
                                    <li><a href="#tabs-1"><?php echo i8ln( 'Hide Pokemon' ) ?></a></li>
                                    <?php
                                } ?>
                                <?php
                                if ( ! $noExcludeMinIV ) {
                                    ?>
                                    <li><a href="#tabs-2"><?php echo i8ln( 'Excl. Min IV/Lvl' ) ?></a></li>
                                    <?php
                                } ?>
                            </ul>
                            <?php
                            if ( ! $noHidePokemon ) {
                                ?>
                                <div id="tabs-1">
                                    <div class="form-control hide-select-2">
                                        <label for="exclude-pokemon">
                                            <div class="pokemon-container">
                                                <input id="exclude-pokemon" type="text" readonly="true">
                                                <?php
                                                pokemonFilterImages( $noPokemonNumbers, '', [], 2 ); ?>
                                            </div>
                                            <a href="#" class="select-all"><?php echo i8ln( 'All' ) ?>
                                                <div>
                                            </a><a href="#" class="hide-all"><?php echo i8ln( 'None' ) ?></a>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <?php
                            if ( ! $noExcludeMinIV ) {
                                ?>
                                <div id="tabs-2">
                                    <div class="form-control hide-select-2">
                                        <label for="exclude-min-iv">
                                            <div class="pokemon-container">
                                                <input id="exclude-min-iv" type="text" readonly="true">
                                                <?php
                                                pokemonFilterImages( $noPokemonNumbers, '', [], 3 ); ?>
                                            </div>
                                            <a href="#" class="select-all"><?php echo i8ln( 'All' ) ?>
                                                <div>
                                            </a><a href="#" class="hide-all"><?php echo i8ln( 'None' ) ?></a>
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
            if ( ! $noPokestops ) {
                if ( ! $noQuests ) {
                ?>
		<h3><?php echo i8ln( 'Pokestops &amp; Quests' ); ?></h3>
                <?php
                } else {
                ?>
		<h3><?php echo i8ln( 'Pokestops' ); ?></h3>
                <?php
                } ?>
		<div>
                <?php
                if ( ! $noPokestops ) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln( 'Pokestops' ) . '</h3>
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
                if ( ! $noLures ) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln( 'Lures only' ) . '</h3>
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
                if ( ! $noQuests ) {
                    echo '<div class="form-control switch-container" style="float:none;height:35px;margin-bottom:0px;">
                    <h3>' . i8ln( 'Quests only' ) . '</h3>
                    <div class="onoffswitch">
                        <input id="quests-switch" type="checkbox" name="quests-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="quests-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
		?>
                    <div id="quests-filter-wrapper" style="display:none">
                        <div id="quests-tabs">
                            <ul>
                                <?php
                                if ( ! $noQuestsPokemon ) {
                                    ?>
                                    <li><a href="#tabs-1"><?php echo i8ln( 'Hide pokémon' ) ?></a></li>
                                    <?php
                                } ?>
                                <?php
                                if ( ! $noQuestsItems ) {
                                    ?>
                                    <li><a href="#tabs-2"><?php echo i8ln( 'Hide items' ) ?></a></li>
                                    <?php
                                } ?>
	                    </ul>
                            <?php
                            if ( ! $noQuestsPokemon ) {
                                ?>
                                <div id="tabs-1">
                                    <div class="form-control hide-select-2">
                                        <label for="exclude-quests-pokemon">
                                            <div class="quest-pokemon-container">
                                                <input id="exclude-quests-pokemon" type="text" readonly="true">
                                                <?php
                                                pokemonFilterImages( $noPokemonNumbers, '', $excludeQuestsPokemon, 8 ); ?>
                                            </div>
                                            <a href="#" class="select-all"><?php echo i8ln( 'All' ) ?>
                                                <div>
                                            </a><a href="#" class="hide-all"><?php echo i8ln( 'None' ) ?> </a>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <?php
                            if ( ! $noQuestsItems ) {
                                ?>
                                <div id="tabs-2">
                                    <div class="form-control hide-select-2">
                                        <label for="exclude-quests-item">
                                            <div class="quest-item-container">
                                                <input id="exclude-quests-item" type="text" readonly="true">
                                                <?php
                                                itemFilterImages( $noItemNumbers, '', $excludeQuestsItem, 9 ); ?>
                                            </div>
                                            <a href="#" class="select-all-item"><?php echo i8ln( 'All' ) ?>
                                                <div>
                                            </a><a href="#" class="hide-all-item"><?php echo i8ln( 'None' ) ?> </a>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            } ?>
                        </div>
                        <div class="dustslider">
			    <input type="range" min="0" max="2000" value="500" class="slider" id="dustrange">
			    <p><?php echo i8ln( 'Show stardust ' ) ?><span id="dustvalue"></span></p>
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
            if ( ! $noCommunity ) {
                ?>
                <h3><?php echo i8ln( 'Communities' ); ?></h3>
		<div>
                <?php
                if ( ! $noCommunity ) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln( 'Communities' ) . '</h3>
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
            if ( ! $noRaids || ! $noGyms ) {
                ?>
                <h3><?php echo i8ln( 'Gym &amp; Raid' ); ?></h3>
                <div>
                    <?php
                    if ( ! $noRaids ) {
                        echo '<div class="form-control switch-container" id="raids-wrapper">
                    <h3>' . i8ln( 'Raids' ) . '</h3>
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
                        <div class="form-control switch-container" id="active-raids-wrapper">
                            <h3><?php echo i8ln( 'Only Active Raids' ) ?></h3>
                            <div class="onoffswitch">
                                <input id="active-raids-switch" type="checkbox" name="active-raids-switch"
                                       class="onoffswitch-checkbox" checked>
                                <label class="onoffswitch-label" for="active-raids-switch">
                                    <span class="switch-label" data-on="On" data-off="Off"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-control switch-container" id="min-level-raids-filter-wrapper">
                            <h3><?php echo i8ln( 'Minimum Raid Level' ) ?></h3>
                            <select name="min-level-raids-filter-switch" id="min-level-raids-filter-switch">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-control switch-container" id="max-level-raids-filter-wrapper">
                            <h3><?php echo i8ln( 'Maximum Raid Level' ) ?></h3>
                            <select name="max-level-raids-filter-switch" id="max-level-raids-filter-switch">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <?php
                    if ( ! $noGymSidebar && ( ! $noGyms || ! $noRaids ) ) {
                        echo '<div id="gym-sidebar-wrapper" class="form-control switch-container">
                    <h3>' . i8ln( 'Use Gym Sidebar' ) . '</h3>
                    <div class="onoffswitch">
                        <input id="gym-sidebar-switch" type="checkbox" name="gym-sidebar-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="gym-sidebar-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                    } ?>
                    <?php
                    if ( ! $noGyms ) {
                        echo '<div class="form-control switch-container">
                    <h3>' . i8ln( 'Gyms' ) . '</h3>
                    <div class="onoffswitch">
                        <input id="gyms-switch" type="checkbox" name="gyms-switch" class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="gyms-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
		    } ?>
                    <?php
                    if ( ! $hideIfManual ) {
                        echo '<div id="gyms-filter-wrapper" style="display:none">
                        <div class="form-control switch-container" id="team-gyms-only-wrapper">
                            <h3>' . i8ln( 'Team' ) . '</h3>
                            <select name="team-gyms-filter-switch" id="team-gyms-only-switch">
                                <option value="0">' . i8ln( 'All' ) . '</option>
                                <option value="1">' . i8ln( 'Mystic' ) . '</option>
                                <option value="2">' . i8ln( 'Valor' ) . '</option>
                                <option value="3">' . i8ln( 'Instinct' ) . '</option>
                            </select>
			</div>
                        <div class="form-control switch-container" id="open-gyms-only-wrapper">
                            <h3>' . i8ln( 'Open Spot' ) . '</h3>
                            <div class="onoffswitch">
                                <input id="open-gyms-only-switch" type="checkbox" name="open-gyms-only-switch"
                                       class="onoffswitch-checkbox" checked>
                                <label class="onoffswitch-label" for="open-gyms-only-switch">
                                    <span class="switch-label" data-on="On" data-off="Off"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-control switch-container" id="min-level-gyms-filter-wrapper">
                            <h3>' . i8ln( 'Minimum Free Slots' ) . '</h3>
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
                            <h3>' . i8ln( 'Maximum Free Slots' ) . '</h3>
                            <select name="max-level-gyms-filter-switch" id="max-level-gyms-filter-switch">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="form-control switch-container" id="last-update-gyms-wrapper">
                            <h3>' . i8ln( 'Last Scan' ) . '</h3>
                            <select name="last-update-gyms-switch" id="last-update-gyms-switch">
                                <option value="0">' . i8ln( 'All' ) . '</option>
                                <option value="1">' . i8ln( 'Last Hour' ) . '</option>
                                <option value="6">' . i8ln( 'Last 6 Hours' ) . '</option>
                                <option value="12">' . i8ln( 'Last 12 Hours' ) . '</option>
                                <option value="24">' . i8ln( 'Last 24 Hours' ) . '</option>
                                <option value="168">' . i8ln( 'Last Week' ) . '</option>
                            </select>
                        </div>
		    </div>';
                    }
                    ?>
                    <div id="gyms-raid-filter-wrapper" style="display:none">
                        <?php
                        if ( ( $fork === "alternate" || $map === "rdm" || ( $map === "rm" && $fork !== "sloppy" ) ) && ! $noExEligible ) {
                            echo '<div class="form-control switch-container" id="ex-eligible-wrapper">
                                <h3>' . i8ln( 'EX Eligible Only' ) . '</h3>
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
            if ( ! $noPortals || ! $noS2Cells ) {
                ?>
                <h3><?php echo i8ln( 'Ingress / S2Cell' ); ?></h3>
		<div>
                <?php
                if ( ! $noPortals ) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln( 'Portals' ) . '</h3>
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
                        <option value = "0"> ' . i8ln( 'All' ) . '</option>
                        <option value = "1"> ' . i8ln( 'Only new' ) . ' </option>
                    </select>
                </div>';
		} ?>
                <?php
                if ( ! $noS2Cells ) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln( 'Show S2 Cells' ) . '</h3>
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
                        <h3>' . i8ln( 'EX trigger Cells' ) . '</h3>
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
                        <h3>' . i8ln( 'Gym placement Cells' ) . '</h3>
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
                        <h3>' . i8ln( 'Pokestop placement Cells' ) . '</h3>
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
            if ( ! $noSearchLocation || ! $noNests || ! $noStartMe || ! $noStartLast || ! $noFollowMe || ! $noPokestops || ! $noScannedLocations || ! $noSpawnPoints || ! $noRanges || ! $noWeatherOverlay || ! $noSpawnArea ) {
                if ( ! $noSearchLocation ) {
                    echo '<h3>' . i8ln( 'Location &amp; Search' ) . '</h3>
                    <div>';
                } else {
                    echo '<h3>' . i8ln( 'Location' ) . '</h3>
                    <div>';
		} ?>
                <?php
                if ( $map != "monocle" && ! $noScannedLocations ) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln( 'Scanned Locations' ) . ' </h3>
                    <div class="onoffswitch">
                        <input id = "scanned-switch" type = "checkbox" name = "scanned-switch" class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="scanned-switch">
                            <span class="switch-label" data - on = "On" data - off = "Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                } ?>
                <?php
                if ( ! $noWeatherOverlay ) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln( 'Weather Conditions' ) . ' </h3>
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
                if ( ! $noSpawnPoints ) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln( 'Spawn Points' ) . ' </h3>
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
                if ( ! $noRanges ) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln( 'Ranges' ) . '</h3>
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
                if ( ! $noScanPolygon ) {
                    echo '<div class="form-control switch-container">
                    <h3>' . i8ln( 'Scan Areas' ) . '</h3>
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
                if ( ! $noSearchLocation ) {
                    echo '<div class="form-control switch-container" style="display:{{is_fixed}}">
                <label for="next-location">
		    <h3>' . i8ln( 'Change search location' ) . '</h3>
                    <form id ="search-places">
		    <input id="next-location" type="text" name="next-location" placeholder="' . i8ln( 'Change search location' ) . '">
                    <ul id="search-places-results" class="search-results places-results"></ul>
                    </form>
                </label>
            </div>';
                } ?>
                <?php
                if ( ! $noStartMe ) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln( 'Start map at my position' ) . ' </h3>
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
                if ( ! $noStartLast ) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln( 'Start map at last position' ) . ' </h3>
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
                if ( ! $noFollowMe ) {
                    echo '<div class="form-control switch-container">
                    <h3> ' . i8ln( 'Follow me' ) . ' </h3>
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
                if ( ! $noSpawnArea ) {
                    echo '<div id="spawn-area-wrapper" class="form-control switch-container">
                <h3> ' . i8ln( 'Spawn area' ) . ' </h3>
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
            if ( ! $noNotifyPokemon || ! $noNotifyRarity || ! $noNotifyIv || ! $noNotifyLevel || ! $noNotifySound || ! $noNotifyRaid || ! $noNotifyBounce || ! $noNotifyNotification ) {
                echo '<h3>' . i8ln( 'Notification' ) . '</h3>
            <div>';
            }
            ?>
            <?php
            if ( ! $noNotifyPokemon ) {
                echo '<div class="form-control hide-select-2">
                    <label for="notify-pokemon">
                        <h3>' . i8ln( 'Notify of Pokemon' ) . '</h3><a href="#" class="select-all">All</a>/<a href="#" class="hide-all">None</a>
                        <div style="max-height:165px;overflow-y:auto;">
                            <input id="notify-pokemon" type="text" readonly="true"/>';
                pokemonFilterImages( $noPokemonNumbers, '', [], 4 );
                echo '</div>
                    </label>
                </div>';
            }
            ?>
            <?php
            if ( ! $noNotifyRarity ) {
                echo '<div class="form-control">
                <label for="notify-rarity">
                    <h3>' . i8ln( 'Notify of Rarity' ) . '</h3>
                    <div style="max-height:165px;overflow-y:auto">
                        <select id="notify-rarity" multiple="multiple"></select>
                    </div>
                </label>
            </div>';
            }
            ?>
            <?php
            if ( ! $noNotifyIv ) {
                echo '<div class="form-control">
                <label for="notify-perfection">
                    <h3>' . i8ln( 'Notify of Perfection' ) . '</h3>
                    <input id="notify-perfection" type="text" name="notify-perfection"
                           placeholder="' . i8ln( 'Minimum perfection' ) . ' %" style="float: right;width: 75px;text-align:center"/>
                </label>
            </div>';
            }
            ?>
            <?php
            if ( ! $noNotifyLevel ) {
                echo '<div class="form-control">
                <label for="notify-level">
                    <h3 style="float:left;">' . i8ln( 'Notify of Level' ) . '</h3>
                    <input id="notify-level" min="1" max="35" type="number" name="notify-level"
                           placeholder="' . i8ln( 'Level' ) . '" style="float: right;width: 75px;text-align:center"/>
                </label>
            </div>';
            }
            ?>
            <?php
            if ( ! $noNotifyRaid ) {
                echo '<div class="form-control switch-container" id="notify-raid-wrapper">
                        <h3>' . i8ln( 'Notify of Minimum Raid Level' ) . '</h3>
                        <select name="notify-raid" id="notify-raid">
                            <option value="0">' . i8ln( 'Disable' ) . '</option>
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
            if ( ! $noNotifySound ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Notify with sound' ) . '</h3>
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
            if ( ! $noCriesSound ) {
                echo '<div class="form-control switch-container" id="cries-switch-wrapper">
                <h3>' . i8ln( 'Use Pokemon cries' ) . '</h3>
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
            if ( ! $noNotifySound ) {
                echo '</div>';
            }
            ?>
            <?php
            if ( ! $noNotifyBounce ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Bounce' ) . '</h3>
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
            if ( ! $noNotifyNotification ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Push Notifications' ) . '</h3>
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
            if ( ! $noNotifyPokemon || ! $noNotifyRarity || ! $noNotifyIv || ! $noNotifyLevel || ! $noNotifySound || ! $noNotifyRaid || ! $noNotifyBounce || ! $noNotifyNotification ) {
                echo '</div>';
            }
            ?>

            <?php
            if ( ! $noMapStyle || ! $noDirectionProvider || ! $noIconSize || ! $noIconNotifySizeModifier || ! $noGymStyle || ! $noLocationStyle ) {
                echo '<h3>' . i8ln( 'Style' ) . '</h3>
            <div>';
            }
            ?>
            <?php
            if ( ! $noMapStyle ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Map Style' ) . '</h3>
                <select id="map-style"></select>
            </div>';
            }
            ?>
            <?php
            if ( ! $noDirectionProvider ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Direction Provider' ) . '</h3>
                <select name="direction-provider" id="direction-provider">
                    <option value="apple">' . i8ln( 'Apple' ) . '</option>
                    <option value="google">' . i8ln( 'Google (Directions)' ) . '</option>
                    <option value="google_pin">' . i8ln( 'Google (Pin)' ) . '</option>
                    <option value="waze">' . i8ln( 'Waze' ) . '</option>
                    <option value="bing">' . i8ln( 'Bing' ) . '</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if ( ! $noIconSize ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Icon Size' ) . '</h3>
                <select name="pokemon-icon-size" id="pokemon-icon-size">
                    <option value="-8">' . i8ln( 'Small' ) . '</option>
                    <option value="0">' . i8ln( 'Normal' ) . '</option>
                    <option value="10">' . i8ln( 'Large' ) . '</option>
                    <option value="20">' . i8ln( 'X-Large' ) . '</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if ( ! $noIconNotifySizeModifier ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Increase Notified Icon Size' ) . '</h3>
                <select name="pokemon-icon-notify-size" id="pokemon-icon-notify-size">
                    <option value="0">' . i8ln( 'Disable' ) . '</option>
                    <option value="15">' . i8ln( 'Large' ) . '</option>
                    <option value="30">' . i8ln( 'X-Large' ) . '</option>
                    <option value="45">' . i8ln( 'XX-Large' ) . '</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if ( ! $noGymStyle ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Gym Marker Style' ) . '</h3>
                <select name="gym-marker-style" id="gym-marker-style">
                    <option value="ingame">' . i8ln( 'In-Game' ) . '</option>
                    <option value="shield">' . i8ln( 'Shield' ) . '</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if ( ! $noLocationStyle ) {
                echo '<div class="form-control switch-container">
                <h3>' . i8ln( 'Location Icon Marker' ) . '</h3>
                <select name="locationmarker-style" id="locationmarker-style"></select>
            </div>';
            }
            ?>
            <?php
            if ( ! $noMapStyle || ! $noDirectionProvider || ! $noIconSize || ! $noIconNotifySizeModifier || ! $noGymStyle || ! $noLocationStyle ) {
                echo '</div>';
            }
            ?>
            <?php
            if ( ! $noAreas ) {
                echo '<h3>' . i8ln( 'Areas' ) . '</h3>';
                $count = sizeof( $areas );
                if ( $count > 0 ) {
                    echo '<div class="form-control switch-container area-container"><ul>';
                    for ( $i = 0; $i <= $count - 1; $i ++ ) {
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
                        onclick="confirm('Are you sure you want to reset settings to default values?') ? (localStorage.clear(), window.location.reload()) : false">
                    <i class="fa fa-refresh" aria-hidden="true"></i> <?php echo i8ln( 'Reset Settings' ) ?>
                </button>
            </center>
        </div>
        <div>
            <center>
                <button class="settings"
                        onclick="download('<?= addslashes( $title ) ?>', JSON.stringify(JSON.stringify(localStorage)))">
                    <i class="fa fa-upload" aria-hidden="true"></i> <?php echo i8ln( 'Export Settings' ) ?>
                </button>
            </center>
        </div>
        <div>
            <center>
                <input id="fileInput" type="file" style="display:none;" onchange="openFile(event)"/>
                <button class="settings"
                        onclick="document.getElementById('fileInput').click()">
                    <i class="fa fa-download" aria-hidden="true"></i> <?php echo i8ln( 'Import Settings' ) ?>
                </button>
            </center>
        </div>
        <?php
        if (($noNativeLogin === false) && !empty($_SESSION['user']->id)) {
            ?>
            <div>
                <center>
                    <button class="settings"
                            onclick="document.location.href='user'">
                        <i class="fa" aria-hidden="true"></i> <?php echo i8ln('Activate Key'); ?>
                    </button>
                </center>
            </div>
            <div>
                <center>
                    <button class="settings"
                            onclick="document.location.href='logout.php'">
                        <i class="fa" aria-hidden="true"></i> <?php echo i8ln('Logout'); ?>
                    </button>
                </center>
            </div><br>
            <div><center><p>
            <?php
            $time = date("Y-m-d", $_SESSION['user']->expire_timestamp);
            
            echo $_SESSION['user']->user . "<br>";
            if ($_SESSION['user']->expire_timestamp < time()) {
                echo "<span style='color: green;'>" . i8ln('Membership expires on') . " {$time}</span>";
            } else {
                echo "<span style='color: red;'>" . i8ln('Membership expired on') . " {$time}</span>";
            } ?>
            </p></center></div>
        <?php
        }
        ?>
        <?php
        if (($noDiscordLogin === false) && !empty($_SESSION['user']->id)) {
            ?>
            <div>
                <center>
                    <button class="settings"
                            onclick="document.location.href='logout.php'">
                        <i class="fa" aria-hidden="true"></i> <?php echo i8ln('Logout'); ?>
                    </button>
                </center>
            </div><br>
            <div><center><p>
            <?php
            echo 'Logged in as: ' . $_SESSION['user']->user . "<br>";
            ?>
	    </p></center></div>
        <?php
        }
        ?>
    </nav>
    <nav id="stats">
        <div class="switch-container">
            <?php
            if ( $worldopoleUrl !== "" ) {
                ?>
                <div class="switch-container">
                    <div>
                        <center><a href="<?= $worldopoleUrl ?>">Full Stats</a></center>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="switch-container">
                <center><h1 id="stats-ldg-label"><?php echo i8ln( 'Loading' ) ?>...</h1></center>
            </div>
            <div class="stats-label-container">
                <center><h1 id="stats-pkmn-label"></h1></center>
            </div>
            <div id="pokemonList" style="color: black;">
                <table id="pokemonList_table" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?php echo i8ln( 'Icon' ) ?></th>
                        <th><?php echo i8ln( 'Name' ) ?></th>
                        <th><?php echo i8ln( 'Count' ) ?></th>
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
                <center><h1 id="stats-pkstop-label"></h1></center>
            </div>
            <div id="pokestopList" style="color: black;"></div>
        </div>
    </nav>
    <nav id="gym-details">
        <center><h1><?php echo i8ln( 'Loading' ) ?>...</h1></center>
    </nav>

    <div id="motd" title=""></div>

    <div id="map"></div>
    <div class="global-raid-modal">

    </div>
    <?php if ( ! $noManualNests ) { ?>
        <div class="global-nest-modal" style="display:none;">
            <input type="hidden" name="pokemonID" class="pokemonID"/>
            <?php pokemonFilterImages( $noPokemonNumbers, 'pokemonSubmitFilter(event)', $excludeNestMons, 5 ); ?>
            <div class="button-container">
                <button type="button" onclick="manualNestData(event);" class="submitting-nests"><i
                        class="fa fa-binoculars"
                        style="margin-right:10px;"></i><?php echo i8ln( 'Submit Nest' ); ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <?php if ( ! $noRenamePokestops ) { ?>
        <div class="rename-modal" style="display: none;">
	   <input type="text" id="pokestop-name" name="pokestop-name"
		  placeholder="<?php echo i8ln( 'Enter New Pokéstop Name' ); ?>" data-type="pokestop"
                  class="search-input">
             <div class="button-container">
                <button type="button" onclick="renamePokestopData(event);" class="renamepokestopid"><i
                        class="fa fa-edit"
                        style="margin-right:10px; vertical-align: middle; font-size: 1.5em;"></i><?php echo i8ln( 'Rename Pokestop' ); ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <?php if ( ! $noConvertPokestops ) { ?>
        <div class="convert-modal" style="display: none;">
             <div class="button-container">
                <button type="button" onclick="convertPokestopData(event);" class="convertpokestopid"><i
                        class="fa fa-refresh"
                        style="margin-right:10px; vertical-align: middle; font-size: 1.5em;"></i><?php echo i8ln( 'Convert to gym' ); ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <?php if ( ! $noEditCommunity ) { ?>
        <div class="editcommunity-modal" style="display: none;">
	   <input type="text" id="community-name" name="community-name"
		  placeholder="<?php echo i8ln( 'Enter New community Name' ); ?>" data-type="community-name"
		  class="search-input">
	   <input type="text" id="community-description" name="community-description"
		  placeholder="<?php echo i8ln( 'Enter New community Description' ); ?>" data-type="community-description"
		  class="search-input">
	   <input type="text" id="community-invite" name="community-invite"
		  placeholder="<?php echo i8ln( 'Enter New community Invite link' ); ?>" data-type="community-invite"
		  class="search-input">
	     <div class="button-container">
                <button type="button" onclick="editCommunityData(event);" class="editcommunityid"><i
                        class="fa fa-edit"
                        style="margin-right:10px; vertical-align: middle; font-size: 1.5em;"></i><?php echo i8ln( 'Save Changes' ); ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <?php if ( ! $noPortals ) { ?>
        <div class="convert-portal-modal" style="display: none;">
             <div class="button-container">
                <button type="button" onclick="convertPortalToPokestopData(event);" class="convertportalid"><i
                        class="fa fa-refresh"
                        style="margin-right:10px; vertical-align: middle; font-size: 1.5em;"></i><?php echo i8ln( 'Convert to pokestop' ); ?>
		</button>
                <button type="button" onclick="convertPortalToGymData(event);" class="convertportalid"><i
                        class="fa fa-refresh"
                        style="margin-right:10px; vertical-align: middle; font-size: 1.5em;"></i><?php echo i8ln( 'Convert to gym' ); ?>
		</button>
                <button type="button" onclick="markPortalChecked(event);" class="convertportalid"><i
                        class="fa fa-times"
                        style="margin-right:10px; vertical-align: middle; font-size: 1.5em;"></i><?php echo i8ln( 'No Pokestop or Gym' ); ?>
		</button>
            </div>
        </div>
    <?php } ?>
    <?php if ( ! $noDiscordLogin ) { ?>
        <div class="accessdenied-modal" style="display: none;">
            <?php if ( $copyrightSafe === false ) { ?>
                <img src="static/images/accessdenied.png" alt="PikaSquad" width="250">
            <?php } ?>
            <center><?php echo i8ln( 'Your access has been denied.' ); ?></center>
            <br>
            <?php echo i8ln('You might not be a member of our Discord or you joined a server which is on our blacklist. Click <a href="' .$discordUrl .'">here</a> to join!'); ?>
        </div>
    <?php } ?>
    <?php if ( ! $noManualQuests ) { ?>
        <div class="quest-modal" style="display: none;">
            <input type="hidden" value="" name="questPokestop" class="questPokestop"/>
            <?php
            $json   = file_get_contents( 'static/dist/data/questtype.min.json' );
            $questtypes  = json_decode( $json, true );

            $json    = file_get_contents( 'static/dist/data/rewardtype.min.json' );
            $rewardtypes   = json_decode( $json, true );

            $json    = file_get_contents( 'static/dist/data/conditiontype.min.json' );
            $conditiontypes   = json_decode( $json, true );

	    $json    = file_get_contents( 'static/dist/data/pokemon.min.json' );
	    $encounters = json_decode( $json, true );

	    $json    = file_get_contents( 'static/dist/data/items.min.json' );
	    $items = json_decode( $json, true );
            ?>
            <label for="questTypeList"><?php echo i8ln( 'Quest' ); ?>
            <select id="questTypeList" name="questTypeList" class="questTypeList">
                <option />
                <?php
                foreach ( $questtypes as $key => $value ) {
                    if ( ! in_array( $key, $hideQuestTypes ) ) {
                    ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln( $value['text'] ); ?></option>
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
            <label for="conditionTypeList"><?php echo i8ln( 'Conditions' ); ?>
            <select id="conditionTypeList" name="conditionTypeList" class="conditionTypeList">
                <option />
                <?php
                foreach ( $conditiontypes as $key => $value ) {
                    if ( ! in_array( $key, $hideConditionTypes ) ) {
                    ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln( $value['text'] ); ?></option>
                    <?php
                    }
                }
                ?>
	    </select>
            <select id="pokeCatchList" name="pokeCatchList" class="pokeCatchList" multiple></select>
	    <select id="typeCatchList" name="typeCatchList" class="typeCatchList" multiple>
                <option value="1"><?php echo i8ln( 'Normal' ); ?></option>
                <option value="2"><?php echo i8ln( 'Fighting' ); ?></option>
                <option value="3"><?php echo i8ln( 'Flying' ); ?></option>
                <option value="4"><?php echo i8ln( 'Poison' ); ?></option>
                <option value="5"><?php echo i8ln( 'Ground' ); ?></option>
                <option value="6"><?php echo i8ln( 'Rock' ); ?></option>
                <option value="7"><?php echo i8ln( 'Bug' ); ?></option>
                <option value="8"><?php echo i8ln( 'Ghost' ); ?></option>
                <option value="9"><?php echo i8ln( 'Steel' ); ?></option>
                <option value="10"><?php echo i8ln( 'Fire' ); ?></option>
                <option value="11"><?php echo i8ln( 'Water' ); ?></option>
                <option value="12"><?php echo i8ln( 'Grass' ); ?></option>
                <option value="13"><?php echo i8ln( 'Electric' ); ?></option>
                <option value="14"><?php echo i8ln( 'Psychic' ); ?></option>
                <option value="15"><?php echo i8ln( 'Ice' ); ?></option>
                <option value="16"><?php echo i8ln( 'Dragon' ); ?></option>
                <option value="17"><?php echo i8ln( 'Dark' ); ?></option>
                <option value="18"><?php echo i8ln( 'Fairy' ); ?></option>
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
                <option value="10"><?php echo i8ln( 'Nice' ); ?></option>
                <option value="11"><?php echo i8ln( 'Great' ); ?></option>
                <option value="12"><?php echo i8ln( 'Excellent' ); ?></option>
            </select>
            <select id="curveThrow" class="curveThrow" class="curveThrow">
		<option />
                <option value="0"><?php echo i8ln( 'Without curve throw' ); ?></option>
                <option value="1"><?php echo i8ln( 'With curve throw' ); ?></option>
            </select>
            </label>
            <label for="rewardTypeList"><?php echo i8ln( 'Reward' ); ?>
            <select id="rewardTypeList" name="rewardTypeList" class="rewardTypeList">
                <option />
                <?php
                foreach ( $rewardtypes as $key => $value ) {
                    if ( ! in_array( $key, $hideRewardTypes ) ) {
                    ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln( $value['text'] ); ?></option>
                    <?php
                    }
                }
                ?>
	    </select>
            <select id="pokeQuestList" name="pokeQuestList" class="pokeQuestList">
                <option />
                <?php
                foreach ( $encounters as $key => $value ) {
                    if ( in_array( $key, $showEncounters ) ) {
                    ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln( $value['name'] ); ?></option>
                    <?php
                    }
                }
                ?>
	    </select>
            <select id="itemQuestList" name="itemQuestList" class="itemQuestList">
                <option />
                <?php
                foreach ( $items as $key => $value ) {
                    if ( in_array( $key, $showItems ) ) {
                    ?>
                        <option value="<?php echo $key; ?>"><?php echo i8ln( $value['name'] ); ?></option>
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
                        class="fa fa-binoculars"
                        style="margin-right:10px;"></i><?php echo i8ln( 'Submit Quest' ); ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <div class="fullscreen-toggle">
        <button class="map-toggle-button" onClick="toggleFullscreenMap();"><i class="fa fa-expand" aria-hidden="true"></i></button>
    </div>
    <?php if ( ( ! $noGyms || ! $noPokestops ) && ! $noSearch ) { ?>
        <div class="search-container">
            <button class="search-modal-button" onClick="openSearchModal(event);"><i class="fa fa-search"
                                                                                     aria-hidden="true"></i></button>
            <div class="search-modal" style="display:none;">
                <div id="search-tabs">
                    <ul>
                        <?php if ( ! $noQuests && ! $noSearchManualQuests ) { ?>
                            <li><a href="#tab-rewards"><img src="static/images/reward.png"/></a></li>
                        <?php }
                        if ( ! $noSearchNests ) { ?>
                            <li><a href="#tab-nests"><img src="static/images/nest.png"/></a></li>
                        <?php }
                        if ( ! $noSearchGyms ) { ?>
                            <li><a href="#tab-gym"><img src="static/forts/ingame/Uncontested.png"/></a></li>
                        <?php }
                        if ( ! $noSearchPokestops ) { ?>
                            <li><a href="#tab-pokestop"><img src="static/forts/Pstop-large.png"/></a></li>
                        <?php }
                        if ( ! $noSearchPortals ) { ?>
                            <li><a href="#tab-portals"><img src="static/images/portal.png"/></a></li>
			<?php } ?>
                    </ul>
                    <?php if ( ! $noQuests && ! $noSearchManualQuests ) { ?>
                        <div id="tab-rewards">
                            <input type="search" id="reward-search" name="reward-search"
                                   placeholder="<?php echo i8ln( 'Enter Reward Name' ); ?>"
                                   data-type="reward" class="search-input"/>
                            <ul id="reward-search-results" class="search-results reward-results"></ul>
                        </div>
                    <?php } ?>
                    <?php if ( ! $noSearchNests ) { ?>
                        <div id="tab-nests">
                            <input type="search" id="nest-search" name="nest-search"
                                   placeholder="<?php echo i8ln( 'Enter Pokemon or Type' ); ?>"
                                   data-type="nests" class="search-input"/>
                            <ul id="nest-search-results" class="search-results nest-results"></ul>
                        </div>
                    <?php } ?>
                    <?php if ( ! $noSearchGyms ) { ?>
                        <div id="tab-gym">
                            <input type="search" id="gym-search" name="gym-search"
                                   placeholder="<?php echo i8ln( 'Enter Gym Name' ); ?>"
                                   data-type="forts" class="search-input"/>
                            <ul id="gym-search-results" class="search-results gym-results"></ul>
                        </div>
		    <?php } ?>
		    <?php if ( ! $noSearchPokestops ) { ?>
                        <div id="tab-pokestop">
                            <input type="search" id="pokestop-search" name="pokestop-search"
                                   placeholder="<?php echo i8ln( 'Enter Pokestop Name' ); ?>" data-type="pokestops"
                                   class="search-input"/>
                            <ul id="pokestop-search-results" class="search-results pokestop-results"></ul>
                        </div>
		    <?php } ?>
		    <?php if ( ! $noSearchPortals ) { ?>
                        <div id="tab-portals">
                            <input type="search" id="portals-search" name="portals-search"
                                   placeholder="<?php echo i8ln( 'Enter Portal Name' ); ?>" data-type="portals"
                                   class="search-input"/>
                            <ul id="portals-search-results" class="search-results portals-results"></ul>
                        </div>
		    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php
    if ( ( ! $noPokemon && ! $noManualPokemon ) || ( ! $noGyms && ! $noManualGyms ) || ( ! $noPokestops && ! $noManualPokestops ) || ( ! $noAddNewNests && ! $noNests ) || ( !$noAddNewCommunity && ! $noCommunity ) ) {
        ?>
        <button class="submit-on-off-button" onclick="$('.submit-on-off-button').toggleClass('on');">
            <i class="fa fa-map-marker submit-to-map" aria-hidden="true"></i>
        </button>
        <div class="submit-modal" style="display:none;">
            <input type="hidden" value="" name="submitLatitude" class="submitLatitude"/>
            <input type="hidden" value="" name="submitLongitude" class="submitLongitude"/>
            <div id="submit-tabs">
                <ul>
                    <?php if ( ! $noManualPokemon && !$noPokemon ) {
                        ?>
                        <li><a href="#tab-pokemon"><img src="static/images/pokeball.png"/></a></li>
                    <?php } ?>
                    <?php if ( ! $noManualGyms && !$noGyms ) {
                        ?>
                        <li><a href="#tab-gym"><img src="static/forts/ingame/Uncontested.png"/></a></li>
                    <?php } ?>
                    <?php if ( ! $noManualPokestops && !$noPokestops) {
                        ?>
                        <li><a href="#tab-pokestop"><img src="static/forts/Pstop-large.png"/></a></li>
                    <?php } ?>
                    <?php if ( ! $noAddNewNests && !$noNests ) {
                        ?>
                        <li><a href="#tab-nests"><img src="static/images/nest.png"/></a></li>
		    <?php } ?>
                    <?php if ( ! $noAddNewCommunity && !$noCommunity ) {
                        ?>
                        <li><a href="#tab-communities"><img src="static/images/community.png"/></a></li>
                    <?php } ?>
                </ul>
                <?php if ( ! $noManualPokemon && !$noPokemon  ) {
                    ?>
                    <div id="tab-pokemon">
                        <input type="hidden" name="pokemonID" class="pokemonID"/>
                        <?php pokemonFilterImages( $noPokemonNumbers, 'pokemonSubmitFilter(event)', $pokemonToExclude, 6 ); ?>
                        <div class="button-container">
                            <button type="button" onclick="manualPokemonData(event);" class="submitting-pokemon"><i
                                    class="fa fa-binoculars"
                                    style="margin-right:10px;"></i><?php echo i8ln( 'Submit Pokemon' ); ?>
                            </button>
                        </div>
                    </div>
                <?php } ?>
                <?php if ( ! $noManualGyms && !$noGyms ) {
                    ?>
                    <div id="tab-gym">
                        <input type="text" id="gym-name" name="gym-name"
                               placeholder="<?php echo i8ln( 'Enter Gym Name' ); ?>" data-type="forts"
                               class="search-input">
                        <div class="button-container">
                            <button type="button" onclick="manualGymData(event);" class="submitting-gym"><i
                                    class="fa fa-binoculars"
                                    style="margin-right:10px;"></i><?php echo i8ln( 'Submit Gym' ); ?>
                            </button>
                        </div>
                    </div>
                <?php } ?>
                <?php if ( ! $noManualPokestops && !$noPokestops ) {
                    ?>
                    <div id="tab-pokestop">
                        <input type="text" id="pokestop-name" name="pokestop-name"
                               placeholder="<?php echo i8ln( 'Enter Pokestop Name' ); ?>" data-type="pokestop"
                               class="search-input">
                        <div class="button-container">
                            <button type="button" onclick="manualPokestopData(event);" class="submitting-pokestop"><i
                                    class="fa fa-binoculars"
                                    style="margin-right:10px;"></i><?php echo i8ln( 'Submit Pokestop' ); ?>
                            </button>
                        </div>
                    </div>
                <?php } ?>
                <?php if ( ! $noAddNewNests && !$noNests ) {
                    ?>
                    <div id="tab-nests">
                        <input type="hidden" name="pokemonID" class="pokemonID"/>
                        <?php pokemonFilterImages( $noPokemonNumbers, 'pokemonSubmitFilter(event)', $excludeNestMons, 7 ); ?>
                        <div class="button-container">
                            <button type="button" onclick="submitNewNest(event);" class="submitting-nest"><i
                                    class="fa fa-binoculars"
                                    style="margin-right:10px;"></i><?php echo i8ln( 'Submit Nest' ); ?>
                            </button>
                        </div>
                    </div>
                <?php } ?>
                <?php if ( ! $noAddNewCommunity && !$noCommunity ) {
                    ?>
                    <div id="tab-communities">
                        <input type="text" name="community-name" class="community-name"
                               placeholder="<?php echo i8ln( 'Enter Community Name' ); ?>" data-type="name"
			       class="search-input">
                        <input type="text" name="community-description" class="community-description"
                               placeholder="<?php echo i8ln( 'Enter description' ); ?>" data-type="description"
			       class="search-input">
                        <input type="text" name="community-invite" class="community-invite"
                               placeholder="<?php echo i8ln( 'Whatsapp, Telegram, Discord Link' ); ?>" data-type="invite-link"
			       class="search-input">
			<h6><center><?php echo i8ln( 'Link must be valid and start with https://' ); ?></center></h6>
                        <div class="button-container">
                            <button type="button" onclick="submitNewCommunity(event);" class="submitting-community"><i
                                    class="fa fa-comments"
                                    style="margin-right:10px;"></i><?php echo i8ln( 'Submit Community' ); ?>
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.9.1/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/js/select2.full.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
<script src="https://code.createjs.com/soundjs-0.6.2.min.js"></script>
<script src="node_modules/push.js/bin/push.min.js"></script>
<script src="node_modules/long/src/long.js"></script>
<script src="https://unpkg.com/leaflet-geosearch@2.7.0/dist/bundle.min.js"></script>
<script src="static/js/vendor/s2geometry.js"></script>
<script src="static/dist/js/app.min.js"></script>
<script src="static/js/vendor/classie.js"></script>
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"></script>
<script src="https://leaflet.github.io/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<script src="static/js/vendor/smoothmarkerbouncing.js"></script>
<script src='https://maps.googleapis.com/maps/api/js?key=<?php $gmapsKey ?> ' async defer></script>
<script src="https://unpkg.com/leaflet.gridlayer.googlemutant@latest/Leaflet.GoogleMutant.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@5/turf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jcanvas/21.0.1/min/jcanvas.min.js"></script>
<script>
    var centerLat = <?= $startingLat; ?>;
    var centerLng = <?= $startingLng; ?>;
    var locationSet = <?= $locationSet; ?>;
    var motd = <?php echo $noMotd ? 'false' : 'true' ?>;
    var zoom<?php echo $zoom ? " = " . $zoom : null; ?>;
    var encounterId<?php echo $encounterId ? " = '" . $encounterId . "'" : null; ?>;
    var maxZoom = <?= $maxZoomIn; ?>;
    var minZoom = <?= $maxZoomOut; ?>;
    var maxLatLng = <?= $maxLatLng; ?>;
    var disableClusteringAtZoom = <?= $disableClusteringAtZoom; ?>;
    var zoomToBoundsOnClick = <?= $zoomToBoundsOnClick; ?>;
    var maxClusterRadius = <?= $maxClusterRadius; ?>;
    var spiderfyOnMaxZoom = <?= $spiderfyOnMaxZoom; ?>;
    var osmTileServer = '<?php echo $osmTileServer; ?>';
    var mapStyle = '<?php echo $mapStyle ?>';
    var gmapsKey = '<?php echo $gmapsKey ?>';
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
    var minRaidLevel = <?php echo $minRaidLevel ?>;
    var maxRaidLevel = <?php echo $maxRaidLevel ?>;
    var enableGyms = <?php echo $noGyms ? 'false' : $enableGyms ?>;
    var enableNests = <?php echo $noNests ? 'false' : $enableNests ?>;
    var enableCommunities = <?php echo $noCommunity ? 'false' : $enableCommunities ?>;
    var gymSidebar = <?php echo $noGymSidebar ? 'false' : $gymSidebar ?>;
    var enablePokemon = <?php echo $noPokemon ? 'false' : $enablePokemon ?>;
    var enablePokestops = <?php echo $noPokestops ? 'false' : $enablePokestops ?>;
    var enableLured = <?php echo $noLures ? 'false' : $enableLured ?>;
    var noQuests = <?php echo $noQuests === true ? 'true' : 'false' ?>;
    var enableQuests = <?php echo $noQuests ? 'false' : $enableQuests ?>;
    var hideQuestsPokemon = <?php echo $hideQuestsPokemon ? '[]' : $hideQuestsPokemon ?>;
    var hideQuestsItem = <?php echo $hideQuestsItem ? '[]' : $hideQuestsItem ?>;
    var enableNewPortals = <?php echo ( ( $map != "monocle" ) || ( $fork == "alternate" ) ) ? $enableNewPortals : 0 ?>;
    var enableWeatherOverlay = <?php echo ! $noWeatherOverlay ? $enableWeatherOverlay : 'false' ?>;
    var enableScannedLocations = <?php echo $map != "monocle" && ! $noScannedLocations ? $enableScannedLocations : 'false' ?>;
    var enableSpawnpoints = <?php echo $noSpawnPoints ? 'false' : $enableSpawnPoints ?>;
    var enableRanges = <?php echo $noRanges ? 'false' : $enableRanges ?>;
    var enableScanPolygon = <?php echo $noScanPolygon ? 'false' : $enableScanPolygon ?>;
    var geoJSONfile = '<?php echo $noScanPolygon ? '' : $geoJSONfile ?>';
    var notifySound = <?php echo $noNotifySound ? 'false' : $notifySound ?>;
    var criesSound = <?php echo $noCriesSound ? 'false' : $criesSound ?>;
    var enableStartMe = <?php echo $noStartMe ? 'false' : $enableStartMe ?>;
    var enableStartLast = <?php echo ( ! $noStartLast && $enableStartMe === 'false' ) ? $enableStartLast : 'false' ?>;
    var enableFollowMe = <?php echo $noFollowMe ? 'false' : $enableFollowMe ?>;
    var enableSpawnArea = <?php echo $noSpawnArea ? 'false' : $enableSpawnArea ?>;
    var iconSize = <?php echo $iconSize ?>;
    var iconNotifySizeModifier = <?php echo $iconNotifySizeModifier ?>;
    var locationStyle = '<?php echo $locationStyle ?>';
    var gymStyle = '<?php echo $gymStyle ?>';
    var spriteFileLarge = '<?php echo $copyrightSafe ? 'static/icons-safe-1-bigger.png' : 'static/icons-im-1-bigger.png' ?>';
    var weatherSpritesSrc = '<?php echo $copyrightSafe ? 'static/sprites-safe/' : 'static/sprites-pokemon/' ?>';
    var icons = '<?php echo $copyrightSafe ? 'static/icons-safe/' : $iconRepository ?>';
    var weatherColors = <?php echo json_encode( $weatherColors ); ?>;
    var mapType = '<?php echo $map; ?>';
    var triggerGyms = <?php echo $triggerGyms ?>;
    var noExGyms = <?php echo $noExGyms === true ? 'true' : 'false' ?>;
    var noParkInfo = <?php echo $noParkInfo === true ? 'true' : 'false' ?>;
    var onlyTriggerGyms = <?php echo $onlyTriggerGyms === true ? 'true' : 'false' ?>;
    var showBigKarp = <?php echo $noBigKarp === true ? 'true' : 'false' ?>;
    var showTinyRat = <?php echo $noTinyRat === true ? 'true' : 'false' ?>;
    var hidePokemonCoords = <?php echo $hidePokemonCoords === true ? 'true' : 'false' ?>;
    var directionProvider = '<?php echo $noDirectionProvider === true ? $directionProvider : 'google' ?>';
    var exEligible = <?php echo $noExEligible === true ? 'false' : $exEligible  ?>;
    var raidBossActive = <?php echo json_encode( $raidBosses ); ?>;
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
    var noConvertPokestops = <?php echo $noConvertPokestops === true ? 'true' : 'false' ?>;
    var noWhatsappLink = <?php echo $noWhatsappLink === true ? 'true' : 'false' ?>;
    var enablePortals = <?php echo $noPortals ? 'false' : $enablePortals ?>;
    var noPortals = <?php echo $noPortals === true ? 'true' : 'false' ?>;
    var enableS2Cells = <?php echo $noS2Cells ? 'false' : $enableS2Cells ?>;
    var enableLevel13Cells = <?php echo $noS2Cells ? 'false' : $enableLevel13Cells ?>;
    var enableLevel14Cells = <?php echo $noS2Cells ? 'false' : $enableLevel14Cells ?>;
    var enableLevel17Cells = <?php echo $noS2Cells ? 'false' : $enableLevel17Cells ?>;
    var noDeletePortal = <?php echo $noDeletePortal === true ? 'true' : 'false' ?>;
    var noConvertPortal = <?php echo $noConvertPortal === true ? 'true' : 'false' ?>;
    var markPortalsAsNew = <?php echo $markPortalsAsNew ?>;
    var copyrightSafe = <?php echo $copyrightSafe === true ? 'true' : 'false' ?>;
    var noRarityDisplay = <?php echo $noRarityDisplay === true ? 'true' : 'false' ?>;
    var noWeatherIcons = <?php echo $noWeatherIcons === true ? 'true' : 'false' ?>;
    var noWeatherShadow = <?php echo $noWeatherShadow === true ? 'true' : 'false' ?>;
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="static/dist/js/map.common.min.js"></script>
<script src="static/dist/js/map.min.js"></script>
<script src="static/dist/js/stats.min.js"></script>
<script src="https://unpkg.com/leaflet-geosearch@latest/dist/bundle.min.js"></script>
<script>
$( document ).ready(function() {
    initMap()
})
</script>
</body>
</html>
