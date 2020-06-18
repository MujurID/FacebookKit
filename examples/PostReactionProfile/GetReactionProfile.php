<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookPostReactionProfile;

$cookie = 'yourcookie';

$ReactionProfile = new FacebookPostReactionProfile();
$ReactionProfile->SetCookie($cookie);

$ReactionProfile->GetReactionProfile([
  'postid' => 'postid',
	'limit' => 100
	]);

$results = $ReactionProfile->results();

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(1) {
  [0]=>
  array(4) {
    ["userlink"]=>
    string(16) "/kharis.azhar.13"
    ["username"]=>
    string(12) "Kharis Azhar"
    ["photo"]=>
    string(317) "https://scontent-sin6-1.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/41727106_125205175102105_7203375920281812992_o.jpg?_nc_cat=111&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_oc=AQlYElJ_7zAg8WKgSc1f6yWIgjgy-0_fNcOgCooBcNX2DgIjJ_bRU-gRxQoB8S2mn18&_nc_ht=scontent-sin6-1.xx&_nc_tp=3&oh=4ae00a6d2e366ac039d1aa03e6cb54b5&oe=5F1122C3"
    ["linksendrequest"]=>
    string(224) "/a/mobile/friends/add_friend.php?id=100028378703738&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D3182336698490546&fref=pb_likes&gfid=AQCak-ViWkxJkTV2"
  }
}
*/