<?php  
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookDTSG;

$cookie = 'yourcookie';

$get_fb_dtsg = new FacebookDTSG();
$fb_dtsg =$get_fb_dtsg->GetFromProfile($cookie);

echo $fb_dtsg;