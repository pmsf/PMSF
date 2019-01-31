<?php

namespace Config;

// Do not touch this!
require 'default.php';
require __DIR__ . '/../Medoo.php';

use Medoo\Medoo;

//======================================================================
// PMSF - CONFIG FILE
// https://github.com/whitewillem/PMSF
//======================================================================

//-----------------------------------------------------
// MAP SETTINGS
//-----------------------------------------------------

/* Location Settings */

$startingLat = 52.084992;                                          // Starting latitude
$startingLng = 5.302366;                                           // Starting longitude

/* Zoom and Cluster Settings */

$maxLatLng = 1;                                                     // Max latitude and longitude size (1 = ~110km, 0 to disable)
$maxZoomOut = 11;                                                   // Max zoom out level (11 ~= $maxLatLng = 1, 0 to disable, lower = the further you can zoom out)
$maxZoomIn = 18;                                                    // Max zoom in level 18
$disableClusteringAtZoom = 15;					    // Disable clustering above this value. 0 to disable
$zoomToBoundsOnClick = 15;					    // Zoomlevel on clusterClick
$maxClusterRadius = 30;						    // The maximum radius that a cluster will cover from the central marker (in pixels).
$spiderfyOnMaxZoom = 'true';					    // Spiderfy cluster markers on click

/* Anti scrape Settings */
$enableCsrf = true;                                                 // Don't disable this unless you know why you need to :)
$sessionLifetime = 43200;                                           // Session lifetime, in seconds
$blockIframe = true;                                                // Block your map being loaded in an iframe

/* Map Title + Language */

$title = "POGOmap";                                                 // Title to display in title bar
$locale = "en";                                                     // Display language
$raidmapLogo = '';                                                  // Upload logo to custom folder, leave '' for empty ( $raidmapLogo = 'custom/logo.png'; )

/* Google Maps ONLY USED FOR TILE LAYERS */

$gmapsKey = "";

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

/* StatsToggle */
$noStatsToggle = false;                                             // Enables or disables the stats button in the header.

/* MOTD */
$noMotd = true;
$motdTitle = "Message of the Day";
$motdContent = "This is an example MOTD<br>Do whatever you like with it.";

/* Favicon */
$faviconPath = '';                                                  // Upload favicon.ico to custom folder, leave '' for empty ( $faviconPath = 'custom/favicon.ico'; )
//-----------------------------------------------------
// Login
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
/* Blacklist Settingss - Only available with Discord login */
$userBlacklist = [''];                                                                // Array of user ID's that are always blocked from accessing the map
$userWhitelist = [''];                                              // Array of user ID's that's allowed to bypass the server blacklist
$serverWhitelist = [''];                                            // Array of server ID's. Your users will need to be in at least one of them
$serverBlacklist = [''];                                            // Array of server ID's. A user that's a member of any of these and not in your user whitelist will be blocked
$logFailedLogin = '';                                               // File location of where to store a log file of blocked users

//-----------------------------------------------------
// FRONTEND SETTINGS
//-----------------------------------------------------

/* Marker Settings */
$noExcludeMinIV = false;                                        // true/false
$noMinIV = false;                                               // true/false
$noMinLevel = false;                                            // true/false
$noHighLevelData = false;                                       // true/false
$noRarityDisplay = false;                                       // true/false
$noWeatherIcons = true;
$noWeatherShadow = false;

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
$noLures = false;
$enableLured = 'false';                                             // true/false
$noQuests = false;                                                  // true/false
$enableQuests = 'false';                                            // true/false
$noQuestsItems = false;
$noQuestsPokemon = false;
$hideQuestsPokemon = '[]';  					    // Pokemon ids will default be hidden in the menu every user is able to change this personaly
$hideQuestsItem = '[4, 5, 301, 401, 402, 403, 404, 501, 602, 603, 604, 702, 704, 707, 801, 901, 902, 903, 1001, 1002, 1401, 1402, 1402, 1403, 1404, 1405]';    // Item ids "See protos https://github.com/Furtif/POGOProtos/blob/master/src/POGOProtos/Inventory/Item/ItemId.proto"
$excludeQuestsPokemon = [];					    // All excluded pokemon wil not be shown in the filter.
$excludeQuestsItem = [4, 5, 301, 401, 402, 403, 404, 501, 602, 603, 604, 702, 704, 707, 801, 901, 902, 903, 1001, 1002, 1401, 1402, 1402, 1403, 1404, 1405];   // All excluded item wil not be shown in the filter.
$noItemNumbers = false;                                             // true/false

