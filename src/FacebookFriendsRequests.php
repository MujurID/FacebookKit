<?php namespace Riedayme\FacebookKit;

class FacebookFriendsRequests
{

	public $cookie;	
	public $results = [];

	public function SetCookie($data) 
	{
		return $this->cookie = $data;
	}

	public function GetFriendsRequests($data,$deep = false)
	{

		if ($deep) {
			$url = "https://mbasic.facebook.com/{$data['url']}";
		}else{
			$url = "https://mbasic.facebook.com/friends/center/requests";
		}
		
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$FriendsRequests = $xpath->query('//div[@id="friends_center_main"]/div[1]');

		if($FriendsRequests->length > 0) 
		{
			foreach ($FriendsRequests as $node) 
			{

				$ProfileList = $xpath->query('//div[@id="friends_center_main"]/div[1]/div/table[@role="presentation"]', $node);

				if($ProfileList->length > 0) 
				{
					foreach ($ProfileList as $key => $profile) {

						$profilelink = $xpath->query('//tr/td[2]/a',$profile)[$key];
						$userid = FacebookHelper::GetStringBetween($profilelink->getAttribute('href'),'uid=','&');

						if ($userid) {
							$username = $profilelink->nodeValue;
							$photo = $xpath->query('//img/@src',$profile)[$key]->value;						
							$linkconfirm = $xpath->query('//tr/td[2]/div[2]/a[1]/@href',$profile)[$key]->value;	
							$linkreject = $xpath->query('//tr/td[2]/div[2]/a[2]/@href',$profile)[$key]->value;

							$this->results[] = [
							'userid' => $userid,
							'username' => $username,
							'photo' => $photo,
							'linkconfirm' => $linkconfirm,
							'linkreject' => $linkreject,
							];

						}

						/* if the results same as limit > break */
						if ($data['limit'] !== false AND count($this->results) >= $data['limit']) break;
					}
				}
			}
		}

		$XpathRequestsPrevious = $xpath->query('//div[@id="friends_center_main"]/div[2]/a/@href');


		if ($XpathRequestsPrevious->length > 0 AND count($this->results) < $data['limit'] OR $XpathRequestsPrevious->length > 0 AND $data['limit'] == false) {

			$XpathRequestsPreviousURL = $XpathRequestsPrevious[0]->value;

			if (strpos($XpathRequestsPreviousURL, 'friends/center/requests')) {
				$data['url'] = $XpathRequestsPreviousURL;
				return self::GetFriendsRequests($data,true);
			}
		}
	}

	public function Results()
	{
		return $this->results;
	}
}