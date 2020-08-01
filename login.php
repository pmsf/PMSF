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
    }
    if ($_GET['action'] == 'facebook-login') {
        $fb = new Facebook\Facebook([
           'app_id' => $facebookAppId,
           'app_secret' => $facebookAppSecret,
           'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl($facebookAppRedirectUri);

        header("Location: {$loginUrl}");
        die();
    } else {
        header("Location: .");
        die();
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
                    die();
                } else {
                    if (in_array($user->id, $userWhitelist)) {
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

                $accessRole = checkAccessLevel($user->id, $guilds);

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
                            'access_level' => intval($accessRole),
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
                            'session_id' => $response->access_token,
                            'id' => $user->id,
                            'user' => $user->username . '#' . $user->discriminator,
                            'avatar' => 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . '.png',
                            'expire_timestamp' => time() + $response->expires_in,
                            'login_system' => 'discord',
                            'discord_guilds' => json_encode($guilds)
                        ]);
                    } else {
                        $manualdb->insert('users', [
                            'session_id' => $response->access_token,
                            'id' => $user->id,
                            'user' => $user->username . '#' . $user->discriminator,
                            'access_level' => intval($accessRole),
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
            if ($granted === true) {
                header("Location: .?login=true");
                die();
            }
	}
    }
    if ($_GET['callback'] == 'facebook') {
        if ($_GET['code']) {
            $fb = new Facebook\Facebook([
                'app_id' => $facebookAppId,
                'app_secret' => $facebookAppSecret,
                'default_graph_version' => 'v2.10',
            ]);
            $helper = $fb->getRedirectLoginHelper();

            try {
                $accessToken = $helper->getAccessToken();
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            if (! isset($accessToken)) {
                if ($helper->getError()) {
                    header('HTTP/1.0 401 Unauthorized');
                    echo "Error: " . $helper->getError() . "\n";
                    echo "Error Code: " . $helper->getErrorCode() . "\n";
                    echo "Error Reason: " . $helper->getErrorReason() . "\n";
                    echo "Error Description: " . $helper->getErrorDescription() . "\n";
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo 'Bad request';
                }
                exit;
            }
            $oAuth2Client = $fb->getOAuth2Client();

            $tokenMetadata = $oAuth2Client->debugToken($accessToken);

            $tokenMetadata->validateExpiration();

            if (! $accessToken->isLongLived()) {
                try {
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                    exit;
                }
            }
	    $userToken = $accessToken->getValue();

            try {
                $response = $fb->get('/me?fields=id,name,picture', $userToken);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            $user = $response->getGraphUser();
            if ($manualdb->has('users', ['id' => $user['id'], 'login_system' => 'facebook'])) {
                if ($manualAccessLevel) {
                    $manualdb->update('users', [
                        'session_id' => $userToken,
                        'user' => $user['name'],
                        'access_level' => $facebookAccessLevel,
                        'avatar' => $user['picture']['url'],
                    ], [
                        'id' => $user['id'],
                        'login_system' => 'facebook'
                    ]);
                } else {
                    $manualdb->update('users', [
                        'session_id' => $userToken,
                        'expire_timestamp' => time() + 86400,
                        'user' => $user['name'],
                        'access_level' => $facebookAccessLevel,
                        'avatar' => $user['picture']['url'],
                    ], [
                        'id' => $user['id'],
                        'login_system' => 'facebook'
                    ]);
                }
            } else {
                if ($manualAccessLevel) {
                    $manualdb->insert('users', [
                        'session_id' => $userToken,
                        'id' => $user['id'],
                        'user' => $user['name'],
                        'access_level' => $facebookAccessLevel,
                        'avatar' => $user['picture']['url'],
                        'expire_timestamp' => time() + 86400,
                        'login_system' => 'facebook'
                    ]);
                } else {
                    $manualdb->insert('users', [
                        'session_id' => $userToken,
                        'id' => $user['id'],
                        'user' => $user['name'],
                        'access_level' => $facebookAccessLevel,
                        'avatar' => $user['picture']['url'],
                        'access_level' => null,
                        'expire_timestamp' => time() + 86400,
                        'login_system' => 'facebook'
                    ]);
                }
            }
            setcookie("LoginCookie", $userToken, time() + 86400);
            setcookie("LoginEngine", 'facebook', time() + 86400);
            header("Location: .?login=true");
            die();
        }
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
if (!empty($_POST['signed_request'])) {
    $request = parse_signed_request($_POST['signed_request']);
    $manualdb->delete('users', ['id' => $request['user_id']]);
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
    $accessRole = '';
    foreach ($guildRoles['guildIDS'] as $guild => $guildRoles) {
        $isMember = array_search($guild , array_column($guilds, 'id'));
        if (!empty($isMember)) {
            $getMemberDetails = $discord->guild->getGuildMember(['guild.id' => $guild, 'user.id' => intval($userId)]);
            foreach ($getMemberDetails->roles as $role) {
                if (array_key_exists($role, $guildRoles)) {
                    if ($accessRole < strval($guildRoles[$role])) {
                        $accessRole = strval($guildRoles[$role]);
                    }
                }
            }
        }
    }
    return $accessRole;
}
function parse_signed_request($signed_request) {
    global $facebookAppSecret;
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    $secret = $facebookAppSecret;

    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);

    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) {
        error_log('Bad Signed JSON signature!');
        return null;
    }

    return $data;
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}
