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

function createUserAccount($email, $password, $new_expire_timestamp)
{
    global $db;

    $count = $db->count("users",[
        "email" => $email
    ]);

    if ($count == 0) {
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        $db->insert("users", [
            "email" => $email,
            "temp_password" => $hashedPwd,
            "expire_timestamp" => $new_expire_timestamp
        ]);
        
        $logMsg = "INSERT INTO users (email, temp_password, expire_timestamp) VALUES ('{$email}', '{$hashedPwd}', '{$new_expire_timestamp}');\r\n";
        file_put_contents($logfile, $logMsg, FILE_APPEND);

        return true;
    } else {
        return false;
    }
}

function resetUserPassword($email, $password, $resetType)
{
    global $db;
    
    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
    if ($resetType == 0) {
        $db->update("users", [
            "temp_password" => $hashedPwd
        ], [
            "email" => $email
        ]);
        $logMsg = "UPDATE users SET temp_password = '{$hashedPwd}' WHERE email = '{$email}';\r\n";
    } elseif ($resetType == 1) {
        $db->update("users", [
            "password" => null,
            "temp_password" => $hashedPwd
        ], [
            "email" => $email
        ]);
        $logMsg = "UPDATE users SET password = null, temp_password = '{$hashedPwd}' WHERE email = '{$email}';\r\n";
    } else {
        $db->update("users", [
            "password" => $hashedPwd,
            "temp_password" => null
        ], [
            "email" => $email
        ]);
        $logMsg = "UPDATE users SET password = '{$hashedPwd}', temp_password = null WHERE email = '{$email}';\r\n";
    }

    file_put_contents($logfile, $logMsg, FILE_APPEND);

    return true;
}

function updateExpireTimestamp($email, $new_expire_timestamp)
{
    global $db;

    $db->update("users", [
        "expire_timestamp" => $new_expire_timestamp
    ], [
        "email" => $email
    ]);

    $logMsg = "UPDATE users SET expire_timestamp = '{$new_expire_timestamp}' WHERE email = '{$email}';\r\n";
    file_put_contents($logfile, $logMsg, FILE_APPEND);

    return true;
}
