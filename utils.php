<?php

$localeData = null;
$checkIndexCache = array();

function i8ln($word)
{
    global $locale;
    $locale = !empty($_COOKIE["LocaleCookie"]) ? $_COOKIE["LocaleCookie"] : $locale;
    if ($locale == "en") {
        return $word;
    }

    global $localeData;
    if ($localeData == null) {
        $filepath = 'static/dist/locales/' . $locale . '.min.json';
        if (file_exists($filepath)) {
            $json_contents = file_get_contents($filepath);
            $localeData = json_decode($json_contents, true);
        } else {
            return $word;
        }
    }

    if (isset($localeData[$word])) {
        return $localeData[$word];
    } else {
        return $word;
    }
}

function setSessionCsrfToken()
{
    if (empty($_SESSION['token'])) {
        generateToken();
    }
}

function refreshCsrfToken()
{
    global $sessionLifetime, $manualdb;
    if (time() - $_SESSION['c'] > $sessionLifetime) {
        session_regenerate_id(true);
        generateToken();
        if (!empty($_SESSION['user']->id)) {
            $manualdb->update('users', ['session_token' => $_SESSION['token']], ['id' => $_SESSION['user']->id]);
        }
    }
    return $_SESSION['token'];
}

function generateToken()
{
    $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
    $_SESSION['c'] = time();
}

function validateToken($token)
{
    global $enableCsrf, $manualdb, $allowMultiLogin, $forcedLogin, $useLoginCookie, $sessionLifetime;
    if ((!$enableCsrf) || ($enableCsrf && isset($token) && $token === $_SESSION['token'])) {
        $validity = 'valid';
        if (!empty($_SESSION['user']->id)) {
            $user = $manualdb->get('users', ['session_token'], ['id' => $_SESSION['user']->id]);
            if ($user['session_token'] == $_SESSION['token'] || $allowMultiLogin) {
                $validity = 'valid';
            } elseif ($useLoginCookie && $_COOKIE['LoginSession'] == $user['session_token']) {
                $manualdb->update('users', ['session_token' => $_SESSION['token']], ['session_id' => $_COOKIE['LoginCookie']]);
                setrawcookie("LoginSession", $_SESSION['token'], time() + $sessionLifetime);
                $validity = 'valid';
            } else {
                $validity = 'invalid';
                destroyCookiesAndSessions();
           }
        } elseif ($forcedLogin) {
            $validity = 'no-id';
        }
        return $validity;
    } else {
        destroyCookiesAndSessions();
        return 'invalid';
    }
}


function sendToWebhook($webhookUrl, $webhook)
{
    if (is_array($webhookUrl)) {
        foreach ($webhookUrl as $hook) {
            $c = curl_init($hook);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($c, CURLOPT_HTTPHEADER, ['Content-type: application/json', 'User-Agent: python-requests/2.18.4']);
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($webhook));
            curl_exec($c);
            curl_close($c);
        }
    } else {
        $c = curl_init($webhookUrl);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, ['Content-type: application/json', 'User-Agent: python-requests/2.18.4']);
        curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($webhook));
        curl_exec($c);
        curl_close($c);
    }
}

function uploadImage($imgurCID, $data)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, 'https://api.imgur.com/3/image');
    curl_setopt($c, CURLOPT_POST, true);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_HTTPHEADER, ["Authorization: Client-ID $imgurCID"]);
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($c);
    curl_close($c);

    return $result;
}

function deleteImage($imgurCID, $data)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, 'https://api.imgur.com/3/image/' . $data);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_HTTPHEADER, ["Authorization: Client-ID $imgurCID"]);
    curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');
    $result = curl_exec($c);
    curl_close($c);

    return $result;
}

