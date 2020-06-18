<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookPostReaction;

$cookie = 'yourcookie';

$data = [ 
  'postid' => 'postid', 
  'type' => 'WOW' // LIKE, LOVE, CARE, HAHA, WOW, SAD, ANGRY, UNREACT
];

$Post = new FacebookPostReaction();
$Post->SetCookie($cookie);

$results =$Post->ReactPostByScraping($data);

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(3) {
  ["status"]=>
  bool(true)
  ["id"]=>
  string(15) "701158903789627"
  ["url"]=>
  string(62) "https://www.facebook.com/701158903789627"
}
*/