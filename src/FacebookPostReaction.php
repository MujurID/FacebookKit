<?php namespace Riedayme\FacebookKit;

class FacebookPostReaction
{

	public $cookie;	
	public $fb_dtsg;

	public function SetCookie($data) 
	{
		return $this->cookie = $data;
	}	

	public function SetFbDstg() 
	{
		return $this->fb_dtsg = FacebookDTSG::GetFromProfile($this->cookie);
	}		

	public function ReactPostByScraping($data)
	{

		$url = self::GetReactionURL($data);
		if (!$url) return 'URL_NOTFOUND';
		if ($url == 'UNREACT') return 'UNREACT';

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-Site: none';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Fetch-Dest: document';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['header'];

		if (strpos($response, '200 OK') OR strpos($response, '302 Found')) {
			$status = true;
		}else{
			$status = false;
		}
		return [
		'status' => $status,
		'id' => $data['postid'],
		'url' => "https://www.facebook.com/{$data['postid']}"
		];
	}

	public function GetReactionURL($data)
	{


		$url = "https://mbasic.facebook.com/reactions/picker/?is_permalink=1&ft_id={$data['postid']}";
		
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-Site: none';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Fetch-Dest: document';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$XpathReactlionlist = $xpath->query('//li/table/tbody/tr/td/a/@href');

		if($XpathReactlionlist->length > 0) 
		{
			$reaction_data = array();
			foreach ($XpathReactlionlist as $node) 
			{
				$url = FacebookHelper::InnerHTML($node);
				$url = "https://mbasic.facebook.com".$url;

				if (!strpos($url, '/story.php')) {

					$type = self::GetReactionTypeForScraping($url);

					$reaction_data[$type] = html_entity_decode(trim($url));
				}
			}

			return (!empty($reaction_data[$data['type']])) ? $reaction_data[$data['type']] : 'UNREACT';
		}

		return false;		
	}

	public function GetReactionTypeForScraping($url)
	{

		$type = false;
		if (strpos($url, 'reaction_type=1&')) {
			$type = 'LIKE';
		}elseif (strpos($url, 'reaction_type=2&')) {
			$type = 'LOVE';
		}elseif (strpos($url, 'reaction_type=16&')) {
			$type = 'CARE';
		}elseif (strpos($url, 'reaction_type=4&')) {
			$type = 'HAHA';
		}elseif (strpos($url, 'reaction_type=3&')) {
			$type = 'WOW';
		}elseif (strpos($url, 'reaction_type=7&')) {
			$type = 'SAD';
		}elseif (strpos($url, 'reaction_type=8&')) {
			$type = 'ANGRY';
		}elseif (strpos($url, 'reaction_type=0&')) {
			$type = 'UNREACT';
		}

		return $type;
	}

	/**
	 * Reaction by touch.facebook.com
	 */
	public function ReactPostByTouch($data)
	{

		$url = 'https://touch.facebook.com/ufi/reaction/';

		$reaction_type = self::GetReactionTypeForTouch($data['type']);
		$postdata = "reaction_type={$reaction_type}&ft_ent_identifier={$data['postid']}&fb_dtsg={$this->fb_dtsg}";

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,$postdata,$headers);

		$response = $access['header'];

		if (strpos($response, '200 OK')) {
			$status = true;
		}else{
			$status = false;
		}
		return [
		'status' => $status,
		'id' => $data['postid'],
		'url' => "https://www.facebook.com/{$data['postid']}"
		];
	}

	public function GetReactionTypeForTouch($data)
	{
		$type = false;
		if ($data == 'LIKE') {
			$type = '1';
		}elseif ($data == 'LOVE') {
			$type = '2';
		}elseif ($data == 'CARE') {
			$type = '1';
		}elseif ($data == 'HAHA') {
			$type = '4';
		}elseif ($data == 'WOW') {
			$type = '3';
		}elseif ($data == 'SAD') {
			$type = '7';
		}elseif ($data == 'ANGRY') {
			$type = '8';
		}elseif ($data == 'UNREACT') {
			$type = '0';
		}

		return $type;
	}

}