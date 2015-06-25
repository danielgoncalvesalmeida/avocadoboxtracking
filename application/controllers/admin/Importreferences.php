<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Importreferences extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'admin';
        parent::__construct();
        $this->isZone('app');
        $this->addJs('tabs.js');
        $this->load->model('log_model');
    }
    
	public function index()
	{
        redirect('admin/dashboard');
    }
    
    
    public function addshipping()
    {
        $this->load->helper('form');
        $this->setTitle('Import shipping | '.$this->config->item('appname'));
        $dview = array();
        
        $isSubmit = (isset($_POST['submitImport']) ? true : false);
        
        if($isSubmit)
        {
            if(isset($_FILES['edfile']['name']) && !empty($_FILES['edfile']['name']))
            {
                $this->log_model->log(array('type' => 1, 'operation' => 1, 'message' => 'Upload of file : '.$_FILES['edfile']['name']));
                $refs = $this->csvToArray($_FILES['edfile']['tmp_name']);
                if(count($refs) == 0)
                {
                    $dview['failed'][] = array('message' => 'Uploaded file is empty');
                    $this->log_model->log(array('type' => 1, 'operation' => 1, 'message' => 'Uploaded file is empty'));
                }
                else
                {
                    $result = $this->parseShippingsAndSave($refs);
                    $dview['success'] = (isset($result['success'])? $result['success'] : null );
                    $dview['failed'] = (isset($result['failed'])? $result['failed'] : null );
                }
            }
            
        } 
            
        $this->display('import_shipping', $dview);
    }
    
    public function addoutbound()
    {
        $this->load->helper('form');
        $this->setTitle('Import outbound | '.$this->config->item('appname'));
        $dview = array();
        
        $isSubmit = (isset($_POST['submitImport']) ? true : false);
        
        if($isSubmit)
        {
            $failed = array();
            $success = array();
            $warning = array();
            
            if(isset($_FILES['edfile']['name']) && !empty($_FILES['edfile']['name']))
            {
                $this->log_model->log(array('type' => 1, 'operation' => 2, 'message' => 'Upload of file : '.$_FILES['edfile']['name']));
                $refs = $this->txtFileToArray($_FILES['edfile']['tmp_name']);
                
                if(count($refs) == 0)
                {
                    $failed[] = array('message' => 'Uploaded file is empty');
                    $this->log_model->log(array('type' => 1, 'operation' => 2, 'message' => 'Uploaded file is empty'));
                }
                else
                    $result = $this->parseOutboundReferences($refs);   
            }
            elseif(isset($_POST['edreferences']) && !empty($_POST['edreferences']))
            {
                $this->log_model->log(array('type' => 1, 'operation' => 2, 'message' => 'References manually added'));
                $edrefs = $_POST['edreferences'];
                $refs = explode("\n", str_replace("\r", "", $edrefs));
                if(count($refs) == 0)
                {
                    $failed[] = array('message' => 'No references have been provided');
                    $this->log_model->log(array('type' => 1, 'operation' => 2, 'message' => 'Manuallay provided references is empty'));
                }
                else
                    $result = $this->parseOutboundReferences($refs);
            }
            // No reference were provided
            else
            {
                $dview['submit_error'] = 'No references were submited';
                $this->display('import_references_outbound', $dview);
                return;
            }
            
            $dview['parse_result'] = $result;
            
            // Begin with adding the failed boxes to $failed
            foreach ($result['failed'] as $f)
            {
                $failed[] = array(
                    'tag' => 'Incorrect box reference',
                    'message' => 'Box reference <strong>'.$f.'</strong> is incorrect',
                );
            }
            
            // Add the parsed boxes to the log
            if(isset($result['sets']) && count($result['sets']) > 0)
            {
                $this->load->model('shipping_model');
                foreach ($result['sets'] as $set)
                {
                    // Buffer for found shippings
                    $set_sh = array();
                    
                    // Buffer for missing shippings
                    $sh_missing = array();
                    
                    // First get the data of the shipping in set
                    // $set_sh -> stores the shipping data
                    foreach ($set['SH'] as $sh)
                    {
                        $_t = $this->shipping_model->exists($sh);
                        if($_t)
                            $set_sh[] = $_t;
                        else
                            $sh_missing[] = $sh;
                    }
                    
                    // Check if all the shipping exist
                    if(count($sh_missing) > 0)
                    {
                        if(count($set['SH']) > 1)
                        {
                            $msg = 'Shippings : '.implode(', ', $set['SH']);
                            if(count($sh_missing) == 1)
                                $msg .= ' have the following shipping who is missing : <br />'.  implode(', ', $sh_missing);
                            else
                                $msg .= ' have the following shippings who are missing : <br />'.  implode(', ', $sh_missing);
                        }
                        else
                            $msg = 'Shipping : '.$sh.' is unknown';
                        
                        $this->log_model->log(array('type' => 3, 'operation' => 2, 'message_short' => 'One or more shippings are unknown', 'message' => $msg));
                        $failed[] = array(
                            'tag' => 'One or more shippings are unknown',
                            'message' => $msg,
                        );
                        continue;
                    }
                    
                    // Check if the usernames are for the same user
                    $username = '';
                    $username_missmatch = array();
                    foreach ($set_sh as $sh) 
                    {
                        if(empty($username))
                            $username = $sh->username;
                        
                        if(strcasecmp($username, $sh->username) != 0)
                            $username_missmatch[] = $sh->username;
                    }
                    
                    if(count($username_missmatch) > 0)
                    {
                        $msg = 'Shippings : '.implode(', ', $set['SH']).' are not for the same username. List of the shippings with their username : <br />';
                        foreach ($set_sh as $sh)
                        {
                            $msg .= '<strong>'.$sh->reference.' | '.$sh->username.'</strong><br />';
                        }
                        
                        $this->log_model->log(array('type' => 3, 'operation' => 2, 'message_short' => 'Username missmatch', 'message' => $msg));
                        $failed[] = array(
                            'tag' => 'Username missmatch',
                            'message' => $msg,
                        );
                        // In case of username missmatch for the shippings, the full set is discarded
                        continue;
                    }
                    
                    
                    // We have so far no errors with the shippings
                    
                    $buffer_pack = array();
                    // Check each pack in order to know if one is assigned to a shipping not from the current set
                    foreach($set['P'] as $p_barcode)
                    {
                        $this->load->model('boxes_model');
                        // First check if the pack is known
                        $_p = $this->boxes_model->getByBarcode($p_barcode);
                        // Pack is missing, so add it
                        if(!$_p)
                        {
                            $data = array(
                                'barcode' => $p_barcode,
                                'date_add' => date('Y-m-d H:i:s'),
                                'date_upd' => date('Y-m-d H:i:s'),
                            );
                            $this->db->insert('pack',$data);
                            $id_pack = $this->db->insert_id();
                            
                            $this->log_model->log(array('type' => 1, 'operation' => 2, 'message_short' => 'New box added', 'message' => 'Missing box <strong>'.$p_barcode.'</strong> was added'));
                            $warning[] = array(
                                'tag' => 'New box added',
                                'message' => 'Missing box <strong>'.$p_barcode.'</strong> was added',
                            );
                        }
                        else
                            $id_pack = $_p->id_pack;
                        $buffer_pack[$id_pack] = $p_barcode;
                    }
                    
                    // Check based on the packs in buffer_pack if they are not already assigned to another shipping
                    // and if so, if the shippings are for the same username
                    foreach ($buffer_pack as $p_id => $p_barcode)
                    {
                        // Get the current username for this pack in case of outbound
                        $sql = "SELECT * 
                                FROM ".$this->db->dbprefix('shipping_pack')." sp
                                LEFT JOIN ".$this->db->dbprefix('shipping')." s ON (sp.id_shipping = s.id_shipping)
                                WHERE sp.id_pack = ?
                                AND sp.inbound = 0
                                GROUP BY username";
                        $result = $this->db->query($sql,array((int)$p_id));
                        // One result : So far so good if the username is the right one
                        if($result->num_rows() == 1)
                        {
                            $row = $result->row();
                            // If pack is in conflict -> do not handle it and notify it
                            if(strcasecmp($row->username, $username) != 0)
                            {
                                $msg = 'Can\'t add box '.$p_barcode.' to shipping'.(count($set['SH']) > 1 ? 's ':'')
                                        .': <strong>'.implode(', ', $set['SH']).'</strong> assigned to customer <strong>'.$username.'</strong>.'
                                        .' It is already in outbound for shipping <strong>'.$row->reference.'</strong> for customer <strong>'.$row->username.'</strong>';
                                $failed[] = array(
                                    'tag' => 'Box discarded due to username missmatch',
                                    'message' => $msg,
                                );
                                $this->log_model->log(array('type' => 3, 'operation' => 2, 'message_short' => 'Box discarded due to username missmatch', 'message' => $msg));
                                // Remove this pack from buffer_pack
                                $packs_to_be_removed[] = $p_id;
                            }
                        }
                        // Definately not good: This pack is already assigned to multiple usernames and in outbound state
                        // this is virualy impossible, though remove it
                        elseif($result->num_rows() > 1)
                            $packs_to_be_removed[] = $p_id;
                    }
                    // Remove packs if needed
                    if(isset($packs_to_be_removed))
                    {
                        foreach ($packs_to_be_removed as $v) 
                            unset($buffer_pack[$v]);
                    }
                    
                    // Everything is ready to insert shipping -> pack
                    
                    foreach ($set_sh as $sh)
                    {
                        
                        foreach ($buffer_pack as $p_id => $p_barcode)
                        {
                            // First check if the record exists in order to avoid duplicates
                            $sql = "SELECT * 
                                    FROM ".$this->db->dbprefix('shipping_pack')."
                                    WHERE `id_pack` = ? 
                                    AND `id_shipping` = ?";
                            $result = $this->db->query($sql,array((int)$p_id, (int)$sh->id_shipping));
                            if($result->num_rows() == 0)
                            {
                                $data = array(
                                    'id_pack' => (int)$p_id,
                                    'id_shipping' => (int)$sh->id_shipping,
                                    'outbound' => true,
                                    'id_user_outbound' => getUserId(),
                                    'date_outbound' => date('Y-m-d'),
                                    'date_add' => date('Y-m-d H:i:s'),
                                    'date_upd' => date('Y-m-d H:i:s'),
                                );
                                $this->db->insert('shipping_pack',$data);
                                $success[] = array(
                                    'tag' => 'Outbound added',
                                    'message' => 'Box '.$p_barcode.' added as outbound for shipping '.$sh->reference,
                                );
                                $this->log_model->log(array('type' => 1, 'operation' => 2, 'message_short' => 'Outbound added', 'message' => 'Box '.$p_barcode.' added as outbound for shipping '.$sh->reference));
                            }
                            else
                            {
                                $warning[] = array(
                                    'tag' => 'Outbound already exists',
                                    'message' => 'Box '.$p_barcode.' is already an outbound for shipping '.$sh->reference,
                                );
                                $this->log_model->log(array('type' => 1, 'operation' => 2, 'message_short' => 'Outbound already exists', 'message' => 'Box '.$p_barcode.' is already an outbound for shipping '.$sh->reference));
                            }
                        }
                    }
                    
                    
                    
                    
                } // End of sets foreach
            }
            else
            // result contains no sets
            {
                $failed[] = array('message' => 'No shipping sets found in provided references');
                $this->log_model->log(array('type' => 2, 'operation' => 1, 'message_short' => 'No shipping sets found', 'message' => 'No shipping sets found in provided references'));
            }
            
      
            $dview['success'] = $success;
            $dview['failed'] = $failed;
            $dview['warning'] = $warning;
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
            $failed = array();
            $success = array();
            
            if(isset($_FILES['edfile']['name']) && !empty($_FILES['edfile']['name']))
            {
                $this->log_model->log(array('type' => 1, 'operation' => 3, 'message' => 'Upload of file : '.$_FILES['edfile']['name']));
                $refs = $this->txtFileToArray($_FILES['edfile']['tmp_name']);
                $packs = $this->parseInboundReferences($refs);   
            }
            elseif(isset($_POST['edreferences']) && !empty($_POST['edreferences']))
            {
                $this->log_model->log(array('type' => 1, 'operation' => 3, 'message' => 'References manually added'));
                $edrefs = $_POST['edreferences'];
                $refs = explode("\n", str_replace("\r", "", $edrefs));
                $packs = $this->parseInboundReferences($refs);
            }
            else
            {
                $dview['submit_error'] = 'No references were submited';
                $this->display('import_references_inbound', $dview);
                return;
            }
            
            if(isset($packs) && count($packs) == 0)
            {
                $failed[] = array('message' => 'No references found');
                $this->log_model->log(array('type' => 1, 'operation' => 3, 'message' => 'No references found'));
            }
            
            
            // Add the parsed boxes to the log
            if(isset($packs) && is_array($packs) && count($packs) > 0)
            {
             
                foreach ($packs as $pack)
                {
                    // First check if the pack is registred
                    $sql = "SELECT *
                        FROM ".$this->db->dbprefix('pack')." 
                        WHERE `barcode` LIKE ?";
                    $_pack = $this->db->query($sql, array($pack))->row();

                    if($_pack === null)
                    {
                        $failed[] = array(
                            'tag' => 'Unidentified box',
                            'message' => $pack.' is unknown'
                        );
                        $this->log_model->log(array('type' => 2, 'operation' => 3, 'message_short' => 'Unidentified box', 'message' => $pack.' is unknown'));
                        continue;
                    }
                    else
                        $id_pack = (int)$_pack->id_pack;
                            
                    // Check if this pack has an open cycle
                    if($id_pack)
                    {
                        $sql = "SELECT *
                            FROM ".$this->db->dbprefix('shipping_pack')." sp
                            LEFT JOIN ".$this->db->dbprefix('shipping')." s ON sp.`id_shipping` = s.`id_shipping`
                            WHERE sp.`outbound` = 1 
                            AND (isnull(sp.`inbound`) OR sp.`inbound` = 0)
                            AND sp.`id_pack` = ?";
                        $shippings = $this->db->query($sql, array($id_pack))->result();

                        // This pack has an open cycle
                        if($shippings)
                        {
                            $str_shippings = array();
                            foreach($shippings as $res)
                            {
                                $data = array(
                                    'inbound' => true,
                                    'id_user_inbound' => getUserId(),
                                    'date_inbound' => date('Y-m-d H:i:s'),
                                    'date_upd' => date('Y-m-d H:i:s'),
                                );
                                $this->db->where('id_shipping_pack',(int)$res->id_shipping_pack);
                                $this->db->update('shipping_pack',$data);
                                $str_shippings[] = $res->reference;
                            }
                            
                            
                            $success[] = array(
                                'tag' => 'Successful inbound',
                                'message' => 'Box <strong>'.$pack.'</strong> is inbound for '.(count($shippings)>1 ? 'shippings : '.implode(', ', $str_shippings) : 'shipping : '.implode(', ', $str_shippings) ),
                            );
                            $this->log_model->log(array('type' => 1, 'operation' => 3, 'message_short' => 'Successful inbound', 'message' => 'Box <strong>'.$pack.'</strong> is inbound for '.(count($shippings)>1 ? 'shippings : '.implode(', ', $str_shippings) : 'shipping : '.implode(', ', $str_shippings) ) ));
                        }
                        else
                            $failed[] = array(
                                'tag' => 'Box is already inbound',
                                'message' => $pack.' is currently inbound',
                            );
                            $this->log_model->log(array('type' => 3, 'operation' => 3, 'message_short' => $pack.' is currently inbound' ));
                    }
                    
                }
            }
            $dview['success'] = $success;
            $dview['failed'] = $failed;
        }
        
        $this->display('import_references_inbound', $dview);
    }
    
    /*
     *  Parse the shippings and packs from a sequenced array
     *  Return an array containing succcesful results and fails etc
     */
    private function parseOutboundReferences($refs = array())
    {
        $sets = array(); // Correct sets of shipping (M):(N) boxes
        $boxes = array(); // Boxes identified during the parse
        $shippings = array(); // Shippings detected
        $orphans = array(); // An orphan is a P without a preceding SH
        $failed = array(); // P* that are out of range
        
        $set = array();
        foreach ($refs as $value)
        {
            // Find separator -> flush the set (to be used for errors bypass
            $pat = '/SEP[0]+/';
            preg_match_all($pat, trim($value), $matchout);
            
            if(isset($matchout[0][0]))
                $set = array();
            
            // Find shipping code
            $pat = '/SH[0-9]+/';
            preg_match_all($pat, trim($value), $matchout);
            
            if(isset($matchout[0][0]) )
            {
                if(!isset($set['P']))
                {
                    // Cycle has started now or is still ongoing
                    $set['SH'][] = $matchout[0][0];
                    $shippings[] = $matchout[0][0];
                }
                elseif(isset($set['SH']) && (isset($set['P']) || isset($set['P-ERROR'])) )
                {
                    // Set contains a SH and P and now a SH is in the run -> this starts a new cycle
                    $sets[] = $set;
                    $set = array();
                    $set['SH'][] = $matchout[0][0];
                }
                
            }
                
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
                        if(isset($set['SH']))
                        {
                            $set['P'][] = $code;
                            $boxes[] = $code;
                        }
                        else
                            // Box is orphan i.e. an SH must come first
                            $orphans[] = $code;
                    }
                    else
                    {
                        $failed[] = $code;
                        $set['P-ERROR'] = true;
                    }
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
                        if(isset($set['SH']))
                        {
                            $set['P'][] = $code;
                            $boxes[] = $code;
                        }
                        else
                            // Box is orphan i.e. an SH must come first
                            $orphans[] = $code;
                    }
                    else
                    {
                        $failed[] = $code;
                        $set['P-ERROR'] = true;
                    }
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
                        if(isset($set['SH']))
                        {
                            $set['P'][] = $code;
                            $boxes[] = $code;
                        }
                        else
                            // Box is orphan i.e. an SH must come first
                            $orphans[] = $code;
                    }
                    else
                    {
                        $failed[] = $code;
                        $set['P-ERROR'] = true;
                    }
                }
            }
        }
        
        // There is one last set to be added
        if(isset($set['SH']) && isset($set['P']))
        {
            $sets[] = $set;
            foreach ($set['SH'] as $v) 
                $shippings[] = $v;
        }
        
        $result = array(
            'sets' => $sets,
            'boxes' => $boxes,
            'shippings' => $shippings,
            'failed' => $failed,
            'orphans' => $orphans,
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
    
    private function parseShippingsAndSave($refs = array())
    {
        $failed = array();
        $success = array();
        foreach ($refs as $key => $value)
        {
            // Discard empty lines
            if($value[0] === null)
                continue;
            
            // Line without the minimum requiered fields
            if(count($value) < 3 || !isset($value[0]) || !isset($value[1]) || !isset($value[2]))
            {
                $failed[] = array('line' => $key+1, 'message' => 'Incomplete field set');
                continue;
            }
            
            // Check username
            if(!$this->isValidUsername($value[1]))
            {
                $failed[] = array('line' => $key+1, 'message' => 'Invalid username');
                $this->log_model->log(array('type' => 3, 'operation' => 1, 'message' => 'Invalid username'));
                continue;
            }
            
            // Check date
            $date = explode('-', $value[2]); 
         
            if(count($date) != 3 || empty($value[2]))
            {
                $failed[] = array('line' => $key+1, 'message' => 'Invalid delivery date');
                $this->log_model->log(array('type' => 3, 'operation' => 1, 'message' => 'Invalid delivery date'));
                continue;
            }
            
            if(!checkdate($date[1],$date[2],$date[0]))
            {
                $failed[] = array('line' => $key+1, 'message' => 'Invalid delivery date');
                $this->log_model->log(array('type' => 3, 'operation' => 1, 'message' => 'Invalid delivery date'));
                continue;
            }
            
            
            // Check shipping
            if(!$this->referenceIsValidShipping($value[0]))
            {
                $failed[] = array('line' => $key+1, 'message' => 'Invalid shipping');
                $this->log_model->log(array('type' => 3, 'operation' => 1, 'message' => 'Invalid shipping reference : '.$value[0]));
                continue;
            }
            else
            {
                $this->load->model('shipping_model');
                $shipping = $this->shipping_model->exists(trim($value[0]));

                if(!$shipping)
                {
                    $success[] = array(
                        'line' => $key+1, 
                        'message' => 'Add of shipping : '.trim($value[0])
                    );
                    $this->log_model->log(array('type' => 1, 'operation' => 1, 'message' => 'Add of shipping : '.trim($value[0])));
                    $data = array(
                        'reference' => trim($value[0]),
                        'username' => trim($value[1]),
                        'date_delivery' => trim($value[2]),
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_upd' => date('Y-m-d H:i:s'),
                    );
                    $this->db->insert('shipping',$data);
                    $id_shipping = $this->db->insert_id();
                }
                else
                {
                    $id_shipping = $shipping->id_shipping;
                    $needsupdate = false;
                    // Update username if necessary
                    if($shipping->username != trim($value[1]))
                    {             
                        $needsupdate = true;
                        $success[] = array(
                            'line' => $key+1, 
                            'message' => 'Update of shipping : '.trim($value[0]).' with former username "'.$shipping->username.'" updated to "'.trim($value[1]).'"'
                        );
                        $this->log_model->log(array('type' => 1, 'operation' => 1, 'message' => 'Update of shipping : '.trim($value[0]).' with former username "'.$shipping->username.'" updated to "'.trim($value[1]).'"'));
                    }
                    // Update of date delivery if necessary
                    if($shipping->date_delivery != trim($value[2]) && isset($_POST['update_delivery_date']))
                    {          
                        $needsupdate = true;
                        $success[] = array(
                            'line' => $key+1, 
                            'message' => 'Update of date delivery : '.trim($value[0]).' with former date delivery "'.$shipping->date_delivery.'" updated to "'.trim($value[2]).'"'
                        );
                        $this->log_model->log(array('type' => 1, 'operation' => 1, 'message' => 'Update of date delivery : '.trim($value[0]).' with former date delivery "'.$shipping->date_delivery.'" updated to "'.trim($value[2]).'"'));
                    }
                    
                    if($needsupdate)
                    {
                        $data = array(
                            'username' => trim($value[1]),
                            'date_delivery' => trim($value[2]),
                            'date_upd' => date('Y-m-d H:i:s'),
                        );
                        $this->db->where('id_shipping',(int)$id_shipping);
                        $this->db->update('shipping',$data);
                    }
                    else
                    {
                        $success[] = array(
                            'line' => $key+1, 
                            'message' => 'Shipping '.trim($value[0]).' already exists and is up to date',
                        );
                        $this->log_model->log(array('type' => 1, 'operation' => 1, 'message' => 'Shipping '.trim($value[0]).' already exists and is up to date'));
                    }
                    
                }
            }
            
            
        }
        
        $result = array(
            'failed' => $failed,
            'success' => $success,
        );
        return $result;
    }
    
    private function txtFileToArray($file)
    {
        if(!file_exists($file))
            return array();
        
        $content = file_get_contents($file);
        return explode("\n", str_replace("\r", "", $content));
    }
    
    private function csvToArray ($csvFile) 
	{   
        $csvArray = array();
        if (is_file($csvFile)) 
		{
            $row = 0;
            $handle = fopen($csvFile, "r");
            while (($data = fgetcsv($handle, 2500, ";")) !== FALSE) 
			{
                for ($c=0; $c < count($data); $c++) 
				{
                    $csvArray[$row][] = $data[$c];
                }
                $row++;
            }
            fclose($handle);
        }
        return $csvArray;
    }
    
    public function referenceIsValidShipping($ref)
    {
        // Find shipping code
        $pat = '/SH[0-9]+/';
        preg_match_all($pat, trim($ref), $matchout);

        if(isset($matchout[0][0]) )
            return true;
        else
            return false;
    }
    
    public function isValidUsername($str)
    {
        if(empty($str))
            return false;
        else
            return true;
        
        $pat = '/SH[0-9]+/';
        preg_match_all($pat, trim($ref), $matchout);

        if(isset($matchout[0][0]) )
            return true;
        else
            return false;
    }
    
}

