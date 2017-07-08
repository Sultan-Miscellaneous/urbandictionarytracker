<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class email_validation extends CI_Model {

	public function __construct(){
		parent::__construct();

		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'urbandictionarytracker',
		    'smtp_pass' => 'Theunkown3',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);

		$this->load->library('email');
		$this->load->model('login_database');
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
	}
	public function sendEmailToUserForValidation($user){
		$this->email->from('urbandictionarytracker@gmail.com', 'Admin');
		$this->email->to($user['user_email']);

		$this->email->subject('Account Validation');

		$validationLink = "http://localhost:8080/Site_1/index.php/User_Authentication/validateUser/{$user['id']}/{$user['user_validation_code']}";

		$message = "Hello {$user['user_name']},<br><br> Please click on the following link to validate your account: {$validationLink}";

		$this->email->message($message);

		$this->email->send();
	}
	public function sendEmailtToUserForPasswordReset($user){
		$this->email->from('urbandictionarytracker@gmail.com', 'Admin');
		$this->email->to($user['user_email']);
		$this->email->subject('Reset Password');

		$resetCode = $this->login_database->encryptText($user['user_name']);
		$resetCode = urlencode($resetCode);

		$validationLink = "http://localhost:8080/Site_1/index.php/User_Authentication/reset_Password_form/{$resetCode}";

		$message = "Hello {$user['user_name']},<br><br> Please click on the following link to reset your password: {$validationLink}";

		$this->email->message($message);

		$this->email->send();
	}
}