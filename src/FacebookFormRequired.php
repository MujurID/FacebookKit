<?php namespace Riedayme\FacebookKit;

/**
 * Handling Cookie
 */
Class FacebookFormRequired
{

	public static function Login() 
	{

		$url = 'https://m.facebook.com/login/';

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$pattern_input = '/<input.*?name="(.*?)".*?value="(.*?)".*?>/';
		preg_match_all($pattern_input, $response, $matches);

		if (empty($matches)) {
			die("Tidak dapat mendapatkan Input Value");
		}

		$params = array();
		foreach ($matches[1] as $index => $key) {
			$params[$key] = $matches[2][$index];
		}


		$cookie = FacebookCookie::ReadCookie($access['header']);

		return [
		'input' => $params,
		'cookie' => $cookie
		];
	}
}
