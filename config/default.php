<?php

//======================================================================
// DO NOT EDIT THIS FILE!
//======================================================================

//======================================================================
// PMSF - DEFAULT CONFIG FILE
// https://github.com/Glennmen/PMSF
//======================================================================
session_start();
require_once(__DIR__ . '/../utils.php');

$libs[] = "Scanner.php";
$libs[] = "Monocle.php";
$libs[] = "Monocle_Asner.php";
$libs[] = "Monocle_Alternate.php";
$libs[] = "RocketMap.php";
$libs[] = "RocketMap_Sloppy.php";

// Include libraries
foreach ($libs as $file) {
    include(__DIR__ . '/../lib/' . $file);
}
setSessionCsrfToken();

//-----------------------------------------------------
// MAP SETTINGS
//-----------------------------------------------------

/* Location Settings */

$startingLat = 37.7749295;                                          // Starting latitude
$startingLng = -122.4194155;                                        // Starting longitude

/* Anti scrape Settings */

$maxLatLng = 1;                                                     // Max latitude and longitude size (1 = ~110km, 0 to disable)
$maxZoomOut = 0;                                                    // Max zoom out level (11 ~= $maxLatLng = 1, 0 to disable, lower = the further you can zoom out)
$enableCsrf = true;                                                 // Don't disable this unless you know why you need to :)
$sessionLifetime = 43200;                                           // Session lifetime, in seconds
$blockIframe = true;                                                // Block your map being loaded in an iframe

/* Map Title + Language */

$title = "PMSF Glennmen";                                           // Title to display in title bar
$locale = "en";                                                     // Display language

/* Google Maps Key */

$gmapsKey = "";                                                     // Google Maps API Key

/* Google Analytics */

$gAnalyticsId = "";                                                 // "" for empty, "UA-XXXXX-Y" add your Google Analytics tracking ID

/* Piwik Analytics */

$piwikUrl = "";
$piwikSiteId = "";

/* PayPal */

$paypalUrl = "";                                                    // PayPal donation URL, leave "" for empty

/* Discord */

$discordUrl = "";                                                   // Discord URL, leave "" for empty

/* Worldopole */

$worldopoleUrl = "";                                                // Link to Worldopole, leave "" for empty

/* StatsToggle */
$noStatsToggle = false;                                             // Enables or disables the stats button in the header.

/* MOTD */
$noMotd = true;
$motdTitle = "";
$motdContent = "";

/* Share links */
$noWhatsappLink = true;
//-----------------------------------------------------
// Login  - You need to create the two tables referenced in sql.sql
//-----------------------------------------------------

$noNativeLogin = true;                                              // true/false - This will enable the built in login system.
$domainName = '';                                                   // If this is empty, reset-password emails will use the domain name taken from the URL.

$noDiscordLogin = true;                                             // true/false - This will enable login through discord.
                                                                    // 1. Create a discord bot here -> https://discordapp.com/developers/applications/me
                                                                    // 2. Install composer with "apt-get install composer".
                                                                    // 3. Navigate to your website's root folder and type "composer install" to install the dependencies.
                                                                    // 4. Add your callback-page as a REDIRECT URI to your discord bot. Should be the same as $discordBotRedirectUri.
                                                                    // 5. Enter Client ID, Client Secret and Redirect URI below.
$discordBotClientId = 0;
$discordBotClientSecret = "";
$discordBotRedirectUri = "https://example.com/discord-callback.php";

$adminUsers = array('admin@example.com', 'Superadmin#13337');       // You can add multiple admins by adding them to the array.
$logfile = '../members.log';                                        // Path to log file. Make sure this works as it will be your life saver if your db crashes.
$daysMembershipPerQuantity = 31;                                    // How many days membership one selly quantity will give.
$sellyPage = '';                                                    // Link to selly purchase page for membership renewal.
$sellyWebhookSecret = '';                                           // Add a secret key at https://selly.gg/settings to make sure the payment webhook is sent from selly to prevent fake payments.
                                                                    // Add the same key to the $sellyWebhookSecret variable.

//-----------------------------------------------------
// FRONTEND SETTINGS
//-----------------------------------------------------

