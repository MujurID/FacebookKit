<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookGroupList;

$cookie = 'yourcookie';

$Group = new FacebookGroupList();
$Group->SetCookie($cookie);

$results =$Group->GetGroupListByScraping();

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(1) {
  [0]=>
  array(3) {
    ["id"]=>
    string(15) "250732861682621"
    ["name"]=>
    string(17) "Blogger Indonesia"
    ["url"]=>
    string(43) "https://facebook.com/groups/250732861682621"
  }
}
*/