<?php namespace Riedayme\FacebookKit;

class FacebookAccessToken
{

	public static function GetTouchToken($cookie)
	{

		$url = 'https://m.facebook.com/composer/ocelot/async_loader/?publisher=feed';
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-Site: none';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Fetch-Dest: document';
		$headers[] = 'Cookie: '.$cookie;		

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$accesstoken = FacebookHelper::GetStringBetween($response,'accessToken\":\"','\",\"useLocalFilePreview\"');

		return $accesstoken;
	}

}