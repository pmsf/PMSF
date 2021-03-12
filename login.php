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
            <title>' . $title . ' ' . i8ln('Login') . '</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">';
            if ($faviconPath != "") {
                echo '<link rel="shortcut icon" href="' . $faviconPath . '" type="image/x-icon">';
            } else {
                echo '<link rel="shortcut icon" href="' . $appIconPath . 'favicon.ico" type="image/x-icon">';
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
                                $html .= "<div id='login-error'>" . i8ln('You are currently not a member of our Discord server. Therefore access has been denied.') . " <a href='" . $discordUrl . "'>" . i8ln('Become a member to gain access.') . "</a></div>";
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
                            case 'bad-response-dc':
                                $html .= "<div id='login-error'>" . i8ln('Something went wrong while receiving Discord information, please try again in a few seconds and contact your admin if the problem persists.') . " (" . $_GET['error-message'] . ")</div>";
                                break;
                            case 'duplicate-login':
                                $html .= "<div id='login-error'>" . i8ln('We logged you out because a different device just logged in with the same account.') . "</div>";
                                break;
                            case 'no-id':
                                $html .= "<div id='login-error'>" . i8ln('Something went wrong as we couldn\'t find your session id.') . "</div>";
                                break;
                            case 'failed-token':
                                $html .= "<div id='login-error'>" . i8ln('Something went wrong while verifying external login') . "</div>";
                                break;
                            case 'no-member-patreon':
                                $html .= "<div id='login-error'>" . i8ln('It seems you haven\'t pledged to our patreon. Therefore access has been denied.') . " <a href='" . $patreonUrl . "'>" . i8ln('Pledge at Patreon to gain access.') . "</a></div>";
                                break;
                            case 'invalid-token':
                                $html .= "<div id='login-error'>" . i8ln('We have logged you out. This might be because of invalid or expired token or your account has been logged in on another device.') . "</div>";
                                break;
                            case 'access-change':
                                $html .= "<div id='login-error'>" . i8ln('Your level of access changed while logged in please login again to get the new level of access.') . "</div>";

                                break;
                        }
                    }
                    $html .= '<div class="imgcontainer">
                    <i class="fas fa-user" style="font-size:80px"></i>
                    </div>
                    <div class="force-container">';
                    if ($noNativeLogin === false) {
                        $html .= "<label for='uname'><b>" . i8ln('Email address') . "</b></label>
                        <input type='email' placeholder='" . i8ln('Enter Email address') . "' name='uname' required>

                        <label for='psw'><b>" . i8ln('Password') . "</b></label>
                        <input type='password' placeholder='" . i8ln('Enter Password') . "' name='psw' required>
        
                        <button type='submit' class='force-button'>" . i8ln('Login') . "</button>";
                    }
                    if ($noDiscordLogin === false) {
                        $html .= "<button type='button' style='background-color: #1877f2; margin: 2px' onclick=\"location.href='./login?action=discord-login';\" value='Login with discord'><i class='fab fa-discord'></i>&nbsp" . i8ln('Login with Discord') . "</button>";
                    }
                    if ($noFacebookLogin === false) {
                        $html .= "<button type='button' style='background-color: #1877f2; margin: 2px' onclick=\"location.href='./login?action=facebook-login';\" value='Login with facebook'><i class='fab fa-facebook'></i>&nbsp" . i8ln('Login with Facebook') . "</button>";
                    }
                    if ($noGroupmeLogin === false) {
                        $html .= "<button type='button' style='background-color: #1877f2; margin: 2px' onclick=\"location.href='./login?action=groupme-login';\" value='Login with groupme'><i class='fas fa-smile'></i>&nbsp" . i8ln('Login with Groupme') . "</button>";
                    }
                    if ($noPatreonLogin === false) {
                        $html .= "<button type='button' style='background-color: #1877f2; margin: 2px' onclick=\"location.href='./login?action=patreon-login';\" value='Login with patreon'><i class='fab fa-patreon'></i>&nbsp" . i8ln('Login with Patreon') . "</button>";
                    }
                    $html .= '</div>
                    <div class="force-container" style="background-color:#f1f1f1">';
                    if ($noNativeLogin === false) {
                        $html .= "<button type='button' style='background-color: #4CAF50; margin: 2px' onclick=\"location.href='./register?action=account';\" value='Register'><i class='fas fa-user'></i>&nbsp" . i8ln('Register') . "</button>";
                        $html .= "<button type='button' style='background-color: #4CAF50; margin: 2px' onclick=\"location.href='./register?action=password-reset';\" value='Forgot password?'><i class='fas fa-lock'></i>&nbsp" . i8ln('Forgot Password') . "</button>";
                    }
                    if ($noNativeLogin && $noDiscordLogin && $noFacebookLogin && $noPatreonLogin) {
                        header("Location: ./");
                        die();
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
                'session_token' => $_SESSION['token'],
                'session_id' => session_id(),
                'last_loggedin' => time()
            ], [
                'user' => $_POST['uname'],
                'login_system' => 'native'
            ]);

            setcookie("LoginCookie", session_id(), time() + $sessionLifetime);
            setcookie("LoginEngine", 'native', time() + $sessionLifetime);
            if ($useLoginCookie) {
                setrawcookie("LoginSession", $_SESSION['token'], time() + $sessionLifetime);
            }
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
    if ($_GET['action'] == 'patreon-login') {
        $params = [
            'client_id' => $patreonClientId,
            'redirect_uri' => $patreonCallbackUri,
            'response_type' => 'code',
            'scope' => 'identity identity.memberships campaigns.members',
            'state' => $_SESSION['token']
        ];
        header('Location: https://www.patreon.com/oauth2/authorize' . '?' . http_build_query($params));
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
    }
    if ($_GET['action'] == 'groupme-login') {
        header("Location: https://oauth.groupme.com/oauth/authorize?client_id=" . $groupmeClientId);
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
                            if (!empty($guild->id)) {
                                if (in_array($guild->id, $serverBlacklist)) {
                                    if ($logFailedLogin) {
                                        logFailure(strval($user->{'username'}) . "#" . $user->{'discriminator'} . " has been blocked for being a member of " . $guild->name . "\n");
                                    }
                                    header("Location: ./login?action=login&error=blacklisted-server-dc&bl-discord=" . $guild->name . " ");
                                    die();
                                } elseif (array_key_exists($guild->id, $guildRoles['guildIDS'])) {
                                    $whiteListed = true;
                                }
                            }
                        }
                    }
                }

                if ($whiteListed !== true) {
                    header("Location: ./login?action=login&error=no-member-dc");
                    die();
                }

                $accessRole = checkAccessLevelDiscord($user->id, $guilds);

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
                        'session_token' => $_SESSION['token'],
                        'session_id' => $response->access_token,
                        'expire_timestamp' => time() + $response->expires_in,
                        'user' => strval($user->username) . '#' . $user->discriminator,
                        'access_level' => intval($accessRole),
                        'avatar' => $avatar,
                        'discord_guilds' => json_encode($guilds),
                        'last_loggedin' => time()
                    ], [
                        'id' => $user->id,
                        'login_system' => 'discord'
                    ]);
                } else {
                    $manualdb->insert('users', [
                        'session_token' => $_SESSION['token'],
                        'session_id' => $response->access_token,
                        'id' => $user->id,
                        'user' => strval($user->username) . '#' . $user->discriminator,
                        'access_level' => intval($accessRole),
                        'avatar' => $avatar,
                        'expire_timestamp' => time() + $response->expires_in,
                        'login_system' => 'discord',
                        'discord_guilds' => json_encode($guilds),
                        'last_loggedin' => time()
                    ]);
                }
                if ($manualdb->has('users', ['linked_account' => $user->id, 'login_system' => 'patreon'])) {
                    $linked_account = $manualdb->get('users', ['linked_account'],['id' => $user->id]);
                    $manualdb->update('users', [
                        'session_token' => null,
                        'session_id' => null,
                        'expire_timestamp' => time() - 86400
                    ], [
                        'id' => $linked_account['linked_account'],
                        'login_system' => 'patreon'
                    ]);
                }
                setcookie("LoginCookie", $response->access_token, time() + $response->expires_in);
                setcookie("LoginEngine", 'discord', time() + $response->expires_in);
                if ($useLoginCookie) {
                    setrawcookie("LoginSession", $_SESSION['token'], time() + $response->expires_in);
                }
            } else {
                header("Location: ./login?action=login&error=bad-response-dc&error-message=token");
                die();
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
                    'session_token' => $_SESSION['token'],
                    'session_id' => $userToken,
                    'expire_timestamp' => time() + $sessionLifetime,
                    'user' => $user['name'],
                    'access_level' => $facebookAccessLevel,
                    'avatar' => $user['picture']['url'],
                    'last_loggedin' => time()
                ], [
                    'id' => $user['id'],
                    'login_system' => 'facebook'
                ]);
            } else {
                $manualdb->insert('users', [
                    'session_token' => $_SESSION['token'],
                    'session_id' => $userToken,
                    'id' => $user['id'],
                    'user' => $user['name'],
                    'access_level' => $facebookAccessLevel,
                    'avatar' => $user['picture']['url'],
                    'access_level' => null,
                    'expire_timestamp' => time() + $sessionLifetime,
                    'login_system' => 'facebook',
                    'last_loggedin' => time()
                ]);
            }
            setcookie("LoginCookie", $userToken, time() + $sessionLifetime);
            setcookie("LoginEngine", 'facebook', time() + $sessionLifetime);
            if ($useLoginCookie) {
                setrawcookie("LoginSession", $_SESSION['token'], time() + $sessionLifetime);
            }
            header("Location: .?login=true");
            die();
        }
    }
    if ($_GET['callback'] == 'groupme') {
        if ($_GET['?access_token']) {
            $userToken = $_GET['?access_token'];
            $headers = array();
            $headers[] = "X-Access-Token: $userToken";
            $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
            $user_request = curl_init();
                curl_setopt($user_request, CURLOPT_URL,"https://api.groupme.com/v3/users/me");
                curl_setopt($user_request, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($user_request, CURLOPT_HTTPHEADER, $headers);
                $user = curl_exec ($user_request);
                $user = json_decode($user);
                $user = $user->response;

            if ($manualdb->has('users', ['id' => $user->user_id, 'login_system' => 'groupme'])) {
                $manualdb->update('users', [
                    'session_id' => $userToken,
                    'expire_timestamp' => time() + 86400,
                    'user' => $user->name,
                    'access_level' => $groupmeAccessLevel,
                    'avatar' => $user->image_url,
                ], [
                    'id' => $user->user_id,
                    'login_system' => 'groupme'
                ]);
            } else {
                $manualdb->insert('users', [
                    'session_id' => $userToken,
                    'id' => $user->user_id,
                    'user' => $user->name,
                    'access_level' => $groupmeAccessLevel,
                    'avatar' => $user->image_url,
                    'access_level' => null,
                    'expire_timestamp' => time() + 86400,
                    'login_system' => 'groupme'
                ]);
            }
            setcookie("LoginCookie", $userToken, time() + 86400);
            setcookie("LoginEngine", 'groupme', time() + 86400);
            header("Location: .?login=true");
            die();
        }
    }
    if ($_GET['callback'] == 'patreon') {
        if ($_GET['state'] != $_SESSION['token']) {
            header("Location: ./login?action=login&error=failed-token");
            die();
        }
        $token_request = 'https://www.patreon.com/api/oauth2/token';
        $token = curl_init();
        curl_setopt_array($token, [
            CURLOPT_URL => $token_request,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => [
                'grant_type' => 'authorization_code',
                'client_id' => $patreonClientId,
                'client_secret' => $patreonClientSecret,
                'redirect_uri' => $patreonCallbackUri,
                'code' => $_GET['code']
            ]
        ]);
        curl_setopt($token, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($token));
        curl_close($token);

        $identity = patreon_call($response->access_token, '/api/oauth2/v2/identity?include=memberships,campaign&fields%5Buser%5D=about,created,email,first_name,full_name,image_url,last_name,social_connections,thumb_url,url,vanity');

        $identity = json_decode($identity, true);
        $linked_discord = (!empty($identity['data']['attributes']['social_connections']['discord'])) ? $identity['data']['attributes']['social_connections']['discord']['user_id'] : null;
        if ($linked_discord && $manualdb->has('users', ['id' => $linked_discord, 'login_system' => 'discord'])) {
            $manualdb->update('users', [
                'session_token' => null,
                'session_id' => null,
                'expire_timestamp' => time() - 86400,
                'linked_account' => $identity['data']['relationships']['memberships']['data']['0']['id'],
                'last_loggedin' => time()
            ], [
                'id' => $linked_discord,
                'login_system' => 'discord'
            ]);
        }

        $accessLevel = checkAccessLevelPatreon($response->access_token, $identity['data']['relationships']['memberships']['data']['0']['id']);
        if (empty($accessLevel) && $patreonTierRequired) {
            header("Location: ./login?action=login&error=no-member-patreon");
            die();
        }
        if ($manualdb->has('users', ['id' => $identity['data']['relationships']['memberships']['data']['0']['id'], 'login_system' => 'patreon'])) {
            $manualdb->update('users', [
                'session_token' => $_SESSION['token'],
                'session_id' => $response->access_token,
                'expire_timestamp' => time() + $response->expires_in,
                'user' => strval($identity['data']['attributes']['full_name']),
                'access_level' => $accessLevel,
                'avatar' => $identity['data']['attributes']['image_url'],
                'linked_account' => $linked_discord,
                'last_loggedin' => time()
            ], [
                'id' => $identity['data']['relationships']['memberships']['data']['0']['id'],
                'login_system' => 'patreon'
            ]);
        } else {
            $manualdb->insert('users', [
                'session_token' => $_SESSION['token'],
                'session_id' => $response->access_token,
                'id' => $identity['data']['relationships']['memberships']['data']['0']['id'],
                'user' => strval($identity['data']['attributes']['full_name']),
                'access_level' => $accessLevel,
                'avatar' => $identity['data']['attributes']['image_url'],
                'expire_timestamp' => time() + $response->expires_in,
                'login_system' => 'patreon',
                'linked_account' => $linked_discord,
                'last_loggedin' => time()
            ]);
        }
        setcookie("LoginCookie", $response->access_token, time() + $response->expires_in);
        setcookie("LoginEngine", 'patreon', time() + $response->expires_in);
        if ($useLoginCookie) {
            setrawcookie("LoginSession", $_SESSION['token'], time() + $sessionLifetime);
        }
        header("Location: .?login=true");
        die();
    }
}
if (!empty($_POST['refresh'])) {
    header('Content-Type: application/json');
    $answer = array();
    if ($_POST['refresh'] == 'discord') {
        $dbUser = $manualdb->get('users', ['id','session_id', 'access_level', 'discord_guilds'],['id' => $_SESSION['user']->id]);
        if (empty($dbUser)) {
            $answer['action'] = 'false';
        } else {
            $accessLevel = checkAccessLevelDiscord($dbUser['id'], json_decode($dbUser['discord_guilds']));
            if ($accessLevel == $dbUser['access_level']) {
                $answer['action'] = 'true';
            } elseif (!empty($accessLevel)) {
                $manualdb->update('users', [
                    'access_level' => $accessLevel,
                    'last_loggedin' => time()
                ], [
                    'id' => $dbUser['id']
                ]);
                $answer['action'] = 'reload';
            } else {
                $manualdb->update('users', [
                    'access_level' => null,
                    'last_loggedin' => time()
                ], [
                    'id' => $dbUser['id']
                ]);
                $answer['action'] = 'reload';
            }
        }
    }
    if ($_POST['refresh'] == 'native') {
        $dbUser = $manualdb->get('users', ['id','session_id', 'access_level'],['id' => $_SESSION['user']->id]);
        if ($_SESSION['user']->access_level != $dbUser['access_level']) {
            $answer['action'] = 'reload';
        }
    }
    if ($_POST['refresh'] == 'patreon') {
        $dbUser = $manualdb->get('users', ['id','session_id', 'access_level'],['id' => $_SESSION['user']->id]);
        if (empty($dbUser)) {
            $answer['action'] = 'false';
        } else {
            $accessLevel = checkAccessLevelPatreon($dbUser['session_id'], $dbUser['id']);
            if ($accessLevel == $dbUser['access_level']) {
                $answer['action'] = 'true';
            } elseif (!empty($accessLevel)) {
                $manualdb->update('users', [
                    'access_level' => $accessLevel
                ], [
                    'id' => $dbUser['id']
                ]);
                $answer['action'] = 'reload';
            } else {
                $manualdb->update('users', [
                    'access_level' => null
                ], [
                    'id' => $dbUser['id']
                ]);
                $answer['action'] = 'reload';
            }
        }
    }
    $json = json_encode($answer);
    echo $json;
    die();
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
    $response = curl_exec($info_request);
    if (curl_getinfo($info_request, CURLINFO_HTTP_CODE) != 200) {
        header("Location: ./login?action=login&error=bad-response-dc&error-message=request");
        die();
    } else {
        curl_close($info_request);
        return json_decode($response);
    }
}

