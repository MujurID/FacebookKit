<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookPostReaction;

$cookie = 'c_user=100016865703374;datr=uCrnXpxjTU48XdB24NCV3vF1;fr=1MEuLVsMfSuW5EQcC.AWX81kWjOv3OtappoZkqECEjcdw.Be5yq4.3_.F7n.0.0.Be6rei.AWXyWi9p;locale=id_ID;m_pixel_ratio=2;presence=EDvF3EtimeF1592440751EuserFA21B16865703374A2EstateFDutF1592440751090CEchF_7bCC;sb=uCrnXvr8auWLd40GMQax5SxG;spin=r.1002259764_b.trunk_t.1592440740_s.1_v.2_;wd=1600x761;x-referer=eyJyIjoiL2Jyb3dzZS9ncm91cC9tZW1iZXJzLz9pZD0zNjQ5OTc2MjcxNjU2OTcmc3RhcnQ9MCZsaXN0VHlwZT1saXN0X25vbmZyaWVuZF9ub25hZG1pbiIsImgiOiIvYnJvd3NlL2dyb3VwL21lbWJlcnMvP2lkPTM2NDk5NzYyNzE2NTY5NyZzdGFydD0wJmxpc3RUeXBlPWxpc3Rfbm9uZnJpZW5kX25vbmFkbWluIiwicyI6Im0ifQ%3D%3D;xs=28%3Af1Ore9WclPiXMg%3A2%3A1592440738%3A17482%3A10881;';

$data = [
  'postid' => '1016137795449486', 
  'type' => 'WOW' // LIKE, LOVE, CARE, HAHA, WOW, SAD, ANGRY, UNREACT
];

$Post = new FacebookPostReaction();
$Post->SetCookie($cookie);
$Post->SetFbDTSG();

$results =$Post->ReactPostByTouch($data);

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