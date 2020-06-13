<?php namespace Riedayme\FacebookKit;

class FacebookUserAgent
{

	public static function Get($type = 'Windows')
	{

		if ($type == 'Windows') {
			return "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36";
		}elseif ($type == 'Linux') {
			return "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36";
		}
	}
}