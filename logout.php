<?php
require_once 'config/config.php';

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'discord-logout') {
        destroyCookiesAndSessions();
        if ($_GET['reason'] == 'change') {
            header('Location: ./login?action=login&error=access-change');
        } else {
            header('Location: .');
        }
        die();
    }
    if ($_GET['action'] == 'facebook-logout') {
        destroyCookiesAndSessions();
        if ($_GET['reason'] == 'change') {
            header('Location: ./login?action=login&error=access-change');
        } else {
            header('Location: .');
        }
        die();
    }
    if ($_GET['action'] == 'native-logout') {
        destroyCookiesAndSessions();
        if ($_GET['reason'] == 'change') {
            header('Location: ./login?action=login&error=access-change');
        } else {
            header('Location: .');
        }
        die();
    }
}
destroyCookiesAndSessions();
header('Location: .');
die;
