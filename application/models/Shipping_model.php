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
                FROM ".$this->db->dbprefix('pack')."
                WHERE `id_pack` = ?";
        $result = $this->db->query($sql,array((int)$id));
        if ( $result !== null )
			return $result->row();
        else
            return false;
    }
    
    /*
     * Get the all the boxes with a current count_outbound
     */
    public function getAll($p = null, $n = null, $orderby = null)
    {
        $sql = "SELECT *,
                (SELECT COUNT(`id_shipping_pack`) FROM ao_shipping_pack WHERE `inbound` = 0 AND `id_pack` = p.`id_pack`) as count_outbound,
                (SELECT @id_shipping_pack := MAX(`id_shipping_pack`) FROM ao_shipping_pack WHERE `id_pack` = p.`id_pack`) as `id_shipping_pack`,
                (SELECT `inbound` FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping_pack` = @id_shipping_pack) as `inbound`,
                (SELECT `outbound` FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping_pack` = @id_shipping_pack) as `outbound`,
                (SELECT `date_outbound` FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping_pack` = @id_shipping_pack) as `date_outbound`,
                (SELECT `date_inbound` FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping_pack` = @id_shipping_pack) as `date_inbound`,
                (SELECT s.`reference` FROM ".$this->db->dbprefix('shipping')." s, ".$this->db->dbprefix('shipping_pack')." sp WHERE sp.`id_shipping_pack` = @id_shipping_pack AND sp.`id_shipping` = s.`id_shipping`) as `reference`
            FROM ".$this->db->dbprefix('pack')." p "
            .(empty($orderby) ? 'ORDER BY p.`barcode` ' : 'ORDER BY '.$orderby)
            .((is_numeric($p) && is_numeric($n)) ? ' LIMIT '.$p * $n.','.$n : '');
        $result = $this->db->query($sql);
        if ( $result !== null )
        {
            $rows = $result->result();
            // set the status to in = 0 or to out = 1
            foreach ($rows as $k => &$v)
            {
                if( $v->count_outbound == 0 )
                    $v->status = 0;
                else
                    $v->status = 1;
                // Retrieve all the rows for multiple outbounds
                if($v->count_outbound > 1)
                {
                    $sql = "SELECT *
                        FROM ".$this->db->dbprefix('shipping_pack')." sp
                        WHERE sp.`id_pack` = ?
                        AND sp.`inbound` = 0";
                    $result = $this->db->query($sql, array((int)$v->id_pack));
                }
            }
			return $rows;
        }
        else
            return false;
    }
    
    /*
     * Get all the boxes in
     */
    public function getAllIn()
    {
        $result = false;
        $items = $this->getAll();
        if ( $items )
        {
            foreach ($items as $v)
            {
                if($v->status == 0)
                    $result[] = $v;
            }
        }
        return $result;
    }
    
    /*
     * Get all the boxes out
     */
    public function getAllOut()
    {
        $result = false;
        $items = $this->getAll();
        if ( $items )
        {
            foreach ($items as $v)
            {
                if($v->status == 1)
                    $result[] = $v;
            }
        }
        return $result;
    }
    
    /*
     * Get the count of all boxes
     */
    public function getAllTotal()
    {
        $result = $this->getAll();
        return count($result);
    }
    
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

