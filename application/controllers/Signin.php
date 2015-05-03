<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signin extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
    }
    
	public function index()
	{
        $dview = array();
        $this->addCss('signin.css');
        $this->setTitle( $this->config->item('appname').' | '.$this->config->item('appcompany') );
        
        $this->load->library('form_validation');

		$this->form_validation->set_rules('edusr', 'username', 'required');
		$this->form_validation->set_rules('edpwd', 'password', 'required');
        
        // Authenticate
		if ( $this->form_validation->run() )
		{

			$this->load->model('login_model');
			if ( !$this->login_model->authenticate($this->input->post('edusr'),$this->input->post('edpwd'), $redirect = 'admin/dashboard') )
            {
                $dview['authfailed'] = 'Username or password are not correct!';
            }
		}
		else 
        {
			$this->form_validation->set_message('required','Username or password are not correct!');
		}

        $this->display('admin/login', $dview, $loadOnlyView = true);
        //$this->load->view('admin/login');
    }
}

