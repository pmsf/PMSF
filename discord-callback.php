<?php

require_once("config/config.php");
require_once("DiscordAuth.php");

if ($noDiscordLogin === false) {
    try {
        if (isset($_GET['code'])) {
            $auth = new DiscordAuth();
            $auth->handleAuthorizationResponse($_GET);
            $user = json_decode($auth->get("/api/users/@me"));

            $count = $db->count("users", [
                "id" => $user->{'id'},
                "login_system" => 'discord'
            ]);

            if ($count === 0) {
                $db->insert("users", [
                    "id" => $user->{'id'},
                    "user" => $user->{'username'} . "#" . $user->{'discriminator'},
                    "expire_timestamp" => time(),
                    "login_system" => 'discord'
                ]);

                $logMsg = "INSERT INTO users (id, user, expire_timestamp, login_system) VALUES ('{$user->id}', '{$user->username}" . "#" . "{$user->discriminator}', '" . time() ."', 'discord'); -- " . date('Y-m-d H:i:s') . "\r\n";
                file_put_contents($logfile, $logMsg, FILE_APPEND);
            }

            setcookie("LoginCookie", session_id(), time()+60*60*24*7);

            $db->update("users", [
                "Session_ID" => session_id(),
                "user" => $user->{'username'} . "#" . $user->{'discriminator'}
            ], [
                "id" => $user->{'id'},
                "login_system" => 'discord'
            ]);
        }
        header("Location: .");
        die();
    } catch (Exception $e) {
        header("Location: ./discord-login");
    }
} else {
    header("Location: .");
}
