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
                'barcode' => (isset($_GET['filter']['edbarcode']) ? $_GET['filter']['edbarcode'] : null),
                'status' => (isset($_GET['filter']['edstatus']) ? $_GET['filter']['edstatus'] : null),
            );
            $this->form_validation->set_data($filter_params);
            $this->form_validation->set_rules('barcode','barcode','alpha_numeric');
            $this->form_validation->set_rules('status','status','numeric');
            if ($this->form_validation->run())
            {
                $filter = array();
                $filter['barcode'] = array('value' => $filter_params['barcode'], 'wildcard' => '{}%');
                $status = null;
                if($filter_params['status'] == 1)
                    $status = 1;
                if($filter_params['status'] == 2)
                    $status = 2;
                    
                $dview['items'] = $this->boxes_model->getAll($this->p, $this->n, null, $filter, $status);
                $dview['items_count'] = $this->boxes_model->getAllCount($filter, $status);
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
            $dview['items'] = $this->boxes_model->getAll($this->p, $this->n);
            $dview['items_count'] = $this->boxes_model->getAllCount();
        }
      
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

