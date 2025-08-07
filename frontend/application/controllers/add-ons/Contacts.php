<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contacts extends CI_Controller {

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
        $this->load->view('app/contacts/contacts_view', $data);
        $this->load->view('app/footer/footer');
    }


} //class ends
