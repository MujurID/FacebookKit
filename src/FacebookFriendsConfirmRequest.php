<?php namespace Riedayme\FacebookKit;

class FacebookFriendsConfirmRequest
{

	public $cookie;	
	public $results = [];

	public function SetCookie($data) 
	{
		return $this->cookie = $data;
	}

	public function Process($dataurl)
	{

		$url = "https://mbasic.facebook.com/{$dataurl}";

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['header'];

		if (strpos($response, '302 Found') AND strpos($response, '200 OK')) {
			return true;
		}else{
			return false;
		}
	}
}