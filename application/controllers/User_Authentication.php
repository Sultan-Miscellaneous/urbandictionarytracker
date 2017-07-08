<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 
*/
class User_Authentication extends CI_Controller {
	
	public function __construct() {
		parent::__construct();

		// Load form helper library
		$this->load->helper('form');

		// Load form validation library
		$this->load->library('form_validation');

		// Load session library
		$this->load->library('session');

		// Load database
		$this->load->model('login_database');

		//load email
		$this->load->model('email_validation');
	}
	public function loadViewWithMessage($view,$message){
		$data = array('message' => $message);
		$this->load->view($view,$data);
	}

	public function show_forgotPassword_form(){
		$this->loadViewWithMessage('forgotPassword_form','Please provide username and email');
	}

	public function forgot_password_process(){
		$this->form_validation->set_rules('username',"Username","required|alpha_numeric|min_length[8]|trim");
		$this->form_validation->set_rules('email',"Email","required|valid_email|trim");
		if ($this->form_validation->run() == FALSE) //failed
		{
			$this->load->view('forgotPassword_form');
		}else{
			$formUserName = $this->input->post('username');
			$formUserEmail = $this->input->post('email');
			$user = $this->login_database->userExists($formUserName);
			if ($user !== false) {
				if ($user->user_email != $formUserEmail) {
					$this->loadViewWithMessage('forgotPassword_form','Invalid email specified');
				}
				else{
					$user = json_decode(json_encode($user), true);
					$this->email_validation->sendEmailtToUserForPasswordReset($user);
					$this->load->view('forgotPassword_emailSent_success');
				}
			}else{
				$this->loadViewWithMessage('forgotPassword_form','User does not exist');
			}
		}
	}

	public function reset_Password_form($resetCode){
		//check if reset code is valid, if it is go to reset password form
		if ($this->login_database->validateResetCode($resetCode)) {
			$data = array('resetCode' => $resetCode);
			$this->load->view('resetPassword_form',$data);
		}else{		//if not then go to forgotPassword_form to resend email
			$this->loadViewWithMessage('forgotPassword_form','Invalid reset code, please provide username and password to resend reset code');
		}
	}

	public function resetPassword(){
		$username = $this->login_database->decryptText($this->input->post('resetCode'));
		$this->form_validation->set_rules('oldPassword',"Old Password","required");
		$this->form_validation->set_rules('newPassword',"New Password","required");
		$this->form_validation->set_rules('ConfirmNewPassword',"Confirm Password","required|matches[newPassword]");
		if ($this->form_validation->run() == FALSE) //failed
		{
			$data = array('resetCode' => $this->input->post('resetCode'));
			$this->load->view('resetPassword_form');
		}else{
			if ($this->login_database->checkPassword($username,$this->input->oldPassword)) {

				$this->login_database->changePassword($username,$this->input->newPassword);
				$this->loadViewWithMessage('login_form','Password has been reset, please login');
			}
		}	
	}

	public function validateUser($id,$validationCode){
		$attemptValidation = $this->login_database->validateUser($id,$validationCode);
		$this->load->view('validation_result',array('message'=>$attemptValidation['message']));
	}


	// Show login page
	public function index() {
		if ($this->session->userdata('user_name') !== NULL) {
			$data = $this->session->userdata();
			$this->load->view('homepage',$data);
		}else{
			$this->loadViewWithMessage('login_form',"Please log in");
		}
	}
	// Show registration page
	public function show_signup_form(){
		$this->load->view('signup_form');
	}

	public function user_login_process(){
		$this->form_validation->set_rules('username',"Username","required|alpha_numeric");
		$this->form_validation->set_rules('password',"Password","required");
		if ($this->form_validation->run() == FALSE) //failed
		{
			$this->load->view('login_form');
		}
		else //success
		{
			$user = array('user_name' => $this->input->post('username'), 'user_password'=> $this->input->post('password'));
			$attemptLogin = $this->login_database->login($user);
			if ($attemptLogin['success'] === true) {	
				$attemptLogin['user'] = json_decode(json_encode($attemptLogin['user']),TRUE);  
				$this->session->set_userdata($attemptLogin['user']);
 				$this->load->view('login_success');
			}else{
				$this->loadViewWithMessage('login_form',$attemptLogin['message']);
			}
		}
	}
	public function logout(){
		$userData = array('username','email','loggedIn');
		session_destroy();
		$this->loadViewWithMessage('login_form','Please log in');
	}
	public function user_signup_process(){

		$this->form_validation->set_rules('username',"Username","required|alpha_numeric|min_length[8]|is_unique[user_login.user_name]|trim",array('is_unique' => "{field} already exists, choose a different {field}"));
		$this->form_validation->set_rules('password',"Password","required|min_length[8]");
		$this->form_validation->set_rules('confirm_password',"Confirm Password","required|matches[password]");
		$this->form_validation->set_rules('email',"Email","required|valid_email|is_unique[user_login.user_email]|trim",array('is_unique' => "{field} already exists, choose a different {field}"));

		if ($this->form_validation->run() == FALSE) //failed
		{
			$this->load->view('signup_form');
		}else{

			//register new user
			$user = array('user_name' => $this->input->post('username'),
				'user_password' => $this->input->post('password'),
				'user_email'=> $this->input->post('email'),
				'user_validated' => false,
				'user_validation_code' => uniqid($this->input->post('username'))
				);

			$user = $this->login_database->sanitizeUserDataAndHashPassword($user);
			if ($this->login_database->signup($user)) {
				$userId = $this->login_database->getUserId($user['user_name']);
				if ($userId === false) {
					$this->loadViewWithMessage('signup_form','New user registration failed, contact admin at urbandictionarytracker@gmail.com<br>');

				}else{
					$user['id'] = $userId;
					$this->loadViewWithMessage('login_form','User has been registered successfully and is pending validation');
					$this->email_validation->sendEmailToUserForValidation($user);
				}
			}else{
				$this->loadViewWithMessage('signup_form','New user registration failed, contact admin at urbandictionarytracker@gmail.com<br>');
			}
		}
	}


	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
}
