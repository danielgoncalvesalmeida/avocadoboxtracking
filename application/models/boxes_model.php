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
    
    public function getAllCount($filter = null)
    {
        $result = $this->getAll(null, null, null, $filter);
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
        
        $sql = "SELECT * 
                FROM ".$this->db->dbprefix('shipping_pack')."
                WHERE `id_pack` = ?
                ORDER BY `id_shipping_pack`";
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
    public function getStatusByBarcode($barcode)
    {
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('pack')." p
            WHERE p.`barcode` = ?";
        $result = $this->db->query($sql, array($barcode))->row();
        
        if($result === null)
            return false;
        
        if(isset($result->id_pack))
            $id_pack = (int)$result->id_pack;
        else
            return false;
        
        $sql = "SELECT *,
                (SELECT @id_shipping_pack := MAX(`id_shipping_pack`) FROM ao_shipping_pack WHERE `id_pack` = p.`id_pack`) as `id_shipping_pack`,
                (SELECT `inbound` FROM ao_shipping_pack WHERE `id_shipping_pack` = @id_shipping_pack) as `inbound`,
                (SELECT `outbound` FROM ao_shipping_pack WHERE `id_shipping_pack` = @id_shipping_pack) as `outbound`,
                (SELECT `date_outbound` FROM ao_shipping_pack WHERE `id_shipping_pack` = @id_shipping_pack) as `date_outbound`,
                (SELECT `date_inbound` FROM ao_shipping_pack WHERE `id_shipping_pack` = @id_shipping_pack) as `date_inbound`,
                (SELECT `reference` FROM ao_shipping_pack WHERE `id_shipping_pack` = @id_shipping_pack) as `reference`
            FROM ".$this->db->dbprefix('pack')." p
            WHERE p.`id_pack` = ?";
        
        $result = $this->db->query($sql, array($id_pack));
        
        if ( $result !== null )
        {
            $rows = $result->result();
            // set the status to in = 0 or to out = 1
            foreach ($rows as $k => &$v)
            {
                if( ($v->outbound == 1 && $v->inbound == 1) || ($v->outbound === null && $v->inbound === null) )
                    $v->status = 0;
                else
                    $v->status = 1;
            }
			return $rows;
        }
        else
            return false;
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

