<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFriendsConfirmRequest;

$cookie = 'yourcookie';
$url = "urlconfirm";

$SendRequest = new FacebookFriendsConfirmRequest();
$SendRequest->SetCookie($cookie);

$process = $SendRequest->Process($url);

echo "<pre>";
var_dump($process);
echo "</pre>";