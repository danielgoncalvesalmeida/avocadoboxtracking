<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profiles_model extends My_Model {

	function __construct()
	{
		parent::__construct();
        $this->table = 'right_profile';
        $this->primaryKey = 'id_right_profile';
	}
    
    /*
     * Get a given profile
     * includes domain_name
     * Is domain safe
     */
    public function getProfile($id)
    {
        $sql = "SELECT p.*, d.name AS domain_name
            FROM ".$this->db->dbprefix('right_profile')." p
            LEFT JOIN ".$this->db->dbprefix('domain')." d ON p.`id_domain` = d.`id_domain`
            WHERE p.`id_right_profile` = ?
            AND p.`id_domain` =  ".(int)getUserDomain();
        $result = $this->db->query($sql, array($id))->row();
        return $result;
    }
    
    /*
     * Get all profiles that are not deleted
     * includes domain_name
     * Is domain safe
     */
    public function getProfiles($p = null, $n = null, $orderby = null)
    {
        $p = (is_numeric($p) ? $p - 1 : null);
        
        $sql = "SELECT p.*, d.name AS domain_name, count(u.`id_user`) as users_count
            FROM ".$this->db->dbprefix('right_profile')." p
            LEFT JOIN ".$this->db->dbprefix('domain')." d ON p.`id_domain` = d.`id_domain`
            LEFT JOIN ".$this->db->dbprefix('user')." u ON u.`id_right_profile` = p.`id_right_profile`
            WHERE p.`id_domain` =  ".(int)getUserDomain()." 
            GROUP BY p.`id_right_profile` "
            .(empty($orderby) ? 'ORDER BY p.`name` ASC ' : 'ORDER BY '.$orderby)
            .((is_numeric($p) && is_numeric($n)) ? ' LIMIT '.$p * $n.','.$n : '');
        $result = $this->db->query($sql)->result();
        // The count returns always 0 even with no record
        if(!isset($result[0]->id_right_profile))
            return false;
        return $result;
    }
    
    /*
     * Check if right profile id does exist
     * Is domain safe
     */
    public function checkRightProfileIsValid($id)
    {
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('right_profile')." s
            WHERE s.`id_domain` = ".(int)getUserDomain()."
            AND s.`id_right_profile` = ?";
        $result = $this->db->query($sql,array((int)$id));
        if($result->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    /*
     * By add replicate the right_name content in table to right_assign
     * This forces a profile name to have every right name mapped
     * Is domain safe
     */
    public function initProfile($id_profile = null)
    {
        if(empty($id_profile))
            return false;
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('right_name');
        $result = $this->db->query($sql)->result();
        $data = array();
        foreach ($result as $row)
        {
            $data[] = array(
                'id_domain' => (int)getUserDomain(),
                'id_right_name' => (int)$row->id_right_name,
                'id_right_profile' => (int)$id_profile,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s'),
            );
        }
        if(count($data) > 0)
            $this->db->insert_batch('right_assign', $data);
    }
    
    /*
     * Sync profiles and rights names
     * Is domain safe
     */
    public function syncRightNames($id_profile = null)
    {
        $sql = "SELECT `id_right_name`
            FROM ".$this->db->dbprefix('right_name')." 
            WHERE `id_right_name` NOT IN (
                SELECT `id_right_name` FROM ".$this->db->dbprefix('right_assign')." 
                WHERE id_right_profile = ?)";
        $result = $this->db->query($sql,$id_profile)->result();
        $data = array();
        foreach ($result as $key => $value) {
            $data[] = array(
                'id_domain' => (int)getUserDomain(),
                'id_right_name' => (int)$value->id_right_name,
                'id_right_profile' => (int)$id_profile,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s'),
            );
        }
        if(count($data) > 0)
            $this->db->insert_batch('right_assign', $data);
    }
    
    /*
     * Reset all the settings for this profile in right_assign
     * All right are set to false
     * Is domain safe
     */
    public function resetProfileRightAssign($id_profile = null)
    {
        $data = array('view' => 0, 'create' => 0, 'edit' => 0, 'delete' => 0);
        $this->db->where('id_right_profile',(int)$id_profile);
        $this->db->where('id_domain',(int)getUserDomain());
        $this->db->update('right_assign',$data);
    }
    
    /*
     * Get the permissions related to a given profile
     * Is domain safe
     */
    public function getPermissions($id_right_profile)
    {
        if(!is_numeric($id_right_profile))
            return false;
        $sql = "SELECT p.*, u.name AS right_name
            FROM ".$this->db->dbprefix('right_assign')." p
            LEFT JOIN ".$this->db->dbprefix('right_name')." u ON u.`id_right_name` = p.`id_right_name`
            WHERE p.`id_domain` =  ".(int)getUserDomain()."
            AND p.`id_right_profile` = ?";
        $result = $this->db->query($sql,array((int)$id_right_profile));
        return $result->result();
    }
    
    
    /*
     * Check if a profile exists with the same name
     * Is domain safe
     */
    public function profileExists($name = null, $id_exclude = null)
    {
        if(is_null($name))
            return false;
        $sql = "SELECT *
            FROM ".$this->db->dbprefix('right_profile')." 
            WHERE `name` LIKE ?
            AND `id_right_profile` NOT IN (".(!empty($id_exclude)? $id_exclude : '0').")
            AND `id_domain` =  ".(int)getUserDomain();
        $result = $this->db->query($sql,array($name));
        if($result->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    
    /*
     * Delete the record (flag delete)
     * Is domain safe
     */
    public function countUsers($id)
    {
        $sql = "SELECT count(`id_user`) as users_count
            FROM ".$this->db->dbprefix('user')." 
            WHERE `id_right_profile` = ?
            AND `deleted` = 0
            AND `id_domain` =  ".(int)getUserDomain();
        $result = $this->db->query($sql,array($id))->result();
        if(isset($result[0]->users_count))
            return (int)$result[0]->users_count;
        else
            return false; 
    }
    
    /*
     * Delete the record (flag delete)
     * Is domain safe
     */
    public function delete($id)
    {
        return $this->db->delete('right_profile', array('id_right_profile' => $id,'id_domain' => (int)getUserDomain())); 
    }
    
}