function generateRandomString($length = 12)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function createUserAccount($user, $password, $newExpireTimestamp)
{
    global $manualdb, $discordUrl, $domainName, $title;

    $count = $manualdb->has("users", [
        "user" => $user,
        "login_system" => 'native'
    ]);

    if (!$count) {
        $getId = $manualdb->query("SELECT TRIM(LEADING '0' FROM MAX(LPAD(`id`, 9, '0'))) FROM `users` WHERE `login_system` = 'native'")->fetchAll();
        $getId = intval($getId[0][0]);

        if (is_int($getId)) {
            $getId++;

            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
            $manualdb->insert("users", [
                "id" => $getId,
                "user" => $user,
                "password" => $hashedPwd,
                "expire_timestamp" => $newExpireTimestamp,
                "login_system" => 'native',
                "access_level" => null
            ]);

            return true;
            $subject = "[{$title}] - " . i8ln('Welcome') . "";
            $message .= i8ln('Dear') . " {$user},<br><br>";
            $message .= i8ln('Your account has been created') . "<br>";
            if ($discordUrl) {
                $message .= i8ln('For support, ask your questions in the ') . "<a href='{$discordUrl}'>" . i8ln('discord guild') . "</a>!<br><br>";
            }
            $message .= i8ln('Best Regards') . "<br>" . i8ln('Admin');
            if ($title) {
                $message .= " @ {$title}";
            }
            !empty($domainName) ? $domainName = $domainName : $domainName = $_SERVER['SERVER_NAME'];
            $headers = "From: no-reply@{$domainName}" . "\r\n" .
                "Reply-To: no-reply@{$domainName}" . "\r\n" .
                'Content-Type: text/html; charset=utf-8' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $sendMail = mail($user, $subject, $message, $headers);

            if (!$sendMail) {
                http_response_code(500);
                die("<h1>Warning</h1><p>The email has not been sent.<br>If you're a user, please contact your administrator.<br>If you're an administrator, install <i><b>apt-get install sendmail</b></i>, restart your web server and try again.</p><p><a href='.'><i class='fas fa-backward'></i> Back to Map</a> - <a href='./register?action=account'>Retry</a></p>");
                return 'success';
            }
        } else {
            return 'error_id';
        }
    } else {
        return 'duplicate';
    }
}

function resetUserPassword($user, $password, $resetType)
{
    global $manualdb, $domainName, $discordUrl, $title;

    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
    if ($resetType === 0) {
        $manualdb->update("users", [
            "temp_password" => $hashedPwd
        ], [
            "user" => $user,
            "login_system" => 'native'
        ]);
        $subject = "[{$title}] - Password Reset";
        $message .= i8ln('Dear') . " {$user},<br><br>";
        $message .= i8ln('Your password has been reset') . "<br>";
        $message .= i8ln('If you haven\'t requested a new password you can ignore this email.') . "<br>";
        $message .= i8ln('Your old password is still working.') . "<br><br>";
        $message .= i8ln('New password: ') . " {$password}<br><br>";
        if ($discordUrl) {
            $message .= i8ln('For support, ask your questions in the ') . "<a href='{$discordUrl}'>" . i8ln('discord guild') . "</a>!<br><br>";
        }
        $message .= i8ln('Best Regards') . "<br>" . i8ln('Admin');
        if ($title) {
            $message .= " @ {$title}";
        }
        !empty($domainName) ? $domainName = $domainName : $domainName = $_SERVER['SERVER_NAME'];
        $headers = "From: no-reply@{$domainName}" . "\r\n" .
            "Reply-To: no-reply@{$domainName}" . "\r\n" .
            'Content-Type: text/html; charset=utf-8' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $sendMail = mail($user, $subject, $message, $headers);

        if (!$sendMail) {
            http_response_code(500);
            die("<h1>Warning</h1><p>The email has not been sent.<br>If you're an user please contact your administrator.<br>If you're an administrator install <i><b>apt-get install sendmail</b></i> and restart your web server and try again.</p><p><a href='.'><i class='fas fa-backward'></i> Back to Map</a> - <a href='./register?action=password-reset'>Retry</a></p>");
        }
    } elseif ($resetType === 1) {
        $manualdb->update("users", [
            "password" => null,
            "temp_password" => $hashedPwd
        ], [
            "user" => $user,
            "login_system" => 'native'
        ]);
    } else {
        $manualdb->update("users", [
            "password" => $hashedPwd,
            "temp_password" => null
        ], [
            "user" => $user,
            "login_system" => 'native'
        ]);
    }

    return true;
}

function destroyCookiesAndSessions()
{
    global $manualdb;

    if (!empty($_SESSION['user']->id)) {
        $manualdb->update("users", [
            "session_id" => null,
            "avatar" => null,
            "discord_guilds" => null
        ], [
            "id" => $_SESSION['user']->id,
            "login_system" => $_SESSION['user']->login_system
        ]);

        unset($_SESSION);
    }
    unset($_COOKIE['LoginCookie']);
    unset($_COOKIE['LoginEngine']);
    unset($_COOKIE['LoginSession']);
    setcookie("LoginCookie", "", time() - 3600);
    setcookie("LoginEngine", "", time() - 3600);
    setcookie("LoginSession", "", time() - 3600);
    session_destroy();
    session_write_close();
}

