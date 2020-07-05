<?php
include('config/config.php');

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'discord-login') {
        $params = array(
            'client_id' => $discordBotClientId,
	    'redirect_uri' => $discordBotRedirectUri,
	    'response_type' => 'code',
	    'scope' => 'identify guilds'
        );
        header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
        die();
    }
}

if (isset($_GET['callback'])) {
    if ($_GET['callback'] == 'discord') {
        if ($_GET['code']) {
            $token_request = 'https://discordapp.com/api/oauth2/token';
            $token = curl_init();
            curl_setopt_array($token, array(
                CURLOPT_URL => $token_request,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => array(
                    'grant_type' => 'authorization_code',
                    'client_id' => $discordBotClientId,
                    'client_secret' => $discordBotClientSecret,
                    'redirect_uri' => $discordBotRedirectUri,
                    'code' => $_GET['code']
                )
            ));
            curl_setopt($token, CURLOPT_RETURNTRANSFER, true);

            $response = json_decode(curl_exec($token));
            curl_close($token);

            if (isset($response->access_token)) {
                $access_token = $response->access_token;
                $user_request = 'https://discordapp.com/api/users/@me';
                $guilds_request = 'https://discordapp.com/api/users/@me/guilds';

                $user = request($user_request, $access_token);
                $guilds = request($guilds_request, $access_token);

		####TESTING
                echo "<h1>Hello, {$user->username}#{$user->discriminator}.</h1><br><h2>{$user->id}</h2><br><img src='https://cdn.discordapp.com/avatars/{$user->id}/{$user->avatar}.jpg' /><br><br>Dashboard Token: {$access_token}";
		file_put_contents('log.txt', print_r($access_token, true), FILE_APPEND);
		####TESTING
                if (in_array($user->id, $userBlacklist)) {
                    header("Location: ./access-denied.php");
                    $granted = false;
                } else {
                    if (in_array($user->id, $userWhitelist)) {
                        header("Location: .?login=true");
                        $granted = true;
		    } else {
                        foreach ($guilds as $guild) {
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
		if ($manualdb->has('users', [
                    'id' => $user->id,
                    'login_system' => 'discord'
                ])) {
                    if ($manualAccessLevel) {
                        $manualdb->update('users', [
                            'session_id' => $response->access_token,
                            'user' => $user->username . '#' . $user->discriminator
                        ], [
                            'id' => $user->id,
                            'login_system' => 'discord'
                        ]);
                    } else {
                        $manualdb->update('users', [
                            'session_id' => $response->access_token,
                            "expire_timestamp" => time() + $response->expires_in,
                            'user' => $user->username . '#' . $user->discriminator
                        ], [
                            'id' => $user->id,
                            'login_system' => 'discord'
                        ]);
                    }
                } else {
                    if ($manualAccessLevel) {
                        $manualdb->insert('users', [
                            'id' => $user->id,
                            'user' => $user->username . '#' . $user->discriminator,
                            'expire_timestamp' => time() + $response->expires_in,
                            'login_system' => 'discord'
                        ]);
                    } else {
                        $manualdb->insert('users', [
                            'id' => $user->id,
                            'user' => $user->username . '#' . $user->discriminator,
                            'expire_timestamp' => time() + $response->expires_in,
                            'login_system' => 'discord'
                        ]);
                    }
                }
                setcookie("LoginCookie", $response->access_token, time() + $response->expires_in, null, null, null, true);
            }
        } else {
            header('Location: .');
        }
    }
}

function request($request, $access_token) {
    $info_request = curl_init();
    curl_setopt_array($info_request, array(
        CURLOPT_URL => $request,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer {$access_token}"
        ),
        CURLOPT_RETURNTRANSFER => true
    ));
    return json_decode(curl_exec($info_request));
    curl_close($info);
}

function logFailure($logFailure) {
    global $logFailedLogin;
    file_put_contents($logFailedLogin, $logFailure, FILE_APPEND);
}
