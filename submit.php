<?php
include('config/config.php');
global $map, $fork, $noSubmit;

if ($noSubmit === true) {
    http_response_code(401);
    die();
}

$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
if (preg_match("/curl|libcurl/", $useragent)) {
    http_response_code(400);
    die();
}

$action                 = ! empty($_POST['action']) ? $_POST['action'] : '';
$lat                    = ! empty($_POST['lat']) ? $_POST['lat'] : '';
$lon                    = ! empty($_POST['lon']) ? $_POST['lon'] : '';
$pokemonId              = ! empty($_POST['pokemonId']) ? $_POST['pokemonId'] : 0;
$gymId                  = ! empty($_POST['gymId']) ? $_POST['gymId'] : 0;
$eggTime                = ! empty($_POST['eggTime']) ? $_POST['eggTime'] : 0;
$monTime                = ! empty($_POST['monTime']) ? $_POST['monTime'] : 0;
$loggedUser             = ! empty($_SESSION['user']->user) ? $_SESSION['user']->user : 'NOLOGIN';
$gymName                = ! empty($_POST['gymName']) ? $_POST['gymName'] : '';
$pokestopId             = ! empty($_POST['pokestopId']) ? $_POST['pokestopId'] : '';
$pokestopName           = ! empty($_POST['pokestopName']) ? $_POST['pokestopName'] : '';
$questType              = ! empty($_POST['questType']) ? $_POST['questType'] : '';
$questTarget            = ! empty($_POST['questTarget']) ? $_POST['questTarget'] : '';
$conditionType          = ! empty($_POST['conditionType']) ? $_POST['conditionType'] : '';
$catchPokemon           = ! empty($_POST['catchPokemon']) ? $_POST['catchPokemon'] : '';
$catchPokemonCategory   = ! empty($_POST['catchPokemonCategory']) ? $_POST['catchPokemonCategory'] : '';
$raidLevel              = ! empty($_POST['raidLevel']) ? $_POST['raidLevel'] : '';
$throwType              = ! empty($_POST['throwType']) ? $_POST['throwType'] : '';
$curveThrow             = ! empty($_POST['curveThrow']) ? $_POST['curveThrow'] : '';
$rewardType             = ! empty($_POST['rewardType']) ? $_POST['rewardType'] : '';
$encounter              = ! empty($_POST['encounter']) ? $_POST['encounter'] : '';
$item                   = ! empty($_POST['item']) ? $_POST['item'] : '';
$itemAmount             = ! empty($_POST['itemamount']) ? $_POST['itemamount'] : '1';
$dust                   = ! empty($_POST['dust']) ? $_POST['dust'] : '';
$xp                     = ! empty($_POST['xp']) ? $_POST['xp'] : '';
$nestId                 = ! empty($_POST['nestId']) ? $_POST['nestId'] : '';
$portalId               = ! empty($_POST['portalId']) ? $_POST['portalId'] : '';
$targetType             = ! empty($_POST['targetType']) ? $_POST['targetType'] : '';
$communityId            = ! empty($_POST['communityId']) ? $_POST['communityId'] : '';
$communityName          = ! empty($_POST['communityName']) ? $_POST['communityName'] : '';
$communityDescription   = ! empty($_POST['communityDescription']) ? $_POST['communityDescription'] : '';
$communityInvite        = ! empty($_POST['communityInvite']) ? $_POST['communityInvite'] : '';
$poiName                = ! empty($_POST['poiName']) ? $_POST['poiName'] : '';
$poiDescription         = ! empty($_POST['poiDescription']) ? $_POST['poiDescription'] : '';
$poiNotes               = ! empty($_POST['poiNotes']) ? $_POST['poiNotes'] : '';
$poiId                  = ! empty($_POST['poiId']) ? $_POST['poiId'] : '';
$poiMarkType            = ! empty($_POST['poiMarkType']) ? $_POST['poiMarkType'] : '';
$poiImage               = ! empty($_POST['poiImage']) ? $_POST['poiImage'] : '';
$poiSurrounding         = ! empty($_POST['poiSurrounding']) ? $_POST['poiSurrounding'] : '';

