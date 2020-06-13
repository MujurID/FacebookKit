<?php  
/**
* Facebook Auto Reaction Group v1.0
* Last Update 10 Juni 2020
* Author : Faanteyki
*/
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;
use Riedayme\FacebookKit\FacebookCookie;
use Riedayme\FacebookKit\FacebookChecker;
use Riedayme\FacebookKit\FacebookGroupList;
use Riedayme\FacebookKit\FacebookFeedGroup;
use Riedayme\FacebookKit\FacebookPostReaction;

Class InputHelper
{
	public function GetInputCookie($data = false) {

		if ($data) return $data;

		$CheckPreviousCookie = FacebookAutoReactGroup::CheckPreviousCookie();

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

	public function GetInputGroupName($data = false) {

		if ($data) return $data;

		echo "Cari Nama Group (karakter): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		return (!$input) ? die('Nama Group Masih Kosong'.PHP_EOL) : $input;
	}	

	public function GetInputChoiceGroup($data = false) {

		if ($data) return $data;

		echo "Masukan Group yang dipilih (angka): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (strval($input) !== strval(intval($input))) {
			die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
		}

		return $input;
	}		
}

Class FacebookAutoReactGroup
{

	public $cookie;
	public $access_token;	
	public $username;
	public $groupid;	
	public $limit;
	public $react;

	public function Auth($data) 
	{

		echo "Masuk Akun <-------------".PHP_EOL;
		echo "Check Login <-------------".PHP_EOL;

		$userid = FacebookCookie::GetUIDCookie($data['cookie']);

		if (!$userid) {
			$results = self::ReadPreviousCookie($data['cookie']);
		}else{			

			$auth = new FacebookAuth();
			$results =$auth->AuthUsingCookie($data['cookie']);

			self::SaveCookie($results);
		}
		
		$data['react'] = InputHelper::GetInputReact();

		$this->cookie = $results['cookie'];
		$this->access_token = $results['access_token'];		
		$this->username = $results['username'];		

		$data['groupid'] = self::ChoiceGroup();
		$data['limit'] = InputHelper::GetInputLimit();

		$this->groupid = $data['groupid'];		
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

	public function ChoiceGroup()
	{
		echo "Mendapatkan List Group <-------------".PHP_EOL;

		$Group = new FacebookGroupList();
		$Group->SetCookie($this->cookie);

		$results =$Group->GetGroupListByScraping();

		echo "Ditemukan ".count($results)." Group <-------------".PHP_EOL;

		$search = InputHelper::GetInputGroupName();

		$search_results = array();
		foreach ($results as $key => $group) {
			if (preg_match("/{$search}/i", $group['name'])) {
				$search_results[] = "[{$key}]".$group['name'].PHP_EOL;
			}
		}

		if (!$search_results) {
			echo "Group tidak ditemukan, coba kembali.".PHP_EOL;
			return self::ChoiceGroup();
		}else{
			echo "Daftar Group yang ditemukan : ".PHP_EOL;			
			echo implode('', $search_results);
		}

		$choice = InputHelper::GetInputChoiceGroup();
		return $results[$choice]['id'];
	}

	public function GetFeed()
	{

		echo "Membaca Feed Group {$this->groupid} <-------------".PHP_EOL;

		$Feed = new FacebookFeedGroup();
		$Feed->SetAccessToken($this->access_token);

		$results =$Feed->GetFeedGroupByToken([
			'groupid' => $this->groupid,
			'limit' => $this->limit
			]);

		echo "Berhasil Mendapatkan Feed <-------------".PHP_EOL;

		return self::SyncPost($results);
	}

	public function ReactPost($datapost)
	{

		$datapost['url'] = "https://www.facebook.com/{$datapost['postid']}";
		$type = ($this->react == 'RANDOM') ? InputHelper::GetInputReact('RANDOM') : $this->react;

		echo "Proses React {$type} Post {$datapost['userid']}||{$datapost['postid']} <-------------".PHP_EOL;

		$react = new FacebookPostReaction();
		$react->SetCookie($this->cookie);
		$process = $react->ReactPostByScraping([
			'postid' => $datapost['postid'], 
			'type' => $type
			]);

		if ($process != false) {

			if ($process == 'URL_NOTFOUND') {
				echo "[!Gagal!] URL React pada post {$datapost['url']} tidak ditemukan.".PHP_EOL;	
			}elseif ($process == 'UNREACT') {
				echo "[!Gagal!] React Post {$datapost['url']}, Kemungkinan post sudah diberi react.".PHP_EOL;	
				self::SaveLog($datapost['postid']);	
			}else{
				echo "Sukses React Post {$process['url']} <-------------".PHP_EOL;
				self::SaveLog($datapost['postid']);
			}
		}else{
			echo "[!Gagal!] React Post {$process['url']}, Kesalahan pada kode.".PHP_EOL;
		}
	}

	public function SyncPost($datafeed)
	{

		echo "Sync Feed <-------------".PHP_EOL;

		$ReadLog = self::ReadLog();

		$results = array();
		foreach ($datafeed as $feed) {
			if (is_array($ReadLog) AND in_array($feed['postid'], $ReadLog)) {
				echo "Skip {$feed['postid']}, post sudah di proses. ".PHP_EOL;
				continue;
			}

			$results[] = $feed;
		}

		return $results;
	}

	public function ReadLog()
	{		

		$logfilename = "log/feedgroup-{$this->groupid}-{$this->username}";
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
		return file_put_contents("log/feedgroup-{$this->groupid}-{$this->username}", $datapost.PHP_EOL, FILE_APPEND);
	}
}

Class Worker
{
	public function Run()
	{

		echo " --- Facebook Auto Reaction Group v1.0 ---".PHP_EOL;

		$data['cookie'] = InputHelper::GetInputCookie();

		$delay_default = 10;
		$delay = 10;
		$delayfeed_default = 10;
		$delayfeed = 10;

		/* Call Class */
		$Working = new FacebookAutoReactGroup();
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
				echo "Tidak ditemukan Post, Coba lagi setelah {$delayfeed} detik".PHP_EOL;
				sleep($delayfeed);

				$delayfeed = $delayfeed*rand(2,3);
				$nofeed++;

				continue;
			}

			foreach ($FeedList as $key => $post) {

				/* when ReactPost 5 reset sleep value to default */
				if ($ReactPost >= 5) {
					$delay = $delay_default;
					$ReactPost = 0;
				}	

				$Working->ReactPost($post);

				echo "Delay {$delay} <--------------".PHP_EOL;
				sleep($delay);

				$delay = $delay+5;
				$ReactPost++;
			}

		}		

	}
}

Worker::Run();
// use at you own risk