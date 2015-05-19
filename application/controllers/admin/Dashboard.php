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
            
            $this->load->model('shipping_model');
            // Find if it is a user
            $by_user = $this->shipping_model->getByUser($search);
            $found_user = false;
       
            if($by_user)
            {
                $dview['quick_search_userfound'] = $by_user;
                $found_user = true;
            }
            if(!$found_user){
                $type = $this->boxes_model->referenceIs(strtoupper( $search ));
                if($type === 'PACK')
                {
                    $result = $this->boxes_model->getQuickSearchByBarcode($search);
                    if($result !== false)
                        $dview['quick_search_packfound'] = $result;
                    else
                        $dview['quick_serach_notfound'] = true;
                }
                elseif($type === 'SHIPPING')
                {
                    $result = $this->shipping_model->getQuickSearchByReference($search);
                    if($result !== false)
                        $dview['quick_search_shippingfound'] = $result;
                    else
                        $dview['quick_serach_notfound'] = true;
                }
                else
                    $dview['quick_serach_notfound'] = true;
            }
        }
            
        
        
        $dview['outbound_count'] = $this->boxes_model->getAllOut();
        $dview['inbound_count'] = $this->boxes_model->getAllIn();
 
        $this->display('dashboard', $dview);
    }
}