function validateCookie($cookie)
{
    global $manualdb, $manualAccessLevel, $sessionLifetime, $useLoginCookie;

    $info = $manualdb->query(
        "SELECT id, user, password, login_system, expire_timestamp, access_level, avatar, session_token FROM users WHERE session_id = :session_id", [
            ":session_id" => $cookie
        ]
    )->fetch();
    if (!empty($info['user'])) {
        if ($useLoginCookie && $info['session_token'] == $_COOKIE['LoginSession']) {
            $manualdb->update('users', ['session_token' => $_SESSION['token']], ['id' => $info['id']]);
        }
        $_SESSION['user'] = new \stdClass();
        $_SESSION['user']->id = $info['id'];
        $_SESSION['user']->user = htmlspecialchars($info['user'], ENT_QUOTES, 'UTF-8');
        $_SESSION['user']->avatar = !empty($info['avatar']) ? $info['avatar'] : 'static/images/avatar.png';
        $_SESSION['user']->login_system = $info['login_system'];
        $_SESSION['user']->expire_timestamp = $info['expire_timestamp'];
        $_SESSION['user']->access_level = $info['access_level'];
        if (empty($info['password']) && $info['login_system'] == 'native') {
            $_SESSION['user']->updatePwd = 1;
        }
        setcookie("LoginCookie", $cookie, time() + $sessionLifetime);
        setcookie("LoginEngine", $info['login_system'], time() + $sessionLifetime);
        setcookie("LoginSession", $_SESSION['token'], time() + $sessionLifetime);
        if (!isset($_SESSION['already_refreshed'])) {
            $_SESSION['already_refreshed'] = true;
            return false;
        } else {
            return true;
        }
    } else {
        destroyCookiesAndSessions();
        return false;
    }
}

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
function randomGymId()
{
    $alphabet    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass        = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 12; $i ++) {
        $n      = rand(0, $alphaLength);
        $pass[] = $alphabet[ $n ];
    }
    return implode($pass); //turn the array into a string
}
function randomNum()
{
    $alphabet    = '1234567890';
    $pass        = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 15; $i ++) {
        $n      = rand(0, $alphaLength);
        $pass[] = $alphabet[ $n ];
    }
    return implode($pass); //turn the array into a string
}

