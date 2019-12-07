<?php

$localeData = null;

function i8ln($word)
{
    global $locale;
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
    global $sessionLifetime;
    if (time() - $_SESSION['c'] > $sessionLifetime) {
        session_regenerate_id(true);
        generateToken();
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
    global $enableCsrf;
    if ((!$enableCsrf) || ($enableCsrf && isset($token) && $token === $_SESSION['token'])) {
        return true;
    } else {
        return false;
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

function generateRandomString($length = 8)
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
    global $manualdb, $logfile;

    $count = $manualdb->count("users", [
        "user" => $user,
        "login_system" => 'native'
    ]);

    if ($count === 0) {
        $getId = $manualdb->count("users", [
            "login_system" => 'native'
        ]);

        if (is_int($getId)) {
            $getId++;

            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
            $manualdb->insert("users", [
                "id" => $getId,
                "user" => $user,
                "temp_password" => $hashedPwd,
                "expire_timestamp" => $newExpireTimestamp,
                "login_system" => 'native',
                "access_level" => '0'
            ]);
            
            $logMsg = "INSERT INTO users (id, user, expire_timestamp, login_system) VALUES ('{$getId}', '{$user}', '{$newExpireTimestamp}', 'native'); -- " . date('Y-m-d H:i:s') . "\r\n";
            file_put_contents($logfile, $logMsg, FILE_APPEND);

            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function resetUserPassword($user, $password, $resetType)
{
    global $manualdb, $logfile;
    
    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
    if ($resetType === 0) {
        $manualdb->update("users", [
            "temp_password" => $hashedPwd
        ], [
            "user" => $user,
            "login_system" => 'native'
        ]);
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

function updateExpireTimestamp($user, $login_system, $newExpireTimestamp)
{
    global $manualdb, $logfile;

    $manualdb->update("users", [
        "expire_timestamp" => $newExpireTimestamp
    ], [
        "user" => $user,
        "login_system" => $login_system
    ]);

    $logMsg = "UPDATE users SET expire_timestamp = '{$newExpireTimestamp}' WHERE user = '{$user}' AND login_system = '{$login_system}'; -- " . date('Y-m-d H:i:s') . "\r\n";
    file_put_contents($logfile, $logMsg, FILE_APPEND);

    return true;
}

function destroyCookiesAndSessions()
{
    global $manualdb;
    
    $manualdb->update("users", [
        "session_id" => null
    ], [
        "id" => $_SESSION['user']->id,
        "login_system" => $_SESSION['user']->login_system
    ]);

    unset($_SESSION);
    unset($_COOKIE['LoginCookie']);
    setcookie("LoginCookie", "", time() - 3600);
    session_destroy();
    session_write_close();
}

function validateCookie($cookie)
{
    global $manualdb;
    $info = $manualdb->query(
        "SELECT id, user, password, login_system, expire_timestamp, access_level FROM users WHERE session_id = :session_id", [
            ":session_id" => $cookie
        ]
    )->fetch();

    if (!empty($info['user'])) {
        $_SESSION['user'] = new \stdClass();
        $_SESSION['user']->id = $info['id'];
        $_SESSION['user']->user = $info['user'];
        $_SESSION['user']->login_system = $info['login_system'];
        $_SESSION['user']->expire_timestamp = $info['expire_timestamp'];
        $_SESSION['user']->access_level = $info['access_level'];
        
        if (empty($info['password']) && $info['login_system'] == 'native') {
            $_SESSION['user']->updatePwd = 1;
        }
        setcookie("LoginCookie", $cookie, time() + 60 * 60 * 24 * 7);
        if (!isset($_SESSION['already_refreshed'])) {
            $_SESSION['already_refreshed'] = true;
            return false;
        } else {
            return true;
        }
    } elseif (!empty($_SESSION['user']->id)) {
        destroyCookiesAndSessions();
        return false;
    } else {
        unset($_COOKIE['LoginCookie']);
        setcookie("LoginCookie", "", time() - 3600);
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