// Manual quest hide options
$hideQuestTypes = [0, 1, 2, 3, 12, 18, 19, 22, 24, 25];
$hideRewardTypes = [0, 1, 4, 5, 6];
$hideConditionTypes = [0, 4, 5, 11, 12, 13, 16, 17, 19, 20];
// Manual quest show options
$showEncounters = [201];
$showItems = [1, 2, 3, 101, 102, 103, 104, 201, 202, 701, 703, 705, 706, 707, 1301];

$noScannedLocations = false;                                        // true/false
$enableScannedLocations = 'false';                                  // true/false

$noSpawnPoints = false;                                             // true/false
$enableSpawnPoints = 'false';                                       // true/false

$noRanges = false;                                                  // true/false
$enableRanges = 'false';                                            // true/false

$noScanPolygon = true;
$enableScanPolygon = 'false';
$geoJSONfile = 'custom/scannerarea.json';			    // path to geoJSON file create your own on http://geojson.io/ adjust filename
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
$iconRepository = '';						    // URLs or folder paths are allowed

$noMapStyle = false;                                                // true/false
$mapStyle = 'openstreetmap';                                        // openstreetmap, darkmatter, styleblackandwhite, styletopo, stylesatellite, stylewikipedia

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

/* Share links */
$noWhatsappLink = true;
//-----------------------------------------------
// Raid API
//-----------------------------------------------------

$raidApiKey = '';                                                   // Raid API Key, '' to deny access
$sendRaidData = false;                                              // Send Raid data, false to only send gym data

//-----------------------------------------------------
// Manual Submissions
// In order to make Manual Raids and Quests work you need to have the $geoJSONfile set to a valid geoJSON.json file
//-----------------------------------------------------
$noSubmit = true;
$hideIfManual = false;
$noManualRaids = true;						 			// Enable/Disable ManualRaids permanently ( Comment this line if you want to use the block below )
$noDiscordSubmitLogChannel = true;                                  			// Send webhooks to discord channel upon submission
$submitMapUrl = '';
$discordSubmitLogChannelUrl = 'https://discordapp.com/api/webhooks/<yourCHANNELhere>';  // Sends gym/pokestop submit & pokestop rename directly to discord
//$currentTime = (int) date('G');				   			/ Uncomment this block to deny Raid submissions over night
//
//if ($currentTime >= 6 && $currentTime < 23) {                     			// noManualRaids = true between 23:00 and 06:00. Adjust hours if needed
//
//	        $noManualRaids = false;
//} else {
//	        $noManualRaids = true;
//}

$noManualPokemon = true;
$pokemonTimer = 900;                               // Time in seconds before a submitted Pokémon despawns.
$noManualGyms = true;
$noManualPokestops = true;
$noRenamePokestops = true;
$noConvertPokestops = true;
$noManualQuests = true;

//-----------------------------------------------------
// Ingress portals
//-----------------------------------------------------
$enablePortals = 'false';
$enableNewPortals = 0;                             // O: all, 1: new portals only
$noPortals = true;
$noDeletePortal = true;
$noConvertPortal = true;
$noS2Cells = true;
$enableS2Cells = 'false';
$enableLevel13Cells = 'false';
$enableLevel14Cells = 'false';
$enableLevel17Cells = 'false';
$markPortalsAsNew = 86400;                         // Time in seconds to mark new imported portals as new ( 86400 for 1 day )
$noPoi = true;					   // Allow users to view POI markers 
$noAddPoi = true;				   // Allow to add POI markers (locations eligible for submitting Pokestops/Ingress portals)
$enablePoi = 'false';
$noDeletePoi = true;
$noMarkPoi = true;

$pokemonReportTime = false;
$pokemonToExclude = [];

$noDeleteGyms = false;
$noToggleExGyms = false;
$noDeletePokestops = false;

$raidBosses = [1, 4, 7, 129, 138, 140, 147, 82, 108, 125, 126, 185, 303, 65, 68, 95, 106, 107, 123, 135, 142, 76, 112, 131, 143, 248, 359, 144, 145, 146, 377];

$sendWebhook = false;				// Sends Raids & Pokémon. Needs a 3th party program like pokealarm.
$webhookUrl = null;                             //['url-1','url-2']

//---------------------------------------------------
// Quest Webhooks
//---------------------------------------------------
$sendQuestWebhook = false;                      // Experimental use only
$questWebhookUrl = null;                        // Experimental use only
$webhookSystem = [''];				// Supported either 'pokealarm' or 'poracle'

