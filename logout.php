<?php
require_once 'config/config.php';

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'discord-logout') {
        destroyCookiesAndSessions();
        header('Location: .');
        die();
    }
}
header('Location: .');
die;
