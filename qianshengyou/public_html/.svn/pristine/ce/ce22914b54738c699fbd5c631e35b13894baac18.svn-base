<?php
require_once 'PHPMailerAutoload.php';

/**
 * AirMail.php
 * @copyright           airplus
 * @license             http://www.airplus.com
 * @lastmodify          2018-08-02
 * */

class AirMail {
	private $mail;
	
	function __construct() {
		$this->mail = new PHPMailer;
		
		$this->mail->isSMTP();                                // Set mailer to use SMTP
		$this->mail->Host = 'smtp.exmail.qq.com';             // Specify main SMTP servers
		$this->mail->SMTPAuth = true;                         // Enable SMTP authentication
		$this->mail->Username = 'ttucar@cywin.cn';			  // SMTP username
		$this->mail->Password = 'Hlg121129';                   // SMTP password
		$this->mail->SMTPSecure = 'ssl';                      // Enable ssl encryption, `tls` also accepted
		$this->mail->Port = 465;                              // TCP port to connect to
		$this->mail->CharSet = "utf-8";                       // 设置字符集编码
		
		$this->mail->From = 'ttucar@cywin.cn';
		$this->mail->FromName = '天天优车';
	}
	
	/**
	 * 根据发送邮件参数向指定用户发送邮件
	 *
	 * @param $from
	 * @param $fromname
	 * @param $tolist
	 * @param $subject
	 * @param $body
	 * @param $cclist
	 * @param $html_ind
	 * @param $bcclist
	 */
	public function airplus_send_mail($subject, $body, $tolist, $html_ind=true, $cclist=null, $bcclist=null) {
	    if($html_ind) $this->mail->IsHTML(true);
	    // format to list
	    foreach ($tolist as $value) {
	        $this->mail->addAddress($value['mail'], $value['name']);
	    }
	    // format cc list
	    if(!empty($cclist)) {
	        foreach ($cclist as $cclvalue) {
	            $this->mail->addCC($cclvalue['mail'], $cclvalue['name']);
	        }
	    }
	    // format bcclist
	    if(!empty($bcclist)) {
	        foreach ($bcclist as $bcclvalue) {
	            $this->mail->addBCC($bcclvalue['mail'], $bcclvalue['name']);
	        }
	    }
	    // mail content
	    $this->mail->Subject = $subject;
	    $this->mail->Body    = $body;
	    $this->mail->AltBody = $body;
	    // send mail
	    if(!$this->mail->send()) {
	        //var_dump($this->mail->ErrorInfo);
	        return false;
	    } else {
	        return true;
	    }
	}
	
	/**
	 * 根据发送邮件参数向指定用户发送邮件
	 * 
	 * @param $from
	 * @param $fromname
	 * @param $tolist
	 * @param $subject
	 * @param $body
	 * @param $cclist
	 * @param $html_ind
	 * @param $bcclist
	 */
	public function cobike_send_mail($from, $fromname, $tolist, $subject, $body, $cclist=null, $html_ind=true, $bcclist=null) {
		if($from) $this->mail->From = $from;
		if($fromname) $this->mail->FromName = $fromname;
		
		if($html_ind) $this->mail->IsHTML(true);
		
		foreach ($tolist as $value) {
			$this->mail->addAddress($value['mail'], $value['name']);
		}
		
		if(!empty($cclist)) {
			foreach ($cclist as $cclvalue) {
				$this->mail->addCC($cclvalue['mail'], $cclvalue['name']);
			}
		}
		
		if(!empty($bcclist)) {
			foreach ($bcclist as $bcclvalue) {
				$this->mail->addBCC($bcclvalue['mail'], $bcclvalue['name']);
			}
		}
		
		$this->mail->Subject = $subject;
		$this->mail->Body    = $body;
		$this->mail->AltBody = $body;
		
		if(!$this->mail->send()) {
			//var_dump($this->mail->ErrorInfo);
			return false;
		} else {
			return true;
		}
	}
}
?>