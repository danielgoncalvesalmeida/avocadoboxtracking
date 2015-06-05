<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employees extends MY_Controller {
    
    public function __construct()
    {   
        
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
        $this->load->model('employees_model');
        $this->addJs('admin_employees.js');
    }
    
    public function index()
	{
        if(!_cr('employees')) return warning_noaccess();
        
        // Handle pagination if provided
        $this->p = $this->input->get_post('p',true);
        $this->n = $this->input->get_post('n',true);
        
        (empty($this->p) ? $this->p = 1 : '' );
        (empty($this->n) ? $this->n = $this->config->item('results_per_page_default') : '' );
        $dview['p'] = $this->p;
        
		$this->setTitle('Employees | '.$this->config->item('appname'));
        
        // Handle filter
        if(isset($_GET['filter']))
        {
            $this->load->library('form_validation');
            $filter_params = array(
                'firstname' => (isset($_GET['filter']['edfirstname']) ? $_GET['filter']['edfirstname'] : null),
                'lastname' => (isset($_GET['filter']['edlastname']) ? $_GET['filter']['edlastname'] : null),
                'username' => (isset($_GET['filter']['edusername']) ? $_GET['filter']['edusername'] : null),
                'userprofile' => (isset($_GET['filter']['eduserprofile']) && $_GET['filter']['eduserprofile'] > 0 ? $_GET['filter']['eduserprofile'] : null),
                'status' => (isset($_GET['filter']['edstatus']) && $_GET['filter']['edstatus'] > 0 ? $_GET['filter']['edstatus'] : null),
                
            );
            $this->form_validation->set_data($filter_params);
            $this->form_validation->set_rules('firstname','firstname','alpha_numeric');
            $this->form_validation->set_rules('lastname','lastname','alpha_numeric');
            $this->form_validation->set_rules('userprofile','right profile','numeric');
            $this->form_validation->set_rules('status','status','numeric');

            if ($this->form_validation->run())
            {
                $filter = array();
                if(!empty($filter_params['firstname']))
                    $filter[] = array('field' => 'u.firstname', 'value' => $filter_params['firstname'], 'wildcard' => '%{}%');
                if(!empty($filter_params['lastname']))
                    $filter[] = array('field' => 'u.lastname', 'value' => $filter_params['lastname'], 'wildcard' => '%{}%');
                if(!empty($filter_params['username']))
                    $filter[] = array('field' => 'u.username', 'value' => $filter_params['username'], 'wildcard' => '%{}%');
                if(!empty($filter_params['userprofile']))
                    $filter[] = array('field' => 'rp.id_right_profile', 'value' => $filter_params['userprofile'], 'isnumeric' => true);
                if(!empty($filter_params['status']))
                {
                    if($filter_params['status'] == 1)
                        $filter[] = array('field' => 'u.active', 'value' => true, 'isnumeric' => true);
                    elseif($filter_params['status'] == 2)
                        $filter[] = array('field' => 'u.active', 'value' => false, 'isnumeric' => true);
                }
                    
                $dview['employees'] = $this->employees_model->getEmployees($this->p, $this->n, null, $filter);
                $dview['employees_count'] = $this->employees_model->getEmployeesCount($filter);
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
        if(!isset($dview['employees']))
        {
            $dview['employees'] = $this->employees_model->getEmployees($this->p, $this->n);
            $dview['employees_count'] = $this->employees_model->getEmployeesCount();
        }
        
        $this->load->model('profiles_model');
        $dview['userprofiles'] = $this->profiles_model->getProfiles();
        
		$this->display('employees_list',$dview);
	}
    
    public function add()
    {
        if(!_cr('employees')) return warning_noaccess();
        
        $this->setTitle('Add an employee - '.$this->config->item('appname'));
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('edlastname','lastname','trim|required');
        $this->form_validation->set_rules('edfirstname','firstname','trim|required');
        $this->form_validation->set_rules('edusername','username','trim|required|callback_checkvalidusername');
        $this->form_validation->set_rules('edpassword','password','required|trim');   
        $this->form_validation->set_rules('edprofile','profile','integer|required|callback_checkRightProfileId');

        if ($this->form_validation->run())
        {
           
            // Save data
            $data = array(
                'id_domain' => (int)getUserDomain(),
                'id_right_profile' => (int)$this->input->post('edprofile'),
                'firstname' => ucwords($this->input->post('edfirstname')), 
                'lastname' => ucwords($this->input->post('edlastname')), 
                'username' => strtolower($this->input->post('edusername')),
                'password' => sha1($this->config->item('salt').$this->input->post('edpassword')),
                'use_passcode' => false,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s')
            );
            $this->db->insert('user',$data);
            $dview['id_user'] = $this->db->insert_id();
            $dview['flash_success'] = 'New employee succesfully added';
        }
        
        $this->load->model('profiles_model');
        $dview['profiles'] = $this->profiles_model->getProfiles();
		$this->display('employees_add',$dview);
    }
    
    public function edit($id_user)
    {
        if(!_cr('employees')) return warning_noaccess();
        
        $this->setTitle('Edit the employee - '.$this->config->item('appname'));
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('edlastname','lastname','trim|required');
        $this->form_validation->set_rules('edfirstname','firstname','trim|required');
        $this->form_validation->set_rules('edusername','username','trim|callback_checkvalidusername['.$id_user.']');
        $this->form_validation->set_rules('edprofile','profile','integer|required|callback_checkRightProfileId');
        $this->form_validation->set_rules('edenabled','enabled','integer');
                
        if ($this->form_validation->run())
        {
            // Protect user from disable himself
            if($id_user == getUserId())
                $enabled = 1;
            else
                $enabled = (int)$this->input->post('edenabled');
            // Save data
            $data = array(
                'id_right_profile' => (int)$this->input->post('edprofile'),
                'firstname' => ucwords($this->input->post('edfirstname')), 
                'lastname' => ucwords($this->input->post('edlastname')), 
                'active' => ($enabled == 1 ? true : false),
                'username' => strtolower($this->input->post('edusername')),
                'date_upd' => date('Y-m-d H:i:s')
            );

            if(strlen($this->input->post('edpassword')) >= 3)
            {
                $data['password'] = sha1($this->config->item('salt').$this->input->post('edpassword'));
                $data['use_passcode'] = (validate_isUnsignedInt($this->input->post('edpassword'))? 1 : 0);
            }
            $this->db->where('id_user',$id_user);
            $this->db->update('user',$data);
            $dview['flash_success'] = 'Modifications successfully saved!';
            
        }
        
        $dview['employee'] = $this->employees_model->getEmployee($id_user);
        $this->load->model('profiles_model');
        $dview['profiles'] = $this->profiles_model->getProfiles();
		$this->display('employees_edit',$dview);
    }
    
    /**
     *  editpassword
     *  User can change password
     */
    function editpassword()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        // For security reasons get the id user from the session
        $id_user = getUserId();
  
        if($this->input->post('submitSave'))
        {
            $this->form_validation->set_rules('edpassword1','password 1','trim|required|min_length[3]');
            $this->form_validation->set_rules('edpassword2','password 2','trim|required|matches[edpassword1]');
            if ($this->form_validation->run())
            {
                $data['password'] = sha1($this->config->item('salt').$this->input->post('edpassword1'));
                $data['use_passcode'] = (validate_isUnsignedInt($this->input->post('edpassword'))? 1 : 0);
                
                $this->db->where('id_user',(int)getUserId());
                $this->db->update('user',$data);
                $dview['flash_success'] = 'Your new password was successfully saved';
                
                $this->load->model('log_model');
                $this->log_model->log(array('type' => 1, 'operation' => 5, 'message' => 'User successfully changed his password'));
            }
            else
                $dview['flash_error'] = validation_errors();
        }
        
        $dview['link_back'] = 'admin/dashboard';
        $this->display('employees_editpassword',$dview);
    }
    
    public function delete($id)
    {
        if(!_cr('employees')) return warning_noaccess();
        $this->employees_model->delete((int)$id);
        redirect('admin/employees');
    }
    
    /*
     *  Multipurpose AJAX call : Purpose is based on type
     */
    public function ajax()
    {
        if(!$this->input->is_ajax_request())
            die();
        
        $type = $this->input->get_post('type',true);
        
        /*
         *  Check if the provided socialid exists
         */
        if($type == 1)
        {
            $name = $this->input->get_post('value',true);
            $id_exclude = $this->input->get_post('exclude',true);
            $id_exclude = (!empty($id_exclude)? $id_exclude : null);
            if($this->employees_model->socialidExists($name, $id_exclude))
                echo json_encode (true);
            else
                echo json_encode (false);
        }
        
        /*
         * Check if the provided birthdate is correct
         * Returns true if correct date
         */
        if($type == 2)
        {
            $birthdate = $this->input->get_post('value',true);
            $_tmp = explode('-', $birthdate);
            // Not a valid date
            if(count($_tmp) != 3 || empty($_tmp[2]))
            {
                echo json_encode (false);
                return false;
            }
            
            if(checkdate($_tmp[1], $_tmp[2], $_tmp[0]))
                echo json_encode (true);
            else
                echo json_encode (false);
        }
        
        /*
         *  Check if the provided employee number exists
         */
        if($type == 3)
        {
            $name = $this->input->get_post('value',true);
            $id_exclude = $this->input->get_post('exclude',true);
            $id_exclude = (!empty($id_exclude)? $id_exclude : null);
            if($this->employees_model->employeenumberExists($name, $id_exclude))
                echo json_encode (true);
            else
                echo json_encode (false);
        }
        
        /*
         *  Check if the provided employee email exists
         */
        if($type == 4)
        {
            $name = $this->input->get_post('value',true);
            $id_exclude = $this->input->get_post('exclude',true);
            $id_exclude = (!empty($id_exclude)? $id_exclude : null);
            if($this->employees_model->emailExists($name, $id_exclude))
                echo json_encode (true);
            else
                echo json_encode (false);
        }
        
        /*
         *  Check if the provided employee username exists
         */
        if($type == 5)
        {
            $name = $this->input->get_post('value',true);
            $id_exclude = $this->input->get_post('exclude',true);
            $id_exclude = (!empty($id_exclude)? $id_exclude : null);
            if($this->employees_model->usernameExists($name, $id_exclude))
                echo json_encode (true);
            else
                echo json_encode (false);
        }
    }
    
    /*
     *  Form validation callback
     */
    public function checkvaliddate($str)
    {
        $_tmp = explode('-', $str);
        // Not a valid date
        if(count($_tmp) != 3){
            $this->form_validation->set_message('checkvaliddate', 'Date non valide');
            return false;
        }

        if(checkdate($_tmp[1], $_tmp[2], $_tmp[0]))
            return true;
        else {
            $this->form_validation->set_message('checkvaliddate', 'Date non valide');
            return false;
        }       
    }
    
    /*
     *  Form validation callback
     */
    public function checkvalidsocialid($str, $exclude = null)
    {
        if($this->employees_model->socialidExists($str, $exclude))
        {
            $this->form_validation->set_message('checkvalidsocialid','Le numéro social est déjà attribué ! Veuillez indiquer un autre.');
            return false;
        }
        else
            return true;
    }
    
    /*
     *  Form validation callback
     */
    public function checkvalidusername($str, $exclude = null)
    {
        if($this->employees_model->usernameExists($str, $exclude))
        {
            $this->form_validation->set_message('checkvalidusername','The given username is already assigned. Please try another one!');
            return false;
        }
        else
            return true;
    }
    
    /*
     *  Form validation callback
     */
    public function checkvalidemployeenumber($str, $exclude = null)
    {
        if($this->employees_model->employeeNumberExists($str, $exclude))
        {
            $this->form_validation->set_message('checkvalidemployeenumber','Le numéro d\'employée est déjà attribuée ! Veuillez indiquer une autre.');
            return false;
        }
        else
            return true;
    }
    
    /*
     *  Form validation callback
     */
    public function checkvalidemail($str, $exclude = null)
    {
        if($this->employees_model->emailExists($str, $exclude))
        {
            $this->form_validation->set_message('checkvalidemail','L\'adresse est déjà attribuée ! Veuillez indiquer une autre.');
            return false;
        }
        else
            return true;
    }
    
    
    /*
     * Callback function for form validation
     */
    public function checkRightProfileId($id)
    {
        $this->load->model('profiles_model');
        if ($this->profiles_model->checkRightProfileIsValid($id))
            return true;
        else
        {
            $this->form_validation->set_message('checkRightProfileId','Profile %s doesn\'t exist! Please select a valid user profile!');
            return false;
        }
    }
    
    
    /*
     * Callback function for form validation
     */
    public function checkSiteId($id)
    {
        $this->load->model('sites_model');
        if ($this->sites_model->checkSiteIsValid($id))
            return true;
        else
        {
            $this->form_validation->set_message('checkSiteId','Le %s indiqué n\'existe pas ! Veuillez choisir un autre.');
            return false;
        }
    }
    
    
    
}