<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Box extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
        $this->load->model('boxes_model');
    }
    
	public function index()
	{
        $this->setTitle('Pack | '.$this->config->item('appname'));
        $dview = array();
        
        
        $dview['items'] = $this->boxes_model->getAll();
      
        $this->display('box_list', $dview);
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

