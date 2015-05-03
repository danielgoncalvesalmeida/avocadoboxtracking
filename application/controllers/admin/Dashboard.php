<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
    }
    
	public function index()
	{
        $this->setTitle('Desktop | '.$this->config->item('appname'));
        $dview = array();
        $this->display('dashboard', $dview);
    }
}

