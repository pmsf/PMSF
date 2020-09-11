<?php
/* Define access levels. (must match authbot config)*/
$userLevel = 1;
$raidLevel = 2;
$questLevel = 3;
$ivLevel = 4;
$ownerLevel = 5;

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
