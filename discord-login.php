<?php
include('config/config.php');

if ($noDiscordLogin === false) {
    require_once "vendor/autoload.php";
    require_once("DiscordAuth.php");

    $auth = new DiscordAuth();
    $auth->gotoDiscord();
} else {
    header("Location: .");
}
