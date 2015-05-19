<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Boxes_Model extends CI_Model {

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
    public function getAll($p = null, $n = null, $orderby = null, $filter = null, $status = null)
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
        
        if($status == 1)
        {
            if(isset($where_str))
                $where_str .= ' AND (SELECT COUNT(`id_shipping_pack`) FROM ao_shipping_pack WHERE `inbound` = 0 AND `id_pack` = p.`id_pack`) = 0 ';
            else
                $where_str = ' WHERE (SELECT COUNT(`id_shipping_pack`) FROM ao_shipping_pack WHERE `inbound` = 0 AND `id_pack` = p.`id_pack`) = 0 ';
        }
        if($status == 2)
        {
            if(isset($where_str))
                $where_str .= ' AND (SELECT COUNT(`id_shipping_pack`) FROM ao_shipping_pack WHERE `inbound` = 0 AND `id_pack` = p.`id_pack`) > 0 ';
            else
                $where_str = ' WHERE (SELECT COUNT(`id_shipping_pack`) FROM ao_shipping_pack WHERE `inbound` = 0 AND `id_pack` = p.`id_pack`) > 0 ';
        }
            
        $sql = "SELECT *,
                (SELECT COUNT(`id_shipping_pack`) FROM ao_shipping_pack WHERE `inbound` = 0 AND `id_pack` = p.`id_pack`) as count_outbound,
                (SELECT @id_shipping_pack := MAX(`id_shipping_pack`) FROM ao_shipping_pack WHERE `id_pack` = p.`id_pack`) as `id_shipping_pack`,
                (SELECT `inbound` FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping_pack` = @id_shipping_pack) as `inbound`,
                (SELECT `outbound` FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping_pack` = @id_shipping_pack) as `outbound`,
                (SELECT `date_outbound` FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping_pack` = @id_shipping_pack) as `date_outbound`,
                (SELECT `date_inbound` FROM ".$this->db->dbprefix('shipping_pack')." WHERE `id_shipping_pack` = @id_shipping_pack) as `date_inbound`,
                (SELECT s.`reference` FROM ".$this->db->dbprefix('shipping')." s, ".$this->db->dbprefix('shipping_pack')." sp WHERE sp.`id_shipping_pack` = @id_shipping_pack AND sp.`id_shipping` = s.`id_shipping`) as `reference`
            FROM ".$this->db->dbprefix('pack')." p "
            .(isset($where_str) ? $where_str : '')
            .(empty($orderby) ? ' ORDER BY p.`barcode` ' : ' ORDER BY '.$orderby)
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
                
                // Retrieve all the rows for multiple outbounds i.e. mulitple shippings
                if($v->count_outbound > 1)
                {
                    $sql = "SELECT *
                        FROM ".$this->db->dbprefix('shipping_pack')." sp
                        LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
                        WHERE sp.`id_pack` = ?
                        AND sp.`inbound` = 0";
                    $shippings = $this->db->query($sql, array((int)$v->id_pack))->result();
                    if($shippings !== null)
                        $v->shippings = $shippings;
                }
            }
            
			return $rows;
        }
        else
            return false;
    }
    
    public function getAllCount($filter = null, $status = null)
    {
        $result = $this->getAll(null, null, null, $filter, $status);
        return count($result);
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
    
    public function getHistory($id)
    {
        if(empty($id))
            return false;
        
        $sql = "SELECT *,
                    u_out.`firstname` as out_firstname,
                    u_out.`lastname` as out_lastname,
                    u_in.`firstname` as in_firstname,
                    u_in.`lastname` as in_lastname,
                    s.`username` as customer
                FROM ".$this->db->dbprefix('shipping_pack')." sp
                LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
                LEFT JOIN ".$this->db->dbprefix('pack')." p ON (sp.`id_pack` = p.`id_pack`)
                LEFT JOIN ".$this->db->dbprefix('user')." u_out ON (sp.`id_user_outbound` = u_out.`id_user`)
                LEFT JOIN ".$this->db->dbprefix('user')." u_in ON (sp.`id_user_inbound` = u_in.`id_user`)
                WHERE sp.`id_pack` = ?
                ORDER BY s.`date_delivery` DESC, sp.`date_inbound`";

        $result = $this->db->query($sql,array((int)$id));
        if ( $result !== null )
			return $result->result();
        else
            return false;
    }
    
    /*
     * Returns the type of the given reference
     */
    public function referenceIs($str)
    {   
        if(empty($str))
            return false;
        
        $value = $str;
        // Find shipping code
        $pat = '/SH[0-9]+/';
        preg_match_all($pat, trim($value), $matchout);

        if(isset($matchout[0][0]) )
            return 'SHIPPING';
                
            
        // Find the boxes (big boxes)
        $pat = '/PB[0-9]+/';
        preg_match_all($pat, trim($value), $matchout);

        if(isset($matchout[0][0]))
        {
            $code = $matchout[0][0];
            $pat = '/\d+/';
            preg_match_all($pat, $code, $matches);
            if(isset($matches[0][0]))
            {
                $val = (int)$matches[0][0];
                if($val > 1000 && $val <= 9999)
                    return 'PACK';
            }
        }

        // Find the boxes (small boxes)
        $pat = '/PS[0-9]+/';
        preg_match_all($pat, trim($value), $matchout);
        if(isset($matchout[0][0]))
        {
            $code = $matchout[0][0];
            $pat = '/\d+/';
            preg_match_all($pat, $code, $matches);
            if(isset($matches[0][0]))
            {
                $val = (int)$matches[0][0];
                if($val > 1000 && $val <= 9999)
                    return 'PACK';
            }
        }

        // Find the boxes (test boxes)
        $pat = '/PT[0-9]+/';
        preg_match_all($pat, trim($value), $matchout);
        if(isset($matchout[0][0]))
        {
            $code = $matchout[0][0];
            $pat = '/\d+/';
            preg_match_all($pat, $code, $matches);
            if(isset($matches[0][0]))
            {
                $val = (int)$matches[0][0];
                if($val >= 1001 && $val <= 1010)
                    return 'PACK';
            }
        }
        
        return false;
    }
    
    
    /*
     * Get status of a given barcode
     */
    public function getQuickSearchByBarcode($barcode)
    {
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('pack')." p
            WHERE p.`barcode` = ?";
        $pack = $this->db->query($sql, array($barcode))->row();
        
        if($pack === null)
            return false;
        
        if(isset($pack->id_pack))
            $id_pack = (int)$pack->id_pack;
        else
            return false;
        
        // id_pack is present -> continue
        // Check if there is history for the pack
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('shipping_pack')." sp
            LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
            WHERE sp.`id_pack` = ?
            ORDER BY s.`date_delivery` DESC";
        $shippings = $this->db->query($sql, array($id_pack))->result_array();

        if($shippings === null)
        {
            // Pack has no history -> consider has beeing IN
            $status = false;
            $history = false;
        }
        else
        {
            // Pack has history -> determine current status
            // Check if it is still outbound
            $sql = "SELECT *
                FROM ".$this->db->dbprefix('shipping_pack')." sp
                LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
                WHERE sp.`id_pack` = ?
                AND sp.`inbound` = 0";
            $shippings_out = $this->db->query($sql, array($id_pack))->result();
            // Pack is still outbound
            if($shippings_out !== null)
            {
                $status = array(
                    'status' => true, // Status = true => is outbound
                    'packs' => $shippings_out,
                );
            }
            else
                // Pack is not outbound
                $status = array('status' => false);
                
            // Get the previous shipping(s) if any
            $sql = "SELECT *
                FROM ".$this->db->dbprefix('shipping_pack')." sp
                LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.`id_shipping` = s.`id_shipping`)
                WHERE sp.`id_pack` = ?
                AND sp.`inbound` = 1
                ORDER BY s.`date_delivery` DESC";
            $shippings_cycle = $this->db->query($sql, array($id_pack))->result();

            if($shippings_cycle === null)
                $history = false;
            else
                $history = $shippings_cycle;   
        }
        
        $result = array(
            'id_pack' => $id_pack,
            'barcode' => $pack->barcode,
            'status' => $status,
            'history' => $history,
        );
 
        return $result;
    }
    
    public function getByBarcode($ref)
    {
        if(empty($ref))
            return false;
        
        $sql = "SELECT * 
                FROM ".$this->db->dbprefix('pack')."
                WHERE `barcode` = ?";
        $result = $this->db->query($sql,array($ref));
        
        if ( $result->num_rows() > 0 )
			return $result->row();
        else
            return false;
    }
}

