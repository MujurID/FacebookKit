<?php namespace Riedayme\FacebookKit;

class FacebookPostReactionProfile
{

	public $cookie;	
	public $results = [];

	public function SetCookie($data) 
	{
		return $this->cookie = $data;
	}

	public function GetReactionProfile($data,$deep = false)
	{

		if ($deep) {
			$url = "https://mbasic.facebook.com/{$data['url']}";
		}else{
			$url = "https://mbasic.facebook.com/ufi/reaction/profile/browser/?ft_ent_identifier={$data['postid']}";
		}
		
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$ProfileXpath = $xpath->query('//div/ul/li/table/tbody/tr/td/table/tbody');

		if($ProfileXpath->length > 0) 
		{

			foreach ($ProfileXpath as $key => $node) 
			{

				$xpathlinkaction = $xpath->query('tr/td[4]/div/a/@href',$node);

				if (is_null($xpathlinkaction[0])) continue;

				$linkaction = $xpathlinkaction[0]->value;

				if (strpos($linkaction, 'friends/add_friend.php')) {

					$profilelink = $xpath->query('//table/tbody/tr/td/table/tbody/tr/td[3]/header/h3[1]/a',$node)[$key];
					$userlink = $profilelink->getAttribute('href');
					$username = $profilelink->nodeValue;
					$photo = $xpath->query('//table/tbody/tr/td/table/tbody/tr/td[1]/img/@src',$node)[$key]->value;

					$this->results[] = [
					'userlink' => $userlink,
					'username' => $username,
					'photo' => $photo,
					'linksendrequest' => "https://mbasic.facebook.com".$linkaction,
					];
				}


				/* if the results same as limit > break */
				if ($data['limit'] !== false AND count($this->results) >= $data['limit']) break;
			}
		}

		// echo json_encode($this->results);
		// exit;

		$XpathProfileListPrevious = $xpath->query('//div/ul/li[last()]/table/tbody/tr/td/div/a/@href');

		if ($XpathProfileListPrevious->length > 0 AND count($this->results) < $data['limit'] OR $XpathProfileListPrevious->length > 0 AND $data['limit'] == false) {

			$XpathProfileListPreviousURL = $XpathProfileListPrevious[0]->value;
			$data['url'] = $XpathProfileListPreviousURL;
			return self::GetReactionProfile($data,true);
		}
	}

	public function Results()
	{
		return $this->results;
	}
}