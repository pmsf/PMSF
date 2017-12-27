<?php
include('config/config.php');
$zoom = !empty($_GET['zoom']) ? $_GET['zoom'] : null;
if (!empty($_GET['lat']) && !empty($_GET['lon'])) {
    $startingLat = $_GET['lat'];
    $startingLng = $_GET['lon'];
    $locationSet = 1;
} else {
    $locationSet = 0;
}
if ($blockIframe) {
    header('X-Frame-Options: DENY');
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
    <link rel="shortcut icon" href="static/appicons/favicon.ico"
          type="image/x-icon">
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
    <script>
        var token = '<?php echo (!empty($_SESSION['token'])) ? $_SESSION['token'] : ""; ?>';
    </script>
    <link rel="stylesheet" href="static/dist/css/app.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="static/js/vendor/modernizr.custom.js"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body id="top">
<div class="wrapper">
    <!-- Header -->
    <header id="header">
        <a href="#nav"><span class="label">Options</span></a>

        <h1><a href="#"><?= $title ?></a></h1>
        <?php
        if ($discordUrl != "") {
            echo '<a href="' . $discordUrl . '" target="_blank" style="margin-bottom: 5px; vertical-align: middle;padding:0 5px;">
            <img src="static/images/discord.png" border="0" style="float: right;">
        </a>';
        }
        if ($paypalUrl != "") {
            echo '<a href="' . $paypalUrl . '" target="_blank" style="margin-bottom: 5px; vertical-align: middle; padding:0 5px;">
            <img src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_donate_74x21.png" border="0" name="submit"
                 title="PayPal - The safer, easier way to pay online!" alt="Donate" style="float: right;">
        </a>';
        }
        ?>
        <a href="#stats" id="statsToggle" class="statsNav" style="float: right;"><span class="label">Stats</span></a>
    </header>
    <!-- NAV -->
    <nav id="nav">
        <div id="nav-accordion">
            <h3>Marker Settings</h3>
            <div>
                <?php
                if (!$noPokemon) {
                    echo '<div class="form-control switch-container">
                    <h3>Pokemon</h3>
                    <div class="onoffswitch">
                        <input id="pokemon-switch" type="checkbox" name="pokemon-switch" class="onoffswitch-checkbox"
                               checked>
                        <label class="onoffswitch-label" for="pokemon-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                }
                ?>
                <?php
                if (!$noRaids) {
                    echo '<div class="form-control switch-container" id="raids-wrapper">
                    <h3>Raids</h3>
                    <div class="onoffswitch">
                        <input id="raids-switch" type="checkbox" name="raids-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="raids-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                }
                ?>
                <div id="raids-filter-wrapper" style="display:none">
                    <div class="form-control switch-container" id="active-raids-wrapper">
                        <h3>Only Active Raids</h3>
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
                        <h3>Minimum Raid Level</h3>
                        <select name="min-level-raids-filter-switch" id="min-level-raids-filter-switch">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="form-control switch-container" id="max-level-raids-filter-wrapper">
                        <h3>Maximum Raid Level</h3>
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
                if (!$noGymSidebar && (!$noGyms || !$noRaids)) {
                    echo '<div id="gym-sidebar-wrapper" class="form-control switch-container">
                    <h3>Use Gym Sidebar</h3>
                    <div class="onoffswitch">
                        <input id="gym-sidebar-switch" type="checkbox" name="gym-sidebar-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="gym-sidebar-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                }
                ?>
                <?php
                if (!$noGyms) {
                    echo '<div class="form-control switch-container">
                    <h3>Gyms</h3>
                    <div class="onoffswitch">
                        <input id="gyms-switch" type="checkbox" name="gyms-switch" class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="gyms-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                }
                ?>
                <div id="gyms-filter-wrapper" style="display:none">
                    <div class="form-control switch-container" id="team-gyms-only-wrapper">
                        <h3>Team</h3>
                        <select name="team-gyms-filter-switch" id="team-gyms-only-switch">
                            <option value="0">All</option>
                            <option value="1">Mystic</option>
                            <option value="2">Valor</option>
                            <option value="3">Instinct</option>
                        </select>
                    </div>
                    <div class="form-control switch-container" id="open-gyms-only-wrapper">
                        <h3>Open Spot</h3>
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
                        <h3>Minimum Free Slots</h3>
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
                        <h3>Maximum Free Slots</h3>
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
                        <h3>Last Scan</h3>
                        <select name="last-update-gyms-switch" id="last-update-gyms-switch">
                            <option value="0">All</option>
                            <option value="1">Last Hour</option>
                            <option value="6">Last 6 Hours</option>
                            <option value="12">Last 12 Hours</option>
                            <option value="24">Last 24 Hours</option>
                            <option value="168">Last Week</option>
                        </select>
                    </div>
                </div>
                <?php
                if (!$noPokestops) {
                    echo '<div class="form-control switch-container">
                    <h3>Pokestops</h3>
                    <div class="onoffswitch">
                        <input id="pokestops-switch" type="checkbox" name="pokestops-switch"
                               class="onoffswitch-checkbox" checked>
                        <label class="onoffswitch-label" for="pokestops-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                }
                ?>
                <?php
                if ($map != "monocle") {
                    echo '<div class="form-control switch-container" id = "lured-pokestops-only-wrapper" style = "display:none">
                    <select name = "lured-pokestops-only-switch" id = "lured-pokestops-only-switch">
                        <option value = "0"> All</option>
                        <option value = "1"> Only Lured </option>
                    </select>
                </div>';
                }
                ?>
                <?php
                if ($map != "monocle" && !$noScannedLocations) {
                    echo '<div class="form-control switch-container">
                    <h3> Scanned Locations </h3>
                    <div class="onoffswitch">
                        <input id = "scanned-switch" type = "checkbox" name = "scanned-switch" class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="scanned-switch">
                            <span class="switch-label" data - on = "On" data - off = "Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                }
                ?>
                <?php
                if (!$noSpawnPoints) {
                    echo '<div class="form-control switch-container">
                    <h3> Spawn Points </h3>
                    <div class="onoffswitch">
                        <input id="spawnpoints-switch" type="checkbox" name="spawnpoints-switch"
                               class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="spawnpoints-switch">
                            <span class="switch-label" data - on="On" data - off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                }
                ?>
                <?php
                if (!$noRanges) {
                    echo '<div class="form-control switch-container">
                    <h3>Ranges</h3>
                    <div class="onoffswitch">
                        <input id="ranges-switch" type="checkbox" name="ranges-switch" class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="ranges-switch">
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
                }
                ?>
                <?php
                if (!$noHidePokemon) {
                    echo '<div class="form-control">
                    <label for="exclude-pokemon">
                        <h3>Hide Pokemon</h3>
                        <div style="max-height:165px;overflow-y:auto">
                            <select id="exclude-pokemon" multiple="multiple"></select>
                        </div>
                    </label>
                </div>';
                }
                ?>
            </div>

            <?php
            if (!$noSearchLocation || !$noStartMe || !$noStartLast || !$noFollowMe) {
                echo '<h3>Location &amp; Search Settings</h3>
            <div>';
            }
            ?>
            <?php
            if (!$noSearchLocation) {
                echo '<div class="form-control switch-container" style="display:{{is_fixed}}">
                <label for="next-location">
                    <h3>Change search location</h3>
                    <input id="next-location" type="text" name="next-location" placeholder="Change search location">
                </label>
            </div>';
            }
            ?>
            <?php
            if (!$noStartMe) {
                echo '<div class="form-control switch-container">
                    <h3> Start map at my position </h3>
                    <div class="onoffswitch">
                        <input id = "start-at-user-location-switch" type = "checkbox" name = "start-at-user-location-switch"
                               class="onoffswitch-checkbox"/>
                        <label class="onoffswitch-label" for="start-at-user-location-switch">
                            <span class="switch-label" data - on = "On" data - off = "Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
            }
            ?>
            <?php
            if (!$noStartLast) {
                echo '<div class="form-control switch-container">
                    <h3> Start map at last position </h3>
                    <div class="onoffswitch">
                        <input id = "start-at-last-location-switch" type = "checkbox" name = "start-at-last-location-switch"
                               class="onoffswitch-checkbox"/>
                        <label class="onoffswitch-label" for="start-at-last-location-switch">
                            <span class="switch-label" data - on = "On" data - off = "Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
            }
            ?>
            <?php
            if (!$noFollowMe) {
                echo '<div class="form-control switch-container">
                    <h3> Follow me </h3>
                    <div class="onoffswitch">
                        <input id = "follow-my-location-switch" type = "checkbox" name = "follow-my-location-switch"
                               class="onoffswitch-checkbox"/>
                        <label class="onoffswitch-label" for="follow-my-location-switch">
                            <span class="switch-label" data - on = "On" data - off = "Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>';
            }
            ?>
            <?php
            if (!$noSpawnArea) {
                echo '<div id="spawn-area-wrapper" class="form-control switch-container">
                <h3> Spawn area </h3>
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
            ?>
            <?php
            if (!$noSearchLocation || !$noStartMe || !$noStartLast || !$noFollowMe) {
                echo '</div>';
            }
            ?>

            <?php
            if (!$noNotifyPokemon || !$noNotifyRarity || !$noNotifyIv || !$noNotifySound || !$noNotifyRaid) {
                echo '<h3>Notification Settings</h3>
            <div>';
            }
            ?>
            <?php
            if (!$noNotifyPokemon) {
                echo '<div class="form-control">
                <label for="notify-pokemon">
                    <h3>Notify of Pokemon</h3>
                    <div style="max-height:165px;overflow-y:auto">
                        <select id="notify-pokemon" multiple="multiple"></select>
                    </div>
                </label>
            </div>';
            }
            ?>
            <?php
            if (!$noNotifyRarity) {
                echo '<div class="form-control">
                <label for="notify-rarity">
                    <h3>Notify of Rarity</h3>
                    <div style="max-height:165px;overflow-y:auto">
                        <select id="notify-rarity" multiple="multiple"></select>
                    </div>
                </label>
            </div>';
            }
            ?>
            <?php
            if (!$noNotifyIv) {
                echo '<div class="form-control">
                <label for="notify-perfection">
                    <h3>Notify of Perfection</h3>
                    <input id="notify-perfection" type="text" name="notify-perfection"
                           placeholder="Minimum perfection %"/>
                </label>
            </div>';
            }
            ?>
            <?php
            if (!$noNotifyLevel) {
                echo '<div class="form-control">
                <label for="notify-level">
                    <h3>'.i8ln('Notify of Level').'</h3>
                    <input id="notify-level" type="text" name="notify-level"
                           placeholder="'.i8ln('Minimum level').'"/>
                </label>
            </div>';
            }
            ?>
            <?php
            if (!$noNotifyRaid) {
                echo '<div class="form-control switch-container" id="notify-raid-wrapper">
                        <h3>Notify of Minimum Raid Level</h3>
                        <select name="notify-raid" id="notify-raid">
                            <option value="0">Disable</option>
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
            if (!$noNotifySound) {
                echo '<div class="form-control switch-container">
                <h3>Notify with sound</h3>
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
            if (!$noCriesSound) {
                echo '<div class="form-control switch-container" id="cries-switch-wrapper">
                <h3>Use Pok&eacute;mon cries</h3>
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
            if (!$noNotifySound) {
                echo '</div>';
            }
            ?>
            <?php
            if (!$noNotifyPokemon || !$noNotifyRarity || !$noNotifyIv || !$noNotifySound || !$noNotifyRaid) {
                echo '</div>';
            }
            ?>

            <?php
            if (!$noMapStyle || !$noIconSize || !$noGymStyle || !$noLocationStyle) {
                echo '<h3>Style Settings</h3>
            <div>';
            }
            ?>
            <?php
            if (!$noMapStyle) {
                echo '<div class="form-control switch-container">
                <h3>Map Style</h3>
                <select id="map-style"></select>
            </div>';
            }
            ?>
            <?php
            if (!$noIconSize) {
                echo '<div class="form-control switch-container">
                <h3>Icon Size</h3>
                <select name="pokemon-icon-size" id="pokemon-icon-size">
                    <option value="-8">Small</option>
                    <option value="0">Normal</option>
                    <option value="10">Large</option>
                    <option value="20">X-Large</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if (!$noGymStyle) {
                echo '<div class="form-control switch-container">
                <h3>Gym Marker Style</h3>
                <select name="gym-marker-style" id="gym-marker-style">
                    <option value="ingame">In-Game</option>
                    <option value="shield">Shield</option>
                </select>
            </div>';
            }
            ?>
            <?php
            if (!$noLocationStyle) {
                echo '<div class="form-control switch-container">
                <h3>Location Icon Marker</h3>
                <select name="locationmarker-style" id="locationmarker-style"></select>
            </div>';
            }
            ?>
            <?php
            if (!$noMapStyle || !$noIconSize || !$noGymStyle || !$noLocationStyle) {
                echo '</div>';
            }
            ?>
        </div>
        <div>
            <center>
                <button class="settings"
                        onclick="confirm('Are you sure you want to reset settings to default values?') ? (localStorage.clear(), window.location.reload()) : false">
                    <i class="fa fa-refresh" aria-hidden="true"></i> Reset Settings
                </button>
            </center>
        </div>
        <div>
            <center>
                <button class="settings"
                        onclick="download('<?= addslashes($title) ?>', JSON.stringify(JSON.stringify(localStorage)))">
                    <i class="fa fa-upload" aria-hidden="true"></i> Export Settings
                </button>
            </center>
        </div>
        <div>
            <center>
                <input id="fileInput" type="file" style="display:none;" onchange="openFile(event)"/>
                <button class="settings"
                        onclick="document.getElementById('fileInput').click()">
                    <i class="fa fa-download" aria-hidden="true"></i> Import Settings
                </button>
            </center>
        </div>
    </nav>
    <nav id="stats">
        <div class="switch-container">
            <!--<div class="switch-container">
                <div><center><a href="stats">Full Stats</a></center></div>
            </div>-->
            <div class="switch-container">
                <center><h1 id="stats-ldg-label">Loading...</h1></center>
            </div>
            <div class="stats-label-container">
                <center><h1 id="stats-pkmn-label"></h1></center>
            </div>
            <div id="pokemonList" style="color: black;">
                <table id="pokemonList_table" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Count</th>
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
        <center><h1>Loading...</h1></center>
    </nav>

    <div id="motd" title=""></div>

    <div id="map"></div>
</div>
<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.9.1/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
<script src="https://code.createjs.com/soundjs-0.6.2.min.js"></script>
<script src="node_modules/push.js/bin/push.min.js"></script>
<script src="static/dist/js/app.min.js"></script>
<script src="static/js/vendor/classie.js"></script>
<script>
    var centerLat = <?= $startingLat; ?>;
    var centerLng = <?= $startingLng; ?>;
    var locationSet = <?= $locationSet; ?>;
    var zoom<?php echo $zoom ? " = " . $zoom : null; ?>;
    var minZoom = <?= $maxZoomOut; ?>;
    var maxLatLng = <?= $maxLatLng; ?>;

    var osmTileServer = '<?php echo $osmTileServer; ?>';
    var mapStyle = '<?php echo $mapStyle ?>';
    var hidePokemon = <?php echo $noHidePokemon ? '[]' : $hidePokemon ?>;
    var notifyPokemon = <?php echo $noNotifyPokemon ? '[]' : $notifyPokemon ?>;
    var notifyRarity = <?php echo $noNotifyRarity ? '[]' : $notifyRarity ?>;
    var notifyIv = <?php echo $noNotifyIv ? '""' : $notifyIv ?>;
    var notifyLevel = <?php echo $noNotifyLevel ? '""' : $notifyLevel ?>;
    var notifyRaid = <?php echo $noNotifyRaid ? 0 : $notifyRaid ?>;
    var enableRaids = <?php echo $noRaids ? 'false' : $enableRaids ?>;
    var activeRaids = <?php echo $activeRaids ?>;
    var minRaidLevel = <?php echo $minRaidLevel ?>;
    var maxRaidLevel = <?php echo $maxRaidLevel ?>;
    var enableGyms = <?php echo $noGyms ? 'false' : $enableGyms ?>;
    var gymSidebar = <?php echo $noGymSidebar ? 'false' : $gymSidebar ?>;
    var enablePokemon = <?php echo $noPokemon ? 'false' : $enablePokemon ?>;
    var enablePokestops = <?php echo $noPokestops ? 'false' : $enablePokestops ?>;
    var enableLured = <?php echo $map != "monocle" ? $enableLured : 0 ?>;
    var enableScannedLocations = <?php echo $map != "monocle" && !$noScannedLocations ? $enableScannedLocations : 'false' ?>;
    var enableSpawnpoints = <?php echo $noSpawnPoints ? 'false' : $enableSpawnPoints ?>;
    var enableRanges = <?php echo $noRanges ? 'false' : $enableRanges ?>;
    var notifySound = <?php echo $noNotifySound ? 'false' : $notifySound ?>;
    var criesSound = <?php echo $noCriesSound ? 'false' : $criesSound ?>;
    var enableStartMe = <?php echo $noStartMe ? 'false' : $enableStartMe ?>;
    var enableStartLast = <?php echo (!$noStartLast && $enableStartMe === 'false') ? $enableStartLast : 'false' ?>;
    var enableFollowMe = <?php echo $noFollowMe ? 'false' : $enableFollowMe ?>;
    var enableSpawnArea = <?php echo $noSpawnArea ? 'false' : $enableSpawnArea ?>;
    var iconSize = <?php echo $iconSize ?>;
    var locationStyle = '<?php echo $locationStyle ?>';
    var gymStyle = '<?php echo $gymStyle ?>';
    var spriteFile = '<?php echo $copyrightSafe ? 'static/icons-safe-1.png' : 'static/icons-im-1.png' ?>';
    var spriteFileLarge = '<?php echo $copyrightSafe ? 'static/icons-safe-1-bigger.png' : 'static/icons-im-1-bigger.png' ?>';
    var icons = '<?php echo $copyrightSafe ? 'static/icons-safe/' : 'static/icons-pokemon/' ?>';
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="static/dist/js/map.common.min.js"></script>
<script src="static/dist/js/map.min.js"></script>
<script src="static/dist/js/stats.min.js"></script>
<script defer
        src="https://maps.googleapis.com/maps/api/js?key=<?= $gmapsKey ?>&amp;callback=initMap&amp;libraries=places,geometry"></script>
<script defer src="static/js/vendor/richmarker-compiled.js"></script>
</body>
</html>
