<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
    {
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
	}


    public function index() 
    {
        redirect('/tickets/all', 'refresh');
    }


    public function my_profile()
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/home/my_profile_view', $data);
        $this->load->view('app/footer/footer');
    }


    public function change_password()
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/home/change_password_view', $data);
        $this->load->view('app/footer/footer');
    }


} //class ends
