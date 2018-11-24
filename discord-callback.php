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
            if (in_array($user->{'id'}, $userBlacklist)) {
                header("Location: ./access-denied.php");
                $granted = false;
            } else {
                if (in_array($user->{'id'}, $userWhitelist)) {
                    header("Location: .?login=true");
                    $granted = true;
                } else {
	            foreach($guilds as $guild) {
                        $uses = $guild->id;
                        $guildName = $guild->name;
                        if (in_array($uses, $serverBlacklist)) {
                            if ($logFailedLogin) {
                                logFailure($user->{'username'} . "#" . $user->{'discriminator'} . " has been blocked for being a member of " . $guildName . "\n");
                            }
                            header("Location: .?login=false");
                            die();
                        } else {
                            if (in_array($uses, $serverWhitelist)) {
                                header("Location: .?login=true");
                                $granted = true;
                            }
                        }
		    }
                }
	    }
            if ($granted !== true) {
                header("Location: .?login=false");
                die();
            }
            $count = $manualdb->count("users", [
                "id" => $user->{'id'},
                "login_system" => 'discord'
            ]);

            if ($count === 0) {
                $manualdb->insert("users", [
                    "id" => $user->{'id'},
                    "user" => $user->{'username'} . "#" . $user->{'discriminator'},
                    "expire_timestamp" => time()+$sessionLifetime,
                    "login_system" => 'discord'
                ]);
            }

            setcookie("LoginCookie", session_id(), time()+$sessionLifetime);

            $manualdb->update("users", [
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

function logFailure($logFailure){
    global $logFailedLogin;
    file_put_contents($logFailedLogin, $logFailure, FILE_APPEND);
}