if ($noNativeLogin === true && $noDiscordLogin == true ||  (($noNativeLogin === false || $noDiscordLogin === false) && !empty($_SESSION['user']->expire_timestamp) && $_SESSION['user']->expire_timestamp > time())) {

    /*
        THESE SETTINGS WILL BE APPLIED IF:
            - LOGIN IS DISABLED
            - LOGIN IS ENABLED AND THE USER IS LOGGED ON
    */

    /* Marker Settings */
    $noExcludeMinIV = false;                                        // true/false
    $noMinIV = false;                                               // true/false
    $noMinLevel = false;                                            // true/false
    $noHighLevelData = false;                                       // true/false

    /* Notification Settings */
    $noNotifyPokemon = false;                                       // true/false
    $noNotifyRarity = false;                                        // true/false
    $noNotifyIv = false;                                            // true/false
    $noNotifyLevel = false;                                         // true/false
    $noNotifyRaid = false;                                          // true/false
    $noNotifySound = false;                                         // true/false
    $noCriesSound = false;                                          // true/false
    $noNotifyBounce = false;                                        // true/false
    $noNotifyNotification = false;                                  // true/false

    /* Style Settings */
    $iconNotifySizeModifier = 15;                                   // 0, 15, 30, 45
} else {

    /*
        THESE SETTINGS WILL BE APPLIED IF:
            - LOGIN IS ENABLED AND THE USER IS NOT A DONATOR
    */

    /* Marker Settings */
    $noExcludeMinIV = true;                                         // true/false
    $noMinIV = true;                                                // true/false
    $noMinLevel = true;                                             // true/false
    $noHighLevelData = true;                                        // true/false

    /* Notification Settings */
    $noNotifyPokemon = true;                                        // true/false
    $noNotifyRarity = true;                                         // true/false
    $noNotifyIv = true;                                             // true/false
    $noNotifyLevel = true;                                          // true/false
    $noNotifyRaid = true;                                           // true/false
    $noNotifySound = true;                                          // true/false
    $noCriesSound = true;                                           // true/false
    $noNotifyBounce = true;                                         // true/false
    $noNotifyNotification = true;                                   // true/false

    /* Style Settings */
    $iconNotifySizeModifier = 0;                                    // 0, 15, 30, 45
}

/* Marker Settings */

$noPokemon = false;                                                 // true/false
$enablePokemon = 'true';                                            // true/false
$noPokemonNumbers = false;                                          // true/false
$noHidePokemon = false;                                             // true/false
$hidePokemon = '[10, 13, 16, 19, 21, 29, 32, 41, 46, 48, 50, 52, 56, 74, 77, 96, 111, 133,
                  161, 163, 167, 177, 183, 191, 194, 168]';         // [] for empty

$hidePokemonCoords = false;                                         // true/false

$excludeMinIV = '[131, 143, 147, 148, 149, 248]';                   // [] for empty

$minIV = '0';                                                       // "0" for empty or a number
$minLevel = '0';                                                    // "0" for empty or a number

$noBigKarp = true;                                                 // true/false
$noTinyRat = true;                                                 // true/false

$noNests = false;                                                   // true/false
$enableNests = 'false';                                             // true/false

$noGyms = false;                                                    // true/false
$enableGyms = 'false';                                              // true/false
$noGymSidebar = false;                                              // true/false
$gymSidebar = 'true';                                               // true/false
$noTrainerName = false;                                             // true/false
$noTrainerLevel = false;                                            // true/false
$noExEligible = false;                                              // true/false
$exEligible = 'false';                                              // true/false

$noRaids = false;                                                   // true/false
$enableRaids = 'false';                                             // true/false
$activeRaids = 'false';                                             // true/false
$minRaidLevel = 1;
$maxRaidLevel = 5;

$noPokestops = false;                                               // true/false
$enablePokestops = 'false';                                         // true/false
$enableLured = 1;                                                   // O: all, 1: lured only

$noScannedLocations = true;                                        // true/false
$enableScannedLocations = 'false';                                  // true/false

$noSpawnPoints = true;                                             // true/false
$enableSpawnPoints = 'false';                                       // true/false

