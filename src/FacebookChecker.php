<?php namespace Riedayme\FacebookKit;

class FacebookChecker
{

	public static function CheckLiveCookie($cookie)
	{

		$url = 'https://mbasic.facebook.com/';
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-Site: none';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Fetch-Dest: document';
		$headers[] = 'Cookie: '.$cookie;		

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		if (!strpos($response, 'mbasic_logout_button')) {
			return false;
		}

		return true;
	}

}