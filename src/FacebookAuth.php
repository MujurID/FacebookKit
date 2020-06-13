<?php namespace Riedayme\FacebookKit;

class FacebookAuth
{

	public static function AuthUsingCookie($cookie)
	{

		$check_cookie = FacebookChecker::CheckLiveCookie($cookie);
		if (!$check_cookie) die("[ERROR] cookie tidak bisa digunakan".PHP_EOL);

		$userid = FacebookCookie::GetUIDCookie($cookie);
		$access_token = FacebookAccessToken::GetTouchToken($cookie);
		$userinfo = FacebookResourceUser::GetUserInfoByToken($access_token);
		if (!$userinfo) die("[ERROR] tidak dapat mengambil informasi user".PHP_EOL);

		return [
		'userid' => $userid,
		'username' => $userinfo['username'], 
		'photo' => $userinfo['photo'],
		'cookie' => $cookie,
		'access_token' => $access_token
		];

	}	

	public static function AuthLoginByMobile($username,$password)
	{

		$url = 'https://m.facebook.com/login/device-based/login/async/';

		$formrequired = FacebookFormRequired::Login();

		$postdata = $formrequired['input'];
		$postdata['email'] = $username;		
		$postdata['prefill_contact_point'] = $username;
		$postdata['encpass'] = '#PWD_BROWSER:0:' . time() . ':' . $password;
		$postdata = http_build_query($postdata);

		$cookie = $formrequired['cookie'];

		$headers = array();
		$headers[] = 'Authority: m.facebook.com';
		$headers[] = 'X-Requested-With: XMLHttpRequest';
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'X-Response-Format: JSONStream';
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'Accept: */*';
		$headers[] = 'Origin: https://m.facebook.com';
		$headers[] = 'Sec-Fetch-Site: same-origin';
		$headers[] = 'Sec-Fetch-Mode: cors';
		$headers[] = 'Referer: https://m.facebook.com/login/?next&ref=dbl&fl&refid=8';
		$headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
		$headers[] = 'Cookie: '.$cookie;	

		$login = FacebookHelper::curl($url,$postdata,$headers);

		$response = $login['header'];

		$cookie = FacebookCookie::ReadCookie($response);

		if (strpos($response, 'checkpoint')) {
			die("Akun terkena checkpoint".PHP_EOL);
		}elseif (!strpos($cookie, 'c_user=')) {
			die("Username atau password salah".PHP_EOL);
		}else{

			$userid = FacebookCookie::GetUIDCookie($cookie);
			$access_token = FacebookAccessToken::GetTouchToken($cookie);
			$userinfo = FacebookResourceUser::GetUserInfoByToken($access_token);
			if (!$userinfo) die("[ERROR] tidak dapat mengambil informasi user".PHP_EOL);

			return [
			'userid' => $userid,
			'username' => $userinfo['username'], 
			'photo' => $userinfo['photo'],
			'cookie' => $cookie,
			'access_token' => $access_token
			];
		}			

	}	

}