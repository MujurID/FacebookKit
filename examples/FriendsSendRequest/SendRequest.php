<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFriendsSendRequest;

$cookie = 'sb=uCrnXvr8auWLd40GMQax5SxG; datr=uCrnXpxjTU48XdB24NCV3vF1; x-referer=eyJyIjoiL2Jyb3dzZS9ncm91cC9tZW1iZXJzLz9pZD0zNjQ5OTc2MjcxNjU2OTcmc3RhcnQ9MCZsaXN0VHlwZT1saXN0X25vbmZyaWVuZF9ub25hZG1pbiIsImgiOiIvYnJvd3NlL2dyb3VwL21lbWJlcnMvP2lkPTM2NDk5NzYyNzE2NTY5NyZzdGFydD0wJmxpc3RUeXBlPWxpc3Rfbm9uZnJpZW5kX25vbmFkbWluIiwicyI6Im0ifQ%3D%3D; m_pixel_ratio=2; wd=1600x761; locale=id_ID; c_user=100016865703374; xs=28%3Af1Ore9WclPiXMg%3A2%3A1592440738%3A17482%3A10881; spin=r.1002259764_b.trunk_t.1592440740_s.1_v.2_; fr=1MEuLVsMfSuW5EQcC.AWWnq3pkicMN-xmgvmJMU0TTBV4.Be5yq4.3_.F7n.0.0.Be6rr9.AWUe8k4Q; act=1592443927142%2F23; presence=EDvF3EtimeF1592444046EuserFA21B16865703374A2EstateFDt3F_5b_5dElm3FA2user_3a839815756123813A2Eutc3F1592441141623G592444046184CEchF_7bCC';

$url = "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100026288624097&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2Ffetch%2F%3Flimit%3D10%26shown_ids%3D100038236254033%252C100036949537963%252C100036548122788%252C100035329129470%252C100034821290428%252C100034456995285%252C100033704205713%252C100031090327351%252C100030709173472%252C100016865703374%252C100046554769198%252C100003946004761%252C756230844427450%252C256033601265744%252C100048220530179%252C100038951044364%252C100038647753388%252C100038400903785%26total_count%3D532%26ft_ent_identifier%3D1691586741125221%26stype%3Dms%26s%3D100026288624097&fref=pb_likes&gfid=AQBD8brz0HZWeyav";

$SendRequest = new FacebookFriendsSendRequest();
$SendRequest->SetCookie($cookie);

$process = $SendRequest->Process($url);

echo "<pre>";
var_dump($process);
echo "</pre>";