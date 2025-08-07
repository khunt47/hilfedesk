<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
    {
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
	}


    public function index()
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/admin/admin_view', $data);
        $this->load->view('app/footer/footer');
    }


    public function projects()
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/admin/projects/projects_admin_view', $data);
        $this->load->view('app/footer/footer');
    }



    public function manage_users() 
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/admin/users/manage_users_view', $data);
        $this->load->view('app/footer/footer');
    }


    public function create_user() 
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/admin/users/create_user_view', $data);
        $this->load->view('app/footer/footer');
    }


    public function timezone() 
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/admin/timezone/timezone_view', $data);
        $this->load->view('app/footer/footer');
    }

} //class ends
