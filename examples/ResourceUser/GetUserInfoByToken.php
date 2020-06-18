<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookResourceUser;

$access_token = 'youraccesstoken';

$Group = new FacebookResourceUser();
$results = $Group->GetUserInfoByToken($access_token);

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(3) {
  ["id"]=>
  string(15) "100016865703374"
  ["username"]=>
  string(8) "Riedayme"
  ["photo"]=>
  string(289) "https://scontent.fcgk18-1.fna.fbcdn.net/v/t1.0-1/cp0/p50x50/65307879_471865026719017_5286366118670237696_o.jpg?_nc_cat=108&_nc_sid=dbb9e7&_nc_oc=AQkA1tPKTEJv0_g_qwuLt2-8CYg6KoEw8XzBH3TxvTIYT4M6IXpRX_ejowVsHnZVx8M&_nc_ht=scontent.fcgk18-1.fna&oh=c36377221c3dde5c3e3edfce982af731&oe=5F07F816"
}
*/