<?php  
/**
* Facebook Auto Reaction TimeLine v1.1
* Last Update 11 Juni 2020
* Author : Faanteyki
*/
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;
use Riedayme\FacebookKit\FacebookCookie;
use Riedayme\FacebookKit\FacebookChecker;
use Riedayme\FacebookKit\FacebookFeedTimeLine;
use Riedayme\FacebookKit\FacebookPostComments;
use Riedayme\FacebookKit\FacebookPostReaction;

Class InputHelper
{
	public function GetInputCookie($data = false) {

		if ($data) return $data;

		$CheckPreviousCookie = FacebookAutoReactTimeLine::CheckPreviousCookie();

		if ($CheckPreviousCookie) {
			echo "Anda Memiliki Cookie yang tersimpan pilih angkanya dan gunakan kembali : ".PHP_EOL;
			foreach ($CheckPreviousCookie as $key => $cookie) {
				echo "[{$key}]".$cookie['username'].PHP_EOL;
			}
			echo "[x] Masukan cookie baru".PHP_EOL;

			echo "Pilihan Anda : ".PHP_EOL;

			$input = strtolower(trim(fgets(STDIN)));			

			if ($input != 'x') {

				if (strval($input) !== strval(intval($input))) {
					die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
				}

				return $input;
			}
		}	

		echo "Masukan Cookie : ".PHP_EOL;

		$input = trim(fgets(STDIN));

		return (!$input) ? die('Cookie Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputLimit($data = false) {

		if ($data) return $data;

		echo "Masukan Limit Feed (angka): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (strval($input) !== strval(intval($input))) {
			die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
		}

		return (!$input) ? die('Limit Feed Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputReact($data = false) {

		if (strtoupper($data) == 'RANDOM') {
			$reactlist = ['LIKE', 'LOVE', 'CARE', 'WOW'];
			return $reactlist[array_rand($reactlist)];
		}

		if ($data) return $data;

		echo "Masukan Reaksi yang dikirim : [LIKE, LOVE, CARE, HAHA, WOW, SAD, ANGRY, RANDOM]".PHP_EOL;

		$input = strtoupper(trim(fgets(STDIN)));

		$react = ['LIKE', 'LOVE', 'CARE', 'HAHA', 'WOW', 'SAD', 'ANGRY', 'RANDOM'];

		if (!in_array($input,$react)) {
			die("Reaksi Pilihan tidak valid".PHP_EOL);
		}


		return (!$input) ? die('Reaction Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputReactComment($data = false) {

		if ($data) return $data;

		echo "React Comment Juga ? (y/n): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (!in_array(strtolower($input),['y','n'])) {
			die("Pilihan tidak diketahui".PHP_EOL);
		}

		return (!$input) ? die('Limit Feed Masih Kosong'.PHP_EOL) : $input;
	}			
}

Class FacebookAutoReactTimeLine
{

	public $cookie;
	public $access_token;	
	public $username;
	public $limit;
	public $react;

	public function Auth($data) 
	{

		echo "[INFO] Masuk Akun <-------------".PHP_EOL;

		$userid = FacebookCookie::GetUIDCookie($data['cookie']);
		
		if (!$userid) {
			$results = self::ReadPreviousCookie($data['cookie']);

			echo "[INFO] Check Live Cookie <-------------".PHP_EOL;
			
			$check_cookie = FacebookChecker::CheckLiveCookie($results['cookie']);
			if (!$check_cookie) die("[ERROR] cookie tidak bisa digunakan".PHP_EOL);
		}else{			

			echo "[INFO] Check Live Cookie <-------------".PHP_EOL;

			$auth = new FacebookAuth();
			$results =$auth->AuthUsingCookie($data['cookie']);

			self::SaveCookie($results);
		}

		$this->username = $results['username'];
		$this->cookie = $results['cookie'];
		$this->access_token = $results['access_token'];
		$this->limit = $data['limit'];
		$this->react = $data['react'];
	}

	public function SaveCookie($data){

		$filename = 'log/log-cookie.json';

		if (file_exists($filename)) {
			$read = file_get_contents($filename);
			$read = json_decode($read,true);
			$dataexist = false;
			foreach ($read as $key => $logdata) {
				if ($logdata['userid'] == $data['userid']) {
					$inputdata[] = $data;
					$dataexist = true;
				}else{
					$inputdata[] = $logdata;
				}
			}

			if (!$dataexist) {
				$inputdata[] = $data;
			}
		}else{
			$inputdata[] = $data;
		}

		return file_put_contents($filename, json_encode($inputdata,JSON_PRETTY_PRINT));
	}

	public function CheckPreviousCookie()
	{

		$filename = 'log/log-cookie.json';
		if (file_exists($filename)) {
			$read = file_get_contents($filename);
			$read = json_decode($read,TRUE);
			foreach ($read as $key => $logdata) {
				$inputdata[] = $logdata;
			}

			return $inputdata;
		}else{
			return false;
		}
	}

	public function ReadPreviousCookie($data)
	{

		$filename = 'log/log-cookie.json';
		if (file_exists($filename)) {
			$read = file_get_contents($filename);
			$read = json_decode($read,TRUE);
			foreach ($read as $key => $logdata) {
				if ($key == $data) {
					$inputdata = $logdata;
					break;
				}
			}

			return $inputdata;
		}else{
			die("file tidak ditemukan");
		}
	}	

	public function GetFeed()
	{

		echo "[INFO] Membaca Feed Timeline <-------------".PHP_EOL;

		$Feed = new FacebookFeedTimeLine();
		$Feed->SetAccessToken($this->access_token);		
		$results =$Feed->GetTimeLineByToken($this->limit);

		/* check if feed not loaded */
		if (!$results) {
			echo "[ERROR] Mendapatkan Feed <-------------".PHP_EOL;

			self::ReAuth();
			return false;
		}

		echo "[INFO] Sukses Mendapatkan Feed <-------------".PHP_EOL;

		return $results;
	}

	public function GetComment($postid)
	{
		$data = [
		'postid' => $postid,
		'limit' => 10,
		];
		$GetComment = new FacebookPostComments();
		$GetComment->SetCookie($this->cookie);
		$GetComment->GetComment($data);
		$results = $GetComment->ResultsIDS();

		if (!$results) {
			return false;
		}

		return $results;
	}

	public function ReactPost($datapost,$is_comment = false)
	{

		$posttype = ($is_comment == true) ? "Komentar" : "Post";

		$datapost['url'] = "https://www.facebook.com/{$datapost['postid']}";

		/* sync react post with log file */
		$sync = self::SyncReact($datapost['postid']);
		if ($sync) {
			echo "[SKIP] React {$posttype} {$datapost['url']} Sudah Diproses.".PHP_EOL;
			return false;
		}

		$type = ($this->react == 'RANDOM') ? InputHelper::GetInputReact('RANDOM') : $this->react;

		echo "[INFO] Proses React {$posttype} {$type} {$datapost['postid']} <-------------".PHP_EOL;

		$react = new FacebookPostReaction();
		$react->SetCookie($this->cookie);
		$process = $react->ReactPostByScraping([
			'postid' => $datapost['postid'], 
			'type' => $type
			]);

		if ($process != false) {

			if ($process == 'URL_NOTFOUND') {
				echo "[FAILED] URL React pada {$posttype} {$datapost['url']} tidak ditemukan.".PHP_EOL;	
			}elseif ($process == 'UNREACT') {
				echo "[SKIP] React {$posttype} {$datapost['url']} Sudah Diproses.".PHP_EOL;	
				self::SaveLog($datapost['postid']);	
			}else{
				echo "[SUCCESS] React {$posttype} {$datapost['url']} <-------------".PHP_EOL;
				self::SaveLog($datapost['postid']);
				return true;
			}
		}else{
			echo "[FAILED] React {$posttype} {$datapost['url']}, Kesalahan pada kode.".PHP_EOL;
		}

		return false;
	}

	public function ReAuth()
	{

		echo "[INFO] Mencoba login kembali <-------------".PHP_EOL;
		self::Auth([
			'cookie' => $this->cookie,
			'limit' => $this->limit,
			'react' => $this->react,
			]);
	}

	public function SyncReact($postid)
	{

		$ReadLog = self::ReadLog();

		if (is_array($ReadLog) AND in_array($postid, $ReadLog)) {
			return true;
		}

		return false;
	}

	public function ReadLog()
	{		

		$logfilename = "log/feedtimeline-data-{$this->username}";
		$log_url = array();
		if (file_exists($logfilename)) 
		{
			$log_url = file_get_contents($logfilename);
			$log_url  = explode(PHP_EOL, $log_url);
		}

		return $log_url;
	}

	public function SaveLog($datapost)
	{
		return file_put_contents("log/feedtimeline-data-{$this->username}", $datapost.PHP_EOL, FILE_APPEND);
	}
}

Class Worker
{
	public function Run()
	{

		echo " --- Facebook Auto Reaction TimeLine v1.0 ---".PHP_EOL;

		$data['cookie'] = InputHelper::GetInputCookie();
		$data['limit'] = InputHelper::GetInputLimit();
		$data['react'] = InputHelper::GetInputReact();
		$react_comment = InputHelper::GetInputReactComment();				

		$delay_default = 10;
		$delay = 10;
		$delayfeed_default = 10;
		$delayfeed = 10;

		/* Call Class */
		$Working = new FacebookAutoReactTimeLine();
		$Working->Auth($data);

		$nofeed = 0;
		$ReactPost = 0;
		while (true) {

			/* when nofeed 5 reset sleep value to default */
			if ($nofeed >= 5) {
				$delayfeed = $delayfeed_default;
				$nofeed = 0;
			}

			$FeedList = $Working->GetFeed();

			if (empty($FeedList)) {
				echo "[INFO] Tidak ditemukan Post, Coba lagi setelah {$delayfeed} detik".PHP_EOL;
				sleep($delayfeed);

				$delayfeed = $delayfeed*rand(2,3);
				$nofeed++;

				continue;
			}

			$temp_post_process = 0;
			foreach ($FeedList as $key => $post) {

				/* when ReactPost 5 reset sleep value to default */
				if ($ReactPost >= 5) {
					$delay = $delay_default;
					$ReactPost = 0;
				}	

				$process_post = $Working->ReactPost($post);

				if ($react_comment == 'y') {
					echo "[INFO] Membaca Komentar Post {$post['postid']} <-------------".PHP_EOL;

					$comment = $Working->GetComment($post['postid']);
					if (!$comment) {
						echo "[INFO] Tidak ada komentar pada post {$post['postid']}".PHP_EOL;
						continue;
					}

					foreach ($comment as $commentid) {

						/* when ReactPost 5 reset sleep value to default */
						if ($ReactPost >= 5) {
							$delay = $delay_default;
							$ReactPost = 0;
						}	

						$commentpost['postid'] = $commentid;
						$process_comment = $Working->ReactPost($commentpost,true);

						if ($process_comment) {
							echo "Delay {$delay} <--------------".PHP_EOL;
							sleep($delay);

							$delay = $delay+5;
							$ReactPost++;
						}

					}
				}

				if ($process_post) {
					echo "Delay {$delay} <--------------".PHP_EOL;
					sleep($delay);
					$delay = $delay+5;
					$ReactPost++;
					$temp_post_process++;
				}
			}

			if ($temp_post_process < 1) {
				echo "[INFO] Tidak ditemukan Post, Coba lagi setelah {$delayfeed} detik".PHP_EOL;
				sleep($delayfeed);

				$delayfeed = $delayfeed*rand(2,3);
				$nofeed++;

				continue;
			}

		}		

	}
}

Worker::Run();
// use at you own risk