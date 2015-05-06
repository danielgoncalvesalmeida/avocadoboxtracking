<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
    }
    
	public function index()
	{
        $this->load->helper('form');
        $this->setTitle('Dashboard | '.$this->config->item('appname'));
        $dview = array();
        
        $this->load->model('boxes_model');
        
        $isQuickSearch = $this->input->post('submitQuicksearch');
        if($isQuickSearch)
        {
            $this->load->model('boxes_model');
            $search = $this->input->post('search');
         
            $type = $this->boxes_model->referenceIs(strtoupper( $search ));
            if($type === 'PACK')
            {
                $dview['quick_search_packfound'] = $this->boxes_model->getStatusByBarcode($search);
            }
            elseif($type === 'SHIPPING')
            {
                
            }
            
        }
            
        
        
        $dview['outbound_count'] = $this->boxes_model->getAllOut();
        $dview['inbound_count'] = $this->boxes_model->getAllIn();
      
        $this->display('dashboard', $dview);
    }
}

