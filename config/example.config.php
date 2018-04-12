<?php

namespace Config;

// Do not touch this!
require 'default.php';
require __DIR__ . '/../Medoo.php';

use Medoo\Medoo;

//======================================================================
// PMSF - CONFIG FILE
// https://github.com/Glennmen/PMSF
//======================================================================

//-----------------------------------------------------
// MAP SETTINGS
//-----------------------------------------------------

/* Location Settings */

$startingLat = 41.771822;                                           // Starting latitude
$startingLng = -87.8549371;                                         // Starting longitude

/* Anti scrape Settings */

$maxLatLng = 1;                                                     // Max latitude and longitude size (1 = ~110km, 0 to disable)
$maxZoomOut = 11;                                                   // Max zoom out level (11 ~= $maxLatLng = 1, 0 to disable, lower = the further you can zoom out)
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

$discordUrl = "https://discord.gg/INVITE_LINK";                     // Discord URL, leave "" for empty

/* Worldopole */

$worldopoleUrl = "";                                                // Link to Worldopole, leave "" for empty

/* MOTD */
$noMotd = true;
$motdTitle = "Message of the Day";
$motdContent = "This is an example MOTD<br>Do whatever you like with it.";

//-----------------------------------------------------
// Login
//-----------------------------------------------------

$noNativeLogin = true;                                              // true/false - This will enable the built in login system.
                                                                    
$noDiscordLogin = true;                                             // true/false - This will enable login through discord. A discord bot is needed for this to work.
                                                                    // Composer is also needed. Type "composer install" to install the dependencies.
                                                                    // Enter client_id, client_secret and callback uri from your discord bot to DiscordAuth.php
                                                                    // https://discordapp.com/developers/applications/me
$discord_bot_client_id = 0;
$discord_bot_client_secret = "";
$discord_bot_redirect_uri = "https://example.com/discord-callback.php";

$adminUsers = array('admin@example.com', 'Superadmin#13337');       // You can add multiple admins by adding them to the array.
$logfile = '../members.log';                                        // Path to log file. Make sure this works as it will be your life saver if your db crashes.

//-----------------------------------------------------
// FRONTEND SETTINGS
//-----------------------------------------------------

if ($noNativeLogin === true && $noDiscordLogin == true ||  (($noNativeLogin === false || $noDiscordLogin === false) && isset($_SESSION['user']->expire_timestamp) && $_SESSION['user']->expire_timestamp > time())) {

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
            - LOGIN IS ENABLED AND THE USER IS _NOT_ LOGGED ON
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

$noBigKarp = false;                                                 // true/false
$noTinyRat = false;                                                 // true/false

$noGyms = false;                                                    // true/false
$enableGyms = 'false';                                              // true/false
$noGymSidebar = false;                                              // true/false
$gymSidebar = 'true';                                               // true/false
$noTrainerName = false;                                             // true/false
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

$noScannedLocations = false;                                        // true/false
$enableScannedLocations = 'false';                                  // true/false

$noSpawnPoints = false;                                             // true/false
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

$notifyPokemon = '[201]';                                           // [] for empty

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
$mapStyle = 'style_pgo_dynamic';                                    // roadmap, satellite, hybrid, nolabels_style, dark_style, style_light2, style_pgo, dark_style_nl, style_pgo_day, style_pgo_night, style_pgo_dynamic

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

//-----------------------------------------------
// Raid API
//-----------------------------------------------------

$raidApiKey = '';                                                   // Raid API Key, '' to deny access
$sendRaidData = false;                                              // Send Raid data, false to only send gym data

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

$weatherColors = [
    'grey',                                                         // no weather
    '#fdfd96',                                                      // clear
    'darkblue',                                                     // rain
    'grey',                                                         // partly cloudy
    'darkgrey',                                                     // cloudy
    'purple',                                                       // windy
    'white',                                                        // snow
    'black'                                                         // fog
];

//-----------------------------------------------------
// DATA MANAGEMENT
//-----------------------------------------------------

// Clear pokemon from database this many hours after they disappear (0 to disable)
// This is recommended unless you wish to store a lot of backdata for statistics etc!

$purgeData = 0;


//-----------------------------------------------------
// DEBUGGING
//-----------------------------------------------------

// Do not enable unless requested

$enableDebug = false;

//-----------------------------------------------------
// DATABASE CONFIG
//-----------------------------------------------------

$map = "monocle";                                                   // monocle/rm
$fork = "default";                                                  // default/asner/sloppy/alternate

$db = new Medoo([// required
    'database_type' => 'mysql',                                     // mysql/mariadb/pgsql/sybase/oracle/mssql/sqlite
    'database_name' => 'Monocle',
    'server' => '127.0.0.1',
    'username' => 'database_user',
    'password' => 'database_password',
    'charset' => 'utf8',

    // [optional]
    //'port' => 5432,                                               // Comment out if not needed, just add // in front!
    //'socket' => /path/to/socket/,
]);
