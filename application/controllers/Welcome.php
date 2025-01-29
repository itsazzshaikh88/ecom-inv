<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends App_Controller
{

	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$this->isUserAuthenticated();
		$data['view_path'] = 'pages/home';
		$this->load->view('layout', $data);
	}
}
