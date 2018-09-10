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
$modLevel = 1;
$adminLevel = 2;

// if you are brave enough and now what you are doing you can add as many as levels you want


if ($noNativeLogin === true && $noDiscordLogin === true ||  (($noNativeLogin === false || $noDiscordLogin === false) && !empty($_SESSION['user']->expire_timestamp) && $_SESSION['user']->expire_timestamp > time()))  {
    $userAccessLevel = $db->get( "users", [ 'access_level' ], [ 'expire_timestamp' => $_SESSION['user']->expire_timestamp ] );
    if ($userAccessLevel['access_level'] == $userLevel) {
	    $noManualGyms = true;
	    $noManualPokestops = true;
	    $noRenamePokestops = true;
	    $noConvertPokestops = true;
	    $noDeleteGyms = true;
	    $noDeletePokestops = true;
	    $noManualQuests = false;
	    $noPokestops = false;                                               // true/false
	    $noGyms = false;                                                    // true/false
	    $noCommunity = false;
	    $noAddNewCommunity = true;
	    $noDeleteCommunity = true;
	    $noEditCommunity = true;
	    $noPortals = true;
	    $noDeletePortal = true;
    } else if ($userAccessLevel['access_level'] == $modLevel) {
	    $noManualGyms = true;
	    $noManualPokestops = true;
	    $noRenamePokestops = true;
	    $noConvertPokestops = false;
	    $noDeleteGyms = true;
	    $noDeletePokestops = true;
	    $noManualQuests = false;
	    $noPokestops = false;                                               // true/false
	    $noGyms = false;                                                    // true/false
	    $noCommunity = false;
	    $noAddNewCommunity = false;
	    $noDeleteCommunity = false;
	    $noEditCommunity = true;
	    $noPortals = true;
	    $noDeletePortal = true;
    } else if ($userAccessLevel['access_level'] == $adminLevel) {
	    $noManualGyms = false;
	    $noManualPokestops = false;
	    $noRenamePokestops = false;
	    $noConvertPokestops = false;
	    $noDeleteGyms = false;
	    $noDeletePokestops = false;
	    $noManualQuests = false;
	    $noPokestops = false;                                               // true/false
	    $noGyms = false;                                                    // true/false
	    $noCommunity = false;
	    $noAddNewCommunity = false;
	    $noDeleteCommunity = false;
	    $noEditCommunity = false;
	    $noPortals = false;
	    $noDeletePortal = false;
    }
}
