<?php namespace Riedayme\FacebookKit;

class FacebookPostComments
{

	public $cookie;	
	public $results = [];
	public $results_reply = [];
	public $comment_count = 0;	

	public function SetCookie($data) 
	{
		return $this->cookie = $data;
	}

	public function GetComment($data,$deep = false)
	{

		if ($deep) {
			$url = "https://mbasic.facebook.com/{$data['url']}";
		}else{
			$url = "https://mbasic.facebook.com/{$data['postid']}";
		}
		
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);
		$XpathCommentList = $xpath->query('//div[@id="ufi_'.$data['postid'].'"]/div/div[4]/div');

		if($XpathCommentList->length > 0) 
		{

			// $XpathCommentSort = iterator_to_array($XpathCommentList);
			// usort($XpathCommentSort, 'self::SortXpath');
			foreach ($XpathCommentList as $key => $node) 
			{

				$commentid = $node->getAttribute('id');

				if (is_numeric($commentid)) {
					$build_commentid = "{$data['postid']}_{$commentid}";
					$profilexpath = $xpath->query('//div[@id="'.$commentid.'"]/div/h3/a',$node)[0];
					$username = $profilexpath->nodeValue;
					$userid = $profilexpath->getAttribute('href');
					$CheckReplyTag = $xpath->query('//div[contains(@id,"'.$build_commentid.'")]/div/a',$node);

					$reply = false;
					if ($CheckReplyTag->length > 0) {
						$this->results_reply = []; /* reset value */
						$data['replyURL'] = $CheckReplyTag[0]->getAttribute('href');
						$reply = self::GetReplyComment($data);
					}

					$this->results[] = [
					'userid' => $userid,					
					'username' => $username,
					'commentid' => $build_commentid,
					'reply' => $reply
					];

					$this->comment_count = $this->comment_count+1;
				}

				/* if the results same as limit > break */
				if ($data['limit'] !== false AND $this->comment_count >= $data['limit']) break;
			}
		}

		// echo json_encode($this->results,JSON_PRETTY_PRINT);

		$XpathCommentPrevious = $xpath->query('//div[@id="ufi_'.$data['postid'].'"]/div/div[4]/div[contains(@id,"see_prev")]/a/@href');

		if ($XpathCommentPrevious->length > 0 AND $this->comment_count < $data['limit'] OR $XpathCommentPrevious->length > 0 AND $data['limit'] == false) {
			$XpathCommentPreviousURL = $XpathCommentPrevious[0]->value;
			$data['url'] = $XpathCommentPreviousURL;
			return self::GetComment($data,true);
		}
	}


	public function GetReplyComment($data,$deep = false)
	{

		if ($deep) {
			$url = "https://mbasic.facebook.com/{$data['url']}";
			//echo $url."<br/>";
		}else{
			$url = "https://mbasic.facebook.com/{$data['replyURL']}";
		}
		
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$XpathCommentList = $xpath->query('//div[@id="objects_container"]/div/div[1]/div[2]/div');

		// echo $XpathCommentList->length."_________".$url."<br/>";

		if($XpathCommentList->length > 0) 
		{

			foreach ($XpathCommentList as $ked => $node) 
			{

				$commentid = $node->getAttribute('id');

				if (is_numeric($commentid)) {
					$build_commentid = "{$data['postid']}_{$commentid}";
					$profilexpath = $xpath->query('//div[@id="'.$commentid.'"]/div/h3/a',$node)[0];
					$username = $profilexpath->nodeValue;
					$userid = $profilexpath->getAttribute('href');

					$this->results_reply[] = [
					'userid' => $userid,					
					'username' => $username,
					'commentid' => $build_commentid
					];

					$this->comment_count = $this->comment_count+1;
				}

				/* if the results same as limit > break */
				if ($data['limit'] !== false AND $this->comment_count >= $data['limit']) break;
			}
		}

		// echo json_encode($this->results_reply,JSON_PRETTY_PRINT);
		
		$XpathCommentPrevious = $xpath->query('/html/body/div/div/div[2]/div/div[1]/div[2]/div[1]/a/@href');

		if ($XpathCommentPrevious->length > 0 AND $this->comment_count < $data['limit'] OR $XpathCommentPrevious->length > 0 AND $data['limit'] == false) {			
			$XpathCommentPreviousURL = $XpathCommentPrevious[0]->value;
			$data['url'] = $XpathCommentPreviousURL;
			return self::GetReplyComment($data,true);
		}else{
			return $this->results_reply;
		}
	}

	public function Results()
	{
		return $this->results;
	}

	public function ResultsIDS()
	{
		if ($this->comment_count > 0) {
			foreach ($this->results as $comment) {

				if ($comment['reply']) {
					foreach ($comment['reply'] as $reply) {
						$ids[] = $reply['commentid'];
					}
				}

				$ids[] = $comment['commentid'];
			}

			return $ids;
		}else{
			return false;
		}
	}

	public function SortXpath($a, $b) {
		return (int) $b->getAttribute('id') - (int) $a->getAttribute('id');
	}

}