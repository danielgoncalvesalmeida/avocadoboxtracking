<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_Model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

    /*
     * Authenticate, populate session vars and redirect to dashbord
     */
	public function authenticate($usr, $pwd, $redirect = null)
	{
        $sql = "SELECT * FROM ".$this->db->dbprefix('user')."
                WHERE `username` = ? 
                AND `password` = ? 
                AND `deleted` = 0
                AND `active` = 1
                LIMIT 1";
        $result = $this->db->query($sql, array($usr, sha1($this->config->item('salt').$pwd)));
		if ( $result->num_rows() > 0 )
        {
			$user = $result->row();

            // Retrieve full information from user
            $sql = "SELECT u.*, d.tag AS domain_tag, d.id_domain, d.name AS domain_name, d.ismultisite AS domain_ismultisite 
                FROM ".$this->db->dbprefix('user')." u,
                ".$this->db->dbprefix('domain')." d
                WHERE u.`id_user` = ?
                AND u.`id_domain` = d.`id_domain`
                AND u.`active` = 1";
            $result = $this->db->query($sql, array($user->id_user));
            $user = $result->row();

           $data = array(
               'zone' => 'app',
               'id_user' => $user->id_user,
               'firstname' => $user->firstname,
               'lastname' => $user->lastname,
               'id_domain' => $user->id_domain,
               'domain_name' => $user->domain_name,
               'is_domain_admin' => ($user->is_domain_admin == 1 ? true : false),
               'timestamp' => date('YmdHis'),
           );
           $this->session->set_userdata($data);

            $this->load->model('log_model');
            $this->log_model->log(array('type' => 1, 'operation' => 7, 'message_short' => 'Successful authentication', 'message' => 'Username '.$this->db->escape($usr).' has successful logged in' ));
           
            if(!empty($redirect))
                redirect($redirect);
            else
                redirect('admin/home');
		}
        else
        {
            // Log and return false
            $this->load->model('log_model');
            $this->log_model->log(array('type' => 2, 'operation' => 6, 'message_short' => 'Username/password failure', 'message' => 'Authentication failure for username '.$this->db->escape($usr)));
            
			return false;
		}

	}

    /*
     * Refresh the user rights
     */
	public function refresh()
	{
        $usrid = $this->session->userdata('id_user');
        if(!isset($usrid))
            redirect('admin/login');
        
		//Throttle refresh
		if( date('YmdHis') - $this->session->userdata('timestamp') >= $this->config->item('rights_time_to_live') )
		{
			// Retrieve full information from user
            $sql = "SELECT u.*, d.tag AS domain_tag, d.id_domain, d.name AS domain_name, d.ismultisite AS domain_ismultisite 
                FROM ".$this->db->dbprefix('user')." u,
                ".$this->db->dbprefix('domain')." d
                WHERE u.`id_user` = ".(int)$usrid."
                AND u.`id_domain` = d.`id_domain`
                AND u.`active` = 1";
            $result = $this->db->query($sql);
            $user = $result->row();
            
            // Retrieve rights
            $sql = "SELECT * 
                FROM ".$this->db->dbprefix('user_rights')." 
                WHERE `id_user` = ".(int)$user->id_user."
                AND `id_domain` = ".(int)$user->id_domain;
            $result = $this->db->query($sql);
            $rights = $result->result_array();

			$cookiedata = array(
               'zone' => 'app',
               'id_user' => $user->id_user,
               'firstname' => $user->firstname,
               'lastname' => $user->lastname,
               'id_domain' => $user->id_domain,
               'domain_name' => $user->domain_name,
               'is_domain_admin' => ($user->is_domain_admin == 1 ? true : false),
               'timestamp' => date('YmdHis'),
           );
           $this->session->set_userdata($cookiedata);
        }
	} //refresh
    
    /*
     * Check if a user exists (a deleted users are discarded)
     */
    public function userexists($username)
    {
        $sql = "SELECT * FROM ".$this->db->dbprefix('user')."
                WHERE `username` = '".$usr."' 
                AND `deleted` = 0
                LIMIT 1";
        $result = $this->db->query($sql);
        if ( $result->num_rows > 0 ){
			return TRUE;
        } else {
            return FALSE;
        }
    }
}

