<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shipping extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
        $this->load->model('shipping_model');
    }
    
	public function index()
	{
        $this->setTitle('Shipping | '.$this->config->item('appname'));
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
                'reference' => (isset($_GET['filter']['edreference']) ? $_GET['filter']['edreference'] : null),
                'username' => (isset($_GET['filter']['edusername']) ? $_GET['filter']['edusername'] : null),
            );
            $this->form_validation->set_data($filter_params);
            $this->form_validation->set_rules('reference','reference','alpha_numeric');
            
            if ($this->form_validation->run())
            {
                $filter = array();
                $filter['reference'] = array('value' => $filter_params['reference'], 'wildcard' => '{}%');
                $filter['username'] = array('value' => $filter_params['username'], 'wildcard' => '{}%');

                $dview['items'] = $this->shipping_model->getAll($this->p, $this->n, null, $filter);
                $dview['items_count'] = $this->shipping_model->getAllCount($filter);
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
            $dview['items'] = $this->shipping_model->getAll($this->p, $this->n);
            $dview['items_count'] = $this->shipping_model->getAllCount();
        }
      
        $this->display('shipping_list', $dview);
    }
    
    public function showAllOut()
	{
        $this->setTitle('Pack in outbound status | '.$this->config->item('appname'));
        $dview = array();
        
        $this->load->model('boxes_model');
        $dview['items'] = $this->boxes_model->getAllOut();
        
        $dview['link_back'] = 1;
      
        $this->display('box_list', $dview);
    }
    
    public function showAllIn()
	{
        $this->setTitle('Pack in inbound status | '.$this->config->item('appname'));
        $dview = array();
        
        $this->load->model('boxes_model');
        $dview['items'] = $this->boxes_model->getAllIn();
        
        $dview['link_back'] = 2;
      
        $this->display('box_list', $dview);
    }
    
    public function viewHistory($id)
    {
        $this->setTitle('Pack history | '.$this->config->item('appname'));
        $dview = array();
        $lback = $this->input->get('link_back');
        if($lback == 1)
            $dview['link_back'] = 'admin/box/showallout';
        if($lback == 2)
            $dview['link_back'] = 'admin/box/showallin';
        
        $dview['pack'] = $this->boxes_model->getitem((int)$id);
        $dview['history'] = $this->boxes_model->getHistory((int)$id);
       
        $this->display('box_history', $dview);
    }
}

