<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
        $this->load->model('log_model');
    }
    
	public function index()
	{
        $this->setTitle('Log | '.$this->config->item('appname'));
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
                'user' => (isset($_GET['filter']['eduser']) && $_GET['filter']['eduser'] > 0 ? $_GET['filter']['eduser'] : null),
                'type' => (isset($_GET['filter']['edtype']) && $_GET['filter']['edtype'] > 0 ? $_GET['filter']['edtype'] : null),
                'operation' => (isset($_GET['filter']['edoperation']) && $_GET['filter']['edoperation'] > 0 ? $_GET['filter']['edoperation'] : null),
            );
            
            $this->form_validation->set_data($filter_params);
            $this->form_validation->set_rules('user','user','numeric');
            $this->form_validation->set_rules('operation','operation','numeric');
            
            if ($this->form_validation->run())
            {
                $filter = array();
                $filter['u.id_user'] = array('value' => $filter_params['user'], 'isnumeric' => true);
                $filter['type'] = array('value' => $filter_params['type'], 'isnumeric' => true);
                $filter['operation'] = array('value' => $filter_params['operation'], 'isnumeric' => true);

                $dview['items'] = $this->log_model->getAll($this->p, $this->n, null, $filter);
                $dview['items_count'] = $this->log_model->getAllCount($filter);
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
            $dview['items'] = $this->log_model->getAll($this->p, $this->n);
            $dview['items_count'] = $this->log_model->getAllCount();
        }
        
        $this->load->model('employees_model');
        $dview['users'] = $this->employees_model->getEmployees();
        $dview['operations'] = $this->log_model->getOperations();
      
        $this->display('log_list', $dview);
    }
    
    
}