$noRanges = false;                                                  // true/false
$enableRanges = 'false';                                            // true/false

/* Location & Search Settings */

$noSearchLocation = false;                                          // true/false

$noStartMe = false;                                                 // true/false
$enableStartMe = 'false';                                           // true/false

$noStartLast = false;                                               // true/false
$enableStartLast = 'false';                                         // true/false

$noFollowMe = false;                                                // true/false
$enableFollowMe = 'false';                                          // true/false

$noSpawnArea = false;                                               // true/false
$enableSpawnArea = 'false';                                         // true/false

/* Notification Settings */

$notifyPokemon = '[]';                                           // [] for empty

$notifyRarity = '[]';                                               // "Common", "Uncommon", "Rare", "Very Rare", "Ultra Rare"

$notifyIv = '""';                                                   // "" for empty or a number

$notifyLevel = '""';                                                // "" for empty or a number

$notifyRaid = 5;                                                    // O to disable

$notifySound = 'false';                                             // true/false

$criesSound = 'false';                                              // true/false

$notifyBounce = 'true';                                             // true/false

$notifyNotification = 'true';                                       // true/false

/* Style Settings */

$copyrightSafe = true;

$noMapStyle = false;                                                // true/false
$mapStyle = 'style_pgo_dynamic';                                    // roadmap, satellite, hybrid, nolabels_style, dark_style, style_light2, style_pgo, dark_style_nl, style_pgo_day, style_pgo_night, style_pgo_dynamic, openstreetmap

$noDirectionProvider = false;                                       // true/false
$directionProvider = 'google';                                      // google, waze, apple, bing, google_pin

$noIconSize = false;                                                // true/false
$iconSize = 0;                                                      // -8, 0, 10, 20

$noIconNotifySizeModifier = false;                                  // true/false | Increase size of notified Pokemon

$noGymStyle = false;                                                // true/false
$gymStyle = 'ingame';                                               // ingame, shield

$noLocationStyle = false;                                           // true/false
$locationStyle = 'none';                                            // none, google, red, red_animated, blue, blue_animated, yellow, yellow_animated, pokesition, pokeball

$osmTileServer = 'tile.openstreetmap.org';                          // osm tile server (no trailing slash)

$triggerGyms = '[]';                                                // Add Gyms that the OSM-Query doesn't take care of like '["gym_id", "gym_id"]'
$onlyTriggerGyms = false;                                           // Only show EX-Gyms that are defined in $triggerGyms
$noExGyms = false;                                                  // Do not display EX-Gyms on the map
$noParkInfo = false;                                                // Do not display Park info on the map

//-----------------------------------------------------
// Raid API
//-----------------------------------------------------

$raidApiKey = '';                                                   // Raid API Key, '' to deny access
$sendRaidData = false;                                              // Send Raid data, false to only send gym data

//-----------------------------------------------------
// Manual Submissions
//-----------------------------------------------------
$hideIfManual = false;
$noManualRaids = false;
$noManualPokemon = false;
$pokemonTimer = 900;                                                // Time in seconds before a submitted Pokémon despawns.
$noManualGyms = false;
$noManualPokestops = false;
$noRenamePokestops = false;
$noManualQuests = false;

$noDiscordSubmitLogChannel = true;                                        // Send webhooks to discord channel upon submission

$pokemonReportTime = true;
$pokemonToExclude = [];

$noDeleteGyms = false;
$noDeletePokestops = false;

$raidBosses = [129,361,333,355,103,303,200,302,215,68,94,124,221,127,248,306,359,365,381,250];

$sendWebhook = false;
$webhookUrl = null;                                             //['url-1','url-2']

$sendQuestWebhook = false;                                          // Experimental use only
$questWebhookUrl = null;                                            // Experimental use only