$manualFiveStar = [
    'webhook' => false,						    // If set to false no webhooks will be send on raid_cron.php
    'pokemon_id' => 377,
    'cp' => 41777,
    'move_1' => null,
    'move_2' => null,
    'form' => 0
];

//-----------------------------------------------
// Search
//-----------------------------------------------------

$noSearch = false;
$noSearchPokestops = true;     //Wont work if noSearch = false
$noSearchGyms = true;          //Wont work if noSearch = false
$noSearchManualQuests = false;  //Wont work if noSearch = false
$noSearchNests = true;
$noSearchPortals = true;
$defaultUnit = "km";                                            // mi/km
$maxSearchResults = 10;		//Max number of search results
//-----------------------------------------------
// Community
//-----------------------------------------------------
$noCommunity = true;
$enableCommunities = 'false';
$noAddNewCommunity = true;
$noDeleteCommunity = true;
$noEditCommunity = true;

//-----------------------------------------------
// Nests
//-----------------------------------------------------
$noNests = true;                                                   // true/false
$enableNests = 'false';                                             // true/false
$noManualNests = true;
$noDeleteNests = true;
$nestVerifyLevel = 1;						    // 1 = Verified 2 = 1 + Unverified 3 = 1 + 2 + Revoked 4 = Get all nests
$deleteNestsOlderThan = 42;					    // days after not updated nests are removed from database by nest cron
$migrationDay = strtotime('5 April 2018');                          // Adjust day value after non consitent 14 day migration
$noAddNewNests = true;
$excludeNestMons = [2,3,5,6,8,9,11,12,14,15,17,18,20,22,24,26,28,29,30,31,32,33,34,36,38,40,42,44,45,49,51,53,55,57,59,61,62,64,65,67,68,70,71,73,75,76,78,80,82,83,85,87,88,89,91,93,94,97,99,101,103,105,106,107,108,109,110,112,113,114,115,117,119,121,122,128,130,131,132,134,135,136,137,139,142,143,144,145,146,147,148,149,150,151,153,154,156,157,159,160,161,162,163,164,165,166,167,168,169,171,172,173,174,175,176,177,178,179,180,181,182,183,184,186,187,188,189,191,192,194,195,196,197,199,201,204,205,207,208,210,212,214,217,218,219,221,222,223,224,225,228,229,230,232,233,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,253,254,256,257,259,260,262,263,264,265,266,267,268,269,270,271,272,274,275,276,277,279,280,281,282,284,286,287,288,289,290,291,292,293,294,295,297,298,301,303,304,305,306,308,310,313,314,316,317,319,321,323,324,326,327,328,329,330,331,332,334,335,336,337,338,339,340,342,344,346,348,349,350,351,352,354,356,357,358,359,360,361,362,363,364,365,366,367,368,369,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386];
$nestCoords = array();                                           //$nestCoords = array(array('lat1' => 42.8307723529682, 'lng1' => -88.7527692278689, 'lat2' => 42.1339901128552, 'lng2' => -88.0688703020877),array(    'lat1' => 42.8529250952743,'lng1' => -88.1292951067752,'lat2' => 41.7929306950085,'lng2' => -87.5662457903689));


//-----------------------------------------------------
// Areas
//-----------------------------------------------------

$noAreas = true;
$areas = [];                                                        // [[latitude,longitude,zoom,"name"],[latitude,longitude,zoom,"name"]]

//-----------------------------------------------------
// Weather Config
//-----------------------------------------------------

$noWeatherOverlay = true;                                          // true/false
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
$map = "rdm";                                                     // {monocle}/{rdm}
$fork = "default";                                                  // {default/alternate/mad}/{default/beta}

$db = new Medoo([// required
    'database_type' => 'mysql',                                    
    'database_name' => 'Monocle',
    'server' => '127.0.0.1',
    'username' => 'database_user',
    'password' => 'database_password',
    'charset' => 'utf8',

    // [optional]
    //'port' => 5432,                                               // Comment out if not needed, just add // in front!
    //'socket' => /path/to/socket/,
]);

//$manualdb = new Medoo([// required
//    'database_type' => 'mysql',
//    'database_name' => 'Monocle',
//    'server' => '127.0.0.1',
//    'username' => 'database_user',
//    'password' => 'database_password',
//    'charset' => 'utf8mb4',

    // [optional]
    //'port' => 5432,                                               // Comment out if not needed, just add // in front!
    //'socket' => /path/to/socket/,
//]);

if(file_exists('config/access-config.php'))
    include 'config/access-config.php';
