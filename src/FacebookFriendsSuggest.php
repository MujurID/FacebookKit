<?php namespace Riedayme\FacebookKit;

class FacebookFriendsSuggest
{

	public $cookie;	
	public $results = [];

	public function SetCookie($data) 
	{
		return $this->cookie = $data;
	}

	public function GetFriendsSuggest($data,$deep = false)
	{

		if ($deep) {
			$url = "https://mbasic.facebook.com/{$data['url']}";
		}else{
			$url = "https://mbasic.facebook.com/friends/center/suggestions/";
		}
		
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$FriendsSuggest = $xpath->query('//div[@id="friends_center_main"]/div[1]');

		if($FriendsSuggest->length > 0) 
		{
			foreach ($FriendsSuggest as $node) 
			{

				$ProfileList = $xpath->query('//div[@id="friends_center_main"]/div[1]/div/table[@role="presentation"]', $node);

				if($ProfileList->length > 0) 
				{
					foreach ($ProfileList as $key => $profile) {

						$profilelink = $xpath->query('//tr/td[2]/a',$profile)[$key];

						$userid = FacebookHelper::GetStringBetween($profilelink->getAttribute('href'),'uid=','&');

						if ($userid) {
							$username = $profilelink->nodeValue;
							$photo = $xpath->query('//tr/td[1]/img/@src',$profile)[$key]->value;						
							$linksendrequest = $xpath->query('//tr/td[2]/div[2]/table/tbody/tr/td/div[1]/a/@href',$profile)[$key]->value;	

							$this->results[] = [
							'userid' => $userid,
							'username' => $username,
							'photo' => $photo,
							'linksendrequest' => "https://mbasic.facebook.com".$linksendrequest,
							];

						}

						/* if the results same as limit > break */
						if ($data['limit'] !== false AND count($this->results) >= $data['limit']) break;
					}
				}
			}
		}

		$XpathSuggestPrevious = $xpath->query('//div[@id="friends_center_main"]/div[2]/a/@href');


		if ($XpathSuggestPrevious->length > 0 AND count($this->results) < $data['limit'] OR $XpathSuggestPrevious->length > 0 AND $data['limit'] == false) {

			$XpathSuggestPreviousURL = $XpathSuggestPrevious[0]->value;

			if (strpos($XpathSuggestPreviousURL, 'friends/center/suggestions')) {
				$data['url'] = $XpathSuggestPreviousURL;
				return self::GetFriendsSuggestByScraping($data,true);
			}
		}
	}

	public function Results()
	{
		return $this->results;
	}
}