function logFailure($logFailure) {
    global $logFailedLogin;
    file_put_contents($logFailedLogin, $logFailure, FILE_APPEND);
}
function checkAccessLevelDiscord ($userId, $guilds) {
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
            if ($guildRoles[$guild]) {
                if ($accessRole < strval($guildRoles[$guild])) {
                    $accessRole = strval($guildRoles[$guild]);
                }
            }
        }
    }
    return $accessRole;
}
function checkAccessLevelPatreon ($accessToken, $userId) {
    global $patreonTiers;
    $member = patreon_call($accessToken, '/api/oauth2/v2/members/' . $userId . '?include=currently_entitled_tiers,user&fields%5Bmember%5D=full_name,patron_status&fields%5Btier%5D=title&fields%5Buser%5D=full_name,hide_pledges');

    $member = json_decode($member, true);
    $accessLevel = null;
    if ($member['data']['attributes']['patron_status'] == 'active_patron') {
        if (!empty($member['data']['relationships']['currently_entitled_tiers']['data'])) {
            $accessLevel = $patreonTiers[$member['data']['relationships']['currently_entitled_tiers']['data'][0]['id']];
        }
    }
    return $accessLevel;
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

function patreon_call($bearer, $api) {
    $ch = curl_init('https://www.patreon.com' . $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $bearer
    ));

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
header("Location: ./");
die();
