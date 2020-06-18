<?php namespace Riedayme\FacebookKit;

class FacebookFriendsSendRequest
{

	public $cookie;	
	public $results = [];

	public function SetCookie($data) 
	{
		return $this->cookie = $data;
	}

	public function Process($url,$postdata = false)
	{

		$headers = array();
		$headers[] = 'Authority: mbasic.facebook.com';
		$headers[] = 'Upgrade-Insecure-Requests: 1';
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Fetch-Site: none';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,$postdata,$headers);

		$response = $access['header'];

		if (strpos($response, '302 Found') AND strpos($response, '200 OK')) {
			return true;
		}elseif (strpos($response, '200 OK')) {

			$data['url'] = $url;
			$data['cookie'] = $this->cookie;
			$getpostdata = FacebookFormRequired::SendRequestFriendship($data);


			$postdata['fb_dtsg'] = $getpostdata['fb_dtsg'];
			$postdata['jazoest'] = $getpostdata['jazoest'];
			$postdata['_wap_notice_shown'] = $getpostdata['_wap_notice_shown'];
			$postdata['_orig_post_vars'] = $getpostdata['_orig_post_vars'];						
			
			$postdata = http_build_query($postdata);

			self::Process($url,$postdata);
		}else{
			return false;
		}
	}
}