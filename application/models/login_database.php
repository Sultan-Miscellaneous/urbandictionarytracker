<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class login_database extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function userExists($userName){
		$this->db->select('*');
		$this->db->from("user_login");
		$this->db->where("user_name",$userName);
		$result = $this->db->get();
		if ($result->num_rows()===1) {
			return $result->row();
		}else{
			return false;
		}
	}

	public function getUserId($userName){
		$user = $this->userExists($userName);
		if ($user === false) {
			return false;
		}else{
			return $user->id;
		}
	}

	public function validateUser($id,$validationCode){
		$this->db->select('user_name,user_validation_code');
		$this->db->from("user_login");
		$this->db->where("id",$id);
		$result = $this->db->get();
		if ($result->num_rows()===1) {
			$user = $result->row();
			if ($user->user_validation_code === $validationCode) {
				$this->db->set('user_validated',true);
				$this->db->set('user_validation_code', uniqid($user->user_name));
				$this->db->where('id',$id);
				$this->db->update('user_login');
				return array('Success' => true,'message' => 'User has been verified successfully');
			}else{
				return array('Success' => false,'message' => 'Invalid Validation Link, contact admin at urbandictionarytracker@gmail.com');
			}
		}else{
			return array('Success' => false,'message' => 'User does not exist, contact admin at urbandictionarytracker@gmail.com');
		}
	}

	public function encryptText($plaintext){
		$method = 'aes-256-ctr';
        $ivlen = openssl_cipher_iv_length($method);
        $clefSecrete = openssl_random_pseudo_bytes($ivlen);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $encrypted = openssl_encrypt($plaintext, $method, $clefSecrete, OPENSSL_RAW_DATA, $iv);

		return urlencode($encrypted);
	}

	public function decryptText($plaintext){
		$method = 'aes-256-ctr';
        $ivlen = openssl_cipher_iv_length($method);
        $clefSecrete = openssl_random_pseudo_bytes($ivlen);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $decrypted = openssl_decrypt(urldecode($plaintext), $method, $clefSecrete, OPENSSL_RAW_DATA, $iv);

		return $decrypted;
	}

	public function testEncryptDecrypt($text){
		return $this->decryptText($this->encryptText($text));
	}

	public function validateResetCode($resetPasswordCode){
		$this->db->select('user_name');
		$this->db->from("user_login");
		$this->db->where("user_name",$this->decryptText($resetPasswordCode));
		$result = $this->db->get();
		if ($result->num_rows()===1) {
			return array('Success' => true,'message' => 'Valid Reset Code');
		}else{
			return array('Success' => false,'message' => 'Invalid Reset Code');
		}
	}

	public function checkPassword($username, $password){
		$user = array('user_name' => $username,'user_password'=>$password);
		return $this->login($user);
	}

	public function login($user){
		$userInDatabase = $this->userExists($user['user_name']);
		if ($userInDatabase === false) {
			$result = array('success' => false,'message'=>'Username does not exist');
			return $result;
		}else{
			//check for valid password
			$validate = password_verify($user['user_password'],$userInDatabase->user_password);
			if ($validate === true) {
				unset($userInDatabase->user_password);
				unset($userInDatabase->user_validated);
				unset($userInDatabase->user_validation_code);
				$result = array('success' => true, 'message'=>'Login Success','user'=>$userInDatabase);
				return $result;
			}else{
				$result = array('success' => false, 'message'=>'Incorrect Username/Password');
				return $result;
			}
		}
	}

	public function signup($user){
		return $this->db->insert('user_login',$user);
	}

	public function hello(){
		echo "Hello";
	}

	public function sanitizeUserDataAndHashPassword($user){

		$user['user_name'] = $this->db->escape_str($user['user_name']);

		$user['user_password'] = password_hash($user['user_password'], PASSWORD_DEFAULT);

		$user['user_email'] = $this->db->escape_str($user['user_email']);

		return $user;
	}

}