<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shipping_Model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
    
    public function getitem($id)
    {
        if(empty($id))
            return false;
        
        $sql = "SELECT * 
                FROM ".$this->db->dbprefix('shipping')."
                WHERE `id_shipping` = ?";
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
    
            foreach ($filter as $fname => $fvalue)
            {
                if(is_array($fvalue))
                {
                    if(isset($fvalue['value']))
                    {
                        $where_str .= ' AND '.$fname;
                        // Consider as string
                        if(isset($fvalue['wildcard']))
                            $where_str .= ' LIKE '.$this->db->escape(str_replace('{}', $fvalue['value'], $fvalue['wildcard'])).' ';
                        // Consider as numeric (integer or currency)
                        if(isset($fvalue['isnumeric']))
                            $where_str .= ' = '.(int)$fvalue['value'].' ';
                    }
                    
                }
                else
                    $where_str .= ' AND '.$fname.' = '.$this->db->escape($fvalue);
            }
        }
        $sql = "SELECT *,
                (SELECT COUNT(`id_pack`) FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping` = s.`id_shipping`) as count_packs,
                (SELECT COUNT(`id_pack`) FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping` = s.`id_shipping` AND `outbound` = 1 AND `inbound` = 0) as count_packs_outbound,
                (SELECT COUNT(`id_pack`) FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping` = s.`id_shipping` AND `outbound` = 1 AND `inbound` = 1) as count_packs_inbound
            FROM ".$this->db->dbprefix('shipping')." s "
            .(isset($where_str) ? $where_str : '')
            .(empty($orderby) ? 'ORDER BY s.`reference` ' : 'ORDER BY '.$orderby)
            .((is_numeric($p) && is_numeric($n)) ? ' LIMIT '.$p * $n.','.$n : '');
        $result = $this->db->query($sql);
  
        if ( $result !== null )
        {
            $rows = $result->result();
            
            // set the status to in = 0 or to out = 1
            foreach ($rows as $k => &$v)
            {
                
                $sql = "SELECT *
                    FROM ".$this->db->dbprefix('shipping_pack')." sp
                    LEFT JOIN ".$this->db->dbprefix('pack')." p ON (sp.`id_pack` = p.`id_pack`)
                    WHERE sp.`id_shipping` = ?
                    GROUP BY sp.`id_pack`
                    ORDER BY p.`barcode`";
                $result = $this->db->query($sql, array((int)$v->id_shipping))->result();
                $packs = array();
                foreach($result as $p)
                    $packs[] = $p;
                $v->packs = $packs;
            }
     
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
    
    /*
     *  Check if the barcode exists
     */
    public function exists($ref)
    {
        if(empty($ref))
            return false;
        
        $sql = "SELECT * 
                FROM ".$this->db->dbprefix('shipping')."
                WHERE `reference` = ?";
        $result = $this->db->query($sql,array($ref));
        
        if ( $result->num_rows() > 0 )
			return $result->row();
        else
            return false;
    }
    
    
}

