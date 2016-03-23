<?php if(!defined('ALLOW_ACCESS')) exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */ 
 
class Base {
	//change this
	var $pk = 'id';
	var $pk_auto = true;//Primary key auto increment
	var $fields = array('field1','field2');//fields in table (excluding Primary Key)
	var $key_prefix = "cache_key_prefix";//cache key prefix
	var $table = "table_name";//table name
	var $Cache = 'Cache';//cache object name
    
    var $error = '';
    
	//
	//CONSTRUCTOR
	//
	
	function Base()
	{	
		foreach($this->fields as $field)
		{
			$this->$field = '';
		}
        //$this->error = '';
        
		
	}
	
	//
	//INSERT
	//
	function insert() 
	{
		
		$Cache = $this->Cache;
		$pk = $this->pk;
		global $$Cache, $oDb;
		
		$fields = $values = '';
		
		
		
		if(!$this->pk_auto)
		{
			$fields .= "`".$pk."`,";
			$values .= "'".$this->$pk."',";
		}
		foreach($this->fields as $field)
		{
			$fields .= "`".$field."`,";
			$values .= "'".addslashes($this->$field)."',";
		}
		$fields = trim($fields,",");
		$values = trim($values,",");
		
		$oDb->query("INSERT INTO ".$this->table." (".$fields.") VALUES (".$values.")");
		
		
		
		if ($oDb->affectedRows()>0)
		{
			if($this->pk_auto) $this->$pk = $oDb->getInsertId();
            
			if(USING_MEMCACHE == '1'){
                $$Cache -> set($this->key_prefix . $this->$pk, $this);
            }
			
			return $this->$pk;
		}
		else return false;
	} 
    
    
    
    function get($field = '*', $con = false, $order = false, $start = false, $num = false){
        
        $Cache = $this->Cache;			
		global $$Cache, $oDb;
        
        $sql = " SELECT $field FROM $this->table ";
        
        if($con){
            $sql .= " WHERE 1 $con ";
        }
        
        if($order){
            $sql .= " ORDER BY $order ";
        }  
        
        if($start>=0 && $num>0){
            $sql .= " LIMIT $start, $num ";    
        }
        
        //var_dump($sql);
        
        $key = md5($sql);        
        if(USING_MEMCACHE == '1'){                    
            $cached_result = $$Cache->get($key);
            if($cached_result!=NULL){
                return $cached_result;
            }    
        }
        
        $query	= $oDb->query($sql);        
    	$result_array 	= $oDb->fetchAll($query);
                
        if(is_array($result_array) && count($result_array)>0){
            if(USING_MEMCACHE == '1'){                
                $$Cache->set($key,$result_array);
            }            
            return $result_array;
            
        }else{
            return null;
        }    
    }
    
    
    function count($con = false){
        global $oDb;
        $sql = " SELECT count(*) as tt FROM $this->table ";
        if($con){
            $sql .= " WHERE 1 $con ";
        }else{
            $sql .= " WHERE 1 ";
        }        
        
        //var_dump($sql);
        
        $query	= $oDb->query($sql);        
    	$result_array 	= $oDb->fetchAll($query);
        if(count($result_array)>0){
            return $result_array[0]['tt'];    
        }else{
            return 0;
        }
         
    }
    
    
	//
	//UPDATE
	//
	function update($id = 0,$update_fields = '')
	{
		
		$Cache = $this->Cache;
		$pk = $this->pk;		
		global $$Cache, $oDb;
        		
		//if($id == 0) return false;
		$this->$pk = $id;
		$set = '';
		if($update_fields == '') $update_fields = $this->fields;
		
		
		foreach($update_fields as $field)
		{
			
			$set .= '`'.$field.'`="'.addslashes($this->$field).'",';
			$arr[$field]	= $this->$field;
		}
	
        
		$set = trim($set,",");

		$this->read($id);
		
		foreach($this->fields as $field_name)
		{
			if(in_array($field_name,$update_fields)){
		
				$this->$field_name	= $arr[$field_name];
			}			
			
		}
		
        $sql = 'UPDATE '.$this->table.' SET '.$set.' WHERE `'.$pk.'` = "'.$this->$pk.'"';
        //var_dump($sql)        
		$query_id = $oDb->query($sql);

	   	if ($query_id > 0)
		{			
			$this->cleanValues();
            
            if(USING_MEMCACHE == '1'){
                $$Cache -> set($this->key_prefix . $this->$pk, $this);
            }
	   	}
	}
	
	
	/**
	 * Update cache, no update DB
	 *
	 * @param int $id
	 * @param array $update_fields
	 * @return unknown
	 */
	function updateOnlyCache($id = 0,$update_fields = '')
	{
		if(USING_MEMCACHE == '0'){
            return false;
        }
        
		$Cache = $this->Cache;
		$pk = $this->pk;		
		global $$Cache, $oDb;
				
		if($id == 0) return false;
				
		$this->$pk = $id;
		
		foreach($update_fields as $field)
		{				
			$arr[$field]	= $this->$field;
		}
				
		foreach($this->fields as $field_name)
		{
			if(in_array($field_name,$update_fields)){
		
				$this->$field_name	= $arr[$field_name];
			}			
			
		}
				
		$$Cache -> set($this->key_prefix . $this->$pk, $this);
				
	}
	
