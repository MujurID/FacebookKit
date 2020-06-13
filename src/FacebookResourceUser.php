<?php namespace Riedayme\FacebookKit;

class FacebookResourceUser
{

	public static function GetUserInfoByToken($token)
	{

		$url = "https://graph.facebook.com/me?fields=name,picture&access_token={$token}";

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');

		$access = FacebookHelper::curl($url,false,$headers);

		$response = json_decode($access['body'],true);

		if (array_key_exists('error', $response)) {
			return false;
		}

		return [
		'id' => $response['id'],
		'username' => $response['name'],
		'photo' => $response['picture']['data']['url']
		];
	}

}