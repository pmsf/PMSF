<?php
/* How it works */
// - Requires the bot Chuckleslove wrote https://github.com/jepke/PMSF-Discord-AuthBot
// - IDs in Authbot config must match user levels below.
// - You can add or remove as many user levels as you like.
// - Access config does work for native login BUT ALL users are set to user level 0. Selly integration could be working, it could also not work. Use at own risk.
// - TIP: Patreon has a Discord integration to set discord roles (read: user levels) when someone makes a pledge. This works perfect in combination with authbot and this example access config.
// - EVERYTHING in this file overwrites config.php.

/* Query the access level of the logged user (DO NOT EDIT OR REMOVE THIS) */
$userAccessLevel = $manualdb->get("users", [ 'access_level' ], [ 'user' => $_SESSION['user']->user ]);

/* Define access levels. (must match authbot config)*/
$userLevel = 0;
$raidLevel = 1;
$questLevel = 2;
$ivLevel = 3;
$ownerLevel = 4;

/* If query result matches defined user level, enable/disable something. */
if ($userAccessLevel['access_level'] == $userLevel) {
    $noMotd = false;
    $motdTitle = "Welcome " . $_SESSION['user']->user . "!";
    $patreonUrl = 'https://yourPatreonURL.com';
    $motdContent = "<center>You can get access to Raids, Quests, Pok&eacute;mon and IV if you make a pledge to our Patreon.<br>
                    <a class='button' style='outline:none;' href='" . $patreonUrl . "'><i class='fab fa-patreon'></i> Pledge</a>
                    </center>";
} elseif ($userAccessLevel['access_level'] == $raidLevel) {
    $noRaids = false;
} elseif ($userAccessLevel['access_level'] == $questLevel) {
    $noRaids = false;
    $noQuests = false;
    $noLures = false;
} elseif ($userAccessLevel['access_level'] == $ivLevel) {
    $noRaids = false;
    $noQuests = false;
    $noLures = false;
    $noPokemon = false;
    $noHighLevelData = false;
} elseif ($userAccessLevel['access_level'] == $ownerLevel) {
    $noRaids = false;
    $noQuests = false;
    $noLures = false;
    $noPokemon = false;
    $noHighLevelData = false;
    $maxZoomOut = 1;
}
