<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_Model extends CI_Model {

	protected $table;
	protected $relatedTable;
	protected $primaryKey;
	
	public function __construct()
	{
		parent::__construct(); 
	}
	
	public function getValue($field, $where = array(), $orderby = null, $related = true)
	{
		$select =  $this->db->select($field.' as field')
                        ->from($this->db->dbprefix($this->table.' a'));
						
		if(!empty($this->relatedTable) && !empty($related)){ 
			$select->join($this->db->dbprefix($this->relatedTable.' r'), 'r.'.$this->primaryKey.' = a.'.$this->primaryKey, 'INNER');
        }	
        if(!empty($where)){
			foreach($where as $key=>$value){
				$select->where($key,$value);
			}
		}
        if(!empty($orderby)){
			$select->order_by($orderby, 'DESC');
		}else{
		  $select->order_by('a.'.$this->primaryKey, 'DESC');
		}
		$select =  $select->limit(1)
							->get()
							->result(); 
		return (!empty($select[0]))?$select[0]->field:false;
	}
	
	public function getValues($select = null, $wereClauses = array(), $limit = null, $offset = null, $orderby = null, $orderDirection = null, $related=true, $likes = array())
	{
		$select = (!empty($select))?$select:'*';
		$select =  $this->db->select($select)
                        ->from($this->db->dbprefix($this->table.' a'));
						
		if(!empty($this->relatedTable) && !empty($related)){ 
			$select->join($this->db->dbprefix($this->relatedTable.' r'), 'r.'.$this->primaryKey.' = a.'.$this->primaryKey.'', 'INNER');
		}
		
		if(!empty($wereClauses)){
			foreach($wereClauses as $key=>$value){
				$select->where($key,$value);
			}
		}
        
        if(!empty($likes)){
			foreach($likes as $key=>$value){
				$select->or_like($key, $value, 'both');
			}
        } 
        
        $select->group_by('a.'.$this->primaryKey);
		
		if(!empty($limit) && isset($offset)){
			$select->limit($limit, $offset);
		}
		
		if(!empty($orderby)){
			$select->order_by($orderby, $orderDirection);
		}else{
		  $select->order_by('a.'.$this->primaryKey, 'DESC');
		}
        		
		return $select->get()->result();
	}
	
	public function getRow($key)
	{
		$select =  $this->db->select('*')
                        ->from($this->db->dbprefix($this->table.' a'));
						
		if(!empty($this->relatedTable)){ 
			$select->join($this->db->dbprefix($this->relatedTable.' r'), 'r.'.$this->primaryKey.' = a.'.$this->primaryKey, 'INNER');
			$select->where(array('a.'.$this->primaryKey => $key));
		}else{
			$select->where($this->primaryKey, $key);
		}
		$select =  $select->limit(1)
							->get()
							->result(); 
		return $select[0];
	}
	
	public function save($data, $where = null)
	{ 
        if(!empty($where)){ 
            return $this->db->update($this->table, $data, $where);
        }else{ 
            if($this->db->insert($this->table, $data)){
                return $this->db->insert_id();
            }
            return false;
        }
	}
	
	public function delete($where)
	{
        if(is_integer($where))
		{
			$where = array($this->primaryKey => $where);
		}  
		return (bool) $this->db->where($where)
                               ->delete($this->table);
	}
	
	public function total($where = array())
	{
	   $count = $this->db;
       if(!empty($where)){
			foreach($where as $key=>$value){
				$count->where($key,$value);
			}
		}
        return $count->from($this->table)->count_all_results(); 
	}
    
    public function getAllValues($table, $where=array()){
        $this->load->model('basic_model', 'db');
        $select =  $this->db->select("*")
                        ->from($this->db->dbprefix($table));
        if(!empty($where)){
			$select->where($where);
		}
        
        return $select->get()->result();
    }
}
// END Model Class

/* End of file Model.php */
/* Location: ./application/core/Model.php */