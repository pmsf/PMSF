<?php
include('config/config.php');
if ($enableLogin === true) {

    $json = json_decode(file_get_contents('php://input'), true);

    $id = !empty($json["id"]) ? $json["id"] : null;
    $product_id = !empty($json["product_id"]) ? $json["product_id"] : null;
    $email = !empty($json["email"]) ? $json["email"] : null;
    $value = !empty($json["value"]) ? $json["value"] : null;
    $quantity = !empty($json["quantity"]) ? $json["quantity"] : null;

    $timestamp = time();

    if (!empty($id) && !empty($product_id) && !empty($email) && !empty($value) && !empty($quantity)) {
        $db->insert("payments", [
            "selly_id" => $id,
            "product_id" => $product_id,
            "email" => $email,
            "value" => $value,
            "quantity" => $quantity,
            "timestamp" => $timestamp
        ]);

        $logMsg = "INSERT INTO payments (selly_id, product_id, email, value, quantity, timestamp) VALUES ('{$id}', '{$product_id}', '{$email}, '{$value}, '{$quantity}, '{$timestamp}');\r\n";
        file_put_contents($logfile, $logMsg, FILE_APPEND);

        $info = $db->query(
            "SELECT email, expire_timestamp FROM users WHERE email = :email", [
                ":email" => $email
            ]
        )->fetch();
        

        $addSeconds = 60 * 60 * 24 * 31 * $quantity;
        
        $message = i8ln('Dear') . " {$email},<br><br>";
        $message .= i8ln('Thank you for your purchase') . "<br>";

        if ($info['email']) {

            if ($info['expire_timestamp'] > time()) {
                $new_expire_timestamp = $info['expire_timestamp'] + $addSeconds;
            } else {
                $new_expire_timestamp = time() + $addSeconds;
            }
            $time = date("Y-m-d H:i", $new_expire_timestamp);
            updateExpireTimestamp($info['email'], $new_expire_timestamp);
            
            $message .= i8ln('Your new expire date is set to') . " {$time}.<br><br>";

        } else {

            $randomPwd = generateRandomString();
            $new_expire_timestamp = time() + $addSeconds;

            createUserAccount($email, $randomPwd, $new_expire_timestamp);

            $time = date("Y-m-d H:i", $new_expire_timestamp);
            
            $message .= i8ln('Your expire date is set to') . " {$time}.<br><br>";
            $message .= "<b>" . i8ln('Credentials') . ":</b><br>
*********************************************************<br>
<b>" . i8ln('Email') . ":</b> {$email}<br>
<b>" . i8ln('Password') . ":</b> {$randomPwd}<br>
*********************************************************<br><br>";
        }

        if ($discordUrl) {
            $message .= i8ln('For support, ask your questions in the ') . "<a href='{$discordUrl}'>discord guild</a>!<br><br>";
        }
        $message .= i8ln('Best Regards') . "<br>Admin";
        if ($title) {
            $message .= " @ {$title}";
        }
        
        $subject = "[{$title}] - Membership";
        $headers = "From: no-reply@{$_SERVER['SERVER_NAME']}" . "\r\n" .
            "Reply-To: no-reply@{$_SERVER['SERVER_NAME']}" . "\r\n" .
            'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($email, $subject, $message, $headers);
    } else {
        header("Location: .");
    }
} else {
    header("Location: .");
}
