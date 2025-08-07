<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Signup extends CI_Controller 
{

	protected $client = '';
	protected $client_id = '';
	protected $client_secret = '';
	protected $redirect_uri = '';

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('form');
		
		$this->client_id = $this->config->item('client_id');
		$this->client_secret = $this->config->item('client_secret');
		$this->redirect_uri = $this->config->item('redirect_uri');
	}



	public function index()
	{
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header_login');
        $this->load->view('app/signup/signup_view', $data);
        $this->load->view('app/footer/footer_login');
	}


	public function confirm($confirm_code)
	{
        $data['api_base_url'] = $this->config->item('api_base_url');
        $data['confirm_code'] = $confirm_code;
        $this->load->view('app/header/header_login');
        $this->load->view('app/signup/confirm_signup_view', $data);
        $this->load->view('app/footer/footer_login');
	}



	// public function google_signup() 
    // {
    // 	$client = new \Google_Client();
	// 	$client->setClientId($this->client_id);
	// 	$client->setClientSecret($this->client_secret);
	// 	$client->setRedirectUri($this->redirect_uri);
	// 	$client->addScope("email");
	// 	$client->addScope("profile");
	// 	$google_login_url = $client->createAuthUrl();
    //     redirect($google_login_url, 'refresh');
    // }


    // private function google_signup_process() 
	// {
	// 	$client = new \Google_Client();
	// 	$client->setClientId($this->client_id);
	// 	$client->setClientSecret($this->client_secret);
	// 	$client->setRedirectUri($this->redirect_uri);
	// 	$client->addScope("email");
	// 	$client->addScope("profile");
	// 	$data = [];
	// 	// authenticate code from Google OAuth Flow
	// 	if (isset($_GET['code'])) {
	// 		$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
	// 		if (!isset($token['access_token'])) {
	// 		  	$data = array(
	// 				'login' => 'no',
	// 		  		'email' => ''
	// 		  	);
	// 		  	return $data;
	// 	  	}
	// 	  	else {
	// 	  		$client->setAccessToken($token['access_token']);
	// 	  		// get profile info
	// 	  		$google_oauth = new \Google_Service_Oauth2($client);
	// 			$google_account_info = $google_oauth->userinfo->get();
	// 			$email =  $google_account_info->email;
	// 			$name =  $google_account_info->name;
	// 			/*
	// 			set session here
	// 			*/
	// 			$session_data = array(
    //                 'team_tasks_google_auth' => 'yes'
    //             );
                
    //             $this->session->set_userdata($session_data);

	// 			$data = array(
	// 				'login' => 'yes',
	// 			  	'email' => $email, 
	// 			  	'auth_url' => ''
	// 			);
	// 			return $data;
	// 		}
	// 	}
	// 	else {
	// 		//return error
	// 	}
	// }


    // public function google_signup_redirect() 
    // {
    //     $login_result = $this->google_signup_process();
    //     $api_key_created = false;
    //     if ($this->session->userdata('team_tasks_google_auth') == 'yes') {
    //     	if ($login_result['login'] === 'yes') {
	//             $user_email = $login_result['email'];
	//             /*
	//             Check if email is a valid email
	//             Make a post call via JS 
	//             */
	//             $data['api_base_url'] = $this->config->item('api_base_url');
	//             $data['user_email'] = $user_email;
	// 	        $this->load->view('app/header/header_login');
	// 	        $this->load->view('app/login/google_login_redirect', $data);
	// 	        $this->load->view('app/footer/footer_login');
	//         }
	//         else {
	//         	$this->google_login();
	//         }
    //     }
    //     else {
    //     	redirect('/login', 'refresh');
    //     }
    // }


} //class ends
