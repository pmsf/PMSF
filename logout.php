<?php
require_once './config/config.php';

destroyCookiesAndSessions();

header('Location: .');
die;
