<?php
if (! file_exists('config/config.php')) {
    http_response_code(500);
    die("<h1>Config file missing</h1><p>Please ensure you have created your config file (<code>config/config.php</code>).</p>");
}
include('config/config.php');
if ($noNativeLogin === false || $noDiscordLogin === false || $noFacebookLogin === false || $noPatreonLogin === false) {
    if (isset($_COOKIE["LoginCookie"])) {
        if (validateCookie($_COOKIE["LoginCookie"]) === false) {
            header("Location: .");
        }
    }
    if (empty($_SESSION['user']->id) && $forcedLogin === true) {
        header("Location: ./login?action=login");
        die();
    }
    if (!empty($_SESSION['user']->updatePwd) && $_SESSION['user']->updatePwd === 1) {
        header("Location: ./register?action=updatePwd");
        die();
    }
}
$zoom        = ! empty($_GET['zoom']) ? $_GET['zoom'] : null;
$encounterId = ! empty($_GET['encId']) ? $_GET['encId'] : null;
$stopId = ! empty($_GET['stopId']) ? $_GET['stopId'] : null;
$gymId = ! empty($_GET['gymId']) ? $_GET['gymId'] : null;
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
    if (strtolower($fork) === "default" || strtolower($fork) === "beta") {
        $getList = new \Scanner\RDM();
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
        echo '<link rel="shortcut icon" href="' . $faviconPath . '" type="image/x-icon">';
    } else {
        echo '<link rel="shortcut icon" href="' . $appIconPath . 'favicon.ico" type="image/x-icon">';
    } ?>
    <!-- non-retina iPhone pre iOS 7 -->
    <link rel="apple-touch-icon" href="<?php echo $appIconPath; ?>114x114.png" sizes="57x57">
    <!-- non-retina iPad pre iOS 7 -->
    <link rel="apple-touch-icon" href="<?php echo $appIconPath; ?>144x144.png" sizes="72x72">
    <!-- non-retina iPad iOS 7 -->
    <link rel="apple-touch-icon" href="<?php echo $appIconPath; ?>152x152.png" sizes="76x76">
    <!-- retina iPhone pre iOS 7 -->
    <link rel="apple-touch-icon" href="<?php echo $appIconPath; ?>114x114.png" sizes="114x114">
    <!-- retina iPhone iOS 7 -->
    <link rel="apple-touch-icon" href="<?php echo $appIconPath; ?>120x120.png" sizes="120x120">
    <!-- retina iPad pre iOS 7 -->
    <link rel="apple-touch-icon" href="<?php echo $appIconPath; ?>144x144.png" sizes="144x144">
    <!-- retina iPad iOS 7 -->
    <link rel="apple-touch-icon" href="<?php echo $appIconPath; ?>152x152.png" sizes="152x152">
    <!-- retina iPhone 6 iOS 7 -->
    <link rel="apple-touch-icon" href="<?php echo $appIconPath; ?>180x180.png" sizes="180x180">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
    <?php
    include('filterimages.php');

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
    /* Cookie Disclamer */
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
    } ?>

    <script>
        var token = '<?php echo (! empty($_SESSION['token'])) ? $_SESSION['token'] : ""; ?>';
    </script>
    <link href="node_modules/leaflet-geosearch/assets/css/leaflet.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
    <link rel="stylesheet" href="node_modules/datatables/media/css/jquery.dataTables.min.css">
    <script src="static/js/vendor/modernizr.custom.js"></script>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.css">
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
    <!-- Flag Icons -->
    <link rel="stylesheet" href="node_modules/flag-icon-css/css/flag-icon.min.css" />
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
        <a class="btn btn-link" data-bs-toggle="offcanvas" href="#leftNav" role="button" title="<?php echo i8ln('Options') ?>" aria-controls="leftNav"><i class='fas fa-sliders-h' style='color:white;font-size:20px;vertical-align:middle;pointer-events:none'></i></a>

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
        if ($patreonUrl != "") {
            echo '<a href="' . $patreonUrl . '" target="_blank" style="float:right;padding:0 5px;">
                 <i class="fab fa-patreon" title="' . i8ln('Patreon') . '" style="position:relative;vertical-align:middle;color:white;margin-left:10px;font-size:20px;"></i>
                 </a>';
        }
        if ($customUrl != "") {
            echo '<a href="' . $customUrl . '" target="_blank" style="float:right;padding:0 5px;">
                 <i class="' . $customUrlFontIcon . '" style="position:relative;vertical-align:middle;color:white;margin-left:10px;font-size:20px;"></i>
                 </a>';
        }
        if (! $noHeaderWeatherIcon) { ?>
            <div id="currentWeather"></div>
        <?php }
        if (!empty($_SESSION['user']->id)) {
            echo "<a href='#accountModal' data-bs-toggle='modal' style='float:right;padding:0 5px;' title='" . i8ln('Profile') . "'><img src='" .  $_SESSION['user']->avatar . "' style='height:40px;width:40px;border-radius:50%;border:2px solid;vertical-align: middle;'></a>";
        } else {
            echo "<a href='#accountModal' data-bs-toggle='modal' style='float:right;padding:0 5px;' title='" . i8ln('Profile') . "'><i class='fas fa-user' style='color:white;font-size:20px;vertical-align:middle;'></i></a>";
        }
        ?>
    </header>
    <!-- NAV -->
    <div class="offcanvas left offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="leftNav" aria-labelledby="leftNavLabel">
        <div class="offcanvas-body left">
            <div class="accordion accordion-flush" id="accordionNav">
                <?php
                if (! $noPokemon || ! $noNests) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemOne" aria-expanded="false" aria-controls="navItemOne">
                                <?php if (! $noNests) { ?>
                                    <h5><?php echo i8ln('Pokémon &amp; Nests') ?></h5>
                                <?php
                                } else { ?>
                                    <h5><?php echo i8ln('Pokémon') ?></h5>
                                <?php } ?>
                            </button>
                        </h2>
                        <div id="navItemOne" class="accordion-collapse collapse" aria-labelledby="navItemOne" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                               <div class="card">
                                  <div class="card-body">
                                        <?php
                                        if (! $noPokemon) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="pokemon-switch" type="checkbox" name="pokemon-switch">
                                                <label class="form-check-label" for="pokemon-switch"><?php echo i8ln('Pokémon') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php
                                        }
                                        if (! $noNests) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="nests-switch" type="checkbox" name="nests-switch">
                                                <label class="form-check-label" for="nests-switch"><?php echo i8ln('Nests') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php } ?>
                                        <div id="pokemon-filter-wrapper" style="display:none">
                                            <?php
                                            if (!$noTinyRat) { ?>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="tiny-rat-switch" type="checkbox" name="tiny-rat-switch">
                                                    <label class="form-check-label" for="tiny-rat-switch"><?php echo i8ln('Only Tiny Rattata') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php }
                                            if (!$noBigKarp) {
                                                ?>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="big-karp-switch" type="checkbox" name="big-karp-switch">
                                                    <label class="form-check-label"  for="big-karp-switch"><?php echo i8ln('Only Big Magikarp') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php } ?>
                                            <div class="overflow-hidden">
                                                <div class="row gx-3">
                                                    <?php
                                                    if (! $noMinIV) { ?>
                                                        <div class="col" >
                                                            <div class="p-1 border bg-light">
                                                                <input id="min-iv" type="number" min="0" max="100" name="min-iv"/>
                                                                <label for="min-iv"><?php echo i8ln('Min IV') ?></label>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                    if (! $noMinLevel) { ?>
                                                        <div class="col">
                                                            <div class="p-1 border bg-light">
                                                                <input id="min-level" type="number" min="0" max="100" name="min-level"/>
                                                                <label for="min-level"><?php echo i8ln('Min Lvl') ?></label>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php if (! $noHidePokemon && ! $noExcludeMinIV) { ?>
                                                <div class="form-control" style="position:relative;top:8px;">
                                                    <ul class="nav nav-tabs nav-fill" id="pokemonHideMin" role="tablist">
                                                        <?php
                                                        $firstTab = 1;
                                                        if (! $noHidePokemon) { ?>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="exclude-pokemon-tab" data-bs-toggle="tab" data-bs-target="#exclude-pokemon" type="button" role="tab" aria-controls="exclude-pokemon" aria-selected="false"><?php echo i8ln('Hide Pokémon') ?></button>
                                                            </li>
                                                        <?php
                                                        $firstTab++;
                                                        }
                                                        if (! $noExcludeMinIV) { ?>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="exclude-min-iv-tab" data-bs-toggle="tab" data-bs-target="#exclude-min-iv" type="button" role="tab" aria-controls="exclude-min-iv" aria-selected="false"><?php echo i8ln('Excl. Min IV/Lvl') ?></button>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                    <div class="tab-content" id="pokemonHideMinContent">
                                                        <?php
                                                        $firstTabContent = 1;
                                                        if (! $noHidePokemon) { ?>
                                                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-pokemon" role="tabpanel" aria-labelledby="exclude-pokemon-tab">
                                                                <div class="container scroll-container">
                                                                    <?php pokemonFilterImages($noPokemonNumbers, '', [], 2); ?>
                                                                </div>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="btn btn-secondary select-all" href="#"><?php echo i8ln('All') ?></a>
                                                                <a class="btn btn-secondary hide-all" href="#"><?php echo i8ln('None') ?></a>
                                                            </div>
                                                        <?php }
                                                        $firstTabContent++;
                                                        if (! $noExcludeMinIV) { ?>
                                                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-min-iv" role="tabpanel" aria-labelledby="exclude-min-iv-tab">
                                                                <div class="container scroll-container">
                                                                    <?php pokemonFilterImages($noPokemonNumbers, '', [], 3); ?>
                                                                </div>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="btn btn-secondary select-all" href="#"><?php echo i8ln('All') ?></a>
                                                                <a class="btn btn-secondary hide-all" href="#"><?php echo i8ln('None') ?></a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                                <?php
                                            } ?>
                                        </div>
                                        <div id="nest-filter-wrapper" style="display:none">
                                            <?php
                                            if (!$noNestPolygon && !$noNests) { ?>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="nest-polygon-switch" type="checkbox" name="nest-polygon-switch">
                                                    <label class="form-check-label" for="nest-polygon-switch"><?php echo i8ln('Nest Polygon') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php }
                                            if (!$noNestsAvg && !$noNests) { ?>
                                                <div class="nestslider-div">
                                                    <input type="range" class="form-range" min="0" max="<?php echo $nestAvgMax ?>" value="<?php echo $nestAvgDefault ?>" id="nestrange">
                                                    <p><?php echo i8ln('Show nest average. ') ?><span id="nestavg"></span></p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                   </div>
                              </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noPokestops) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemTwo" aria-expanded="false" aria-controls="navItemTwo">
                                <?php if (!$noQuests) { ?>
                                    <h5><?php echo i8ln('Pokéstops &amp; Quests'); ?></h5>
                                <?php
                                } else { ?>
                                    <h5><?php echo i8ln('Pokéstops'); ?></h5>
                                <?php } ?>
                            </button>
                        </h2>
                        <div id="navItemTwo" class="accordion-collapse collapse" aria-labelledby="navItemTwo" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                               <div class="card">
                                  <div class="card-body">
                                        <?php
                                        if (! $noPokestops) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="pokestops-switch" type="checkbox" name="pokestops-switch">
                                                <label class="form-check-label" for="pokestops-switch"><?php echo i8ln('Pokéstops') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php
                                        } ?>
                                        <div id="pokestops-filter-wrapper" style="display:none">
                                            <?php
                                            if (! $noAllPokestops) { ?>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="allPokestops-switch" type="checkbox" name="allPokestops-switch">
                                                    <label class="form-check-label" for="allPpokestops-switch"><?php echo i8ln('All Pokéstops') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php
                                            }
                                            if (! $noLures) { ?>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="lures-switch" type="checkbox" name="lures-switch">
                                                    <label class="form-check-label" for="lures-switch"><?php echo i8ln('Lured Pokéstops only') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php
                                            }
                                            if (! $noTeamRocket) { ?>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="rocket-switch" type="checkbox" name="rocket-switch">
                                                    <label class="form-check-label" for="rocket-switch"><?php echo i8ln('Rocket Pokéstops only') ?></label>
                                                </div>
                                            <?php
                                            } ?>
                                            <div id="rocket-wrapper" style="display:none">
                                                <div class="dropdown-divider"></div>
                                                <?php
                                                if (! $noTeamRocket && ! $noTeamRocketTimer) { ?>
                                                    <div class="form form-switch">
                                                        <input class="form-check-input" id="rocket-timer-switch" type="checkbox" name="rocket-timer-switch">
                                                        <label class="form-check-label" for="rocket-timer-switch"><?php echo i8ln('Rocket Pokéstops timer') ?></label>
                                                    </div>
                                                    <div class="dropdown-divider"></div>
                                                <?php
                                                } ?>
                                                <div class="form-control">
                                                    <ul class="nav nav-tabs nav-fill" id="rocketHide" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="exclude-rocket-tab" data-bs-toggle="tab" data-bs-target="#exclude-rocket" type="button" role="tab" aria-controls="exclude-rocket" aria-selected="false"><?php echo i8ln('Hide Grunts') ?></button>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content" id="rocketHideContent">
                                                        <div class="tab-pane fade show active" id="exclude-rocket" role="tabpanel" aria-labelledby="exclude-rocket-tab">
                                                            <div class="container scroll-container">
                                                                <?php
                                                                if ($generateExcludeGrunts === true) {
                                                                    gruntFilterImages($noGruntNumbers, '', array_diff(range(1, $numberOfGrunt), $getList->generated_exclude_list('gruntlist')), 10);
                                                                } else {
                                                                    gruntFilterImages($noGruntNumbers, '', $excludeGrunts, 10);
                                                                } ?>
                                                            </div>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="btn btn-secondary select-all-grunt" href="#"><?php echo i8ln('All') ?></a>
                                                            <a class="btn btn-secondary hide-all-grunt" href="#"><?php echo i8ln('None') ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            if (! $noQuests) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="quests-switch" type="checkbox" name="quests-switch">
                                                    <label class="form-check-label" for="quests-switch"><?php echo i8ln('Quest Pokéstops only') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php
                                            } ?>
                                            <div id="quests-filter-wrapper" style="display:none">
                                                <div class="form-control">
                                                    <ul class="nav nav-tabs nav-fill" id="questHide" role="tablist">
                                                        <?php
                                                        $firstTab = 1;
                                                        if (! $noQuestsPokemon) { ?>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="exclude-quest-pokemon-tab" data-bs-toggle="tab" data-bs-target="#exclude-quest-pokemon" type="button" role="tab" aria-controls="exclude-quest-pokemon" aria-selected="false"><?php echo i8ln('Pokémon') ?></button>
                                                            </li>
                                                        <?php
                                                        $firstTab++;
                                                        }
                                                        if (! $noQuestsItems) { ?>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="exclude-quest-item-tab" data-bs-toggle="tab" data-bs-target="#exclude-quest-item" type="button" role="tab" aria-controls="exclude-quest-item" aria-selected="false"><?php echo i8ln('Items') ?></button>
                                                            </li>
                                                        <?php }
                                                        if (! $noQuestsEnergy) { ?>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="exclude-quest-energy-tab" data-bs-toggle="tab" data-bs-target="#exclude-quest-energy" type="button" role="tab" aria-controls="exclude-quest-energy" aria-selected="false"><?php echo i8ln('Energy') ?></button>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                    <div class="tab-content" id="pokemonHideMinContent">
                                                        <?php
                                                        $firstTabContent = 1;
                                                        if (! $noQuestsPokemon) { ?>
                                                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-quest-pokemon" role="tabpanel" aria-labelledby="exclude-quest-pokemon-tab">
                                                                <div class="container scroll-container">
                                                                    <?php
                                                                    if ($generateExcludeQuestsPokemon === true) {
                                                                        pokemonFilterImages($noPokemonNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('pokemonlist')), 8);
                                                                    } else {
                                                                        pokemonFilterImages($noPokemonNumbers, '', $excludeQuestsPokemon, 8);
                                                                    } ?>
                                                                </div>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="btn btn-secondary select-all" href="#"><?php echo i8ln('All') ?></a>
                                                                <a class="btn btn-secondary hide-all" href="#"><?php echo i8ln('None') ?></a>
                                                            </div>
                                                        <?php }
                                                        $firstTabContent++;
                                                        if (! $noQuestsItems) { ?>
                                                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-quest-item" role="tabpanel" aria-labelledby="exclude-quest-item-tab">
                                                                <div class="container scroll-container">
                                                                    <?php
                                                                    if ($generateExcludeQuestsItem === true) {
                                                                        itemFilterImages($noItemNumbers, '', array_diff(range(1, $numberOfItem), $getList->generated_exclude_list('itemlist')), 9);
                                                                    } else {
                                                                        itemFilterImages($noItemNumbers, '', $excludeQuestsItem, 9);
                                                                    } ?>
                                                                </div>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="btn btn-secondary select-all-item" href="#"><?php echo i8ln('All') ?></a>
                                                                <a class="btn btn-secondary hide-all-item" href="#"><?php echo i8ln('None') ?></a>
                                                            </div>
                                                        <?php }
                                                        $firstTabContent++;
                                                        if (! $noQuestsEnergy) { ?>
                                                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-quest-energy" role="tabpanel" aria-labelledby="exclude-quest-energy-tab">
                                                                <div class="container scroll-container">
                                                                    <?php
                                                                    if ($generateExcludeQuestsEnergy === true) {
                                                                        energyFilterImages($noPokemonNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('energylist')), 9);
                                                                    } else {
                                                                        energyFilterImages($noPokemonNumbers, '', $excludeQuestsEnergy, 9);
                                                                    } ?>
                                                                </div>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="btn btn-secondary select-all-energy" href="#"><?php echo i8ln('All') ?></a>
                                                                <a class="btn btn-secondary hide-all-energy" href="#"><?php echo i8ln('None') ?></a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                                <div class="dustslider">
                                                    <input type="range" class="form-range" min="0" max="3500" value="500" class="slider" id="dustrange">
                                                    <p><?php echo i8ln('Show stardust ') ?><span id="dustvalue"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noGyms) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemThree" aria-expanded="false" aria-controls="navItemThree">
                                <?php if (! $noRaids) { ?>
                                     <h5><?php echo i8ln('Gym &amp; Raid') ?></h5>
                                <?php
                                } else { ?>
                                     <h5><?php echo i8ln('Gym') ?></h5>
                                <?php } ?>
                            </button>
                        </h2>
                        <div id="navItemThree" class="accordion-collapse collapse" aria-labelledby="navItemThree" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                        <?php
                                        if (! $noRaids) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="raids-switch" type="checkbox" name="raids-switch">
                                                <label class="form-check-label" for="raids-switch"><?php echo i8ln('Raids') ?></label>
                                            </div>
                                            <div id="raids-filter-wrapper" style="display:none">
                                                <div class="dropdown-divider"></div>
                                                <?php
                                                if (! $noRaidTimer) { ?>
                                                    <div class="form form-switch">
                                                        <input class="form-check-input" id="raid-timer-switch" type="checkbox" name="raid-timer-switch">
                                                        <label class="form-check-label" for="raid-timer-switch"><?php echo i8ln('Raids Timer') ?></label>
                                                    </div>
                                                    <div class="dropdown-divider"></div>
                                                <?php
                                                }
                                                if (! $noActiveRaids) { ?>
                                                    <div class="form form-switch">
                                                        <input class="form-check-input" id="active-raids-switch" type="checkbox" name="active-raids-switch">
                                                        <label class="form-check-label" for="active-raids-switch"><?php echo i8ln('Only Active Raids') ?></label>
                                                    </div>
                                                    <div class="dropdown-divider"></div>
                                                <?php
                                                }
                                                if (! $noMinMaxRaidLevel) { ?>
                                                    <div class="form-floating">
                                                        <select class="form-select" aria-label="min-level-raids-filter" name="min-level-raids-filter-switch" id="min-level-raids-filter-switch">
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                        </select>
                                                        <label for="min-level-raids-filter-switch"><?php echo i8ln('Minimum Raid Level') ?></label>
                                                    </div>
                                                    <div class="dropdown-divider"></div>
                                                    <div class="form-floating">
                                                        <select class="form-select" aria-label="max-level-raids-filter" name="max-level-raids-filter-switch" id="max-level-raids-filter-switch">
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                        </select>
                                                        <label for="max-level-raids-filter-switch"><?php echo i8ln('Maximum Raid Level') ?></label>
                                                    </div>
                                                    <div class="dropdown-divider"></div>
                                                <?php } ?>
                                                <div class="form-control">
                                                    <ul class="nav nav-tabs nav-fill" id="raidHide" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="exclude-raidboss-tab" data-bs-toggle="tab" data-bs-target="#exclude-raidboss" type="button" role="tab" aria-controls="exclude-raidboss" aria-selected="false"><?php echo i8ln('Hide Raidboss') ?></button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="exclude-raidegg-tab" data-bs-toggle="tab" data-bs-target="#exclude-raidegg" type="button" role="tab" aria-controls="exclude-raidegg" aria-selected="false"><?php echo i8ln('Hide Raidegg') ?></button>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content" id="raidHideContent">
                                                        <div class="tab-pane fade show active" id="exclude-raidboss" role="tabpanel" aria-labelledby="exclude-raidboss-tab">
                                                            <div class="container scroll-container">
                                                                <?php
                                                                if ($generateExcludeRaidboss === true) {
                                                                    pokemonFilterImages($noRaidbossNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('raidbosslist')), 11);
                                                                } else {
                                                                    pokemonFilterImages($noRaidbossNumbers, '', $excludeRaidboss, 11);
                                                                } ?>
                                                            </div>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="btn btn-secondary select-all" href="#"><?php echo i8ln('All') ?></a>
                                                            <a class="btn btn-secondary hide-all" href="#"><?php echo i8ln('None') ?></a>
                                                        </div>
                                                        <div class="tab-pane fade" id="exclude-raidegg" role="tabpanel" aria-labelledby="exclude-raidegg-tab">
                                                            <div class="container scroll-container">
                                                                <?php raideggFilterImages($noRaideggNumbers, '', $excludeRaidegg, 12); ?>
                                                            </div>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="btn btn-secondary select-all-egg" href="#"><?php echo i8ln('All') ?></a>
                                                            <a class="btn btn-secondary hide-all-egg" href="#"><?php echo i8ln('None') ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php } ?>
                                        <div class="form form-switch">
                                            <input class="form-check-input" id="gyms-switch" type="checkbox" name="gyms-switch">
                                            <label class="form-check-label" for="gyms-switch"><?php echo i8ln('Gyms') ?></label>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                        <div id="gyms-filter-wrapper" style="display:none">
                                            <div class="dropdown-divider"></div>
                                            <?php
                                            if (! $noTeams) { ?>
                                                <div class="form-floating">
                                                    <select class="form-select" aria-label="teams-gyms-filter" name="team-gyms-filter-switch" id="team-gyms-only-switch">
                                                        <option value="0"><?php echo i8ln('All'); ?></option>
                                                        <option value="1"><?php echo i8ln('Mystic'); ?></option>
                                                        <option value="2"><?php echo i8ln('Valor'); ?></option>
                                                        <option value="3"><?php echo i8ln('Instinct'); ?></option>
                                                    </select>
                                                    <label for="team-gyms-only-switch"><?php echo i8ln('Team'); ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php }
                                            if (! $noOpenSpot) { ?>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="open-gyms-only-switch" type="checkbox" name="open-gyms-only-switch">
                                                    <label class="form-check-label" for="open-gyms-only-switch"><?php echo i8ln('Open Spot') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php }
                                            if (! $noMinMaxFreeSlots) { ?>
                                                <div class="form-floating">
                                                    <select class="form-select" aria-label="min-level-gyms-filter" name="min-level-gyms-filter-switch" id="min-level-gyms-filter-switch">
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                    </select>
                                                    <label for="min-level-gyms-filter-switch"><?php echo i8ln('Minimum Free Slots'); ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-floating">
                                                    <select class="form-select" aria-label="max-level-gyms-filter" name="max-level-gyms-filter-switch" id="max-level-gyms-filter-switch">
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                    </select>
                                                    <label for="max-level-gyms-filter-switch"><?php echo i8ln('Maximum Free Slots'); ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php }
                                            if (! $noLastScan) { ?>
                                                <div class="form-floating">
                                                    <select class="form-select" aria-label="last-update-gyms-filter" name="last-update-gyms-switch" id="last-update-gyms-switch">
                                                        <option value="0"><?php echo i8ln('All'); ?></option>
                                                        <option value="1"><?php echo i8ln('Last Hour'); ?></option>
                                                        <option value="6"><?php echo i8ln('Last 6 Hours'); ?></option>
                                                        <option value="12"><?php echo i8ln('Last 12 Hours'); ?></option>
                                                        <option value="24"><?php echo i8ln('Last 24 Hours'); ?></option>
                                                        <option value="168"><?php echo i8ln('Last Week'); ?></option>
                                                    </select>
                                                    <label for="last-update-gyms-switch"><?php echo i8ln('Last Scan'); ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                            <?php } ?>
                                            <div id="gyms-raid-filter-wrapper" style="display:none">
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="ex-eligible-switch" type="checkbox" name="ex-eligible-switch">
                                                    <label class="form-check-label" for="ex-eligible-switch"><?php echo i8ln('EX Eligible Only') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noCommunity) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemFour" aria-expanded="false" aria-controls="navItemFour">
                                <h5><?php echo i8ln('Communities'); ?></h5>
                            </button>
                        </h2>
                        <div id="navItemFour" class="accordion-collapse collapse" aria-labelledby="navItemFour" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form form-switch">
                                            <input class="form-check-input" id="communities-switch" type="checkbox" name="communities-switch">
                                            <label class="form-check-label" for="communities-switch"><?php echo i8ln('Communities') ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noPortals || ! $noS2Cells || ! $noPoi) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemFive" aria-expanded="false" aria-controls="navItemFive">
                                <h5><?php echo i8ln('Ingress / S2Cell'); ?></h5>
                            </button>
                        </h2>
                        <div id="navItemFive" class="accordion-collapse collapse" aria-labelledby="navItemFive" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                        <?php
                                        if (! $noPortals) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="portals-switch" type="checkbox" name="portals-switch">
                                                <label class="form-check-label" for="portals-switch"><?php echo i8ln('Portals') ?></label>
                                            </div>
                                            <div class="form-floating" id="new-portals-only-wrapper" style="display:none">
                                                <select class="form-select" aria-label="new-portals-only-switch" name="new-portals-only-switch" id="new-portals-only-switch">
                                                    <option value = "0"><?php echo i8ln('All'); ?></option>
                                                    <option value = "1"><?php echo i8ln('Only new'); ?></option>
                                                </select>
                                                <label for="new-portals-only-switch"><?php echo i8ln('Portal age') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noPoi) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="poi-switch" type="checkbox" name="poi-switch">
                                                <label class="form-check-label" for="poi-switch"><?php echo i8ln('POI') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noS2Cells) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="s2-switch" type="checkbox" name="s2-switch">
                                                <label class="form-check-label" for="s2-switch"><?php echo i8ln('Show S2 Cells') ?></label>
                                            </div>
                                            <div id="s2-switch-wrapper" style="display:none">
                                                <div class="dropdown-divider"></div>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="s2-level13-switch" type="checkbox" name="s2-level13-switch">
                                                    <label class="form-check-label" for="s2-level13-switch"><?php echo i8ln('EX trigger Cells') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="s2-level14-switch" type="checkbox" name="s2-level14-switch">
                                                    <label class="form-check-label" for="s2-level14-switch"><?php echo i8ln('Gym placement Cells') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="s2-level17-switch" type="checkbox" name="s2-level17-switch">
                                                    <label class="form-check-label" for="s2-level17-switch"><?php echo i8ln('Pokéstop placement Cells') ?></label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noSearchLocation || ! $noNests || ! $noStartMe || ! $noStartLast || ! $noFollowMe || ! $noPokestops || ! $noSpawnPoints || ! $noRanges || ! $noWeatherOverlay || ! $noSpawnArea) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemSix">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemSix" aria-expanded="false" aria-controls="navItemSix">
                                <?php if (! $noSearchLocation) { ?>
                                    <h5><?php echo i8ln('Location &amp; Search') ?></h5>
                                <?php
                                } else { ?>
                                    <h5><?php echo i8ln('Location') ?></h5>
                                <?php } ?>
                            </button>
                        </h2>
                        <div id="navItemSix" class="accordion-collapse collapse" aria-labelledby="navItemSix" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                        <?php
                                        if (! $noWeatherOverlay) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="weather-switch" type="checkbox" name="weather-switch">
                                                <label class="form-check-label" for="weather-switch"><?php echo i8ln('Weather Conditions') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noSpawnPoints) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="spawnpoints-switch" type="checkbox" name="spawnpoints-switch">
                                                <label class="form-check-label" for="spawnpoints-switch"><?php echo i8ln('Spawn Points') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noRanges) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="ranges-switch" type="checkbox" name="ranges-switch">
                                                <label class="form-check-label" for="ranges-switch"><?php echo i8ln('Ranges') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noScanPolygon) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="scan-area-switch" type="checkbox" name="scan-area-switch">
                                                <label class="form-check-label" for="scan-area-switch"><?php echo i8ln('Scan Areas') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noSearchLocation) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="scan-location-switch" type="checkbox" name="scan-location-switch">
                                                <label class="form-check-label" for="scan-location-switch"><?php echo i8ln('Real time scanner location') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noSearchLocation) { ?>
                                            <div class="input-group mb-3" id="search-places">
                                                <span class="input-group-text" id="next-location"><?php echo i8ln('Search location'); ?></span>
                                                <input type="text" class="form-control" id="next-location" aria-describedby="next-location">
                                            </div>
                                            <ul id="search-places-results" class="search-results places-results"></ul>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noStartMe) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="start-at-user-location-switch" type="checkbox" name="start-at-user-location-switch">
                                                <label class="form-check-label" for="start-at-user-location-switch"><?php echo i8ln('Start map at my position') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noStartLast) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="start-at-last-location-switch" type="checkbox" name="start-at-last-location-switch">
                                                <label class="form-check-label" for="start-at-last-location-switch"><?php echo i8ln('Start map at last position') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noFollowMe) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="follow-my-location-switch" type="checkbox" name="follow-my-location-switch">
                                                <label class="form-check-label" for="follow-my-location-switch"><?php echo i8ln('Follow me') ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noNotifyPokemon || ! $noNotifyRarity || ! $noNotifyIv || ! $noNotifyLevel || ! $noNotifySound || ! $noNotifyRaid || ! $noNotifyBounce || ! $noNotifyNotification) { ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingItemSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemSeven" aria-expanded="false" aria-controls="navItemSeven">
                            <h5><?php echo i8ln('Notification') ?></h5>
                        </button>
                    </h2>
                    <div id="navItemSeven" class="accordion-collapse collapse" aria-labelledby="navItemSeven" data-bs-parent="#accordionNav">
                        <div class="accordion-body bg-light">
                            <div class="card">
                                <div class="card-body">
                                    <?php
                                    if (! $noNotifyPokemon) { ?>
                                        <div class="form-control">
                                            <ul class="nav nav-tabs nav-fill" id="notifyPokemon" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="notify-pokemon-tab" data-bs-toggle="tab" data-bs-target="#notify-pokemon" type="button" role="tab" aria-controls="notify-pokemon" aria-selected="false"><?php echo i8ln('Notify of Pokémon') ?></button>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="notifyPokemonContent">
                                                <div class="tab-pane fade show active" id="notify-pokemon" role="tabpanel" aria-labelledby="notify-pokemon-tab">
                                                    <div class="container scroll-container">
                                                        <?php pokemonFilterImages($noPokemonNumbers, '', [], 4); ?>
                                                    </div>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="btn btn-secondary select-all notify-pokemon-button" href="#"><?php echo i8ln('All') ?></a>
                                                    <a class="btn btn-secondary hide-all notify-pokemon-button" href="#"><?php echo i8ln('None') ?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                    <?php }
                                    if (! $noNotifyRarity) { ?>
                                        <div class="form-floating">
                                            <select class="form-select" multiple aria-label="notify-rarity" name="notify-rarity" id="notify-rarity">
                                                <option value="Common"><?php echo i8ln('Common'); ?></option>
                                                <option value="Uncommon"><?php echo i8ln('Uncommon'); ?></option>
                                                <option value="Rare"><?php echo i8ln('Rare'); ?></option>
                                                <option value="Very Rare"><?php echo i8ln('Very Rare'); ?></option>
                                                <option value="Ultra Rare"><?php echo i8ln('Ultra Rare'); ?></option>
                                            </select>
                                            <label for="notify-rarity"><?php echo i8ln('Notify of Rarity'); ?></label>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                    <?php }
                                    if (! $noNotifyIv || ! $noNotifyLevel) { ?>
                                        <div class="overflow-hidden">
                                            <div class="row gx-3">
                                                <?php
                                                if (! $noNotifyIv) { ?>
                                                    <div class="col" >
                                                        <div class="p-1 border bg-light">
                                                            <input id="notify-perfection" type="number" min="0" max="100" name="notify-perfection"/>
                                                            <label for="notify-perfection"><?php echo i8ln('Notify of IV') ?></label>
                                                        </div>
                                                    </div>
                                                <?php }
                                                if (! $noNotifyLevel) { ?>
                                                    <div class="col">
                                                        <div class="p-1 border bg-light">
                                                            <input id="notify-level" type="number" min="0" max="35" name="notify-level"/>
                                                            <label for="notify-level"><?php echo i8ln('Notify of Level') ?></label>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                    <?php }
                                    if (! $noNotifyRaid) { ?>
                                        <div class="form-floating">
                                            <select class="form-select" aria-label="notify-raid" name="notify-raid" id="notify-raid">
                                                <option value="0"><?php echo i8ln('Disable') ?></option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                            </select>
                                            <label for="notify-raid"><?php echo i8ln('Notify of Minimum Raid Level') ?></label>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                    <?php }
                                    if (! $noNotifySound) { ?>
                                        <div class="form form-switch">
                                            <input class="form-check-input" id="sound-switch" type="checkbox" name="sound-switch">
                                            <label class="form-check-label" for="sound-switch"><?php echo i8ln('Notify with sound') ?></label>
                                        </div>
                                        <?php
                                        if (! $noCriesSound) { ?>
                                            <div id="cries-switch-wrapper" style="display:none">
                                                <div class="dropdown-divider"></div>
                                                <div class="form form-switch">
                                                    <input class="form-check-input" id="cries-switch" type="checkbox" name="cries-switch">
                                                    <label class="form-check-label" for="cries-switch"><?php echo i8ln('Use Pokémon cries') ?></label>
                                                </div>
                                            </div>
                                        <?php }
                                    }
                                    if (! $noNotifyBounce) { ?>
                                        <div class="dropdown-divider"></div>
                                        <div class="form form-switch">
                                            <input class="form-check-input" id="bounce-switch" type="checkbox" name="bounce-switch">
                                            <label class="form-check-label" for="bounce-switch"><?php echo i8ln('Bounce') ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }
                if (! $noDarkMode || ! $noMapStyle || ! $noDirectionProvider || ! $noIconSize || ! $noIconNotifySizeModifier || ! $noGymStyle || ! $noLocationStyle) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemEight">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemEight" aria-expanded="false" aria-controls="navItemEight">
                                <h5><?php echo i8ln('Style') ?></h5>
                            </button>
                        </h2>
                        <div id="navItemEight" class="accordion-collapse collapse" aria-labelledby="navItemEight" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                        <?php
                                        if (! $noDarkMode) { ?>
                                            <div class="form form-switch">
                                                <input class="form-check-input" id="dark-mode-switch" type="checkbox" name="dark-mode-switch">
                                                <label class="form-check-label" for="dark-mode-switch"><?php echo i8ln('Dark Mode') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noMapStyle && ! $forcedTileServer) {
                                            $mapStyleJson = file_get_contents('static/dist/data/mapstyle.min.json');
                                            $mapStyles = json_decode($mapStyleJson, true); ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="map-style" name="map-style" id="map-style">
                                                    <?php
                                                    foreach ($mapStyles as $k => $mapStyle) {
                                                        echo '<option value="' . $k  . '">' . i8ln($mapStyle) . '</option>';
                                                    } ?>
                                                </select>
                                                <label for="mapstyle"><?php echo i8ln('Map Style') ?></label>
                                            </div>
                                        <?php }
                                        if (! $noDirectionProvider) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="direction-provider" name="direction-provider" id="direction-provider">
                                                    <option value="apple"><?php echo i8ln('Apple') ?></option>
                                                    <option value="google"><?php echo i8ln('Google (Directions)') ?></option>
                                                    <option value="goolge-pin"><?php echo i8ln('Google (Pin)') ?></option>
                                                    <option value="waze"><?php echo i8ln('Waze') ?></option>
                                                    <option value="bing"><?php echo i8ln('Bing') ?></option>
                                                    <option value="geouri"><?php echo i8ln('GeoUri') ?></option>
                                                </select>
                                                <label for="direction-provider"><?php echo i8ln('Direction Provider') ?></label>
                                            </div>
                                        <?php }
                                        if (! $noMultipleRepos && ! $copyrightSafe && (sizeof($iconRepos) > 0)) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="icon-style" name="icon-style" id="icon-style">
                                                    <?php
                                                    $count = sizeof($iconRepos);
                                                    for ($i = 0; $i <= $count - 1; $i ++) {
                                                        echo '<option value="' . $iconRepos[$i][1] . '">' . $iconRepos[$i][0] . '</option>';
                                                    } ?>
                                                </select>
                                                <label for="icon-style"><?php echo i8ln('Icon Style') ?></label>
                                            </div>
                                        <?php }
                                        if (! $noIconNotifySizeModifier) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="pokemon-icon-notify-size" name="pokemon-icon-notify-size" id="pokemon-icon-notify-size">
                                                    <option value="0"><?php echo i8ln('Disable') ?></option>
                                                    <option value="15"><?php echo i8ln('Large') ?></option>
                                                    <option value="30"><?php echo i8ln('X-Large') ?></option>
                                                    <option value="45"><?php echo i8ln('XX-Large') ?></option>
                                                </select>
                                                <label for="pokemon-icon-notify-size"><?php echo i8ln('Increase Notified Icon Size') ?></label>
                                            </div>
                                        <?php }
                                        if (! $noGymStyle) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="gym-marker-style" name="gym-marker-style" id="gym-marker-style">
                                                    <option value="ingame"><?php echo i8ln('In-Game') ?></option>
                                                    <option value="shield"><?php echo i8ln('Shield') ?></option>
                                                    <option value="rocketmap"><?php echo i8ln('Rocketmap') ?></option>
                                                    <option value="comic"><?php echo i8ln('Comic') ?></option>
                                                </select>
                                                <label for="gym-marker-style"><?php echo i8ln('Gym Marker Style') ?></label>
                                            </div>
                                        <?php }
                                        if (! $noLocationStyle) {
                                            $markerStyleJson = file_get_contents('static/dist/data/searchmarkerstyle.min.json');
                                            $markerStyles = json_decode($markerStyleJson, true); ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="locationmarker-style" name="locationmarker-style" id="locationmarker-style">
                                                    <?php
                                                    foreach ($markerStyles as $k => $markerStyle) {
                                                        echo '<option value="' . $k  . '">' . i8ln($markerStyle['name']) . '</option>';
                                                    } ?>
                                                </select>
                                                <label for="locationmarker"><?php echo i8ln('Location Icon Marker') ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noAreas && ! empty($quickAreas)) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemNine">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemNine" aria-expanded="false" aria-controls="navItemNine">
                                <h5><?php echo i8ln('Areas') ?></h5>
                            </button>
                        </h2>
                        <div id="navItemNine" class="accordion-collapse collapse" aria-labelledby="navItemNine" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="btn-group flex-wrap" role="group" aria-label="Nested area dropdown">
                                            <?php
                                            foreach ($quickAreas as $name => $areaInfo) {
                                                if (is_string($areaInfo)) {
                                                    $areaLatLon = explode(",", $areaInfo);
                                                    $lat = $areaLatLon[0];
                                                    $lon = $areaLatLon[1];
                                                    $zoom = $areaLatLon[2];
                                                    echo '<a href="" data-lat="' . $lat . '" data-lng="' . $lon . '" data-zoom="' . $zoom . '" class="btn btn-secondary area-go-to">' . $name . '</a>';
                                                } else if (is_array($areaInfo)) { ?>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="<?php echo $name; ?>-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <?php echo $name; ?>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="<?php echo $name; ?>-dropdown">
                                                        <?php
                                                        foreach ($areaInfo as $name => $areaInfo) {
                                                            $areaLatLon = explode(",", $areaInfo);
                                                            $lat = $areaLatLon[0];
                                                            $lon = $areaLatLon[1];
                                                            $zoom = $areaLatLon[2];
                                                            echo '<li><a href="" data-lat="' . $lat . '" data-lng="' . $lon . '" data-zoom="' . $zoom . '" class="dropdown-item area-go-to">' . $name . '</a></li>';
                                                        } ?>
                                                        </ul>
                                                    </div>
                                                <?php }
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if (! $noInfoModal && ! empty($infoModalTitle) && ! empty($infoModalContent)) {
                            echo '<div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#infoModal">' . $infoModalTitle . '</button>
                            </div>';
                        } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <nav id="stats">
        <div class="switch-container">
            <?php
            if ($worldopoleUrl !== "") {
                ?>
                <div class="switch-container">
                    <div>
                        <center><a class="button" href="<?= $worldopoleUrl ?>" target="_blank"><i class="far fa-chart-bar"></i><?php echo i8ln(' Full Stats') ?></a></center>
                    </div>
                </div>
            <?php } ?>
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

    <div id="map"></div>

    <div class="loader" style="display:none;"></div>

    <div class="fullscreen-toggle">
        <button class="map-toggle-button" onClick="toggleFullscreenMap();"><i class="fa fa-expand" aria-hidden="true"></i></button>
    </div>
    <?php if (!$noSearch || (!$noSearchPokemons && !$noSearchPokestops && !$noSearchGyms && !$noSearchManualQuests && !$noSearchNests && !$noSearchPortals)) { ?>
        <div class="search-container">
            <button type="button" class="search-modal-button" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search" aria-hidden="true"></i></button>
        </div>
    <?php }
    if ((! $noPokemon && ! $noManualPokemon) || (! $noGyms && ! $noManualGyms) || (! $noPokestops && ! $noManualPokestops) || (! $noAddNewNests && ! $noNests) || (!$noAddNewCommunity && ! $noCommunity) || (!$noAddPoi && ! $noPoi)) {
        ?>
        <button class="submit-on-off-button" onclick="$('.submit-on-off-button').toggleClass('on');">
            <i class="fas fa-map-marker-alt submit-to-map" aria-hidden="true"></i>
        </button>
    <?php } ?>
</div>

<!-- Load modals.php -->
<?php
include('modals.php');
?>
<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.9.1/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel.min.js"></script>
<script src="node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="node_modules/moment/min/moment-with-locales.min.js"></script>
<script src="https://code.createjs.com/soundjs-0.6.2.min.js"></script>
<script src="node_modules/push.js/bin/push.min.js"></script>
<script src="node_modules/long/dist/long.js"></script>
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
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var centerLat = <?= $startingLat; ?>;
    var centerLng = <?= $startingLng; ?>;
    var locationSet = <?= $locationSet; ?>;
    var motd = <?php echo $noMotd ? 'false' : 'true' ?>;
    var motdContent = <?php echo json_encode($motdContent) ?>;
    var showMotdOnlyOnce = <?php echo $showMotdOnlyOnce === true ? 'true' : 'false' ?>;
    var zoom<?php echo $zoom ? " = " . $zoom : null; ?>;
    var encounterId<?php echo $encounterId ? " = '" . $encounterId . "'" : null; ?>;
    var stopId<?php echo $stopId ? " = '" . $stopId . "'" : null; ?>;
    var gymId<?php echo $gymId ? " = '" . $gymId . "'" : null; ?>;
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
    var hideQuestsEnergy = <?php echo $noQuestsEnergy ? '[]' : $hideQuestsEnergy ?>;
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
    var noNestPolygon = <?php echo $noNestPolygon === true ? 'true' : 'false' ?>;
    var nestGeoJSONfile = '<?php echo $noNestPolygon ? '' : (!empty($nestGeoJSONfile) ? $nestGeoJSONfile : '') ?>';
    var nestBotName = '<?php echo $nestBotName ? $nestBotName : 'Bot' ?>';
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
    var showYourLove = <?php echo $showYourLove === true ? 'true' : 'false' ?>;
    var defaultDustAmount = <?php echo $defaultDustAmount; ?>;
    var nestAvgDefault = <?php echo $nestAvgDefault; ?>;
    var noDarkMode = <?php echo $noDarkMode === true ? 'true' : 'false' ?>;
    var noCatchRates = <?php echo $noCatchRates === true ? 'true' : 'false' ?>;
    var noHideSingleMarker = <?php echo $noHideSingleMarker === true ? 'true' : 'false' ?>;
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
