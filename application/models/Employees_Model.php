<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employees_model extends My_Model {

	function __construct()
	{
		parent::__construct();
        $this->table = 'parents';
        $this->primaryKey = 'id_parents';
	}
    
    /*
     * Get a given child that is not deleted
     * includes domain_name, site_name
     */
    public function getEmployee($id)
    {
        $sql = "SELECT u.*, s.name AS site_name, d.name AS domain_name
            FROM ".$this->db->dbprefix('user')." u
            LEFT JOIN ".$this->db->dbprefix('site')." s ON u.`id_default_site` = s.`id_site`
            LEFT JOIN ".$this->db->dbprefix('domain')." d ON s.`id_domain` = d.`id_domain`
            WHERE u.`deleted` = 0 
            AND u.`id_user` = ?
            AND u.`id_domain` =  ".(int)getUserDomain();
        $result = $this->db->query($sql, array($id))->row();
        return $result;
    }
    
    /*
     * Get all employees that are not deleted
     * includes domain_name, site_name
     * Is domain safe
     */
    public function getEmployees($p = null, $n = null, $orderby = null)
    {
        $p = (is_numeric($p) ? $p - 1 : null);
        
        $sql = "SELECT u.*, rp.`name`AS right_profile_name
            FROM ".$this->db->dbprefix('user')." u
            LEFT JOIN ".$this->db->dbprefix('right_profile')." rp ON u.`id_right_profile` = rp.`id_right_profile`
            WHERE u.`deleted` = 0 
            AND u.`id_domain` =  ".(int)getUserDomain()." "
            .(empty($orderby) ? 'ORDER BY u.`lastname`, u.`firstname` ' : 'ORDER BY '.$orderby)
            .((is_numeric($p) && is_numeric($n)) ? ' LIMIT '.$p * $n.','.$n : '');
        $result = $this->db->query($sql)->result();
        return $result;
    }
    
    public function getEmployeesCount()
    {
        $result = $this->getEmployees();
        return count($result);
    }
    
    /*
     * Check if a not deleted employee number exists
     * Is domain safe
     */
    public function employeeNumberExists($value = null, $id_exclude = null)
    {
        if(is_null($value))
            return false;
        
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('user')." c 
            WHERE c.`deleted` = 0 
            AND c.`employee_number` LIKE ? 
            AND c.`id_user` NOT IN (".(!empty($id_exclude)? $id_exclude : '0').")
            AND c.`id_domain` =  ".(int)getUserDomain();
        $result = $this->db->query($sql,array($value));
        if($result->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    /*
     * Check if a not deleted employee with same email exists
     * Is domain safe
     */
    public function emailExists($value = null, $id_exclude = null)
    {
        if(is_null($value))
            return false;
        
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('user')." c 
            WHERE c.`deleted` = 0 
            AND c.`email` LIKE ? 
            AND c.`id_user` NOT IN (".(!empty($id_exclude)? $id_exclude : '0').")
            AND c.`id_domain` =  ".(int)getUserDomain();
        $result = $this->db->query($sql,array($value));
        if($result->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    /*
     * Check if a not deleted employee with same username exists
     * Is domain safe
     */
    public function usernameExists($value = null, $id_exclude = null)
    {
        if(is_null($value))
            return false;
        
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('user')." c 
            WHERE c.`deleted` = 0 
            AND c.`username` LIKE ? 
            AND c.`id_user` NOT IN (".(!empty($id_exclude)? $id_exclude : '0').")
            AND c.`id_domain` =  ".(int)getUserDomain();
        $result = $this->db->query($sql,array($value));
        if($result->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    /*
     * Delete the record (flag delete)
     * Is domain safe
     */
    public function delete($id)
    {
        return $this->db->update('user', array('deleted' => 1), array('id_user' => $id, 'id_domain' => (int)getUserDomain(), 'is_domain_admin' => 0)); 
    }

    
}
