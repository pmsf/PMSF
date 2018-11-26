<?php
include('config/config.php');

$json = json_decode(file_get_contents('php://input'), true);

$id = !empty($json["id"]) ? $json["id"] : null;
$product_id = !empty($json["product_id"]) ? $json["product_id"] : null;
$email = !empty($json["email"]) ? $json["email"] : null;
$value = !empty($json["value"]) ? $json["value"] : null;
$quantity = !empty($json["quantity"]) ? $json["quantity"] : null;
$timestamp = time();

if (($noNativeLogin === false || $noDiscordLogin === false) && !empty($id) && !empty($product_id) && !empty($email) && !empty($value) && !empty($quantity)) {
    $signatureFromHeader = getallheaders();
    $signature = hash_hmac('sha512', file_get_contents('php://input'), $sellyWebhookSecret);

    if (hash_equals($signature, $signatureFromHeader['X-Selly-Signature'])) {
        $manualdb->insert("payments", [
            "selly_id" => $id,
            "product_id" => $product_id,
            "email" => $email,
            "value" => $value,
            "quantity" => $quantity,
            "timestamp" => $timestamp
        ]);

        $logMsg = "INSERT INTO payments (selly_id, product_id, email, value, quantity, timestamp) VALUES ('{$id}', '{$product_id}', '{$email}, '{$value}, '{$quantity}, '{$timestamp}');\r\n";
        file_put_contents($logfile, $logMsg, FILE_APPEND);
    }
} else {
    header("Location: .");
}
