<?php
/* How it works */
// - Requires the bot Chuckleslove wrote https://github.com/jepke/PMSF-Discord-AuthBot or $manualAccessLevel
// - IDs in Authbot config must match user levels below.
// - You can add or remove as many user levels as you like.
// - Access config does work for native login with the use off $manualAccessLevel.
// - TIP: Patreon has a Discord integration to set discord roles (read: user levels) when someone makes a pledge. This works perfect in combination with authbot and this example access config.
// - EVERYTHING in this file overwrites config.php.

/* Define access levels. (must match authbot config)*/
$userLevel = 0;
$raidLevel = 1;
$questLevel = 2;
$ivLevel = 3;
$ownerLevel = 4;

/* If $_SESSION['user']->access_level matches defined user level, enable/disable something. */
if ($_SESSION['user']->access_level == $userLevel) {
    $noMotd = false;
    $motdTitle = "Welcome " . $_SESSION['user']->user . "!";
    $patreonUrl = 'https://yourPatreonURL.com';
    $motdContent = "<center>You can get access to Raids, Quests, Pok&eacute;mon and IV if you make a pledge to our Patreon.<br>
                    <a class='button' style='outline:none;' href='" . $patreonUrl . "'><i class='fab fa-patreon'></i> Pledge</a>
                    </center>";
} elseif ($_SESSION['user']->access_level == $raidLevel) {
    $noRaids = false;
} elseif ($_SESSION['user']->access_level == $questLevel) {
    $noRaids = false;
    $noQuests = false;
    $noLures = false;
} elseif ($_SESSION['user']->access_level == $ivLevel) {
    $noRaids = false;
    $noQuests = false;
    $noLures = false;
    $noPokemon = false;
    $noHighLevelData = false;
} elseif ($_SESSION['user']->access_level == $ownerLevel) {
    $noRaids = false;
    $noQuests = false;
    $noLures = false;
    $noPokemon = false;
    $noHighLevelData = false;
    $maxZoomOut = 1;
}