// set content type
header('Content-Type: application/json');
$now = new DateTime();
$now->sub(new DateInterval('PT20S'));
$d           = array();
$d['status'] = "ok";
$d["timestamp"] = $now->getTimestamp();

if (strtolower($map) === "rdm") {
    $submit = new \Submit\RDM();
} elseif (strtolower($map) === "golbat") {
    $submit = new \Submit\Golbat();
} elseif (strtolower($map) === "rocketmap") {
    if (strtolower($fork) === "mad") {
        $submit = new \Submit\Rocketmap_MAD();
    }
}
$submitManual = new \Submit\Manual();

if ($action === "raid") {
    $submit->submit_raid($pokemonId, $gymId, $eggTime, $monTime, $loggedUser);
}
if ($action === "pokemon") {
    $submit->submit_pokemon($lat, $lon, $pokemonId);
}
if ($action === "gym") {
    $submit->submit_gym($lat, $lon, $gymName, $loggedUser);
}
if ($action === "toggle-ex-gym") {
    $submit->toggle_ex($gymId, $loggedUser);
}
if ($action === "delete-gym") {
    $submit->delete_gym($gymId, $loggedUser);
}
if ($action === "renamegym") {
    $submit->modify_gym($gymId, $gymName, $loggedUser);
}
if ($action === "pokestop") {
    $submit->submit_pokestop($lat, $lon, $pokestopName, $loggedUser);
}
if ($action === "renamepokestop") {
    $submit->modify_pokestop($pokestopId, $pokestopName, $loggedUser);
}
if ($action === "delete-pokestop") {
    $submit->delete_pokestop($pokestopId, $loggedUser);
}
if ($action === "convertpokestop") {
    $submit->convert_pokestop($pokestopId, $loggedUser);
}
if ($action === "quest") {
    $submit->submit_quest($pokestopId, $questType, $questTarget, $conditionType, $catchPokemonCategory, $catchPokemon, $raidLevel, $throwType, $curveThrow, $rewardType, $encounter, $item, $itemAmount, $dust, $xp, $loggedUser);
}
if ($action === "convertportal") {
    $submit->convert_portal($portalId, $targetType, $loggedUser);
}
if ($action === "markportal") {
    $submitManual->mark_portal($portalId, $loggedUser);
}
if ($action === "delete-portal") {
    $submitManual->delete_portal($portalId, $loggedUser);
}
if ($action === "nest") {
    $submitManual->modify_nest($nestId, $pokemonId, $loggedUser);
}
if ($action === "new-nest") {
    $submitManual->submit_nest($lat, $lon, $pokemonId, $loggedUser);
}
if ($action === "delete-nest") {
    $submitManual->delete_nest($nestId);
}
if ($action === "community-add") {
    $submitManual->submit_community($lat, $lon, $communityName, $communityDescription, $communityInvite, $loggedUser);
}
if ($action === "editcommunity") {
    $submitManual->modify_community($communityId, $communityName, $communityDescription, $communityInvite, $loggedUser);
}
if ($action === "delete-community") {
    $submitManual->delete_community($communityId, $loggedUser);
}
if ($action === "poi-add") {
    $submitManual->submit_poi($lat, $lon, $poiName, $poiDescription, $poiNotes, $poiImage, $poiSurrounding, $loggedUser);
}
if ($action === "edit-poi") {
    $submitManual->modify_poi($poiId, $poiName, $poiDescription, $poiNotes, $poiImage, $poiSurrounding, $loggedUser);
}
if ($action === "delete-poi") {
    $submitManual->delete_poi($poiId, $loggedUser);
}
if ($action === "markpoi") {
    $submitManual->mark_poi($poiId, $poiMarkType, $loggedUser);
}
$jaysson = json_encode($d);
echo $jaysson;
