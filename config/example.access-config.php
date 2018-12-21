<?php
/* User levels */
// The part below requires the bot Chuckleslove wrote https://github.com/jepke/PMSF-Discord-AuthBot
// This bot is included when you pull PMSF so no need to pull it separately
// IDs must match the bots config
//
//THIS PART IS NOT PMSF CONFIG
//"guilds": {
//    "guildID": {		// Change GuildID into the discord server ID
//        "roleID":1,		// Change roleID into the role ID of the desired role
//        "roleID2":2		// Change roleID into the role ID of the desired role
//    },
//    "guildID2": {		// If only one server is used remove this.
//        "roleID": 3
//    }
//}
//THIS PART IS NOT PMSF CONFIG

$userLevel = 0;
$donorLevel = 1;
$helperLevel = 2;
$adminLevel = 3;
$ownerLevel = 4;

// if you are brave enough and now what you are doing you can add as many as levels you want
// If variable is the same as in config.php it may be removed from that specific level


if ($noNativeLogin === true && $noDiscordLogin === true ||  (($noNativeLogin === false || $noDiscordLogin === false) && !empty($_SESSION['user']->expire_timestamp) && $_SESSION['user']->expire_timestamp > time()))  {
    $userAccessLevel = $manualdb->get( "users", [ 'access_level' ], [ 'expire_timestamp' => $_SESSION['user']->expire_timestamp ] );
    if ($userAccessLevel['access_level'] == $userLevel) {
// Editting variables
        $noManualGyms = true;                                          // true/false
        $noManualPokestops = true;                                     // true/false
        $noRenamePokestops = true;                                     // true/false
        $noConvertPokestops = true;                                    // true/false
        $noDeleteGyms = true;                                          // true/false
        $noDeletePokestops = true;                                     // true/false
        $noAddNewCommunity = true;                                     // true/false
        $noDeleteCommunity = true;                                     // true/false
        $noEditCommunity = true;                                       // true/false
        $noAddNewNests = true;                                         // true/false
        $noDeletePortal = true;                                        // true/false
// Markers
        $noGyms = false;                                               // true/false
        $noPokestops = false;                                          // true/false  
        $noNests = false;                                              // true/false
        $noPortals = true;                                             // true/false
        $noCommunity = false;                                          // true/false
        $noRaids = false;                                              // true/false
        $noAreas = false;                                              // true/false
// Functionality
        $noManualPokemon = false;                                      // true/false
	    $noManualQuests = false;                                       // true/false
        $noManualRaids = false;                                        // true/false
        $copyrightSafe = true;                                         // true/false
        $noStartLast = false;                                          // true/false
        $noMapStyle = false;                                           // true/false
        $noSearchPortals = false;                                      // true/false
// Message of the Day
        $noMotd = true;                                                // true/false
        $motdTitle = "Message of the Day";
        $motdContent = "This is an example MOTD<br>Do whatever you like with it.";
    } else if ($userAccessLevel['access_level'] == $donorLevel) {
// Editting variables
        $noManualGyms = true;                                          // true/false
        $noManualPokestops = true;                                     // true/false
        $noRenamePokestops = true;                                     // true/false
        $noConvertPokestops = true;                                    // true/false
        $noDeleteGyms = true;                                          // true/false
        $noDeletePokestops = true;                                     // true/false
        $noAddNewCommunity = true;                                     // true/false
        $noDeleteCommunity = true;                                     // true/false
        $noEditCommunity = true;                                       // true/false
        $noAddNewNests = true;                                         // true/false
        $noDeletePortal = true;                                        // true/false
// Markers
        $noGyms = false;                                               // true/false
	    $noPokestops = false;                                          // true/false
        $noNests = false;                                              // true/false
        $noPortals = true;                                             // true/false
        $noCommunity = false;                                          // true/false
        $noRaids = false;                                              // true/false
        $noAreas = false;                                              // true/false
// Functionality
        $noManualPokemon = false;                                      // true/false
        $noManualQuests = false;                                       // true/false
        $noManualRaids = false;                                        // true/false
        $copyrightSafe = false;                                        // true/false
        $noStartLast = false;                                          // true/false
        $noMapStyle = false;                                           // true/false
        $noSearchPortals = false;                                      // true/false
// Message of the Day
        $noMotd = true;                                                // true/false
        $motdTitle = "Message of the Day";
        $motdContent = "This is an example MOTD<br>Do whatever you like with it.";
    } else if ($userAccessLevel['access_level'] == $helperLevel) {
// Editting variables
        $noManualGyms = false;                                         // true/false
        $noManualPokestops = false;                                    // true/false
        $noRenamePokestops = false;                                    // true/false
        $noConvertPokestops = true;                                    // true/false
        $noDeleteGyms = true;                                          // true/false
        $noDeletePokestops = true;                                     // true/false
        $noAddNewCommunity = true;                                     // true/false
        $noDeleteCommunity = true;                                     // true/false
        $noEditCommunity = true;                                       // true/false
        $noAddNewNests = true;                                         // true/false
        $noDeletePortal = true;                                        // true/false
// Markers
        $noGyms = false;                                               // true/false
        $noPokestops = false;                                          // true/false  
        $noNests = false;                                              // true/false
        $noPortals = false;                                            // true/false
        $noCommunity = true;                                           // true/false
        $noRaids = false;                                              // true/false
        $noAreas = false;                                              // true/false
// Functionality
        $noManualPokemon = false;                                      // true/false
        $noManualQuests = false;                                       // true/false
        $noManualRaids = false;                                        // true/false
        $copyrightSafe = false;                                        // true/false
        $noStartLast = false;                                          // true/false
        $noMapStyle = false;                                           // true/false
        $noSearchPortals = false;                                      // true/false
// Message of the Day
        $noMotd = true;                                                // true/false
        $motdTitle = "Message of the Day";
        $motdContent = "This is an example MOTD<br>Do whatever you like with it.";
    } else if ($userAccessLevel['access_level'] == $adminLevel) {
// Editting variables
        $noManualGyms = false;                                         // true/false
        $noManualPokestops = false;                                    // true/false
        $noManualPokemon = false;                                      // true/false
        $noRenamePokestops = false;                                    // true/false
        $noConvertPokestops = false;                                   // true/false
        $noDeleteGyms = false;                                         // true/false
        $noDeletePokestops = false;                                    // true/false
        $noAddNewCommunity = false;                                    // true/false
        $noDeleteCommunity = false;                                    // true/false
        $noEditCommunity = false;                                      // true/false
        $noDeletePortal = false;                                       // true/false
// Markers
        $noGyms = false;                                               // true/false
        $noPokestops = false;                                          // true/false  
        $noNests = false;                                              // true/false
        $noPortals = false;                                            // true/false
        $noCommunity = false;                                          // true/false
        $noRaids = false;                                              // true/false
        $noAreas = false;                                              // true/false
// Functionality
        $copyrightSafe = false;                                        // true/false
        $noManualQuests = false;                                       // true/false
        $noStartLast = false;                                          // true/false
        $noMapStyle = false;                                           // true/false
        $noManualRaids = false;                                        // true/false
        $noAddNewNests = false;                                        // true/false
        $noSearchPortals = false;                                      // true/false
// Message of the Day
        $noMotd = true;                                                // true/false
        $motdTitle = "Message of the Day";
        $motdContent = "This is an example MOTD<br>Do whatever you like with it.";
    } else if ($userAccessLevel['access_level'] == $ownerLevel) {
// Editting variables
        $noManualGyms = false;                                         // true/false
        $noManualPokemon = false;                                      // true/false
        $noManualPokestops = false;                                    // true/false
        $noRenamePokestops = false;                                    // true/false
        $noConvertPokestops = false;                                   // true/false
        $noDeleteGyms = false;                                         // true/false
        $noDeletePokestops = false;                                    // true/false
        $noAddNewCommunity = false;                                    // true/false
        $noDeleteCommunity = false;                                    // true/false
        $noEditCommunity = false;                                      // true/false
        $noDeletePortal = false;                                       // true/false
// Markers
        $noGyms = false;                                               // true/false
        $noPokestops = false;                                          // true/false  
        $noNests = false;                                              // true/false
        $noPortals = false;                                            // true/false
        $noCommunity = false;                                          // true/false
        $noRaids = false;                                              // true/false
        $noAreas = false;                                              // true/false
// Functionality
        $copyrightSafe = false;                                        // true/false
        $noManualQuests = false;                                       // true/false
        $noStartLast = false;                                          // true/false
        $noMapStyle = false;                                           // true/false
        $noManualRaids = false;                                        // true/false
        $noAddNewNests = false;                                        // true/false
        $noSearchPortals = false;                                      // true/false
//Message of the Day
        $noMotd = true;                                                // true/false
        $motdTitle = "Message of the Day";
        $motdContent = "This is an example MOTD<br>Do whatever you like with it.";
    }
}