function getIcon($iconRepo, $folder, $fileType, $iconKeyId, ...$varArgs) {
    if (is_array($iconRepo)) {
        $iconRepo = current($iconRepo);
    }
    $availableArray = checkIndex($iconRepo, $folder);
    $icon = '0.png';
    switch ($folder) {
        case 'gym/':
            /* varArgs trainer_count battle ex */
            $teamId = $iconKeyId;
            $trainerCount = isset($varArgs[0]) ? ["_t{$varArgs[0]}", ''] : [''];
            $battle = isset($varArgs[1]) ? ['_b', ''] : [''];
            $ex = isset($varArgs[2]) ? ['_ex', ''] : [''];
            $requestedIcon = $teamId . (isset($trainerCount[0]) ? $trainerCount[0] : '') . (isset($battle[0]) ? $battle[0] : '') . (isset($ex[0]) ? $ex[0] : '');
            if (array_search($requestedIcon, $availableArray) !== false) {
                $icon = $availableArray[array_search($requestedIcon, $availableArray)];
            } else {
                foreach ($trainerCount as $trainer) {
                    foreach ($battle as $b) {
                        foreach ($ex as $e) {
                            $searchIcon = $teamId . $trainer . $b . $e . $fileType;
                            if (array_search($searchIcon, $availableArray) !== false) {
                                $icon = $searchIcon;
                                break 3;
                            }
                        }
                    }
                }
            }
            break;
        case 'invasion/':
            /* No varArgs */
            $gruntId = $iconKeyId;
            if (array_search($gruntId . $fileType, $availableArray) !== false) {
                $icon = $gruntId . $fileType;
            }
            break;
        case 'misc/':
            /* No varArgs */
            $icon = '0.png';
            break;
        case 'pokemon/':
            /* varArgs order = evolution, form, costume, gender, shiny */
            $pokemonId = $iconKeyId;
            $evolutionId = isset($varArgs[0]) ? ["_e{$varArgs[0]}", ''] : [''];
            $formId = isset($varArgs[1]) ? ["_f{$varArgs[1]}", ''] : [''];
            $costumeId = isset($varArgs[2]) ? ["_c{$varArgs[2]}", ''] : [''];
            $genderId = isset($varArgs[3]) ? ["_g{$varArgs[3]}", ''] : [''];
            $shinyId = isset($varArgs[4]) ? ["_s", ''] : [''];
            $requestedIcon = $pokemonId . (isset($evolutionId[0]) ? $evolutionId[0] : '') . (isset($formId[0]) ? $formId[0] : '') . (isset($costumeId[0]) ? $costumeId[0] : '') . (isset($genderId[0]) ? $genderId[0] : '') . (isset($shinyId[0]) ? $shinyId[0] : '') . $fileType;
            if (array_search($requestedIcon, $availableArray) !== false) {
                $icon = $requestedIcon;
            } else {
                /* dont care about costume, gender, shiny if requestedIcon is not available for now*/
                foreach ($evolutionId as $evolution) {
                    foreach ($formId as $form) {
                        $searchIcon = $pokemonId . $evolution . $form . $fileType;
                        if (array_search($searchIcon, $availableArray) !== false) {
                            $icon = $searchIcon;
                            break 2;
                        }
                    }
                }
            }
            break;
        case 'pokestop/':
            /* varArgs invasion quest */
            $lureId = $iconKeyId;
            $invasion = isset($varArgs[0]) ? '_i' : '';
            $quest = isset($varArgs[1]) ? '_q' : '';
            if (array_search($lureId . $invasion . $quest . $fileType, $availableArray) !== false) {
                $icon = $lureId . $invasion . $quest . $fileType;
            } elseif (array_search($lureId . $invasion . $fileType, $availableArray) !== false) {
                $icon = $lureId . $invasion . $fileType;
            } elseif (array_search($lureId . $quest . $fileType, $availableArray) !== false) {
                $icon = $lureId . $quest . $fileType;
            } elseif (array_search($lureId . $fileType, $availableArray) !== false) {
                $icon = $lureId . $fileType;
            }
            break;
        case 'raid/':
            /* varArgs hatched ex */
            $eggLevel = $iconKeyId;
            $hatched = isset($varArgs[0]) ? ($varArgs[0] === true ? ["_h", ''] : ['']) : [''];
            $ex = isset($varArgs[1]) ? ["_ex{$varArgs[0]}", ''] : [''];
            $requestedIcon = $eggLevel . (isset($hatched[0]) ? $hatched[0] : '') . (isset($ex[0]) ? $ex[0] : '') . $fileType;
            if (array_search($requestedIcon, $availableArray) !== false) {
                $icon = $requestedIcon;
            } else {
                foreach ($hatched as $hatch) {
                    foreach ($ex as $e) {
                        $searchIcon = $eggLevel . $hatch . $e . $fileType;
                        if (array_search($searchIcon, $availableArray) !== false) {
                            $icon = $searchIcon;
                            break 2;
                        }
                    }
                }
            }
            break;
        case 'reward/item/':
            /* varArgs amount */
            $itemId = $iconKeyId;
            $amount = isset($varArgs[0]) ? "_a{$varArgs[0]}" : '';
            if (array_search($itemId . $amount . $fileType, $availableArray) !== false) {
                $icon = $itemId . $amount . $fileType;
            } elseif (array_search($itemId . $fileType, $availableArray) !== false) {
                $icon = $itemId . $fileType;
            }
            break;
        case 'reward/mega_resource/':
            /* No varArgs */
            $pokemonId = $iconKeyId;
            if (array_search($pokemonId . $fileType, $availableArray) !== false) {
                $icon = $pokemonId . $fileType;
            }
            break;
        case 'reward/stardust/':
            /* No varArgs*/
            $amount = $iconKeyId;
            if (array_search($amount . $fileType, $availableArray) !== false) {
                $icon = $amount . $fileType;
            }
            break;
        case 'team/':
            /* No varArgs*/
            $teamId = $iconKeyId;
            if (array_search($teamId . $fileType, $availableArray) !== false) {
                $icon = $teamId . $fileType;
            }
            break;
        case 'type/':
            /* No varArgs*/
            $typeId = $iconKeyId;
            if (array_search($typeId . $fileType, $availableArray) !== false) {
                $icon = $typeId . $fileType;
            }
            break;
        case 'weather/':
            /* varArgs severity*/
            $weatherId = $iconKeyId;
            $severityLevel = isset($varArgs[0]) ? "_l{$varArgs[0]}" : '';
            if (array_search($weatherId . $severityLevel . $fileType, $availableArray) !== false) {
                $icon = $weatherId . $severityLevel . $fileType;
            } elseif (array_search($weatherId . $fileType, $availableArray) !== false) {
                $icon = $weatherId . $fileType;
            }
            break;
    }
    return $iconRepo . $folder . $icon;
}

function checkIndex($repo, $subfolder = '') {
    if (!is_dir('.cache')) {
        mkdir('.cache', 0744);
    }
    $indexFile = '.cache/' . preg_replace('~[/:]~', '_', $repo) . preg_replace('~[/:]~', '_', $subfolder) . 'index.json';
    if (isset($checkIndexCache[$indexFile])) {
        $index = $checkIndexCache[$indexFile];
    } elseif (file_exists($indexFile) && (filemtime($indexFile) > (time() - 60 * 120))) {
        $index = file_get_contents($indexFile);
        $checkIndexCache[$indexFile] = $index;
    } else {
        $index = file_get_contents($repo . $subfolder . 'index.json');
        file_put_contents($indexFile, $index);
        $checkIndexCache[$indexFile] = $index;
    }
    return json_decode($index);
}
