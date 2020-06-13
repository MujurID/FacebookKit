<?php  
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookPostComments;

$cookie = 'yourcookie';

$postid = 'postid';

$data = [
'postid' => $postid,
'limit' => 5,
];

$GetComment = new FacebookPostComments();
$GetComment->SetCookie($cookie);
$GetComment->GetComment($data);
$results = $GetComment->Results();

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(1) {
  [0]=>
  array(4) {
    ["userid"]=>
    string(27) "/Riedayme?refid=52&__tn__=R"
    ["username"]=>
    string(8) "Riedayme"
    ["commentid"]=>
    string(31) "702613526977498_702614376977413"
    ["reply"]=>
    array(3) {
      [0]=>
      array(3) {
        ["userid"]=>
        string(18) "/Riedayme?__tn__=R"
        ["username"]=>
        string(8) "Riedayme"
        ["commentid"]=>
        string(31) "702613526977498_702614523644065"
      }
      [1]=>
      array(3) {
        ["userid"]=>
        string(18) "/Riedayme?__tn__=R"
        ["username"]=>
        string(8) "Riedayme"
        ["commentid"]=>
        string(31) "702613526977498_702614543644063"
      }
      [2]=>
      array(3) {
        ["userid"]=>
        string(18) "/Riedayme?__tn__=R"
        ["username"]=>
        string(8) "Riedayme"
        ["commentid"]=>
        string(31) "702613526977498_702614580310726"
      }
    }
  }
}
*/

/*
array(2) {
  [0]=>
  string(33) "1556345327869916_1556722051165577"
  [1]=>
  string(33) "1556345327869916_1556672651170517"
}
*/