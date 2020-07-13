<?php
include('config/config.php');
require __DIR__.'/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use RestCord\DiscordClient;

switch ($discordLogLevel) {
case "NOTICE":
    $loglevel = Logger::NOTICE;
    break;
case "INFO":
    $loglevel = Logger::INFO;
    break;
case "DEBUG":
    $loglevel = Logger::DEBUG;
    break;
default:
    $loglevel = Logger::INFO;
}

$logger = new Logger('PMSFLogger');
$logger->pushHandler(new StreamHandler($monologPath, $loglevel));
$logger->pushHandler(new FirePHPHandler());


$discord = new DiscordClient(['token' => $discordBotToken, 'logger' => $logger]); // Token is required

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'discord-login') {
        $params = [
            'client_id' => $discordBotClientId,
	        'redirect_uri' => $discordBotRedirectUri,
	        'response_type' => 'code',
	        'scope' => 'identify guilds'
        ];
        header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
        die();
    } else {
        header("Location: .");
    }
}

if (isset($_GET['callback'])) {
    if ($_GET['callback'] == 'discord') {
        if ($_GET['code']) {
            $token_request = 'https://discordapp.com/api/oauth2/token';
            $token = curl_init();
            curl_setopt_array($token, [
                CURLOPT_URL => $token_request,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $discordBotClientId,
                    'client_secret' => $discordBotClientSecret,
                    'redirect_uri' => $discordBotRedirectUri,
                    'code' => $_GET['code']
                ]
            ]);
            curl_setopt($token, CURLOPT_RETURNTRANSFER, true);

            $response = json_decode(curl_exec($token));
            curl_close($token);

            if (isset($response->access_token)) {
                $access_token = $response->access_token;
                $user_request = 'https://discordapp.com/api/users/@me';
                $guilds_request = 'https://discordapp.com/api/users/@me/guilds';

                $user = request($user_request, $access_token);
                $guilds = request($guilds_request, $access_token);

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
                if (!empty($guildRoles)) {
                    $accessRole = null;
                    foreach ($guildRoles['guildIDS'] as $guild => $guildRoles) {
                        $isMember = array_search($guild , array_column($guilds, 'id'));
                        if (!empty($isMember)) {
                            $getMemberDetails = $discord->guild->getGuildMember(['guild.id' => $guild, 'user.id' => intval($user->id)]);
                            foreach ($getMemberDetails->roles as $role) {
                                if (array_key_exists($role, $guildRoles)) {
                                    if ($accessRole < $guildRoles[$role]) {
                                        $accessRole = $guildRoles[$role];
                                    }
                                }
                            }
                        }
                    }
                }
                if ($manualdb->has('users', ['id' => $user->id, 'login_system' => 'discord'])) {
                    if ($manualAccessLevel) {
                        $manualdb->update('users', [
                            'session_id' => $response->access_token,
                            'user' => $user->username . '#' . $user->discriminator,
                            'avatar' => 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . '.png',
                            'discord_guilds' => json_encode($guilds)
                        ], [
                            'id' => $user->id,
                            'login_system' => 'discord'
                        ]);
                    } else {
                        $manualdb->update('users', [
                            'session_id' => $response->access_token,
                            'expire_timestamp' => time() + $response->expires_in,
                            'user' => $user->username . '#' . $user->discriminator,
                            'access_level' => $accessRole,
                            'avatar' => 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . '.png',
                            'discord_guilds' => json_encode($guilds)
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
                            'avatar' => 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . '.png',
                            'expire_timestamp' => time() + $response->expires_in,
                            'login_system' => 'discord',
                            'discord_guilds' => json_encode($guilds)
                        ]);
                    } else {
                        $manualdb->insert('users', [
                            'id' => $user->id,
                            'user' => $user->username . '#' . $user->discriminator,
                            'access_level' => $accessRole,
                            'avatar' => 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . '.png',
                            'expire_timestamp' => time() + $response->expires_in,
                            'login_system' => 'discord',
                            'discord_guilds' => json_encode($guilds)
                        ]);
                    }
                }
                setcookie("LoginCookie", $response->access_token, time() + $response->expires_in);
                setcookie("LoginEngine", 'discord', time() + $response->expires_in);
            }
	}
    } else {
        header("Location: .");
    }

}
if (!empty($_POST['refresh'])) {
    if ($_POST['refresh'] == 'discord') {
        $dbUser = $manualdb->get('users', ['id','session_id', 'access_level', 'discord_guilds'],['id' => $_SESSION['user']->id]);
        if (empty($dbUser)) {
            $answer = 'false';
        } else {
            $accessLevel = checkAccessLevel($dbUser['id'], json_decode($dbUser['discord_guilds']));
            if ($accessLevel == $dbUser['access_level']) {
                $answer = 'true';
            } elseif (!empty($accessLevel)) {
                $manualdb->update('users', [
                    'access_level' => $accessLevel
                ], [
                    'id' => $dbUser['id']
                ]);
                $answer = 'reload';
            } else {
                $manualdb->update('users', [
                    'access_level' => null
                ], [
                    'id' => $dbUser['id']
                ]);
                $answer = 'reload';
            }
        }
    }
    $answer = json_encode($answer);
    echo $answer;
}

function request($request, $access_token) {
    $info_request = curl_init();
    curl_setopt_array($info_request, [
        CURLOPT_URL => $request,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$access_token}"
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    return json_decode(curl_exec($info_request));
    curl_close($info);
}

function logFailure($logFailure) {
    global $logFailedLogin;
    file_put_contents($logFailedLogin, $logFailure, FILE_APPEND);
}
function checkAccessLevel ($userId, $guilds) {
    global $guildRoles, $discord;
    $accessRole = null;
    foreach ($guildRoles['guildIDS'] as $guild => $guildRoles) {
        $isMember = array_search($guild , array_column($guilds, 'id'));
        if (!empty($isMember)) {
            $getMemberDetails = $discord->guild->getGuildMember(['guild.id' => $guild, 'user.id' => intval($userId)]);
            foreach ($getMemberDetails->roles as $role) {
                if (array_key_exists($role, $guildRoles)) {
                    if ($accessRole < $guildRoles[$role]) {
                        $accessRole = $guildRoles[$role];
                    }
                }
            }
        }
    }
    return $accessRole;
}
