<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employees extends MY_Controller {
    
    public function __construct()
    {   
        
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
        $this->load->model('employees_model');
    }
    
    public function index()
	{
        // Handle pagination if provided
        $this->p = $this->input->get_post('p',true);
        $this->n = $this->input->get_post('n',true);
        
        (empty($this->p) ? $this->p = 1 : '' );
        (empty($this->n) ? $this->n = $this->config->item('results_per_page_default') : '' );
        $dview['p'] = $this->p;
        
		$this->setTitle('Employees | '.$this->config->item('appname'));
        
        $dview['employees'] = $this->employees_model->getEmployees($this->p, $this->n);
        $dview['employees_count'] = $this->employees_model->getEmployeesCount();
        
		$this->display('employees_list',$dview);
	}
    
    public function add()
    {
        $this->setTitle('Ajouter un employée - '.$this->config->item('appname'));
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('edlastname','nom','trim|required|xss_clean');
        $this->form_validation->set_rules('edfirstname','prénom','trim|required|xss_clean');
        $this->form_validation->set_rules('edsocialid','matricule sociale','trim|callback_checkvalidsocialid||xss_clean');
        $this->form_validation->set_rules('edbirthdate','date de naissance','required|callback_checkvaliddate|xss_clean');
        $this->form_validation->set_rules('edusername','nom d\'utilisateur','trim|alpha_numeric|callback_checkvalidusername|xss_clean');
        $this->form_validation->set_rules('edpassword','mot de pass','required|trim|xss_clean');
        $this->form_validation->set_rules('edemployeenumber','numéro employée','trim|callback_checkvalidemployeenumber|xss_clean');
        $this->form_validation->set_rules('edemail','email','trim|valid_email|callback_checkvalidemail|xss_clean');
        $this->form_validation->set_rules('edprofile','profile','integer|required|callback_checkRightProfileId|xss_clean');
        $this->form_validation->set_rules('edsite','site','integer|required|callback_checkSiteId|xss_clean');
                
        if ($this->form_validation->run())
        {
            // Save data
            $data = array(
                'id_default_site' => $this->input->post('edsite'),
                'id_domain' => (int)getUserDomain(),
                'id_right_profile' => (int)$this->input->post('edprofile'),
                'firstname' => ucwords($this->input->post('edfirstname')), 
                'lastname' => ucwords($this->input->post('edlastname')), 
                'socialid' => strtoupper($this->input->post('edsocialid')),
                'birthdate' => $this->input->post('edbirthdate'), 
                'username' => strtolower($this->input->post('edusername')),
                'password' => sha1($this->config->item('salt').$this->input->post('edpassword')),
                'use_passcode' => (validate_isUnsignedInt($this->input->post('edpassword'))? 1 : 0),
                'employee_number' => $this->input->post('edemployeenumber'),
                'email' => strtolower($this->input->post('edemail')),
                'tag' => genTag(),
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s')
            );
            $this->db->insert('user',$data);
            $dview['id_user'] = $this->db->insert_id();
            $dview['flash_success'] = 'Nouvel employée ajouté avec succès';
        }
        
        $this->load->model('profiles_model');
        $this->load->model('sites_model');
        $dview['profiles'] = $this->profiles_model->getProfiles();
        $dview['sites'] = $this->sites_model->getSites();
		$this->display('employees_add',$dview);
    }
    
    public function edit($id_user)
    {
        $this->setTitle('Editer l\'employée - '.$this->config->item('appname'));
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('edlastname','nom','trim|required|xss_clean');
        $this->form_validation->set_rules('edlastname','nom','trim|required|xss_clean');
        $this->form_validation->set_rules('edfirstname','prénom','trim|required|xss_clean');
        $this->form_validation->set_rules('edsocialid','matricule sociale','trim|callback_checkvalidsocialid['.$id_user.']|xss_clean');
        $this->form_validation->set_rules('edbirthdate','date de naissance','required|callback_checkvaliddate['.$id_user.']|xss_clean');
        $this->form_validation->set_rules('edusername','nom d\'utilisateur','trim|alpha_numeric|callback_checkvalidusername['.$id_user.']|xss_clean');
        $this->form_validation->set_rules('edpassword','mot de pass','trim|min_length[3]|integer|xss_clean');
        $this->form_validation->set_rules('edemployeenumber','numéro employée','trim|callback_checkvalidemployeenumber['.$id_user.']|xss_clean');
        $this->form_validation->set_rules('edemail','email','trim|valid_email|callback_checkvalidemail['.$id_user.']|xss_clean');
        $this->form_validation->set_rules('edprofile','profile','integer|required|callback_checkRightProfileId|xss_clean');
        $this->form_validation->set_rules('edsite','site','integer|required|callback_checkSiteId|xss_clean');
                
        if ($this->form_validation->run())
        {
            // Save data
            $data = array(
                'id_default_site' => $this->input->post('edsite'),
                'id_right_profile' => (int)$this->input->post('edprofile'),
                'firstname' => ucwords($this->input->post('edfirstname')), 
                'lastname' => ucwords($this->input->post('edlastname')), 
                'socialid' => strtoupper($this->input->post('edsocialid')),
                'birthdate' => $this->input->post('edbirthdate'), 
                'username' => strtolower($this->input->post('edusername')),
                'employee_number' => $this->input->post('edemployeenumber'),
                'email' => strtolower($this->input->post('edemail')),
                'date_upd' => date('Y-m-d H:i:s')
            );

            if(strlen($this->input->post('edpassword')) >= 3)
            {
                $data['password'] = sha1($this->config->item('salt').$this->input->post('edpassword'));
                $data['use_passcode'] = (validate_isUnsignedInt($this->input->post('edpassword'))? 1 : 0);
            }
            $this->db->where('id_user',$id_user);
            $this->db->update('user',$data);
            $dview['flash_success'] = 'Modifications enregistrées avec succès';
            
        }
        
        $dview['employee'] = $this->employees_model->getEmployee($id_user);
        $this->load->model('profiles_model');
        $this->load->model('sites_model');
        $dview['profiles'] = $this->profiles_model->getProfiles();
        $dview['sites'] = $this->sites_model->getSites();
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
            $this->form_validation->set_rules('edpassword1','password 1','trim|required|min_length[3]|integer|xss_clean');
            $this->form_validation->set_rules('edpassword2','password 2','trim|required|matches[edpassword1]|xss_clean');
            if ($this->form_validation->run())
            {
                $data['password'] = sha1($this->config->item('salt').$this->input->post('edpassword1'));
                $data['use_passcode'] = (validate_isUnsignedInt($this->input->post('edpassword'))? 1 : 0);
                
                $this->db->where('id_user',(int)getUserId());
                $this->db->update('user',$data);
                $dview['flash_success'] = 'Votre nouveau password fut enregistré avec succès';
            }
            else
                $dview['flash_error'] = validation_errors();
        }
        
        $dview['link_back'] = 'admin/desktop';
        $this->display('employees_editpassword',$dview);
    }
    
    public function delete($id)
    {
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
            $this->form_validation->set_message('checkvalidusername','Le nom d\'utilisateur est déjà utilisé ! Veuillez choisir un autre.');
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
            $this->form_validation->set_message('checkRightProfileId','Le %s indiqué n\'existe pas ! Veuillez choisir un autre.');
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