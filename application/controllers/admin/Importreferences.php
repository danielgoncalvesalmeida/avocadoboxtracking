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
                $refs = $this->csvToArray($_FILES['edfile']['tmp_name']);
                $result = $this->parseShippingsAndSave($refs);
                $dview['success'] = (isset($result['success'])? $result['success'] : null );
                $dview['failed'] = (isset($result['failed'])? $result['failed'] : null );
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
            
            $failed = array();
            $success = array();
            
            // Add the parsed boxes to the log
            if(isset($result['sets']))
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
                        $msg = 'Shippings : '.implode(', ', $set['SH']).' are not for the same username. List of the shippings with their username : <b />';
                        foreach ($set_sh as $sh)
                        {
                            $msg .= $sh->reference.' | '.$sh->username.'<br />';
                        }
                        
                        $failed[] = array(
                            'tag' => 'Username missmatch',
                            'message' => $msg,
                        );
                        continue;
                    }
                    
                    
                    // We have so far no errors with the shippings
                    
                    $buffer_pack = array();
                    // Check each pack in order to know if one is assigned to a shipping not from the current set
                    foreach($set['P'] as $p)
                    {
                        $this->load->model('boxes_model');
                        // First check if the pack is known
                        $_p = $this->boxes_model->getByBarcode($p);
                        // Pack is missing, so add it
                        if(!$_p)
                        {
                            $data = array(
                                'barcode' => $p,
                                'date_add' => date('Y-m-d H:i:s'),
                                'date_upd' => date('Y-m-d H:i:s'),
                            );
                            $this->db->insert('pack',$data);
                            $id_pack = $this->db->insert_id();
                            $success[] = array(
                                'tag' => 'New pack added',
                                'message' => 'Missing pack <strong>'.$p.'</strong> was added',
                            );
                        }
                        else
                            $id_pack = $_p->id_pack;
                        $buffer_pack[] = $id_pack;
                    }
                    
                    // Everything is ready to insert shipping -> pack
                    
                    foreach ($set_sh as $sh)
                    {
                        
                        foreach ($buffer_pack as $p)
                        {
                            // First check if the record exists in order to avoid duplicates
                            $sql = "SELECT * 
                                    FROM ".$this->db->dbprefix('shipping_pack')."
                                    WHERE `id_pack` = ? 
                                    AND `id_shipping` = ?";
                            $result = $this->db->query($sql,array((int)$p, (int)$sh->id_shipping));
                            if($result->num_rows() == 0)
                            {
                                $data = array(
                                    'id_pack' => $p,
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
                                    'message' => 'Pack '.$p.' added as outbound for shipping '.$sh->reference,
                                );
                            }
                        }
                    }
                    
                    
                    
                    
                }
            }
            
      
            $dview['success'] = $success;
            $dview['failed'] = $failed;
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
                $packs = $this->parseInboundReferences($refs);   
            }
            elseif(isset($_POST['edreferences']) && !empty($_POST['edreferences']))
            {
                $edrefs = $_POST['edreferences'];
                $refs = explode("\n", str_replace("\r", "", $edrefs));
                $packs = $this->parseInboundReferences($refs);
            }
            
            $failed = array();
            $success = array();
            
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
                            'tag' => 'Unidentified pack',
                            'message' => $pack.' is unknown'
                        );
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
                                'message' => 'Pack <strong>'.$pack.'</strong> is inbound for '.(count($shippings)>1 ? 'shippings : '.implode(', ', $str_shippings) : 'shipping : '.implode(', ', $str_shippings) ),
                            );
                        }
                        else
                            $failed[] = array(
                                'tag' => 'Pack is already inbound',
                                'message' => $pack.' is currently inbound',
                            );
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
                continue;
            }
            
            // Check date
            $date = explode('-', $value[2]); 
         
            if(count($date) != 3 || empty($value[2]))
            {
                $failed[] = array('line' => $key+1, 'message' => 'Invalid delivery date');
                continue;
            }
            
            if(!checkdate($date[1],$date[2],$date[0]))
            {
                $failed[] = array('line' => $key+1, 'message' => 'Invalid delivery date');
                continue;
            }
            
            
            // Check shipping
            if(!$this->referenceIsValidShipping($value[0]))
            {
                $failed[] = array('line' => $key+1, 'message' => 'Invalid shipping');
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
                    // Update username if necessary
                    if($shipping->username != trim($value[1]))
                    {                    
                        $success[] = array(
                            'line' => $key+1, 
                            'message' => 'Update of shipping : '.trim($value[0]).' with former username "'.$shipping->username.'" updated to "'.trim($value[1]).'"'
                        );
                    }
                    if($shipping->date_delivery != trim($value[2]))
                    {                    
                        $success[] = array(
                            'line' => $key+1, 
                            'message' => 'Update of date delivery : '.trim($value[0]).' with former date delivery "'.$shipping->date_delivery.'" updated to "'.trim($value[2]).'"'
                        );
                    }
                    $data = array(
                        'username' => trim($value[1]),
                        'date_delivery' => trim($value[2]),
                        'date_upd' => date('Y-m-d H:i:s'),
                    );
                    $this->db->where('id_shipping',(int)$id_shipping);
                    $this->db->update('shipping',$data);
                    
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

