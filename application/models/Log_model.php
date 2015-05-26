<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log_Model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
    
    public function getitem($id)
    {
        if(empty($id))
            return false;
        
        $sql = "SELECT * 
                FROM ".$this->db->dbprefix('log')."
                WHERE `id_log` = ?";
        $result = $this->db->query($sql,array((int)$id));
        if ( $result !== null )
			return $result->row();
        else
            return false;
    }
    
    /*
     * Get the all the objects
     */
    public function getAll($p = null, $n = null, $orderby = null, $filter = null)
    {
        $p = (is_numeric($p) ? $p - 1 : null);
        
        // Build the where clause
        if(is_array($filter) && count($filter) > 0)
        {
            $where_str = 'WHERE 1 ';
    
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
        $sql = "SELECT l.*, lo.*, u.`firstname`, u.`lastname`, u.`username`
            FROM ".$this->db->dbprefix('log')." l 
            LEFT JOIN ".$this->db->dbprefix('user')." u ON (l.`id_user` = u.`id_user`)
            LEFT JOIN ".$this->db->dbprefix('log_operation')." lo ON (l.`operation` = lo.`id_log_operation`) "
            .(isset($where_str) ? $where_str : '')
            .(empty($orderby) ? 'ORDER BY l.`date_add` DESC ' : 'ORDER BY '.$orderby)
            .((is_numeric($p) && is_numeric($n)) ? ' LIMIT '.$p * $n.','.$n : '');
        $result = $this->db->query($sql);

        if ( $result !== null )
        {
            $rows = $result->result();
			return $rows;
        }
        else
            return false;
    }
    
    public function getAllCount($filter = null)
    {
        $result = $this->getAll(null, null, null, $filter);
        return count($result);
    }
    
    /**
     * 
     *  TYPE
        1 : Information
        2 : Warning
        3 : Error


        OPERATION
        0 : Miscelleneous action
        1 : Import shipping
        2 : Import outbound
        3 : Import inbound
        4 : User creation or edition
        5 : Password change
        6 : Username/password failure
        7 : User log in
     */
    
    public function log($message_array = array())
    {
        $id_user = (isset($_SESSION['id_user']) && !empty($_SESSION['id_user']) ? $_SESSION['id_user'] : 0);
        if(!is_array($message_array))
            return;
        
        $data = array();
        $data['id_user'] = $id_user;
        if(isset($message_array['message']))
            $data['message'] = $message_array['message'];
        if(isset($message_array['message_short']))
            $data['message_short'] = $message_array['message_short'];
        if(isset($message_array['type']))
            $data['type'] = $message_array['type'];
        if(isset($message_array['operation']))
            $data['operation'] = $message_array['operation'];
        $data['date_add'] = date('Y-m-d H:i:s');
        $this->db->insert('log',$data);
        return true;
    }
    
    
    public function getOperations()
    {
        $sql = "SELECT * 
                FROM ".$this->db->dbprefix('log_operation')."
                ORDER BY `label`";
        $result = $this->db->query($sql);
        if ( $result !== null )
			return $result->result();
        else
            return false;
    }
    
    
    
}

