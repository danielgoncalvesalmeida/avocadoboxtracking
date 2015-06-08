<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer_Model extends CI_Model {

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
        $sql = "SELECT s.`username`
            FROM ".$this->db->dbprefix('shipping')." s "
            
            .(isset($where_str) ? $where_str : '')
            ."GROUP BY s.`username` "   
            .(empty($orderby) ? 'ORDER BY s.`username` ' : 'ORDER BY '.$orderby)
            .((is_numeric($p) && is_numeric($n)) ? ' LIMIT '.$p * $n.','.$n : '');
        
        $result = $this->db->query($sql);

        if ( $result === null )
            return false;
        
        // Got results 
        $rows = $result->result();
        
        foreach ($rows as &$v)
        {
            $usernames[] = "'".$v->username."'";
            $v->boxes = null;
        }
        
        // Get the missing boxes
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping_pack')." sp 
            LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
            LEFT JOIN ".$this->db->dbprefix('pack')." p ON (sp.`id_pack` = p.`id_pack`) 
            WHERE sp.`outbound` = 1 AND sp.`inbound` = 0
            AND s.`username` IN (".implode(',', $usernames).")
            GROUP BY s.`username`, p.`id_pack`
            ORDER BY p.`barcode`";
        $boxes = $this->db->query($sql);

        if($boxes !== null)
        {
            $boxes = $boxes->result();
            foreach ($rows as &$v)
            {
                foreach ($boxes as $b)
                {
                    if($b->username == $v->username)
                        $v->boxes[] = $b;
                }
            }
        }
     
		return $rows;
    }
    
    public function getAllCount($filter = null)
    {
        $result = $this->getAll(null, null, null, $filter);
        return count($result);
    }
    
    public function getHistory($username)
    {
        if(strlen($username) == 0 || $this->validate_username($username) < 1)
            return false;
        
        
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping_pack')." sp 
            LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
            LEFT JOIN ".$this->db->dbprefix('pack')." p ON (sp.`id_pack` = p.`id_pack`) 
            WHERE s.`username` LIKE ".$this->db->escape($username)."
            GROUP BY p.`barcode`
            ORDER BY sp.`id_shipping_pack` DESC";
        
        $result = $this->db->query($sql);
        if($result !== null)
            return $result->result();
        else
            return null;
    }
    
    public function validate_username($username)
    {
        return preg_match('/^[a-zA-Z0-9_-\s]+$/', $username);
    }
    
    
}

