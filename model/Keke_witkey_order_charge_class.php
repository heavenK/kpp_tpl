<?php
class Keke_witkey_order_charge_class{
	public $_db;
	public $_tablename;
	public $_dbop;
	public $_order_id;
	public $_order_type;
	public $_pay_type;
	public $_return_order_id;
	public $_obj_id;
	public $_uid;
	public $_username;
	public $_pay_time;
	public $_pay_money;
	public $_order_status;
	public $_pay_info;

	public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	public $_replace=0;
	public $_where;
	function  keke_witkey_order_charge_class(){
		var_dump("1");
		$this->_db = new db_factory ( );
		var_dump("2");
		$this->_dbop = $this->_db->create(DBTYPE);
		var_dump("3");
		$this->_tablename = TABLEPRE."witkey_order_charge";
		var_dump("4");
	}
	 
	public function getOrder_id(){
		return $this->_order_id ;
	}
	public function getOrder_type(){
		return $this->_order_type ;
	}
	public function getPay_type(){
		return $this->_pay_type ;
	}
	public function getReturn_order_id(){
		return $this->_return_order_id ;
	}
	public function getObj_id(){
		return $this->_obj_id ;
	}
	public function getUid(){
		return $this->_uid ;
	}
	public function getUsername(){
		return $this->_username ;
	}
	public function getPay_time(){
		return $this->_pay_time ;
	}
	public function getPay_money(){
		return $this->_pay_money ;
	}
	public function getOrder_status(){
		return $this->_order_status ;
	}
	public function getPay_info(){
		return $this->_pay_info ;
	}
	public function getWhere(){
		return $this->_where ;
	}
	public function getCache_config() {
		return $this->_cache_config;
	}

	public function setOrder_id($value){
		$this->_order_id = $value;
	}
	public function setOrder_type($value){
		$this->_order_type = $value;
	}
	public function setPay_type($value){
		$this->_pay_type = $value;
	}
	public function setReturn_order_id($value){
		$this->_return_order_id = $value;
	}
	public function setObj_id($value){
		$this->_obj_id = $value;
	}
	public function setUid($value){
		$this->_uid = $value;
	}
	public function setUsername($value){
		$this->_username = $value;
	}
	public function setPay_time($value){
		$this->_pay_time = $value;
	}
	public function setPay_money($value){
		$this->_pay_money = $value;
	}
	public function setOrder_status($value){
		$this->_order_status = $value;
	}
	public function setPay_info($value){
		$this->_pay_info = $value;
	}
	public function setWhere($value){
		$this->_where = $value;
	}
	public function setCache_config($_cache_config) {
		$this->_cache_config = $_cache_config;
	}

	 
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

	 
	/**
	 * insert into  keke_witkey_order_charge  ,or add new record
	 * @return int last_insert_id
	 */
	function create_keke_witkey_order_charge(){
		$data =  array();

		if(!is_null($this->_order_id)){
			$data['order_id']=$this->_order_id;
		}
		if(!is_null($this->_order_type)){
			$data['order_type']=$this->_order_type;
		}
		if(!is_null($this->_pay_type)){
			$data['pay_type']=$this->_pay_type;
		}
		if(!is_null($this->_return_order_id)){
			$data['return_order_id']=$this->_return_order_id;
		}
		if(!is_null($this->_obj_id)){
			$data['obj_id']=$this->_obj_id;
		}
		if(!is_null($this->_uid)){
			$data['uid']=$this->_uid;
		}
		if(!is_null($this->_username)){
			$data['username']=$this->_username;
		}
		if(!is_null($this->_pay_time)){
			$data['pay_time']=$this->_pay_time;
		}
		if(!is_null($this->_pay_money)){
			$data['pay_money']=$this->_pay_money;
		}
		if(!is_null($this->_order_status)){
			$data['order_status']=$this->_order_status;
		}
		if(!is_null($this->_pay_info)){
			$data['pay_info']=$this->_pay_info;
		}

		return $this->_order_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace);
	}

	/**
	 * edit table keke_witkey_order_charge
	 * @return int affected_rows
	 */
	function edit_keke_witkey_order_charge(){
		$data =  array();

		if(!is_null($this->_order_id)){
			$data['order_id']=$this->_order_id;
		}
		if(!is_null($this->_order_type)){
			$data['order_type']=$this->_order_type;
		}
		if(!is_null($this->_pay_type)){
			$data['pay_type']=$this->_pay_type;
		}
		if(!is_null($this->_return_order_id)){
			$data['return_order_id']=$this->_return_order_id;
		}
		if(!is_null($this->_obj_id)){
			$data['obj_id']=$this->_obj_id;
		}
		if(!is_null($this->_uid)){
			$data['uid']=$this->_uid;
		}
		if(!is_null($this->_username)){
			$data['username']=$this->_username;
		}
		if(!is_null($this->_pay_time)){
			$data['pay_time']=$this->_pay_time;
		}
		if(!is_null($this->_pay_money)){
			$data['pay_money']=$this->_pay_money;
		}
		if(!is_null($this->_order_status)){
			$data['order_status']=$this->_order_status;
		}
		if(!is_null($this->_pay_info)){
			$data['pay_info']=$this->_pay_info;
		}

		if($this->_where){
			return $this->_db->updatetable($this->_tablename,$data,$this->getWhere());
		}
		else{
			$where = array('order_id' => $this->_order_id);
			return $this->_db->updatetable($this->_tablename,$data,$where);
		}
	}

	/**
	 * query table: keke_witkey_order_charge,if isset where return where record,else return all record
	 * @return array
	 */
	function query_keke_witkey_order_charge($is_cache=0, $cache_time=0){
		if($this->_where){
			$sql = "select * from $this->_tablename where ".$this->_where;
		}
		else{
			$sql = "select * from $this->_tablename";
		}
		if ($is_cache) {
			$this->_cache_config ['is_cache'] = $is_cache;
		}
		if ($cache_time) {
			$this->_cache_config ['time'] = $cache_time;
		}
		if ($this->_cache_config ['is_cache']) {
			if (CACHE_TYPE) {
				$keke_cache = new keke_cache_class ( CACHE_TYPE );
				$id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');
				$data = $keke_cache->get ( $id );
				if ($data) {
					return $data;
				} else {
					$res = $this->_dbop->query ( $sql );
					$keke_cache->set ( $id, $res,$this->_cache_config['time'] );
					$this->_where = "";
					return $res;
				}
			}
		}else{
			$this->_where = "";
			return  $this->_dbop->query ( $sql );
		}
	}

	/**
	 * query count keke_witkey_order_charge records,if iset where query by where
	 * @return int count records
	 */
	function count_keke_witkey_order_charge(){
		if($this->_where){
			$sql = "select count(*) as count from $this->_tablename where ".$this->_where;
		}
		else{
			$sql = "select count(*) as count from $this->_tablename";
		}
		$this->_where = "";
		return $this->_dbop->getCount($sql);
	}

	/**
	 * delete table keke_witkey_order_charge, if isset where delete by where
	 * @return int deleted affected_rows
	 */
	function del_keke_witkey_order_charge(){
		if($this->_where){
			$sql = "delete from $this->_tablename where ".$this->_where;
		}
		else{
			$sql = "delete from $this->_tablename where order_id = $this->_order_id ";
		}
		$this->_where = "";
		return $this->_dbop->execute($sql);
	}


	 
	 
}
?>