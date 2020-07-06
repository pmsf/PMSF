<?php
require_once 'config/config.php';

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'discord-logout') {
        $revoke_request = 'https://discordapp.com/api/oauth2/token/revoke';
        $credentials = base64_encode("{$discordClientId}:{$discordClientSecret}");
	$header = array("Authorization: Basic {$credentials}", "Content-Type: application/x-www-form-urlencoded");
        $post = "access_token={$_COOKIE['LoginCookie']}";

        $revoke = curl_init();
        curl_setopt_array($revoke, array(
            CURLOPT_URL => $revoke_request,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post
        ));
        $response = json_decode(curl_exec($revoke));
        curl_close($revoke);

	file_put_contents('log.txt', print_r($response, true), FILE_APPEND);
	#file_put_contents('log.txt', curl_error($revoke), FILE_APPEND);
        header('Location: .');
        #die();
    }
}


destroyCookiesAndSessions();

header('Location: .');
die;
