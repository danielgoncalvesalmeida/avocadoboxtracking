<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
        $this->load->model('customer_model');
    }
    
	public function index()
	{
        $this->setTitle('Customers | '.$this->config->item('appname'));
        $dview = array();

        // Handle pagination if provided
        $this->p = $this->input->get_post('p',true);
        $this->n = $this->input->get_post('n',true);
        
        (empty($this->p) ? $this->p = 1 : '' );
        (empty($this->n) ? $this->n = $this->config->item('results_per_page_default') : '' );
        $dview['p'] = $this->p;

        // Handle filter
        if(isset($_GET['filter']))
        {
            $this->load->library('form_validation');
            $filter_params = array(
                'username' => (isset($_GET['filter']['edusername']) ? $_GET['filter']['edusername'] : null),
            );
            $this->form_validation->set_data($filter_params);
            $this->form_validation->set_rules('username','username','alpha_numeric');
            if ($this->form_validation->run())
            {
                $filter = array();
                $filter[] = array('field' => 's.username', 'value' => $filter_params['username'], 'wildcard' => '%{}%');
                
                    
                $dview['items'] = $this->customer_model->getAll($this->p, $this->n, null, $filter);
                $dview['items_count'] = $this->customer_model->getAllCount($filter);
                $dview['items_filtered'] = true;
                $dview['filter'] = $_GET['filter'];
                $filter_url_params = '';
                foreach($_GET['filter'] as $p => $v)
                    $filter_url_params .= '&filter['.$p.']='.$v;
                $dview['filter_url_params'] = $filter_url_params;
            }
            else
                $dview['filter_errors'] = true;
        }
        
        // Set the recordset for unfiltred
        if(!isset($dview['items']))
        {
            $dview['items'] = $this->customer_model->getAll($this->p, $this->n);
            $dview['items_count'] = $this->customer_model->getAllCount();
        }
        $this->display('customer_list', $dview);
    }
    
    public function viewHistory($username)
    {
        $this->setTitle('Customer history | '.$this->config->item('appname'));
        $dview = array();
        
        $filter_url_params = '';
        if(isset($_GET['p']))
            $filter_url_params .= '&p='.$_GET['p'];
        if(isset($_GET['n']))
            $filter_url_params .= '&n='.$_GET['n'];
        if(isset($_GET['filter']))
        {
            foreach($_GET['filter'] as $p => $v)
                $filter_url_params .= '&filter['.$p.']='.$v;
        }
            
        
        $link_back = 'admin/customer/?'.$filter_url_params;
        $dview['link_back'] = $link_back;
        
        
        $dview['history'] = $this->customer_model->getHistory($username);
        $dview['username'] = $username;

        $this->display('customer_history', $dview);
    }
}

