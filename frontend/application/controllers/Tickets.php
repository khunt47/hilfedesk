<?php defined('BASEPATH') OR exit('No direct script access allowed');

 	/**
    *
    **This class contains methods related to autentication of a login user.
    *
    **@author Krishnan ks@geedesk.com
    *
    *
    **/

class Tickets extends CI_Controller 
{

	public function __construct()
    {
		parent::__construct();
	}

    public function index() 
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/tickets/tickets_index', $data);
        $this->load->view('app/footer/footer');
    }

    public function projects()
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/tickets/projects/projects_view', $data);
        $this->load->view('app/footer/footer');
    }

    public function create_project()
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/tickets/projects/create_project_view', $data);
        $this->load->view('app/footer/footer');
    }


    public function project_tickets($project_id)
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $data['project_id'] = $project_id;
        $this->load->view('app/header/header');
        $this->load->view('app/tickets/tickets_view', $data);
        $this->load->view('app/footer/footer');
    }


    public function create()
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $this->load->view('app/header/header');
        $this->load->view('app/tickets/ticket_create_view', $data);
        $this->load->view('app/footer/footer');
    }


    public function ticket_details($project_id, $ticket_id)
    {
        $data['api_base_url'] = $this->config->item('api_base_url');
        $data['linode_base_url'] = $this->config->item('linode_base_url');
        $data['project_id'] = $project_id;
        $data['ticket_id'] = $ticket_id;
        $this->load->view('app/header/header');
        $this->load->view('app/tickets/ticket_details_view', $data);
        $this->load->view('app/footer/footer');
    }


} //class ends
