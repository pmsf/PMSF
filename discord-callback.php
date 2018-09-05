<?php

require_once("config/config.php");
require_once("DiscordAuth.php");

if ($noDiscordLogin === false) {
    try {
        if (isset($_GET['code'])) {
            $auth = new DiscordAuth();
            $auth->handleAuthorizationResponse($_GET);
            $user = json_decode($auth->get("/api/users/@me"));
	    $guilds = json_decode($auth->get("/api/users/@me/guilds"));
            if (in_array($user->{'id'}, $userWhitelist)) {
                header("Location: .?login=true");
            } else {
	        foreach($guilds as $guild) {
                    $uses = $guild->id;
                    if (in_array($uses, $serverWhitelist)) {
                        header("Location: .?login=true");
                        $granted = true;
                    }
		}
	    }
            if (!$granted) {
                header("Location: ./access-denied.php");
                die();
            }
            $count = $db->count("users", [
                "id" => $user->{'id'},
                "login_system" => 'discord'
            ]);

            if ($count === 0) {
                $db->insert("users", [
                    "id" => $user->{'id'},
                    "user" => $user->{'username'} . "#" . $user->{'discriminator'},
                    "expire_timestamp" => time()+$sessionLifetime,
                    "login_system" => 'discord'
                ]);
            }

            setcookie("LoginCookie", session_id(), time()+$sessionLifetime);

            $db->update("users", [
                "expire_timestamp" => time()+$sessionLifetime,
                "session_id" => session_id(),
                "user" => $user->{'username'} . "#" . $user->{'discriminator'}
            ], [
                "id" => $user->{'id'},
                "login_system" => 'discord'
            ]);
	}
        die();
    } catch (Exception $e) {
        header("Location: ./discord-login");
    }
} else {
    header("Location: .");
}
