<?php
class ShortMsg {
	private $account;
	private $pwd;
	
	function __construct($account, $password){
		$this->account = $account;
		$this->pwd = strtoupper(md5($password));
	}
	
	public function sendSMS($mobile, $content) {
		$uri="http://api.chanzor.com/send";
		
		$data = array (
				'account' => $this->account,
				'password' => $this->pwd,
				'content' => $content,
				'mobile' => $mobile
		);
		
		return $this->Post($uri, $data);
	}
	
	function Post($uri, $data) {
		$ch = curl_init ();
		// print_r($ch);
		curl_setopt ( $ch, CURLOPT_URL, $uri );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		$return = curl_exec ( $ch );
		curl_close ( $ch );
		//返回结果
		if($return) return json_decode($return, true);
		
		return false;
	}
}
?>