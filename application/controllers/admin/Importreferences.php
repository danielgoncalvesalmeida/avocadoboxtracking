<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Importreferences extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
    }
    
	public function index()
	{
        redirect('admin/dashboard');
    }
    
    public function addoutbound()
    {
        $this->load->helper('form');
        $this->setTitle('Import outbound | '.$this->config->item('appname'));
        $dview = array();
        
        $isSubmit = (isset($_POST['submitImport']) ? true : false);
        
        if($isSubmit)
        {
            if(isset($_FILES['edfile']['name']) && !empty($_FILES['edfile']['name']))
            {
                $refs = $this->txtFileToArray($_FILES['edfile']['tmp_name']);
                $result = $this->parseOutboundReferences($refs);   
            }
            elseif(isset($_POST['edreferences']) && !empty($_POST['edreferences']))
            {
                $edrefs = $_POST['edreferences'];
                $refs = explode("\n", str_replace("\r", "", $edrefs));
                $result = $this->parseOutboundReferences($refs);
            }
            
            $operation = array(
                'success' => array(),
                'conflict' => array(),
            );
            
            // Add the parsed boxes to the log
            if(isset($result['shipping']))
            {
                
                foreach ($result['shipping'] as $shipping)
                {
                    // Check if a valid reference could by identified
                    if(isset($shipping['reference']) && !empty($shipping['reference']) && isset($shipping['packs']))
                    {

                        foreach ($shipping['packs'] as $pack)
                        {
                            // First check if the pack is registred
                            $sql = "SELECT *
                                FROM ".$this->db->dbprefix('pack')." 
                                WHERE `barcode` LIKE ?";
                            $result = $this->db->query($sql, array($pack))->row();
                            
                            if($result === null)
                            {
                                $data = array(
                                    'barcode' => $pack,
                                    'date_add' => date('Y-m-d H:i:s'),
                                    'date_upd' => date('Y-m-d H:i:s'),
                                );
                                $this->db->insert('pack',$data);
                                $id_pack = $this->db->insert_id();
                            }
                            else
                                $id_pack = (int)$result->id_pack;
                            
                            // Check if this pack has an open cycle
                            if($id_pack)
                            {
                                $insert = false;
                                $sql = "SELECT *
                                    FROM ".$this->db->dbprefix('shipping_pack')."
                                    WHERE `outbound` = 1 
                                    AND (isnull(`inbound`) OR `inbound` = 0)
                                    AND `id_pack` = ?";
                                $result = $this->db->query($sql, array($id_pack))->row();
                                
                                // If the pack has no open cycle, check that this shipping reference and the id_pack as not once be used
                                if($result === null)
                                {
                                    $sql = "SELECT *
                                        FROM ".$this->db->dbprefix('shipping_pack')."
                                        WHERE `reference` = ?
                                        AND `id_pack` = ?";
                                    $result = $this->db->query($sql, array($shipping['reference'], $id_pack))->row();
                                    if($result === null)
                                        $insert = true;
                                    else
                                    {
                                        $operation['conflict'][] = array(
                                            'cycled' => true,
                                            'pack' => $pack,
                                            'shipping' => $result->reference,
                                            'date_outbound' => $result->date_outbound,
                                            'date_inbound' => $result->date_inbound,
                                        );
                                    }
                                }
                                else
                                {
                                    $operation['conflict'][] = array(
                                        'pack' => $pack,
                                        'shipping' => $result->reference,
                                        'date_outbound' => $result->date_outbound,
                                    );
                                }
                                
                                // This shipping : pack can be inserted as outbound
                                if($insert)
                                {
                                    $data = array(
                                        'reference' => $shipping['reference'],
                                        'id_pack' => (int)$id_pack,
                                        'outbound' => true,
                                        'date_outbound' => date('Y-m-d H:i:s'),
                                        'date_add' => date('Y-m-d H:i:s'),
                                        'date_upd' => date('Y-m-d H:i:s'),
                                    );
                                    $this->db->insert('shipping_pack',$data);
                                    
                                    if(!isset($operation['success'][$shipping['reference']]))
                                        $operation['success'][$shipping['reference']][] = $pack;
                                    else
                                        $operation['success'][$shipping['reference']][] = $pack;
                                }
                                
                            }
                            
                            
                        }
                    }
                    
                }
            }
            $dview['success'] = $operation['success'];
            $dview['conflict'] = $operation['conflict'];
        }
        
        $this->display('import_references_outbound', $dview);
    }
    
    public function addinbound()
    {
        $this->load->helper('form');
        $this->setTitle('Import inbound | '.$this->config->item('appname'));
        $dview = array();
        
        $isSubmit = (isset($_POST['submitImport']) ? true : false);
        
        if($isSubmit)
        {
            if(isset($_FILES['edfile']['name']) && !empty($_FILES['edfile']['name']))
            {
                $refs = $this->txtFileToArray($_FILES['edfile']['tmp_name']);
                $result = $this->parseInboundReferences($refs);   
            }
            elseif(isset($_POST['edreferences']) && !empty($_POST['edreferences']))
            {
                $edrefs = $_POST['edreferences'];
                $refs = explode("\n", str_replace("\r", "", $edrefs));
                $result = $this->parseInboundReferences($refs);
            }
            
            $operation = array(
                'unidentified' => array(),
                'success' => array(),
                'conflict' => array(),
            );
            
            // Add the parsed boxes to the log
            if(isset($result) && is_array($result) && count($result) > 0)
            {
                
                foreach ($result as $pack)
                {
                    // First check if the pack is registred
                    $sql = "SELECT *
                        FROM ".$this->db->dbprefix('pack')." 
                        WHERE `barcode` LIKE ?";
                    $result = $this->db->query($sql, array($pack))->row();

                    if($result === null)
                    {
                        $operation['unidentified'][] = $pack;
                        continue;
                    }
                    else
                        $id_pack = (int)$result->id_pack;
                            
                    // Check if this pack has an open cycle
                    if($id_pack)
                    {
                        $sql = "SELECT *
                            FROM ".$this->db->dbprefix('shipping_pack')."
                            WHERE `outbound` = 1 
                            AND (isnull(`inbound`) OR `inbound` = 0)
                            AND `id_pack` = ?";
                        $result = $this->db->query($sql, array($id_pack))->row();

                        // This pack has an open cycle
                        if(isset($result->id_shipping_pack))
                        {
                            $data = array(
                                'inbound' => true,
                                'date_inbound' => date('Y-m-d H:i:s'),
                                'date_upd' => date('Y-m-d H:i:s'),
                            );
                            $this->db->where('id_shipping_pack',(int)$result->id_shipping_pack);
                            $this->db->update('shipping_pack',$data);
                            
                            $operation['success'][] = array(
                                'pack' => $pack,
                                'shipping' => $result->reference,
                                'date_outbound' => $result->date_outbound,
                            );
                        }
                        else
                            $operation['conflict'][] = $pack;
                    }
                    
                }
            }
            $dview['success'] = $operation['success'];
            $dview['unidentified'] = $operation['unidentified'];
            $dview['conflict'] = $operation['conflict'];
        }
        
        $this->display('import_references_inbound', $dview);
    }
    
    private function parseOutboundReferences($refs = array())
    {
        $shipping = array();
        $shipping_counter = 0;
        $boxes = array();
        $failed = array();
        $unassigned = array();
        
        $block_open = false;
        
        
        foreach ($refs as $value)
        {
            //echo $value.'<br>';
            
            // Find separator for block open
            $pat = '/SEP[0]+/';
            preg_match_all($pat, trim($value), $matchout);
            
            if(isset($matchout[0][0]))
            {   
                $block_open = 0;
            }
            
            // Find shipping code
            $pat = '/SH[0-9]+/';
            preg_match_all($pat, trim($value), $matchout);
            
            if(isset($matchout[0][0]) )
            {
                $shipping_counter = $shipping_counter + 1;
                $shipping[$shipping_counter] = array(
                    'reference' => $matchout[0][0],
                    'packs' => array()
                );
                $block_open = 1;
            }
                
            $pack_found = false;
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
                    {
                        $boxes[] = $code;
                        $pack_found = $code;
                    }
                    else
                        $failed[] = $code;
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
                    {
                        $boxes[] = $code;
                        $pack_found = $code;
                    }
                    else
                        $failed[] = $code;
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
                    {
                        $boxes[] = $code;
                        $pack_found = $code;
                    }
                    else
                        $failed[] = $code;
                }
            }
            
            // Add a pack to the shipping
            if($pack_found && $block_open >= 0)
            {
                $block_open = $block_open + 1;
                $shipping[$shipping_counter]['packs'][] = $pack_found;
            }
            elseif($pack_found)
            {
                $unassigned[] = $pack_found;
            }
        }
        
        $result = array(
            'shipping' => $shipping,
            'boxes' => $boxes,
            'failed' => $failed,
            'unassigned' => $unassigned,
        );
        
        return $result;
        
    }
    
    private function parseInboundReferences($refs = array())
    {
        $boxes = array();
        
        foreach ($refs as $value)
        {
            
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
                        $boxes[] = $code;
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
                        $boxes[] = $code;
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
                        $boxes[] = $code;
                }
            }
            
            
        }
        
        return $boxes;
    }
    
    private function txtFileToArray($file)
    {
        if(!file_exists($file))
            return array();
        
        $content = file_get_contents($file);
        return explode("\n", str_replace("\r", "", $content));
    }
    
}

