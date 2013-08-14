<?php
keke_lang_class::load_lang_class('keke_shop_release_class');
abstract class keke_shop_release_class {
	public $_uid;
	public $_username;
	public $_user_info; 
	public $_kf_info; 
	public $_pay_item; 
	public $_priv; 
	public $_model_id; 
	public $_model_info; 
	public $_service_config; 
	public $_inited = false;
	public $_service_obj; 
	public $_std_obj; 
	function __construct($model_id) {
		global $kekezu;
		$this->_service_obj = new Keke_witkey_service_class ();
		$this->_model_id = $model_id;
		$this->_model_info = $kekezu->_model_list [$model_id];
		$this->_std_obj = new stdClass ();
		$this->_std_obj->_release_info = array (); 
		$this->init ();
	}
	function init() {
		if (! $this->_inited) {
			$this->user_info_init ();
			$this->pay_item_init ();
			$this->get_rand_kf ();
		}
		$this->_inited = true;
	}
	public function pay_item_init() {
		$this->_pay_item = keke_payitem_class::get_payitem_config ( 'employer', $this->_model_info ['model_code'] );
	}
	public function get_rand_kf() {
		$this->_kf_info = kekezu::get_rand_kf ();
	}
	public function get_bind_indus() {
		global $kekezu;
		if ($this->_model_info ['indus_bid']) {
			$bind_indus = implode ( ',', array_filter ( explode ( ',', $this->_model_info ['indus_bid'] ) ) );
			return kekezu::get_table_data ( '*', "witkey_industry", "indus_id in (select indus_pid from " . TABLEPRE . "witkey_industry where indus_id in({$bind_indus}))", 'listorder desc', '', '', 'indus_id', null );
		} else {
			return $this->_indus_arr = $kekezu->_indus_p_arr;
		}
	}
	function user_info_init() {
		global $user_info, $uid, $username;
		$this->_user_info = $user_info;
		$this->_uid = $uid;
		$this->_username = $username;
	}
	public function get_service_indus($indus_pid = '', $ajax = '') {
		global $kekezu;
		global $_lang;
		if ($indus_pid > 0) {
			$indus_ids = kekezu::get_table_data ( '*', "witkey_industry", " indus_pid = $indus_pid", 'listorder desc', '', '', 'indus_id', null );
			switch ($ajax == 'show_indus') {
				case "0" :
					return $indus_ids;
					break;
				case "1" :
					$option .= '<option value=""> '.$_lang['please_choose_son_industry'].' </option>';
					foreach ( $indus_ids as $v ) {
						$option .= '<option value=' . $v [indus_id] . '>' . $v [indus_name] . '</option>';
					}
					CHARSET == 'gbk' and $option = kekezu::gbktoutf ( $option );
					echo $option;
					die ();
					break;
			}
		} else {
			return false;
		}
	}
	public function get_service_bind_indus($indus_pid = '', $ajax = '') {
		global $kekezu;
		global $_lang;
		if ($indus_pid > 0) {
			if ($this->_model_info ['indus_bid']) {
				$indus_ids = kekezu::get_table_data ( '*', "witkey_industry", "indus_id in ({$this->_model_info['indus_bid']}) and indus_pid = $indus_pid", 'listorder desc', '', '', 'indus_id', null );
			} else {
				$indus_ids = kekezu::get_table_data ( '*', "witkey_industry", " indus_pid = $indus_pid", 'listorder desc', '', '', 'indus_id', null );
			}
			switch ($ajax == 'show_indus') {
				case "0" :
					return $indus_ids;
					break;
				case "1" :
					$option .= '<option value=""> ' . '请选择子行业' . ' </option>';
					foreach ( $indus_ids as $v ) {
						$option .= '<option value=' . $v [indus_id] . '>' . $v [indus_name] . '</option>';
					}
					echo $option;
					die ();
					break;
			}
		} else
			return false;
	}
	function save_service_file($service_id, $title) {
		$release_info = $this->_std_obj->_release_info;
		if ($release_info ['file_ids']) {
			$file_obj = new Keke_witkey_file_class ();
			$file_arr = array_filter ( explode ( ',', $release_info ['file_ids'] ) );
			foreach ( $file_arr as $v ) {
				$file_obj->setFile_id ( $v );
				$file_obj->setUid ( $this->_uid );
				$file_obj->setUsername ( $this->_username );
				$file_obj->setObj_id ( $service_id );
				$file_obj->setTask_title ( $title );
				$file_obj->edit_keke_witkey_file ();
			}
		}
	}
	public function notify_user($service_id, $service_status = '2') {
		global $_K;
		global $_lang;
		$service_obj = $this->_service_obj;
		$model_code = $this->_model_info ['model_code'];
		switch($model_code){
			case "goods":
				$status_arr = goods_shop_class::get_goods_status (); 
				break;
			case "service":
				$status_arr = service_shop_class::get_service_status (); 
				break;
		}
		$message_obj = new keke_msg_class ();
		$url = "<a href=\"" . $_K [siteurl] . "/index.php?do=service&sid=" . $service_id . "\">" . $service_obj->getTitle () . "</a>";
		$v = array ($_lang['service_type'] => $this->_model_info ['model_name'], $_lang['goods_link'] => $url, $_lang['goods_status'] => $status_arr [$service_status], $_lang['pub_time'] => date ( 'Y-m-d H:i:s', $service_obj->getOn_time () ) );
		$message_obj->send_message ( $this->_uid, $this->_username, "service_pub", $this->_model_info ['model_name'] . $_lang['release_tips'], $v, $this->_user_info ['email'], $this->_user_info ['mobile'] );
	}
	public function get_payitem_cash($service_cash) {
		$payitem_cash = number_format ( $service_cash + $this->_std_obj->_att_cash, 2 ); 
		return $payitem_cash;
	}
	public function set_service_status($service_cash) {
		$audit_cash = $this->_service_config ['audit_cash']; 
		if ($audit_cash) { 
			if ($service_cash >= $audit_cash) { 
				$service_status = '2'; 
			} else {
				$service_status = '1'; 
			}
		} else {
			$service_status = '2'; 
		}
		$this->_service_obj->setService_status ( $service_status ); 
	}
	public function public_pubservice() {
		$std_obj = $this->_std_obj; 
		$release_info = $std_obj->_release_info; 
		$service_obj = $this->_service_obj; 
		$txt_service_title = kekezu::str_filter ( $release_info ['txt_title'] ); 
		$service_obj->setTitle ( $txt_service_title );
		$service_obj->setModel_id ( $this->_model_id ); 
 		if($release_info[submit_method]=='inside'){
 			$service_obj->setFile_path($release_info[file_path_2]);
 		}  
		$tar_content = kekezu::str_filter ( $release_info ['tar_content'] );
		$service_obj->setContent ( $tar_content );
		$service_obj->setIndus_id ( $release_info [indus_id] );
		$service_obj->setIndus_pid ( $release_info ['indus_pid'] );
		$shop_id = db_factory::get_count ( sprintf ( " select shop_id from %switkey_shop where uid ='%d'", TABLEPRE, $this->_uid ) );
		$service_obj->setShop_id ( $shop_id ); 
		$service_obj->setUid ( $this->_uid );
		$service_obj->setUsername ( $this->_username );
		$service_obj->setPrice ( $release_info ['txt_price'] ); 
		$service_obj->setUnite_price ( $release_info ['unite_price'] ); 
		$service_obj->setService_time ( $release_info ['service_time'] ); 
		$service_obj->setUnit_time ( $release_info ['unit_time'] ); 
		$service_obj->setProfit_rate ( $this->_service_config ['service_profit'] ); 
		$service_obj->setConfirm_max ( $this->_service_config ['confirm_max_day'] ); 
		$release_info ['pic_patch'] and $service_obj->setPic ( $release_info ['pic_patch'] );
		$payitem_arr[top] = 1000000000;
		if(is_array($std_obj->_att_info)){
			$att_info = array_filter ( $std_obj->_att_info ); 
			foreach ($att_info as $k=>$v) {  
				if($v['item_num']!=0){
					$new_att_info[$k]=$v;
					$v [item_code] == 'top' and $payitem_arr [top] = time () + 3600 * 24 * $v [item_num];
					$v [item_code] == 'urgent' and $payitem_arr [urgent] = time () + 3600 * 24 * $v [item_num];
				}
			}
		}
		$service_obj->setAtt_cash ( floatval ( $std_obj->_att_cash ) ); 
		if(is_array($new_att_info)){
			$att_ids = implode ( ",", array_keys ( $new_att_info ) ); 
		}
		$service_obj->setPay_item ( $att_ids );
		$payitem_time =serialize($payitem_arr);
		$service_obj->setPayitem_time($payitem_time);
		$service_obj->setOn_time ( time () ); 
	}
	public function update_service_info($service_id, $obj_name) {
		global $_K;
		global $_lang,$uid,$username;
		$std_obj = $this->_std_obj;
		$release_info = $std_obj->_release_info; 
		$att_info = $std_obj->_att_info; 
		$user_info = $this->_user_info; 
		$service_obj = $this->_service_obj; 
		if ($service_id) {
			$service_status = $service_obj->getService_status (); 
			$service_title = $service_obj->getTitle();
			$service_cash = $service_obj->getPrice();
			switch ($service_status) {
				case "2" : 
					$att_info = $this->create_payitem_reocrd ( $service_id, $att_info, $release_info ); 
					$service_title = $service_obj->getTitle (); 
					$feed_arr = array ("feed_username" => array ("content" => $this->_username, "url" => "index.php?do=space&member_id=$this->_uid" ), "action" => array ("content" => $_lang['has_pub_goods'], "url" => "" ), "event" => array ("content" => "$service_title", "url" => "index.php?do=service&sid=$service_id" ) );
					kekezu::save_feed ( $feed_arr, $this->_uid, $this->_username, 'pub_service', $service_id );
					$this->notify_user ( $service_id, '2' );
					$status = '2';
					db_factory::execute(' update '.TABLEPRE.'witkey_shop set on_sale=on_sale+1 where shop_id='.$service_obj->getShop_id());
					break;
				case "1" : 
					$att_info = $this->create_payitem_reocrd ( $service_id, $att_info, $release_info );
					$this->notify_user ( $service_id, '1' );
					$status = '1';
					break;
			}
		}
		$this->del_service_obj ( $obj_name ); 
		header ( "Location:" . $_K ['siteurl'] . "/index.php?do=shop_release&model_id=$this->_model_id&r_step=step3&service_id=$service_id&status=" . $status );
	}
	public abstract function get_service_config();
	public abstract function pub_service();
	public function create_payitem_reocrd($service_id, $att_info = array(), $release_info) {
		if (! empty ( $att_info )) {
			$payitem_list = keke_payitem_class::get_payitem_config (); 
			foreach ( $att_info as $k => $v ) {
				if($v['item_num']!=0){
					if($v['item_type']=='buy'){
						keke_payitem_class::payitem_cost ( $v ['item_code'], $v ['item_num'], $payitem_list [$v ['item_code']] ['item_type'], 'buy', $service_id, $service_id ); 
					}
					if($v['item_code']=='map'){
						$v ['record_id'] = $pay_id = keke_payitem_class::payitem_cost ( $v ['item_code'], 1, $payitem_list [$v ['item_code']] ['item_type'], 'spend', $service_id, $service_id );
						$pay_id and db_factory::execute ( sprintf ( " update %switkey_service set point='%s',city='%s' where service_id = '%d'", TABLEPRE, $release_info ['point'], $release_info ['province'], $service_id ) );
					}else{
						$v ['record_id'] = $pay_id = keke_payitem_class::payitem_cost ( $v ['item_code'], $v ['item_num'], $payitem_list [$v ['item_code']] ['item_type'], 'spend', $service_id, $service_id );
					}
				}
			}
			if (! empty ( $att_info )) { 
				foreach ( $att_info as $v ) {
					if($v['item_type']=='buy'&&$v['item_num']!=0){
						keke_order_class::create_order_detail ( $order_id, $v ['item_name'], 'payitem', intval ( $v ['record_id'] ), $v ['item_cash'] );
					}
				}
			}
		}
		return $att_info;
	}
	public function save_pay_item($item_id, $item_code, $item_name, $item_cash, $obj_name,$item_num,$item_type) {
		$att_info = $this->_std_obj->_att_info; 
		if ($att_info [$item_id] ['item_cash'] != $item_cash) { 
			CHARSET == 'gbk' and $item_name = kekezu::utftogbk ( $item_name );
			$att_info [$item_id] ['item_code'] = $item_code;
			$att_info [$item_id] ['item_name'] = $item_name;
			$att_info [$item_id] ['item_cash'] = $item_cash;
			$att_info [$item_id] ['item_num'] = $item_num;
			$att_info[$item_id]['item_type'] = $item_type;
		}
		$this->_std_obj->_att_info = array_filter ( $att_info ); 
		$this->save_service_obj ( array (), $obj_name ); 
		$total_cash = $this->get_total_cash ( 0 );
		$item_detail = $this->get_pay_item_info();
		kekezu::echojson(keke_curren_class::output( $total_cash, 2),1,$item_detail );
		die ();
	}
	public function get_total_cash($service_cash) {
		$total_cash = $service_cash + $this->_std_obj->_att_cash; 
		return $total_cash;
	}
	public function get_pay_item() {
		return $this->_std_obj->_att_info; 
	}
	public function remove_pay_item($item_id, $obj_name) {
		$att_info = $this->_std_obj->_att_info; 
		if ($att_info [$item_id]) {
			unset ( $att_info [$item_id] );
		}
		$this->_std_obj->_att_info = array_filter ( $att_info ); 
		$this->save_service_obj ( array (), $obj_name ); 
		foreach ($this->_std_obj->_att_info  as $k=>$v) {
			$total_cash +=$v[item_cash] ;
		} 
		kekezu::echojson ( number_format($total_cash,2), 1 );
		die ();
	}
	public function get_pay_item_info() {
		$att_info = $this->_std_obj->_att_info; 
		$item_cash = '';
		$item_count = 0;
		if(is_array($att_info)){
			foreach($att_info as $k=>$v){
				if($v['item_type']=='buy'&&$v['item_num']>0){
					$item_cash += $v['item_cash'];
					$item_count += 1;
				}
			}
		}
		$item_arr['item_cash'] = $item_cash;
		$item_arr['item_count'] = $item_count;
		return $item_arr;
	}
	public function solve_pay_item($att_info = array()) {
		$att_cash = '0';
		if (is_array ( $att_info )) {
			foreach ( $att_info as $v ) {
				if($v['item_type']=='buy'){
					$att_cash += floatval ( $v ['item_cash'] );
				}
			}
		}
		return $att_cash;
	}
	public function save_service_obj($release_info = array(), $obj_name) {
		global $kekezu;
		if ($release_info ['p_step'] == 'step1') {
			if ($_POST['file_ids']){
				$pic = kekezu::escape($_POST['file_ids']);
				$release_info['pic_patch'] = $pic;
 			}
		}
		empty ( $release_info ) or $this->_std_obj->_release_info = $release_info; 
		$this->_std_obj->_att_cash = $this->solve_pay_item ( $this->_std_obj->_att_info ); 
		$_SESSION [$obj_name] = serialize ( $this->_std_obj ); 
	}
	public function get_service_obj($obj_name) {
		$_SESSION [$obj_name] and $this->_std_obj = unserialize ( $_SESSION [$obj_name] );
	}
	public function del_service_obj($obj_name) {
		if (isset ( $_SESSION [$obj_name] )) {
			unset ( $_SESSION [$obj_name] );
		}
		if (isset ( $_SESSION ['formhash'] )) {
			unset ( $_SESSION ['formhash'] );
		}
	}
	public function check_access($r_step, $model_id, $release_info, $service_id = null, $output = 'normal') {
		global $_lang;
		switch ($r_step) {
			case "step1" : 
				break;
			case "step2" : 
				if (! $release_info ['step1']) {
					kekezu::keke_show_msg ( "index.php?do=shelves&model_id=$model_id&r_step=step2", $_lang['no_input_goods_need_notice'], "error", $output );
				}
				break;
			case "step3" : 
				$sql = sprintf ( " select service_status,service_id from %switkey_service where service_id = '%d' and on_time>%d", TABLEPRE, $service_id, time () - 600 );
				$service_info = db_factory::get_one ( $sql );
				$service_info or kekezu::keke_show_msg ( "index.php?do=shelves", $_lang['page_expired_notice'], "error", $output );
				return $service_info;
				break;
		}
	}
	public static function get_price_unit() {
		global $_lang;
		return array ($_lang['ge'], $_lang['pieces'], $_lang['times'], $_lang['copy'] );
	}
	public static function get_service_unit() {
		global $_lang;
		return array ($_lang['hour'], $_lang['day'], $_lang['week'], $_lang['month'] );
	}
}