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
        $sql = "SELECT u.*, d.name AS domain_name
            FROM ".$this->db->dbprefix('user')." u
            LEFT JOIN ".$this->db->dbprefix('domain')." d ON u.`id_domain` = d.`id_domain`
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
    public function getEmployees($p = null, $n = null, $orderby = null, $filter = null)
    {
        $p = (is_numeric($p) ? $p - 1 : null);
        
        // Build the where clause
        if(is_array($filter) && count($filter) > 0)
        {
            $where_str = ''; // The default SQL statement further below contains already the where clause
    
            foreach ($filter as $fvalue)
            {
                if(is_array($fvalue))
                {
                    if(isset($fvalue['field']) && isset($fvalue['value']))
                    {
                        $where_str .= ' AND '.$fvalue['field'];
                        // Consider as string
                        if(isset($fvalue['wildcard']))
                            $where_str .= ' LIKE '.$this->db->escape(str_replace('{}', $fvalue['value'], $fvalue['wildcard'])).' ';
                        // Consider as numeric (integer or currency)
                        if(isset($fvalue['isnumeric']))
                            $where_str .= ' = '.(int)$fvalue['value'].' ';
                        
                        // Consider field as datetime
                        if(isset($fvalue['isdatetime']))
                            $where_str .= ' = \''.$fvalue['value'].'\' ';
                        if(isset($fvalue['isdatetime_from']))
                            $where_str .= ' >= \''.$fvalue['value'].'\' ';
                        if(isset($fvalue['isdatetime_to']))
                            $where_str .= ' <= \''.$fvalue['value'].' 23:59:59\' ';
                    }  
                }
            }
        }
        
        $sql = "SELECT u.*, rp.`name`AS right_profile_name
            FROM ".$this->db->dbprefix('user')." u
            LEFT JOIN ".$this->db->dbprefix('right_profile')." rp ON u.`id_right_profile` = rp.`id_right_profile`
            WHERE u.`deleted` = 0 "
            .(isset($where_str) ? $where_str : '')
            ." AND u.`id_domain` =  ".(int)getUserDomain()." "
            .(empty($orderby) ? 'ORDER BY u.`firstname`, u.`lastname` ' : 'ORDER BY '.$orderby)
            .((is_numeric($p) && is_numeric($n)) ? ' LIMIT '.$p * $n.','.$n : '');
        $result = $this->db->query($sql)->result();
        return $result;
    }
    
    public function getEmployeesCount($filter = null)
    {
        $result = $this->getEmployees(null, null, null, $filter);
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
