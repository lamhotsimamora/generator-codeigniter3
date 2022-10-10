<?php
defined('BASEPATH') or exit('No direct script access allowed');

class View extends CI_Controller
{
	public function index(){
		$this->load->view('view');
	}

}
