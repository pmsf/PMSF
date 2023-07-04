<?php
if (! file_exists('config/config.php')) {
    http_response_code(500);
    die("<h1>Config file missing</h1><p>Please ensure you have created your config file (<code>config/config.php</code>).</p>");
}
include('config/config.php');
if ($noNativeLogin === false || $noDiscordLogin === false || $noPatreonLogin === false) {
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
    <link rel="stylesheet" href="node_modules/datatables/media/css/jquery.dataTables.min.css">
    <script src="static/js/vendor/modernizr.custom.js"></script>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <!--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">-->
    <!-- Leaflet -->
    <link rel="stylesheet" href="node_modules/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="static/dist/css/app.min.css">
    <?php if (file_exists('static/dist/css/custom.min.css')) { ?>
        <link rel="stylesheet" href="static/dist/css/custom.min.css">
    <?php } ?>
    <link rel="stylesheet" href="node_modules/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <link href='static/css/leaflet.fullscreen.css' rel='stylesheet' />
    <!-- Flag Icons -->
    <link rel="stylesheet" href="node_modules/flag-icons/css/flag-icons.min.css" />
</head>
<body id="top">
<div class="loader"></div>
<div class="wrapper">
    <!-- Header -->
    <header id="header">
        <a class="btn btn-link" data-bs-toggle="offcanvas" href="#leftNav" role="button" title="<?php echo i8ln('Options'); ?>" aria-controls="leftNav"><i class='fas fa-sliders-h'></i></a>

        <h1><a href="#"><?= $headerTitle ?><img src="<?= $raidmapLogo ?>"></a></h1>
        <?php
        if (! $noStatsToggle) { ?>
            <a class="btn btn-link" data-bs-toggle="offcanvas" href="#rightNav" role="button" title="<?php echo i8ln('Stats'); ?>" aria-controls="rightNav"><i class="fas fa-chart-bar"></i></a>
        <?php }
        if ($paypalUrl != "") { ?>
            <a class="config-icon" href="<?php echo $paypalUrl; ?>" target="_blank">
                <i class="fab fa-paypal" title="<?php echo i8ln('PayPal'); ?>"></i>
            </a>
        <?php }
        if ($telegramUrl != "") { ?>
            <a class="config-icon" href="<?php echo $telegramUrl; ?>" target="_blank">
                <i class="fab fa-telegram" title="<?php echo i8ln('Telegram'); ?>"></i>
            </a>
        <?php }
        if ($whatsAppUrl != "") { ?>
            <a class="config-icon" href="<?php echo $whatsAppUrl; ?>" target="_blank">
                <i class="fab fa-whatsapp" title="<?php echo i8ln('WhatsApp'); ?>"></i>
            </a>
        <?php }
        if ($discordUrl != "") { ?>
            <a class="config-icon" href="<?php echo $discordUrl; ?>" target="_blank">
                <i class="fab fa-discord" title="<?php echo i8ln('Discord'); ?>"></i>
            </a>
        <?php }
        if ($patreonUrl != "") { ?>
            <a class="config-icon" href="<?php echo $patreonUrl; ?>" target="_blank">
                <i class="fab fa-patreon" title="<?php echo i8ln('Patreon'); ?>"></i>
            </a>
        <?php }
        if ($customUrl != "") { ?>
            <a class="config-icon" href="<?php echo $customUrl; ?>" target="_blank">
                <i class="<?php echo $customUrlFontIcon; ?>"></i>
            </a>
        <?php }
        if (! $noHeaderWeatherIcon) { ?>
            <div id="currentWeather"></div>
        <?php }
        if (! $noNotifyNotification) { ?>
            <i id="pushNotifyIcon" data-bs-toggle="tooltip" title=""></i>
        <?php }
        if (!empty($_SESSION['user']->id)) { ?>
            <a href="#accountModal" data-bs-toggle="modal" title="<?php echo i8ln('Profile'); ?>"><img src="<?php echo $_SESSION['user']->avatar; ?>"></a>
            <?php
        } else { ?>
            <a href="#accountModal" data-bs-toggle="modal" title="<?php echo i8ln('Profile'); ?>"><i class="fas fa-user"></i></a>
        <?php } ?>
    </header>
    <!-- Toastr Container -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container right-top position-absolute top-0 end-0 p-3">
            <!-- Toasts generated in map.js -->
        </div>
    </div>
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container right-bottom position-absolute p-3 top-0 end-0">
            <!-- Toasts generated in map.js -->
        </div>
    </div>
    <!-- NAV -->
    <div class="offcanvas left offcanvas-start" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="leftNav" aria-labelledby="leftNavLabel">
        <div class="offcanvas-body left">
            <div class="accordion accordion-flush" id="accordionNav">
                <?php
                if (! $noPokemon || ! $noNests) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemOne" aria-expanded="false" aria-controls="navItemOne">
                                <?php
                                if (! $noPokemon && ! $noNests) { ?>
                                    <h5><?php echo i8ln('Pokémon &amp; Nests') ?></h5>
                                <?php
                                } elseif (! $noPokemon) { ?>
                                    <h5><?php echo i8ln('Pokémon') ?></h5>
                                <?php
                                } else { ?>
                                    <h5><?php echo i8ln('Nests') ?></h5>
                                <?php
                                } ?>
                            </button>
                        </h2>
                        <div id="navItemOne" class="accordion-collapse collapse" aria-labelledby="navItemOne" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                    <?php
                                    if (! $noPokemon) { ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="pokemon-switch" type="checkbox" name="pokemon-switch">
                                            <label class="form-check-label" for="pokemon-switch"><?php echo i8ln('Pokémon') ?></label>
                                        </div>
                                        <div id="pokemon-filter-wrapper" style="display:none">
                                        <?php
                                        if (! $noHighLevelData && ! $noMissingIVOnly) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="missing-iv-only-switch" type="checkbox" name="missing-iv-only-switch">
                                                <label class="form-check-label" for="missing-iv-only-switch"><?php echo i8ln('Only Missing IV') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && ! $noTinyRat) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="tiny-rat-switch" type="checkbox" name="tiny-rat-switch">
                                                <label class="form-check-label" for="tiny-rat-switch"><?php echo i8ln('Only Tiny Rattata') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && ! $noBigKarp) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="big-karp-switch" type="checkbox" name="big-karp-switch">
                                                <label class="form-check-label" for="big-karp-switch"><?php echo i8ln('Only Big Magikarp') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && ! $noZeroIvToggle) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="no-zero-iv-switch" type="checkbox" name="no-zero-iv-switch">
                                                <label class="form-check-label" for="no-zero-iv-switch"><?php echo i8ln('Ignore Filters for 0%IV') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && ! $noHundoIvToggle) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="no-hundo-iv-switch" type="checkbox" name="no-hundo-iv-switch">
                                                <label class="form-check-label" for="no-hundo-iv-switch"><?php echo i8ln('Ignore Filters for 100%IV') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && ! $noXXSToggle) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="no-xxs-switch" type="checkbox" name="no-xxs-switch">
                                                <label class="form-check-label" for="no-xxs-switch"><?php echo i8ln('Ignore Filters for XXS') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && ! $noXXLToggle) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="no-xxl-switch" type="checkbox" name="no-xxl-switch">
                                                <label class="form-check-label" for="no-xxl-switch"><?php echo i8ln('Ignore Filters for XXL') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && ! $noPvp && ! $noIndependantPvpAndStatsToggle) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="no-independant-pvp-switch" type="checkbox" name="no-independant-pvp-switch">
                                                <label class="form-check-label" for="no-independant-pvp-switch"><?php echo i8ln('Independant PVP and IV/LVL Filters') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noDespawnTimeType) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="despawn-time-type-filter" name="despawn-time-type-select" id="despawn-time-type-select">
                                                    <option value="0"><?php echo i8ln('All') ?></option>
                                                    <option value="1"><?php echo i8ln('Verified') ?></option>
                                                    <option value="2"><?php echo i8ln('Unverified') ?></option>
                                                    <option value="3"><?php echo i8ln('Unverified + Nearby') ?></option>
                                                </select>
                                                <label for="despawn-time-type-select"><?php echo i8ln('Despawn Time Type'); ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noPokemonGender) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="pokemon-gender-filter" name="pokemon-gender-select" id="pokemon-gender-select">
                                                    <option value="0"><?php echo i8ln('All') ?></option>
                                                    <option value="1"><?php echo i8ln('Male') ?></option>
                                                    <option value="2"><?php echo i8ln('Female') ?></option>
                                                </select>
                                                <label for="pokemon-gender-select"><?php echo i8ln('Gender'); ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && ! $noPvp && (! $noMinLLRank || ! $noMinGLRank || ! $noMinULRank)) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="overflow-hidden">
                                                <div class="row gx-3">
                                                <?php
                                                if (! $noMinLLRank) { ?>
                                                    <div class="col" >
                                                        <div class="p-1 border bg-light">
                                                            <input id="min-ll-rank" type="number" min="0" max="100" name="min-ll-rank"/>
                                                            <label for="min-ll-rank"><?php echo i8ln('Min LLR') ?></label>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (! $noMinGLRank) { ?>
                                                    <div class="col" >
                                                        <div class="p-1 border bg-light">
                                                            <input id="min-gl-rank" type="number" min="0" max="100" name="min-gl-rank"/>
                                                            <label for="min-gl-rank"><?php echo i8ln('Min GLR') ?></label>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (! $noMinULRank) { ?>
                                                    <div class="col">
                                                        <div class="p-1 border bg-light">
                                                            <input id="min-ul-rank" type="number" min="0" max="100" name="min-ul-rank"/>
                                                            <label for="min-ul-rank"><?php echo i8ln('Min ULR') ?></label>
                                                        </div>
                                                    </div>
                                                <?php
                                                } ?>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHighLevelData && (! $noMinIV || ! $noMinLevel)) { ?>
                                            <div class="dropdown-divider"></div>
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
                                                <?php
                                                }
                                                if (! $noMinLevel) { ?>
                                                    <div class="col">
                                                        <div class="p-1 border bg-light">
                                                            <input id="min-level" type="number" min="0" max="100" name="min-level"/>
                                                            <label for="min-level"><?php echo i8ln('Min Lvl') ?></label>
                                                        </div>
                                                    </div>
                                                <?php
                                                } ?>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        if (! $noHidePokemon || ! $noExcludeMinIV) { ?>
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
                                                if (! $noHighLevelData && ! $noExcludeMinIV) { ?>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="exclude-min-iv-tab" data-bs-toggle="tab" data-bs-target="#exclude-min-iv" type="button" role="tab" aria-controls="exclude-min-iv" aria-selected="false"><?php echo i8ln('Excl. Min IV/Lvl') ?></button>
                                                    </li>
                                                <?php
                                                } ?>
                                            </ul>
                                            <div class="border with-radius">
                                                <div class="tab-content" id="pokemonHideMinContent">
                                                <?php
                                                $firstTabContent = 1;
                                                if (! $noHidePokemon) { ?>
                                                    <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-pokemon" role="tabpanel" aria-labelledby="exclude-pokemon-tab">
                                                        <div class="scroll-container">
                                                            <?php pokemonFilterImages($noPokemonNames, $noPokemonNumbers, '', $pokemonToExclude, 2); ?>
                                                        </div>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="btn btn-secondary select-all" href="#"><?php echo i8ln('All') ?></a>
                                                        <a class="btn btn-secondary hide-all" href="#"><?php echo i8ln('None') ?></a>
                                                    </div>
                                                <?php
                                                    $firstTabContent++;
                                                }
                                                if (! $noExcludeMinIV) { ?>
                                                    <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-min-iv" role="tabpanel" aria-labelledby="exclude-min-iv-tab">
                                                        <div class="scroll-container">
                                                            <?php pokemonFilterImages($noPokemonNames, $noPokemonNumbers, '', $pokemonToExclude, 3); ?>
                                                        </div>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="btn btn-secondary select-all" href="#"><?php echo i8ln('All') ?></a>
                                                        <a class="btn btn-secondary hide-all" href="#"><?php echo i8ln('None') ?></a>
                                                    </div>
                                                <?php
                                                } ?>
                                                </div>
                                            </div>
                                        <?php
                                        } ?>
                                        </div>
                                    <?php
                                    }
                                    if (! $noNests) {
                                        if (! $noPokemon) { ?>
                                        <div class="dropdown-divider"></div>
                                        <?php
                                        } ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="nests-switch" type="checkbox" name="nests-switch">
                                            <label class="form-check-label" for="nests-switch"><?php echo i8ln('Nests') ?></label>
                                        </div>
                                        <div id="nest-filter-wrapper" style="display:none">
                                            <?php
                                            if (! $noNestPolygon) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="nest-polygon-switch" type="checkbox" name="nest-polygon-switch">
                                                    <label class="form-check-label" for="nest-polygon-switch"><?php echo i8ln('Nest Polygon') ?></label>
                                                </div>
                                            <?php }
                                            if (! $noNestsAvg) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="nestslider-div">
                                                    <input type="range" class="form-range" min="0" max="<?php echo $nestAvgMax ?>" value="<?php echo $nestAvgDefault ?>" id="nestrange">
                                                    <p><?php echo i8ln('Show nest average. ') ?><span id="nestavg"></span></p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php
                                    } ?>
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
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="pokestops-switch" type="checkbox" name="pokestops-switch">
                                                <label class="form-check-label" for="pokestops-switch"><?php echo i8ln('Pokéstops') ?></label>
                                            </div>
                                        <?php
                                        } ?>
                                        <div id="pokestops-filter-wrapper" style="display:none">
                                            <?php
                                            if (! $noAllPokestops) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="allPokestops-switch" type="checkbox" name="allPokestops-switch">
                                                    <label class="form-check-label" for="allPpokestops-switch"><?php echo i8ln('All Pokéstops') ?></label>
                                                </div>
                                            <?php
                                            }
                                            if (! $noLures) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="lures-switch" type="checkbox" name="lures-switch">
                                                    <label class="form-check-label" for="lures-switch"><?php echo i8ln('Lured Pokéstops only') ?></label>
                                                </div>
                                            <?php
                                            }
                                            if (! $noEventStops) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="eventstops-switch" type="checkbox" name="eventstops-switch">
                                                    <label class="form-check-label" for="eventstops-switch"><?php echo i8ln('Event Pokéstops only') ?></label>
                                                </div>
                                                <div id="eventstops-wrapper" style="display:none">
                                                <?php
                                                if (! $noEventStopsTimer) { ?>
                                                    <div class="dropdown-divider"></div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" id="eventstops-timer-switch" type="checkbox" name="eventstops-timer-switch">
                                                        <label class="form-check-label" for="eventstops-timer-switch"><?php echo i8ln('Event Pokéstops timer') ?></label>
                                                    </div>
                                                <?php
                                                } ?>
                                                </div>
                                            <?php
                                            }
                                            if (! $noTeamRocket) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="rocket-switch" type="checkbox" name="rocket-switch">
                                                    <label class="form-check-label" for="rocket-switch"><?php echo i8ln('Rocket Pokéstops only') ?></label>
                                                </div>
                                                <div id="rocket-wrapper" style="display:none">
                                                <?php
                                                if (! $noTeamRocketTimer) { ?>
                                                    <div class="dropdown-divider"></div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" id="rocket-timer-switch" type="checkbox" name="rocket-timer-switch">
                                                        <label class="form-check-label" for="rocket-timer-switch"><?php echo i8ln('Rocket Pokéstops timer') ?></label>
                                                    </div>
                                                <?php
                                                } ?>
                                                    <div class="dropdown-divider"></div>
                                                    <div class="border with-radius">
                                                        <ul class="nav nav-tabs nav-fill" id="rocketHide" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="exclude-rocket-tab" data-bs-toggle="tab" data-bs-target="#exclude-rocket" type="button" role="tab" aria-controls="exclude-rocket" aria-selected="false"><?php echo i8ln('Hide Grunts') ?></button>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content" id="rocketHideContent">
                                                            <div class="tab-pane fade show active" id="exclude-rocket" role="tabpanel" aria-labelledby="exclude-rocket-tab">
                                                                <div class="scroll-container">
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
                                            }
                                            if (! $noQuests) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="quests-switch" type="checkbox" name="quests-switch">
                                                    <label class="form-check-label" for="quests-switch"><?php echo i8ln('Quest Pokéstops only') ?></label>
                                                </div>
                                            <?php
                                            if (! $noQuestsARTaskToggle) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="quests-with_ar" type="checkbox" name="quests-with_ar">
                                                    <label class="form-check-label" for="quests-with_ar"><?php echo i8ln('With AR-Scan Task') ?></label>
                                                </div>
                                            <?php
                                            } ?>
                                                <div id="quests-filter-wrapper" style="display:none">
                                                    <div class="dropdown-divider"></div>
                                                    <div class="border with-radius">
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
                                                            <?php
                                                                $firstTab++;
                                                            }
                                                            if (! $noQuestsEnergy) { ?>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="exclude-quest-energy-tab" data-bs-toggle="tab" data-bs-target="#exclude-quest-energy" type="button" role="tab" aria-controls="exclude-quest-energy" aria-selected="false"><?php echo i8ln('Energy') ?></button>
                                                                </li>
                                                            <?php
                                                                $firstTab++;
                                                            }
                                                            if (! $noQuestsCandy) { ?>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="exclude-quest-candy-tab" data-bs-toggle="tab" data-bs-target="#exclude-quest-candy" type="button" role="tab" aria-controls="exclude-quest-candy" aria-selected="false"><?php echo i8ln('Candy') ?></button>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                        <div class="tab-content" id="pokemonHideMinContent">
                                                            <?php
                                                            $firstTabContent = 1;
                                                            if (! $noQuestsPokemon) { ?>
                                                                <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-quest-pokemon" role="tabpanel" aria-labelledby="exclude-quest-pokemon-tab">
                                                                    <div class="scroll-container">
                                                                        <?php
                                                                        if ($generateExcludeQuestsPokemon === true) {
                                                                            pokemonFilterImages($noPokemonNames, $noPokemonNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('pokemonlist')), 8);
                                                                        } else {
                                                                            pokemonFilterImages($noPokemonNames, $noPokemonNumbers, '', $excludeQuestsPokemon, 8);
                                                                        } ?>
                                                                    </div>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="btn btn-secondary select-all" href="#"><?php echo i8ln('All') ?></a>
                                                                    <a class="btn btn-secondary hide-all" href="#"><?php echo i8ln('None') ?></a>
                                                                </div>
                                                            <?php
                                                                $firstTabContent++;
                                                            }
                                                            if (! $noQuestsItems) { ?>
                                                                <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-quest-item" role="tabpanel" aria-labelledby="exclude-quest-item-tab">
                                                                    <div class="scroll-container">
                                                                        <?php
                                                                        if ($generateExcludeQuestsItem === true) {
                                                                            itemFilterImages($noItemNames, $noItemNumbers, '', array_diff(range(1, $numberOfItem), $getList->generated_exclude_list('itemlist')), 9);
                                                                        } else {
                                                                            itemFilterImages($noItemNames, $noItemNumbers, '', $excludeQuestsItem, 9);
                                                                        } ?>
                                                                    </div>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="btn btn-secondary select-all-item" href="#"><?php echo i8ln('All') ?></a>
                                                                    <a class="btn btn-secondary hide-all-item" href="#"><?php echo i8ln('None') ?></a>
                                                                </div>
                                                            <?php
                                                                $firstTabContent++;
                                                            }
                                                            if (! $noQuestsEnergy) { ?>
                                                                <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-quest-energy" role="tabpanel" aria-labelledby="exclude-quest-energy-tab">
                                                                    <div class="scroll-container">
                                                                        <?php
                                                                        if ($generateExcludeQuestsEnergy === true) {
                                                                            energyFilterImages($noPokemonNames, $noPokemonNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('energylist')), 9);
                                                                        } else {
                                                                            energyFilterImages($noPokemonNames, $noPokemonNumbers, '', $excludeQuestsEnergy, 9);
                                                                        } ?>
                                                                    </div>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="btn btn-secondary select-all-energy" href="#"><?php echo i8ln('All') ?></a>
                                                                    <a class="btn btn-secondary hide-all-energy" href="#"><?php echo i8ln('None') ?></a>
                                                                </div>
                                                            <?php
                                                                $firstTabContent++;
                                                            }
                                                            if (! $noQuestsCandy) { ?>
                                                                <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="exclude-quest-candy" role="tabpanel" aria-labelledby="exclude-quest-candy-tab">
                                                                    <div class="scroll-container">
                                                                        <?php
                                                                        if ($generateExcludeQuestsCandy === true) {
                                                                            candyFilterImages($noPokemonNames, $noPokemonNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('candylist')), 13);
                                                                        } else {
                                                                            candyFilterImages($noPokemonNames, $noPokemonNumbers, '', $excludeQuestsCandy, 13);
                                                                        } ?>
                                                                    </div>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="btn btn-secondary select-all-candy" href="#"><?php echo i8ln('All') ?></a>
                                                                    <a class="btn btn-secondary hide-all-candy" href="#"><?php echo i8ln('None') ?></a>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    if (! $noQuestsStardust) { ?>
                                                        <div class="dropdown-divider"></div>
                                                        <div class="dustslider">
                                                            <input type="range" class="form-range" min="0" max="3500" step="10" value="500" class="slider" id="dustrange">
                                                            <p><?php echo i8ln('Show stardust ') ?><span id="dustvalue"></span></p>
                                                        </div>
                                                    <?php } ?>
                                                    <?php
                                                    if (! $noQuestsXP) { ?>
                                                        <div class="dropdown-divider"></div>
                                                        <div class="xpslider">
                                                            <input type="range" class="form-range" min="0" max="5000" step="10" value="500" class="slider" id="xprange">
                                                            <p><?php echo i8ln('Show XP ') ?><span id="xpvalue"></span></p>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php
                                            } ?>
                                        </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noGyms || ! $noRaids) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemThree" aria-expanded="false" aria-controls="navItemThree">
                                <?php if (! $noGyms && ! $noRaids) { ?>
                                     <h5><?php echo i8ln('Gyms &amp; Raids') ?></h5>
                                <?php
                                } elseif (! $noGyms) { ?>
                                     <h5><?php echo i8ln('Gyms') ?></h5>
                                <?php
                                } else { ?>
                                     <h5><?php echo i8ln('Raids') ?></h5>
                                <?php
                                } ?>
                            </button>
                        </h2>
                        <div id="navItemThree" class="accordion-collapse collapse" aria-labelledby="navItemThree" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                    <?php
                                    if (! $noGyms) { ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="gyms-switch" type="checkbox" name="gyms-switch">
                                            <label class="form-check-label" for="gyms-switch"><?php echo i8ln('Gyms') ?></label>
                                        </div>
                                        <div id="gyms-filter-wrapper" style="display:none">
                                        <?php
                                        if (! $noTeams) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="teams-gyms-filter" name="team-gyms-filter-switch" id="team-gyms-only-switch">
                                                    <option value="0"><?php echo i8ln('All'); ?></option>
                                                    <option value="1"><?php echo i8ln('Mystic'); ?></option>
                                                    <option value="2"><?php echo i8ln('Valor'); ?></option>
                                                    <option value="3"><?php echo i8ln('Instinct'); ?></option>
                                                </select>
                                                <label for="team-gyms-only-switch"><?php echo i8ln('Team'); ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noOpenSpot) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="open-gyms-only-switch" type="checkbox" name="open-gyms-only-switch">
                                                <label class="form-check-label" for="open-gyms-only-switch"><?php echo i8ln('Open Spot') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noMinMaxFreeSlots) { ?>
                                            <div class="dropdown-divider"></div>
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
                                        <?php
                                        }
                                        if (! $noLastScan) { ?>
                                            <div class="dropdown-divider"></div>
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
                                        <?php
                                        } ?>
                                        </div>
                                    <?php
                                    }
                                    if (! $noRaids) {
                                        if (! $noGyms) { ?>
                                        <div class="dropdown-divider"></div>
                                        <?php
                                        } ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="raids-switch" type="checkbox" name="raids-switch">
                                            <label class="form-check-label" for="raids-switch"><?php echo i8ln('Raids') ?></label>
                                        </div>
                                        <div id="raids-filter-wrapper" style="display:none">
                                        <?php
                                        if (! $noRaidTimer) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="raid-timer-switch" type="checkbox" name="raid-timer-switch">
                                                <label class="form-check-label" for="raid-timer-switch"><?php echo i8ln('Raids Timer') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noActiveRaids) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="active-raids-switch" type="checkbox" name="active-raids-switch">
                                                <label class="form-check-label" for="active-raids-switch"><?php echo i8ln('Only Active Raids') ?></label>
                                            </div>
                                        <?php
                                        }
                                        if (! $noMinMaxRaidLevel) { ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="min-level-raids-filter" name="min-level-raids-filter-switch" id="min-level-raids-filter-switch">
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
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
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
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                </select>
                                                <label for="max-level-raids-filter-switch"><?php echo i8ln('Maximum Raid Level') ?></label>
                                            </div>
                                        <?php
                                        } ?>
                                            <div class="dropdown-divider"></div>
                                            <div class="border with-radius">
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
                                                        <div class="scroll-container">
                                                            <?php
                                                            if ($generateExcludeRaidboss === true) {
                                                                pokemonFilterImages($noRaidbossNames, $noRaidbossNumbers, '', array_diff(range(1, $numberOfPokemon), $getList->generated_exclude_list('raidbosslist')), 11);
                                                            } else {
                                                                pokemonFilterImages($noRaidbossNames, $noRaidbossNumbers, '', $excludeRaidboss, 11);
                                                            } ?>
                                                        </div>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="btn btn-secondary select-all" href="#"><?php echo i8ln('All') ?></a>
                                                        <a class="btn btn-secondary hide-all" href="#"><?php echo i8ln('None') ?></a>
                                                    </div>
                                                    <div class="tab-pane fade" id="exclude-raidegg" role="tabpanel" aria-labelledby="exclude-raidegg-tab">
                                                        <div class="scroll-container">
                                                            <?php raideggFilterImages($noRaideggNumbers, '', $excludeRaidegg, 30); ?>
                                                        </div>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="btn btn-secondary select-all-egg" href="#"><?php echo i8ln('All') ?></a>
                                                        <a class="btn btn-secondary hide-all-egg" href="#"><?php echo i8ln('None') ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    } ?>
                                        <div id="gyms-raid-filter-wrapper" style="display:none">
                                            <div class="dropdown-divider"></div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="ex-eligible-switch" type="checkbox" name="ex-eligible-switch">
                                                <label class="form-check-label" for="ex-eligible-switch"><?php echo i8ln('EX Eligible Only') ?></label>
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
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="communities-switch" type="checkbox" name="communities-switch">
                                            <label class="form-check-label" for="communities-switch"><?php echo i8ln('Communities') ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noPortals || ! $noPoi || ! $noS2Cells) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingItemFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navItemFive" aria-expanded="false" aria-controls="navItemFive">
                                <h5><?php echo i8ln('Ingress / S2 Cells'); ?></h5>
                            </button>
                        </h2>
                        <div id="navItemFive" class="accordion-collapse collapse" aria-labelledby="navItemFive" data-bs-parent="#accordionNav">
                            <div class="accordion-body bg-light">
                                <div class="card">
                                    <div class="card-body">
                                        <?php
                                        if (! $noPortals) { ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="portals-switch" type="checkbox" name="portals-switch">
                                                <label class="form-check-label" for="portals-switch"><?php echo i8ln('Portals') ?></label>
                                            </div>
                                            <div class="form-floating" id="new-portals-only-wrapper" style="display:none">
                                               <div class="dropdown-divider"></div>
                                                <select class="form-select" aria-label="new-portals-only-switch" name="new-portals-only-switch" id="new-portals-only-switch">
                                                    <option value = "0"><?php echo i8ln('All'); ?></option>
                                                    <option value = "1"><?php echo i8ln('Only new'); ?></option>
                                                </select>
                                                <label for="new-portals-only-switch"><?php echo i8ln('Portal age') ?></label>
                                            </div>
                                        <?php }
                                        if (! $noPoi) {
                                            if (! $noPortals) { ?>
                                            <div class="dropdown-divider"></div>
                                            <?php
                                            } ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="poi-switch" type="checkbox" name="poi-switch">
                                                <label class="form-check-label" for="poi-switch"><?php echo i8ln('POI') ?></label>
                                            </div>
                                        <?php }
                                        if (! $noS2Cells) {
                                            if (! $noPortals || !$noPoi) { ?>
                                            <div class="dropdown-divider"></div>
                                            <?php
                                            } ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="s2-switch" type="checkbox" name="s2-switch">
                                                <label class="form-check-label" for="s2-switch"><?php echo i8ln('Show S2 Cells') ?></label>
                                            </div>
                                            <div id="s2-switch-wrapper" style="display:none">
                                                <?php if (! $noPlacementRanges) { ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="placement-ranges-switch" type="checkbox" name="placement-ranges-switch">
                                                    <label class="form-check-label" for="placement-ranges-switch"><?php echo i8ln('Placement Ranges') ?></label>
                                                </div>
                                                <?php } ?>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="s2-level13-switch" type="checkbox" name="s2-level13-switch">
                                                    <label class="form-check-label" for="s2-level13-switch"><?php echo i8ln('L13 - EX trigger') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="s2-level14-switch" type="checkbox" name="s2-level14-switch">
                                                    <label class="form-check-label" for="s2-level14-switch"><?php echo i8ln('L14 - Gym placement') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="s2-level15-switch" type="checkbox" name="s2-level15-switch">
                                                    <label class="form-check-label" for="s2-level15-switch"><?php echo i8ln('L15 - Nearby Pokémon') ?></label>
                                                </div>
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="s2-level17-switch" type="checkbox" name="s2-level17-switch">
                                                    <label class="form-check-label" for="s2-level17-switch"><?php echo i8ln('L17 - Pokéstop placement') ?></label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noWeatherOverlay || ! $noSpawnPoints || ! $noRanges || ! $noScanPolygon || ! $noLiveScanLocation || ! $noSearchLocation || ! $noStartMe || ! $noStartLast || ! $noFollowMe) { ?>
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
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="weather-switch" type="checkbox" name="weather-switch">
                                                <label class="form-check-label" for="weather-switch"><?php echo i8ln('Weather Conditions') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noSpawnPoints) { ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="spawnpoints-switch" type="checkbox" name="spawnpoints-switch">
                                                <label class="form-check-label" for="spawnpoints-switch"><?php echo i8ln('Spawn Points') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noRanges) { ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="ranges-switch" type="checkbox" name="ranges-switch">
                                                <label class="form-check-label" for="ranges-switch"><?php echo i8ln('Ranges') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noScanPolygon) { ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="scan-area-switch" type="checkbox" name="scan-area-switch">
                                                <label class="form-check-label" for="scan-area-switch"><?php echo i8ln('Scan Areas') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noLiveScanLocation) { ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="scan-location-switch" type="checkbox" name="scan-location-switch">
                                                <label class="form-check-label" for="scan-location-switch"><?php echo i8ln('Real time scanner location') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noSearchLocation) { ?>
                                            <div class="input-group input-group-sm" id="search-places">
                                                <span class="input-group-text" id="next-location"><?php echo i8ln('Search location'); ?></span>
                                                <input type="text" class="form-control" id="next-location" aria-describedby="next-location">
                                            </div>
                                            <ul id="search-places-results" class="search-results places-results"></ul>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noStartMe) { ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="start-at-user-location-switch" type="checkbox" name="start-at-user-location-switch">
                                                <label class="form-check-label" for="start-at-user-location-switch"><?php echo i8ln('Start map at my position') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noStartLast) { ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="start-at-last-location-switch" type="checkbox" name="start-at-last-location-switch">
                                                <label class="form-check-label" for="start-at-last-location-switch"><?php echo i8ln('Start map at last position') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noFollowMe) { ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="follow-my-location-switch" type="checkbox" name="follow-my-location-switch">
                                                <label class="form-check-label" for="follow-my-location-switch"><?php echo i8ln('Follow me') ?></label>
                                            </div>
                                            <?php if (! $noSpawnArea) { ?>
                                            <div id="spawn-area-wrapper" style="display:none">
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="spawn-area-switch" type="checkbox" name="spawn-area-switch">
                                                    <label class="form-check-label" for="spawn-area-switch"><?php echo i8ln('Spawn Area') ?></label>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                if (! $noNotifyPokemon || ! $noNotifyRarity || ! $noNotifyIv || ! $noNotifyLevel || ! $noNotifyNotification || ! $noNotifyRaid || ! $noNotifySound || ! $noNotifyBounce) { ?>
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
                                        <div class="border with-radius">
                                            <ul class="nav nav-tabs nav-fill" id="notifyPokemon" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="notify-pokemon-tab" data-bs-toggle="tab" data-bs-target="#notify-pokemon" type="button" role="tab" aria-controls="notify-pokemon" aria-selected="false"><?php echo i8ln('Notify of Pokémon') ?></button>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="notifyPokemonContent">
                                                <div class="tab-pane fade show active" id="notify-pokemon" role="tabpanel" aria-labelledby="notify-pokemon-tab">
                                                    <div class="scroll-container">
                                                        <?php pokemonFilterImages($noPokemonNames, $noPokemonNumbers, '', $pokemonToExclude, 4); ?>
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
                                                            <label for="notify-perfection"><?php echo i8ln('Notify IV') ?></label>
                                                        </div>
                                                    </div>
                                                <?php }
                                                if (! $noNotifyLevel) { ?>
                                                    <div class="col">
                                                        <div class="p-1 border bg-light">
                                                            <input id="notify-level" type="number" min="0" max="35" name="notify-level"/>
                                                            <label for="notify-level"><?php echo i8ln('Notify Lvl') ?></label>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                    <?php }
                                    if (! $noNotifyNotification) { ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="toast-switch" type="checkbox" name="toast-switch">
                                            <label class="form-check-label" for="toast-switch"><?php echo i8ln('Notify with popup') ?></label>
                                        </div>
                                        <div id="toast-switch-wrapper" style="display:none">
                                            <div class="dropdown-divider"></div>
                                            <div class="toast-slider">
                                                <label for="toast-delay-slider" class="form-label"><?php echo i8ln('Popup close delay') ?></label>
                                                <input type="range" class="form-range" min="0" max="20000" step="1000" id="toast-delay-slider">
                                            </div>
                                            <span id="toast-delay-set"></span>
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
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                            </select>
                                            <label for="notify-raid"><?php echo i8ln('Notify of Minimum Raid Level') ?></label>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                    <?php }
                                    if (! $noNotifySound) { ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="sound-switch" type="checkbox" name="sound-switch">
                                            <label class="form-check-label" for="sound-switch"><?php echo i8ln('Notify with sound') ?></label>
                                        </div>
                                        <?php
                                        if (! $noCriesSound) { ?>
                                            <div id="cries-switch-wrapper" style="display:none">
                                                <div class="dropdown-divider"></div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="cries-switch" type="checkbox" name="cries-switch">
                                                    <label class="form-check-label" for="cries-switch"><?php echo i8ln('Use Pokémon cries') ?></label>
                                                </div>
                                            </div>
                                        <?php }
                                    }
                                    if (! $noNotifyBounce) { ?>
                                        <div class="dropdown-divider"></div>
                                        <div class="form-check form-switch">
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
                if (! $noDarkMode || ! $noMapStyle || ! $noDirectionProvider || ! $noIconSize || ! $noIconNotifySizeModifier || ! $noLocationStyle) { ?>
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
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="dark-mode-switch" type="checkbox" name="dark-mode-switch">
                                                <label class="form-check-label" for="dark-mode-switch"><?php echo i8ln('Dark Mode') ?></label>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                        <?php }
                                        if (! $noMapStyle && ! $forcedTileServer) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="map-style" name="map-style" id="map-style">
                                                    <?php
                                                    foreach ($mapStyleList as $k => $mapStyleInfo) {
                                                        if ((strpos($k, 'google') === false ) && (strpos($k, 'mapbox') === false) && ! empty($mapStyleInfo['url'])) {
                                                            echo '<option value="' . $k  . '">' . i8ln($mapStyleInfo['name']) . '</option>';
                                                        } elseif ((strpos($k, 'google') !== false) && ! empty($gmapsKey)) {
                                                            echo '<option value="' . $k  . '">' . i8ln($mapStyleInfo['name']) . '</option>';
                                                        } elseif ((strpos($k, 'mapbox') !== false) && ! empty($mapStyleInfo['key'])) {
                                                            echo '<option value="' . $k  . '">' . i8ln($mapStyleInfo['name']) . '</option>';
                                                        }
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
                                                    <option value="google_pin"><?php echo i8ln('Google (Pin)') ?></option>
                                                    <option value="waze"><?php echo i8ln('Waze') ?></option>
                                                    <option value="bing"><?php echo i8ln('Bing') ?></option>
                                                    <option value="geouri"><?php echo i8ln('GeoUri') ?></option>
                                                </select>
                                                <label for="direction-provider"><?php echo i8ln('Direction Provider') ?></label>
                                            </div>
                                        <?php }
                                        if (! $copyrightSafe && is_array($iconFolderArray['pokemon']) && (sizeof($iconFolderArray['pokemon']) > 0)) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="pokemon-icon-style" name="pokemon-icon-style" id="pokemon-icon-style">
                                                    <?php
                                                    foreach ($iconFolderArray['pokemon'] as $name => $repo) {
                                                        echo '<option value="' . $repo . '">' . $name . '</option>';
                                                    } ?>
                                                </select>
                                                <label for="pokemon-icon-style"><?php echo i8ln('Pokemon Icon Style') ?></label>
                                            </div>
                                        <?php }
                                        if (! $noIconSize) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="pokemon-icon-size" name="pokemon-icon-size" id="pokemon-icon-size">
                                                    <option value="20"><?php echo i8ln('Small') ?></option>
                                                    <option value="30"><?php echo i8ln('Normal') ?></option>
                                                    <option value="45"><?php echo i8ln('Large') ?></option>
                                                    <option value="60"><?php echo i8ln('X-Large') ?></option>
                                                </select>
                                                <label for="pokemon-icon-size"><?php echo i8ln('Pokemon Markericon Size') ?></label>
                                            </div>
                                        <?php }
                                        if (! $copyrightSafe && is_array($iconFolderArray['reward']) && (sizeof($iconFolderArray['reward']) > 0)) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="reward-icon-style" name="reward-icon-style" id="reward-icon-style">
                                                    <?php
                                                    foreach ($iconFolderArray['reward'] as $name => $repo) {
                                                        echo '<option value="' . $repo . '">' . $name . '</option>';
                                                    } ?>
                                                </select>
                                                <label for="reward-icon-style"><?php echo i8ln('Reward Icon Style') ?></label>
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
                                        if (is_array($iconFolderArray['gym']) && (sizeof($iconFolderArray['gym']) > 0)) { ?>
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="gym-marker-style" name="gym-marker-style" id="gym-marker-style">
                                                    <?php
                                                    foreach ($iconFolderArray['gym'] as $name => $repo) {
                                                        echo '<option value="' . $repo . '">' . $name . '</option>';
                                                    } ?>
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
                                            foreach ($quickAreas as $areaname => $areaInfo) {
                                                if (is_string($areaInfo)) {
                                                    $areaLatLon = explode(",", $areaInfo);
                                                    $arealat = $areaLatLon[0];
                                                    $arealon = $areaLatLon[1];
                                                    $areazoom = $areaLatLon[2];
                                                    echo '<a href="" data-lat="' . $arealat . '" data-lng="' . $arealon . '" data-zoom="' . $areazoom . '" class="btn btn-secondary area-go-to">' . $areaname . '</a>';
                                                } elseif (is_array($areaInfo)) { ?>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="<?php echo $areaname; ?>-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <?php echo $areaname; ?>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="<?php echo $areaname; ?>-dropdown">
                                                        <?php
                                                        foreach ($areaInfo as $areaname => $areaInfo) {
                                                            $areaLatLon = explode(",", $areaInfo);
                                                            $arealat = $areaLatLon[0];
                                                            $arealon = $areaLatLon[1];
                                                            $areazoom = $areaLatLon[2];
                                                            echo '<li><a href="" data-lat="' . $arealat . '" data-lng="' . $arealon . '" data-zoom="' . $areazoom . '" class="dropdown-item area-go-to">' . $areaname . '</a></li>';
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
                    </div>
                <?php }
                if (! $noInfoModal && ! empty($infoModalTitle) && ! empty($infoModalContent)) { ?>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#infoModal"><?php echo $infoModalTitle; ?></button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="rightNav" aria-labelledby="rightNavLabel">
        <div class="offcanvas-body right">
            <?php if (! $noFullStats ) { ?>
            <div class="card">
                <div class="card-header">
                    <?php echo i8ln('Full Stats') ?>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button id="fullStatsToggle" type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#statsModal">
                            <i class="far fa-chart-bar"></i> <?php echo i8ln('Full Stats') ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="card" id="loadingSpinner">
                <div class="card-header">
                    <?php echo i8ln('Loading...') ?>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden"><?php echo i8ln('Loading...') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <?php echo i8ln('Pokémon') ?>
                </div>
                <div class="card-body">
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
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <?php echo i8ln('Gyms') ?>
                </div>
                <div class="card-body">
                    <div id="arenaList" style="color: black;"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <?php echo i8ln('Raids') ?>
                </div>
                <div class="card-body">
                    <div id="raidList" style="color: black;"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <?php echo i8ln('Pokéstops') ?>
                </div>
                <div class="card-body">
                    <div id="pokestopList" style="color: black;"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <?php echo i8ln('Spawnpoints') ?>
                </div>
                <div class="card-body">
                    <div id="spawnpointList" style="color: black;"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="map"></div>

    <div class="fullscreen-toggle">
        <button class="map-toggle-button" onClick="toggleFullscreenMap();"><i class="fa fa-expand" aria-hidden="true"></i></button>
    </div>
    <?php if (!$noSearch || (!$noSearchPokemon && !$noSearchPokestops && !$noSearchGyms && !$noSearchManualQuests && !$noSearchNests && !$noSearchPortals)) { ?>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel.min.js"></script>
<script src="node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="node_modules/moment/min/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/SoundJS/1.0.2/soundjs.min.js"></script>
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
<?php echo (!empty($gmapsKey)) ? '<script src="https://maps.googleapis.com/maps/api/js?key=' . $gmapsKey . '" async defer></script>' : ''; ?>
<?php echo (!empty($gmapsKey)) ? '<script src="static/js/vendor/Leaflet.GoogleMutant.js"></script>' : ''; ?>
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
    var toastDelay = <?= $toastDelay; ?>;
    var mapStyle = '<?php echo $mapStyle ?>';
    var mapStyleList = <?php echo json_encode($mapStyleList) ?>;
    var hidePokemon = <?php echo $noHidePokemon ? '[]' : $hidePokemon ?>;
    var minLLRank = <?php echo ($noHighLevelData || $noPvp || $noMinLLRank) ? '""' : $minLLRank ?>;
    var minGLRank = <?php echo ($noHighLevelData || $noPvp || $noMinGLRank) ? '""' : $minGLRank ?>;
    var minULRank = <?php echo ($noHighLevelData || $noPvp || $noMinULRank) ? '""' : $minULRank ?>;
    var excludeMinIV = <?php echo $noExcludeMinIV ? '[]' : $excludeMinIV ?>;
    var minIV = <?php echo ($noHighLevelData || $noMinIV) ? '""' : $minIV ?>;
    var minLevel = <?php echo ($noHighLevelData || $noMinLevel) ? '""' : $minLevel ?>;
    var notifyPokemon = <?php echo $noNotifyPokemon ? '[]' : $notifyPokemon ?>;
    var notifyRarity = <?php echo $noNotifyRarity ? '[]' : $notifyRarity ?>;
    var notifyIv = <?php echo $noNotifyIv ? '""' : $notifyIv ?>;
    var notifyLevel = <?php echo $noNotifyLevel ? '""' : $notifyLevel ?>;
    var notifyRaid = <?php echo $noNotifyRaid ? 0 : $notifyRaid ?>;
    var notifyBounce = <?php echo $notifyBounce ?>;
    var notifyNotification = <?php echo $noNotifyNotification ? 'false' : $notifyNotification ?>;
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
    var enableEventStops = <?php echo $noEventStops ? 'false' : $enableEventStops ?>;
    var enableRocket = <?php echo $noTeamRocket ? 'false' : $enableTeamRocket ?>;
    var noQuests = <?php echo $noQuests === true ? 'true' : 'false' ?>;
    var noQuestsARTaskToggle = <?php echo $noQuestsARTaskToggle === true ? 'true' : 'false' ?>;
    var noLures = <?php echo $noLures === true ? 'true' : 'false' ?>;
    var noEventStops = <?php echo $noEventStops === true ? 'true' : 'false' ?>;
    var noTeamRocket = <?php echo $noTeamRocket === true ? 'true' : 'false' ?>;
    var hideGrunts = <?php echo $noTeamRocket ? '[]' : $hideGrunts ?>;
    var noAllPokestops = <?php echo $noAllPokestops === true ? 'true' : 'false' ?>;
    var enableAllPokestops = <?php echo $noAllPokestops ? 'false' : $enableAllPokestops ?>;
    var enableQuests = <?php echo $noQuests ? 'false' : $enableQuests ?>;
    var hideQuestsPokemon = <?php echo $noQuestsPokemon ? '[]' : $hideQuestsPokemon ?>;
    var hideQuestsItem = <?php echo $noQuestsItems ? '[]' : $hideQuestsItem ?>;
    var hideQuestsEnergy = <?php echo $noQuestsEnergy ? '[]' : $hideQuestsEnergy ?>;
    var hideQuestsCandy = <?php echo $noQuestsCandy ? '[]' : $hideQuestsCandy ?>;
    var enableNewPortals = <?php echo (($map != "monocle") || ($fork == "alternate")) ? $enableNewPortals : 0 ?>;
    var enableWeatherOverlay = <?php echo ! $noWeatherOverlay ? $enableWeatherOverlay : 'false' ?>;
    var enableSpawnpoints = <?php echo $noSpawnPoints ? 'false' : $enableSpawnPoints ?>;
    var enableLiveScan = <?php echo $noLiveScanLocation ? 'false' : $enableLiveScan ?>;
    var deviceOfflineAfterSeconds = <?php echo $deviceOfflineAfterSeconds ?>;
    var enableRanges = <?php echo $noRanges ? 'false' : $enableRanges ?>;
    var noScanPolygon = <?php echo $noScanPolygon === true ? 'true' : 'false' ?>;
    var enableScanPolygon = <?php echo $noScanPolygon ? 'false' : $enableScanPolygon ?>;
    var geoJSONfile = '<?php echo $noScanPolygon ? '' : (!empty($geoJSONfile) ? $geoJSONfile : '') ?>';
    var notifySound = <?php echo $noNotifySound ? 'false' : $notifySound ?>;
    var criesSound = <?php echo $noCriesSound ? 'false' : $criesSound ?>;
    var enableStartMe = <?php echo $noStartMe ? 'false' : $enableStartMe ?>;
    var enableStartLast = <?php echo (! $noStartLast && $enableStartMe === 'false') ? $enableStartLast : 'false' ?>;
    var enableFollowMe = <?php echo $noFollowMe ? 'false' : $enableFollowMe ?>;
    var enableSpawnArea = <?php echo $noSpawnArea ? 'false' : $enableSpawnArea ?>;
    var pokemonIconSize = <?php echo $pokemonIconSize ?>;
    var iconNotifySizeModifier = <?php echo $iconNotifySizeModifier ?>;
    var locationStyle = '<?php echo $locationStyle ?>';
    var iconFolderArray = <?php echo json_encode($iconFolderArray) ?>;
    var weatherColors = <?php echo json_encode($weatherColors); ?>;
    var s2Colors = <?php echo json_encode($s2Colors); ?>;
    var mapType = '<?php echo strtolower($map); ?>';
    var mapFork = '<?php echo strtolower($fork); ?>';
    var triggerGyms = <?php echo $triggerGyms ?>;
    var noExGyms = <?php echo $noExGyms === true ? 'true' : 'false' ?>;
    var onlyTriggerGyms = <?php echo $onlyTriggerGyms === true ? 'true' : 'false' ?>;
    var showBigKarp = <?php echo (!$noHighLevelData && !$noBigKarp) ? 'true' : 'false' ?>;
    var showTinyRat = <?php echo (!$noHighLevelData && !$noTinyRat) ? 'true' : 'false' ?>;
    var showZeroIv = <?php echo (!$noHighLevelData && !$noZeroIvToggle) ? 'true' : 'false' ?>;
    var showHundoIv = <?php echo (!$noHighLevelData && !$noHundoIvToggle) ? 'true' : 'false' ?>;
    var showXXS = <?php echo (!$noHighLevelData && !$noXXSToggle) ? 'true' : 'false' ?>;
    var showXXL = <?php echo (!$noHighLevelData && !$noXXLToggle) ? 'true' : 'false' ?>;
    var showMissingIVOnly = <?php echo (!$noHighLevelData && !$noMissingIVOnly ) ? 'true' : 'false' ?>;
    var showIndependantPvpAndStats = <?php echo (!$noHighLevelData && !$noPvp && !$noIndependantPvpAndStatsToggle) ? 'true' : 'false' ?>;
    var showDespawnTimeType = <?php echo $noDespawnTimeType ? 0 : $showDespawnTimeType ?>;
    var showPokemonGender = <?php echo $noPokemonGender ? 0 : $showPokemonGender ?>;
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
    var enablePlacementRanges = <?php echo $noPlacementRanges ? 'false' : $enablePlacementRanges ?>;
    var enableLevel13Cells = <?php echo $noS2Cells ? 'false' : $enableLevel13Cells ?>;
    var enableLevel14Cells = <?php echo $noS2Cells ? 'false' : $enableLevel14Cells ?>;
    var enableLevel15Cells = <?php echo $noS2Cells ? 'false' : $enableLevel15Cells ?>;
    var enableLevel17Cells = <?php echo $noS2Cells ? 'false' : $enableLevel17Cells ?>;
    var noDeletePortal = <?php echo $noDeletePortal === true ? 'true' : 'false' ?>;
    var noConvertPortal = <?php echo $noConvertPortal === true ? 'true' : 'false' ?>;
    var markPortalsAsNew = <?php echo $markPortalsAsNew ?>;
    var copyrightSafe = <?php echo $copyrightSafe === true ? 'true' : 'false' ?>;
    var forcedTileServer = <?php echo $forcedTileServer === true ? 'true' : 'false' ?>;
    var noRarityDisplay = <?php echo $noRarityDisplay === true ? 'true' : 'false' ?>;
    var noWeatherIcons = <?php echo $noWeatherIcons === true ? 'true' : 'false' ?>;
    var no0IvShadow = <?php echo $no0IvShadow === true ? 'true' : 'false' ?>;
    var no100IvShadow = <?php echo $no100IvShadow === true ? 'true' : 'false' ?>;
    var noPvpShadow = <?php echo $noPvpShadow === true ? 'true' : 'false' ?>;
    var noRaidTimer = <?php echo $noRaidTimer === true ? 'true' : 'false' ?>;
    var enableRaidTimer = <?php echo $noRaidTimer ? 'false' : $enableRaidTimer ?>;
    var noRocketTimer = <?php echo $noTeamRocketTimer === true ? 'true' : 'false' ?>;
    var enableRocketTimer = <?php echo $noTeamRocketTimer ? 'false' : $enableTeamRocketTimer ?>;
    var noEventStopsTimer = <?php echo $noEventStopsTimer === true ? 'true' : 'false' ?>;
    var enableEventStopsTimer = <?php echo $noEventStopsTimer ? 'false' : $enableEventStopsTimer ?>;
    var enableNestPolygon = <?php echo $noNestPolygon ? 'false' : $enableNestPolygon ?>;
    var noNestPolygon = <?php echo $noNestPolygon === true ? 'true' : 'false' ?>;
    var nestGeoJSONfile = '<?php echo $noNestPolygon ? '' : (!empty($nestGeoJSONfile) ? $nestGeoJSONfile : '') ?>';
    var nestBotName = '<?php echo $nestBotName ? $nestBotName : 'Bot' ?>';
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
    var defaultXpAmount = <?php echo $defaultXpAmount; ?>;
    var nestAvgDefault = <?php echo $nestAvgDefault; ?>;
    var noDarkMode = <?php echo $noDarkMode === true ? 'true' : 'false' ?>;
    var noCatchRates = <?php echo $noCatchRates === true ? 'true' : 'false' ?>;
    var noPvp = <?php echo $noPvp === true ? 'true' : 'false' ?>;
    var noPvpCapText = <?php echo $noPvpCapText === true ? 'true' : 'false' ?>;
    var noHideSingleMarker = <?php echo $noHideSingleMarker === true ? 'true' : 'false' ?>;
    var enableJSDebug = <?php echo $enableJSDebug === true ? 'true' : 'false' ?>;
    // When A Setting Is Disabled, Ensure Filtering Is Also Disabled to Prevent Invisible Filtering
    if (minIV === "") { localStorage.setItem('remember_text_min_iv', <?php echo $minIV; ?>) }
    if (minLevel === "") { localStorage.setItem('remember_text_min_level', <?php echo $minLevel; ?>) }
    if (minLLRank === "") { localStorage.setItem('remember_text_min_ll_rank', <?php echo $minLLRank; ?>) }
    if (minGLRank === "") { localStorage.setItem('remember_text_min_gl_rank', <?php echo $minGLRank; ?>) }
    if (minULRank === "") { localStorage.setItem('remember_text_min_ul_rank', <?php echo $minULRank; ?>) }
    if (String(showBigKarp) !== String(localStorage.getItem('showBigKarp'))) { localStorage.setItem('showBigKarp', false) }
    if (String(showTinyRat) !== String(localStorage.getItem('showTinyRat'))) { localStorage.setItem('showTinyRat', false) }
    if (String(showZeroIv) !== String(localStorage.getItem('showZeroIv'))) { localStorage.setItem('showZeroIv', false) }
    if (String(showHundoIv) !== String(localStorage.getItem('showHundoIv'))) { localStorage.setItem('showHundoIv', false) }
    if (String(showXXS) !== String(localStorage.getItem('showXXS'))) { localStorage.setItem('showXXS', false) }
    if (String(showXXL) !== String(localStorage.getItem('showXXL'))) { localStorage.setItem('showXXL', false) }
    if (String(showMissingIVOnly) !== String(localStorage.getItem('showMissingIVOnly'))) { localStorage.setItem('showMissingIVOnly', false) }
    if (String(showIndependantPvpAndStats) !== String(localStorage.getItem('showIndependantPvpAndStats'))) { localStorage.setItem('showIndependantPvpAndStats', false) }

</script>
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
