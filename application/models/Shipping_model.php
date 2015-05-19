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
            // Retrieve the packs used for each shipping
            foreach ($rows as $k => &$v)
            {
                $sql = "SELECT *,
                        u_out.`firstname` as out_firstname,
                        u_out.`lastname` as out_lastname,
                        u_in.`firstname` as in_firstname,
                        u_in.`lastname` as in_lastname
                    FROM ".$this->db->dbprefix('shipping_pack')." sp
                    LEFT JOIN ".$this->db->dbprefix('pack')." p ON (sp.`id_pack` = p.`id_pack`)
                    LEFT JOIN ".$this->db->dbprefix('user')." u_out ON (sp.`id_user_outbound` = u_out.`id_user`)
                    LEFT JOIN ".$this->db->dbprefix('user')." u_in ON (sp.`id_user_inbound` = u_in.`id_user`)
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
    
    public function getQuickSearchByReference($ref)
    {
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping')." s
            WHERE s.`reference` = ?";
        $shipping = $this->db->query($sql, array($ref))->row();
        
        if($shipping === null)
            return false;
        
        if(isset($shipping->id_shipping))
            $id_shipping = (int)$shipping->id_shipping;
        else
            return false;
        
        // id_shipping is present -> continue
        // Retrieve the packs related to the shipping
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping_pack')." sp
            LEFT JOIN ".$this->db->dbprefix('pack')." p ON (sp.`id_pack` = p.`id_pack`)
            WHERE sp.`id_shipping` = ?
            ORDER BY p.`barcode`";
        $packs = $this->db->query($sql, array($id_shipping))->result();
        if($packs === null)
            $packs = false;
        
        $result = array(
            'id_shipping' => $id_shipping,
            'reference' => $shipping->reference,
            'username' => $shipping->username,
            'date_delivery' => $shipping->date_delivery,
            'packs' => $packs,
        );
        
        return $result;
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
    
    /*
     *  Get the user if it is related to a shipping
     */
    public function getByUser($username)
    {
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping')." s
            WHERE s.`username` = ?";
        $user = $this->db->query($sql, array($username))->row();
        
        if($user === null)
            return false;
        
        // User identified -> get shippings
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping')." s
            WHERE s.`username` = ?
            ORDER BY s.`date_delivery` DESC
            LIMIT 10";
        $shippings = $this->db->query($sql, array($username))->result();
        if($shippings === null)
            $shippings = false;
        
        // User identified -> get packs
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping_pack')." sp
            LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
            LEFT JOIN ".$this->db->dbprefix('pack')." p ON (sp.`id_pack` = p.`id_pack`)
            WHERE s.`username` = ?
            ORDER BY s.`date_delivery` DESC
            LIMIT 10";
        $packs = $this->db->query($sql, array($username))->result();
        if($packs === null)
            $packs = false;
        
        // User identified -> get packs outbound packs (unreturned packs)
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping_pack')." sp
            LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
            LEFT JOIN ".$this->db->dbprefix('pack')." p ON (sp.`id_pack` = p.`id_pack`)
            WHERE s.`username` = ?
            AND sp.`inbound` = 0
            GROUP BY sp.`id_pack`
            ORDER BY p.`barcode`";
        $packs_outbound = $this->db->query($sql, array($username))->result();
        if($packs_outbound === null)
            $packs_outbound = false;
        
        $result = array(
            'username' => $user->username,
            'shippings' => $shippings,
            'packs' => $packs,
            'packs_outbound' => $packs_outbound,
        );
     
        return $result;
    }
    
    
}