	/**
	 * Read data from Cache
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	function readOnlyCache($id = 0) 
	{
        
        if($id <0){
            return false;
        }
        
        if(USING_MEMCACHE == '0'){
            return false;
        }
        
		$Cache = $this->Cache;
		$pk = $this->pk;		
		global $$Cache, $oDb;
				
		if($id!=0) $this->$pk = $id;
		
		$item_key = $this->key_prefix . $this->$pk;
		$cached = $$Cache -> get($item_key);
					
		foreach($this->fields as $field)
		{
			if( $cached->$field != ""){
				$data = $cached->$field;
			}else{
				$data = "";
			}
			$this->$field =  $data;
		}
		$$Cache -> set($item_key, $this);
		return true;
		
		
	}
	

	//
	//READ
	//
	function read($id = 0) 
	{
        
        if($id <0){
            return false;
        }        
        $cached = NULL;
        $Cache = $this->Cache;
        $pk = $this->pk;		
        global $$Cache, $oDb;
        if($id!=0) $this->$pk = $id;
        
        $item_key = $this->key_prefix . $this->$pk;
        
        if(USING_MEMCACHE == '1'){                    
            $cached = $$Cache -> get($item_key);    
        }
        		
		if($cached!=NULL)
		{	
			foreach($this->fields as $field)
			{
				$this->$field = $cached->$field;
			}
			return true;
		}
		else 
		{
			
			$query = $oDb->query("SELECT * FROM ".$this->table." WHERE `".$pk."` = '".$this->$pk."'");			
			if($oDb->numRows($query)>0)
			{
				$result = $oDb->fetchObject($query);
					
				foreach($this->fields as $field)
				{
					$this->$field = $result->$field;
				}
                if(USING_MEMCACHE == '1'){
                    $$Cache -> set($item_key, $this);    
                }				
				return true;
			}
			else return false;
		}
	}
    
	//
	function getMulti($ids)
	{
		$Cache = $this->Cache;
		global $$Cache;
		$arr = array();
		foreach($ids as $id){
			$arr[] = $this->key_prefix.$id;
		}
		return $$Cache->get($arr);
	}
    
	//
	//DELETE
	//
	function remove($id=0)
	{
		if($id == 0) return false;
		
		$Cache = $this->Cache;
		$pk = $this->pk;
		global $$Cache,$oDb;
		
		$this->$pk = $id;
		
		$query_id = $oDb->query("DELETE FROM ".$this->table." WHERE `".$pk."` = '".$this->$pk."'");
		if($oDb->affectedRows()>0)
		{
            if(USING_MEMCACHE == '1'){
                $$Cache -> delete($this->key_prefix . $this->$pk);
            }
			return true;
		}
		else return false;
	}
	//
	//CLEAR CACHE
	//
	function clear($id = 0)
	{
		$Cache = $this->Cache;
		$pk = $this->pk;
		global $$Cache;
		if($id!=0) $this->$pk = $id;
		$$Cache -> delete($this->key_prefix . $this->$pk);
	}
	//
	//CLEAN SOME VALUE BEFORE ADDING TO CACHE
	//
	function cleanValues()
	{
		//stripslahes
		foreach($this->fields as $field)
		{
			$this->$field = stripslashes($this->$field);		
		}
	}
    
    
    
	
}
?>