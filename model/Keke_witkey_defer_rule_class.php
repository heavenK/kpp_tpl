<?php
class Keke_witkey_defer_rule_class{
	public $_db;
	public $_tablename;
	public $_dbop;
	public $_defer_rule_id;	public $_defer_times;	public $_defer_cash_scale;	public $_model_id;
	public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	public $_replace=0;
	public $_where;
	function  keke_witkey_defer_rule_class(){		$this->_db = new db_factory ( );		$this->_dbop = $this->_db->create(DBTYPE);		$this->_tablename = TABLEPRE."witkey_defer_rule";	}	 
	public function getDefer_rule_id(){		return $this->_defer_rule_id ;	}	public function getDefer_times(){		return $this->_defer_times ;	}	public function getDefer_cash_scale(){		return $this->_defer_cash_scale ;	}	public function getModel_id(){		return $this->_model_id ;	}	public function getWhere(){		return $this->_where ;	}	public function getCache_config() {		return $this->_cache_config;	}
	public function setDefer_rule_id($value){		$this->_defer_rule_id = $value;	}	public function setDefer_times($value){		$this->_defer_times = $value;	}	public function setDefer_cash_scale($value){		$this->_defer_cash_scale = $value;	}	public function setModel_id($value){		$this->_model_id = $value;	}	public function setWhere($value){		$this->_where = $value;	}	public function setCache_config($_cache_config) {		$this->_cache_config = $_cache_config;	}
	 
	public  function __set($property_name, $value) {
		$this->$property_name = $value;
	}
	public function __get($property_name) {
		if (isset ( $this->$property_name )) {
			return $this->$property_name;
		} else {
			return null;
		}
	}
	 
	/**	 * insert into  keke_witkey_defer_rule  ,or add new record	 * @return int last_insert_id	 */	function create_keke_witkey_defer_rule(){		$data =  array();		if(!is_null($this->_defer_rule_id)){			$data['defer_rule_id']=$this->_defer_rule_id;		}		if(!is_null($this->_defer_times)){			$data['defer_times']=$this->_defer_times;		}		if(!is_null($this->_defer_cash_scale)){			$data['defer_cash_scale']=$this->_defer_cash_scale;		}		if(!is_null($this->_model_id)){			$data['model_id']=$this->_model_id;		}		return $this->_defer_rule_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace);	}
	/**	 * edit table keke_witkey_defer_rule	 * @return int affected_rows	 */	function edit_keke_witkey_defer_rule(){		$data =  array();		if(!is_null($this->_defer_rule_id)){			$data['defer_rule_id']=$this->_defer_rule_id;		}		if(!is_null($this->_defer_times)){			$data['defer_times']=$this->_defer_times;		}		if(!is_null($this->_defer_cash_scale)){			$data['defer_cash_scale']=$this->_defer_cash_scale;		}		if(!is_null($this->_model_id)){			$data['model_id']=$this->_model_id;		}		if($this->_where){			return $this->_db->updatetable($this->_tablename,$data,$this->getWhere());		}		else{			$where = array('defer_rule_id' => $this->_defer_rule_id);			return $this->_db->updatetable($this->_tablename,$data,$where);		}	}
	/**	 * query table: keke_witkey_defer_rule,if isset where return where record,else return all record	 * @return array	 */	function query_keke_witkey_defer_rule($is_cache=0, $cache_time=0){		if($this->_where){			$sql = "select * from $this->_tablename where ".$this->_where;		}		else{			$sql = "select * from $this->_tablename";		}		if ($is_cache) {			$this->_cache_config ['is_cache'] = $is_cache;		}		if ($cache_time) {			$this->_cache_config ['time'] = $cache_time;		}		if ($this->_cache_config ['is_cache']) {			if (CACHE_TYPE) {				$keke_cache = new keke_cache_class ( CACHE_TYPE );				$id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');				$data = $keke_cache->get ( $id );				if ($data) {					return $data;				} else {					$res = $this->_dbop->query ( $sql );					$keke_cache->set ( $id, $res,$this->_cache_config['time'] );					$this->_where = "";					return $res;				}			}		}else{			$this->_where = "";			return  $this->_dbop->query ( $sql );		}	}
	/**	 * query count keke_witkey_defer_rule records,if iset where query by where	 * @return int count records	 */	function count_keke_witkey_defer_rule(){		if($this->_where){			$sql = "select count(*) as count from $this->_tablename where ".$this->_where;		}		else{			$sql = "select count(*) as count from $this->_tablename";		}		$this->_where = "";		return $this->_dbop->getCount($sql);	}
	/**	 * delete table keke_witkey_defer_rule, if isset where delete by where	 * @return int deleted affected_rows	 */	function del_keke_witkey_defer_rule(){		if($this->_where){			$sql = "delete from $this->_tablename where ".$this->_where;		}		else{			$sql = "delete from $this->_tablename where defer_rule_id = $this->_defer_rule_id ";		}		$this->_where = "";		return $this->_dbop->execute($sql);	}

	 
	 
}
?>