$manualOneStar = [
    'webhook' => true,
    'pokemon_id' => 387,
    'cp' => 0,
    'move_1' => null,
    'move_2' => null
];
$manualTwoStar = [
    'webhook' => true,
    'pokemon_id' => 388,
    'cp' => 0,
    'move_1' => null,
    'move_2' => null
];
$manualThreeStar = [
    'webhook' => true,
    'pokemon_id' => 389,
    'cp' => 0,
    'move_1' => null,
    'move_2' => null
];
$manualFourStar = [
    'webhook' => true,
    'pokemon_id' => 390,
    'cp' => 0,
    'move_1' => null,
    'move_2' => null
];
$manualFiveStar = [
    'webhook' => true,
    'pokemon_id' => 391,
    'cp' => 0,
    'move_1' => null,
    'move_2' => null
];

//-----------------------------------------------
// Search
//-----------------------------------------------------

$noSearch = false;
$noSearchPokestops = false;     //Wont work if noSearch = false
$noSearchGyms = false;          //Wont work if noSearch = false
$noSearchManualQuests = false;  //Wont work if noSearch = false
$noSearchNests = false;
$defaultUnit = "km";                                            // mi/km

//-----------------------------------------------
// Nests
//-----------------------------------------------------
$noNests = false;                                                   // true/false
$enableNests = 'false';                                             // true/false
$noManualNests = false;
$noDeleteNests = false;
$nestVerifyLevel = 1;						    // 1 = Verified 2 = 1 + Unverified 3 = 1 + 2 + Revoked 4 = Get all nests
$deleteNestsOlderThan = 42;					    // days after not updated nests are removed from database by nest cron
$migrationDay = strtotime('5 April 2018');                          // Adjust day value after non consitent 14 day migration
$noAddNewNests = false;
$excludeNestMons = [2,3,5,6,8,9,11,12,14,15,17,18,20,22,24,26,28,29,30,31,32,33,34,36,38,40,42,44,45,49,51,53,55,57,59,61,62,64,65,67,68,70,71,73,75,76,78,80,82,83,85,87,88,89,91,93,94,97,99,101,103,105,106,107,108,109,110,112,113,114,115,117,119,121,122,128,130,131,132,134,135,136,137,139,142,143,144,145,146,147,148,149,150,151,153,154,156,157,159,160,162,164,166,168,169,171,172,173,174,175,176,178,179,180,181,182,184,185,186,188,189,192,195,196,197,199,201,204,205,207,208,210,212,214,217,219,221,222,224,225,226,227,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,253,254,256,257,259,260,262,264,266,267,268,269,270,271,272,274,275,277,279,280,281,282,284,286,287,288,289,290,291,292,294,295,297,298,301,302,303,305,306,308,310,311,312,313,314,317,319,321,323,324,326,327,328,329,330,331,332,334,335,336,337,338,340,342,344,345,346,347,348,349,350,351,352,354,356,357,358,359,360,361,362,364,365,366,367,368,369,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386];
$nestCoords = array();                                           //$nestCoords = array(array('lat1' => 42.8307723529682, 'lng1' => -88.7527692278689, 'lat2' => 42.1339901128552, 'lng2' => -88.0688703020877),array(    'lat1' => 42.8529250952743,'lng1' => -88.1292951067752,'lat2' => 41.7929306950085,'lng2' => -87.5662457903689));


//-----------------------------------------------------
// Areas
//-----------------------------------------------------

$noAreas = true;
$areas = [];                                                        // [[latitude,longitude,zoom,"name"],[latitude,longitude,zoom,"name"]]

//-----------------------------------------------------
// Weather Config
//-----------------------------------------------------

$noWeatherOverlay = false;                                          // true/false
$enableWeatherOverlay = 'false';                                    // true/false

$weather = [
    0 => null,
    1 => 'clear',
    2 => 'rain',
    3 => 'partly_cloudy',
    4 => 'cloudy',
    5 => 'windy',
    6 => 'snow',
    7 => 'fog'
];

$weatherColors = [
    'grey',
    '#fdfd96',
    'darkblue',
    'grey',
    'darkgrey',
    'purple',
    'white',
    'black'
];

//-----------------------------------------------------
// DEBUGGING
//-----------------------------------------------------

// Do not enable unless requested

$enableDebug = false;

//-----------------------------------------------------
// DATABASE CONFIG
//-----------------------------------------------------

$fork = "default";                                                  // default/asner/sloppy
