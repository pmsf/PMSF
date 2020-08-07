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
    if ($_GET['action'] == 'login') {
        $html = '<html lang="' . $locale . '">
        <head>
        <meta charset="utf-8">
        <title>' . $title . ' Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">';
        if ($faviconPath != "") {
            echo '<link rel="shortcut icon" href="' . $faviconPath . '"
                 type="image/x-icon">';
        } else {
            echo '<link rel="shortcut icon" href="static/appicons/favicon.ico"
                 type="image/x-icon">';
        }
        $html .= '<link rel="stylesheet" href="static/dist/css/app.min.css">
        </head>
        <body>
            <h2>' . $title . ' Login</h2>
            <div id="login-force" class="force-modal">
                 <form class="force-modal-content animate" action="/login?action=native" method="post">';
                 if (!empty($_GET['error'])) {
                     switch ($_GET['error']) {
                         case 'no-member-dc':
                             $html .= "<div id='login-error'>" . i8ln('You are currently not a member of our discord there for access has been denied. Become a member to gain access at ') . "<a href='" . $discordUrl . "'></div>";
                             break;
                         case 'no-account':
                             $html .= "<div id='login-error'>" . i8ln('We couldn\'t find the account you are trying to use to login.') . "</div>";
                             break;
                         case 'password':
                             $html .= "<div id='login-error'>" . i8ln('Oops we might need to use the password reset if you can\'t remember.') . "</div>";
                             break;
                         case 'blacklisted-member':
                             $html .= "<div id='login-error'>" . i8ln('Your account is banned for the use of this website please contact the site admin.') . "</div>";
                             break;
                         case 'blacklisted-server-dc':
                             $html .= "<div id='login-error'>" . i8ln('We found you are a member of the following discord server we have blacklisted: ') . $_GET['bl-discord'] . "</div>";
                             break;
                     }
                 }
                     $html .= '<div class="imgcontainer">
                         <i class="fas fa-user" style="font-size:80px"></i>
                     </div>
		     <div class="force-container">';
                         if ($noNativeLogin === false) {
                             $html .= "<label for='uname'><b>Email address</b></label>
                             <input type='email' placeholder='Enter Email address' name='uname' required>

                             <label for='psw'><b>Password</b></label>
                             <input type='password' placeholder='Enter Password' name='psw' required>
        
                             <button type='submit' class='force-button'>Login</button>";
                         }
                         if ($noDiscordLogin === false) {
			     $html .= "<button type='button' style='background-color: #1877f2; margin: 2px' onclick=\"location.href='./login?action=discord-login';\" value='Login with discord'><i class='fab fa-discord'></i>&nbsp" . i8ln('Login with Discord') . "</button>";
			 }
                         if ($noFacebookLogin === false) {
                             $html .= "<button type='button' style='background-color: #1877f2; margin: 2px' onclick=\"location.href='./login?action=facebook-login';\" value='Login with discord'><i class='fab fa-facebook'></i>&nbsp" . i8ln('Login with Facebook') . "</button>";
                         }
                     $html .= '</div>

                     <div class="force-container" style="background-color:#f1f1f1">';
                         if ($noNativeLogin === false) {
                             $html .= "<button type='button' style='background-color: #4CAF50; margin: 2px' onclick=\"location.href='./register?action=account';\" value='Register'><i class='fas fa-user'></i>&nbsp" . i8ln('Register') . "</button>";
                             $html .= "<button type='button' style='background-color: #4CAF50; margin: 2px' onclick=\"location.href='./register?action=password-reset';\" value='Forgot password?'><i class='fas fa-lock'></i>&nbsp" . i8ln('Forgot Password') . "</button>";
                         }
                     $html .= '</div>
                 </form>
	</div>
        </body>
	</html>';
        echo $html;
        die();
    }
    if ($_GET['action'] == 'native') {
        $info = $manualdb->query(
            "SELECT id, user, password, expire_timestamp, temp_password FROM users WHERE user = :user AND login_system = 'native'", [
                ":user" => $_POST['uname']
            ]
        )->fetch();
        if (!$info) {
            header("Location: ./login?action=login&error=no-account");
            die();
        }
        if (password_verify($_POST['psw'], $info['password']) === true || password_verify($_POST['psw'], $info['temp_password']) === true) {
            if (password_verify($_POST['psw'], $info['temp_password']) === true) {
                header("Location: ./register?action=password-update&username=" . $_POST['uname'] . "");
                die();
            }
            if (password_verify($_POST['psw'], $info['password']) === true) {
                if (!empty($info['temp_password'])) {
                    $manualdb->update("users", [
                        "temp_password" => null
                    ], [
                        "user" => $_POST['uname'],
                        "login_system" => 'native'
                    ]);
                }
	    } else {
                header("Location: ./login?action=login&error=password");
            }
            $manualdb->update("users", [
                "session_id" => session_id()
            ], [
                "user" => $_POST['uname'],
                "login_system" => 'native'
            ]);

            setcookie("LoginCookie", session_id(), time()+60*60*24*7);
            setcookie("LoginEngine", 'native', time()+60*60*24*7);
            header("Location: .?login=true");
            die();
	} else {
            header("Location: ./login?action=login&error=password");
            die();
        }
    }
    if ($_GET['action'] == 'discord-login') {
        $params = [
            'client_id' => $discordBotClientId,
            'redirect_uri' => $discordBotRedirectUri,
            'response_type' => 'code',
            'scope' => 'identify guilds'
        ];
        header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params));
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
            $token_request = 'https://discord.com/api/oauth2/token';
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
                $user_request = 'https://discord.com/api/users/@me';
                $guilds_request = 'https://discord.com/api/users/@me/guilds';

                $user = request($user_request, $access_token);
                $guilds = request($guilds_request, $access_token);

                if (in_array($user->id, $userBlacklist)) {
                    header("Location: ./login?action=login&error=blacklisted-member");
                    die();
                } else {
                    $whiteListed = false;
                    if (in_array($user->id, $userWhitelist)) {
                        $whiteListed = true;
                    } else {
                        foreach ($guilds as $guild) {
                            $uses = $guild->id;
                            $guildName = $guild->name;
                            if (in_array($uses, $serverBlacklist)) {
                                if ($logFailedLogin) {
                                    logFailure(strval($user->{'username'}) . "#" . $user->{'discriminator'} . " has been blocked for being a member of " . $guildName . "\n");
                                }
                                header("Location: ./login?action=login&error=blacklisted-server-dc&bl-discord=" . $guildName . " ");
                                die();
                            } else if (array_key_exists($uses, $guildRoles['guildIDS'])) {
                                $whiteListed = true;
                            }
                        }
                    }
                }

                if ($whiteListed !== true) {
                    header("Location: ./login?action=login&error=no-member-dc");
                    die();
                }

                $accessRole = checkAccessLevel($user->id, $guilds);

                $format = '.png';
                if (strpos($user->avatar, 'a_') === 0) {
                    $format = '.gif';
                }

                $avatar = 'https://discordapp.com/assets/6debd47ed13483642cf09e832ed0bc1b.png';
                if (!empty($user->avatar)) {
                    $avatar = 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . $format;
		}

                if ($manualdb->has('users', ['id' => $user->id, 'login_system' => 'discord'])) {
                    $manualdb->update('users', [
                        'session_id' => $response->access_token,
                        'expire_timestamp' => time() + $response->expires_in,
                        'user' => strval($user->username) . '#' . $user->discriminator,
                        'access_level' => intval($accessRole),
                        'avatar' => $avatar,
                        'discord_guilds' => json_encode($guilds)
                    ], [
                        'id' => $user->id,
                        'login_system' => 'discord'
                    ]);
                } else {
                    $manualdb->insert('users', [
                        'session_id' => $response->access_token,
                        'id' => $user->id,
                        'user' => strval($user->username) . '#' . $user->discriminator,
                        'access_level' => intval($accessRole),
                        'avatar' => $avatar,
                        'expire_timestamp' => time() + $response->expires_in,
                        'login_system' => 'discord',
                        'discord_guilds' => json_encode($guilds)
                    ]);
                }
                setcookie("LoginCookie", $response->access_token, time() + $response->expires_in);
                setcookie("LoginEngine", 'discord', time() + $response->expires_in);
            }
            if ($whiteListed === true) {
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
            setcookie("LoginCookie", $userToken, time() + 86400);
            setcookie("LoginEngine", 'facebook', time() + 86400);
            header("Location: .?login=true");
            die();
        }
    }
}
if (!empty($_POST['refresh'])) {
    $answer = '';
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
        $isMember = in_array($guild , array_column($guilds, 'id'));
        if ($isMember) {
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
