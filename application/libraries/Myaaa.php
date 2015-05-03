<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/*
 * Library to control access to ressources based on rights
 */

class Myaaa {
    var $rights = array();
    var $CI;
    
    public function __construct($config = array())
    {
		log_message('debug', "Myaaa Class Initialized");
        
        $this->CI =& get_instance();
        
        // The rights
        $sql = "SELECT u.`id_user`, rn.`name`, ra.`view`, ra.`create`, ra.`edit`, ra.`delete` 
        FROM ".$this->CI->db->dbprefix('user')." u,
        ".$this->CI->db->dbprefix('right_name')." rn,
        ".$this->CI->db->dbprefix('right_assign')." ra
        WHERE u.`id_domain` = ".(int)$this->CI->session->userdata('id_domain')."
        AND u.`id_user` = ".(int)$this->CI->session->userdata('id_user')." 
        AND rn.`id_right_name` = ra.`id_right_name`
        AND u.`id_right_profile` = ra.`id_right_profile`";
        $rights = $this->CI->db->query($sql)->result_array();
        
        foreach ($rights as $value)
            $this->rights[$value['name']] = $value;
    }
    
}

/* End of file Myaaa.php */