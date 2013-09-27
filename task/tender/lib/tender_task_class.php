<?php
keke_lang_class::load_lang_class ( 'tender_task_class' );
class tender_task_class extends keke_task_class {
	public $_task_status_arr; 
	public $_work_status_arr;
	public $_delay_rule;
	public $_cove_obj;
	public $_cash_cove_obj;
	public $_task_bid_obj;
	public $_cash_arr;
	protected $_inited = false;
	public static function get_instance($task_info) {
		static $obj = null;
		if ($obj == null) {
			$obj = new tender_task_class ( $task_info );
		}
		return $obj;
	}
	public function __construct($task_info) {
		parent::__construct ( $task_info );
		$this->init ();
	}
	public function init() {
		if (! $this->_inited) {
			$this->status_init ();
			$this->delay_rule_init ();
			$this->wiki_priv_init ();
		}
		$this->_inited = true;
		$this->_task_bid_obj = new Keke_witkey_task_bid_class ();
		$this->_cash_cove_obj = new Keke_witkey_task_cash_cove_class ();
		$this->_cash_arr = $this->_cash_cove_obj->query_keke_witkey_task_cash_cove ();
	}
	public function status_init() {
		$this->_task_status_arr = $this->get_task_status ();
		$this->_work_status_arr = $this->get_work_status ();
	}
	public function delay_rule_init() {
		$this->_delay_rule = keke_task_config::get_delay_rule ( $this->_model_id, '3600' );
	}
	public function wiki_priv_init() {
		$arr = tender_priv_class::get_priv ( $this->_task_id, $this->_model_id, $this->_userinfo );
		$this->_priv = $this->user_priv_format ( $arr );
	}
	public function get_task_coverage() {
		$covers = kekezu::get_cash_cove ();
		$cover_info = $covers [$this->_task_info ['task_cash_coverage']];
		return $cover_info ['cove_desc'];
	}
	public function work_hand($work_desc, $hdn_att_file, $hidework = '2', $url = '', $output = 'normal') {
	}
	public function tender_work_hand($work_info, $hand_credit) {
		global $kekezu, $_K;
		global $_lang;
		if ($this->check_if_can_hand ( $url, $output )) {
			$this->_task_bid_obj->setWhere ( "task_id = $this->_task_id and uid = $this->_uid and bid_status=0" );
			$is_hand = $this->_task_bid_obj->count_keke_witkey_task_bid ();
			$is_hand and kekezu::keke_show_msg ( '', $_lang['you_haved_tender'], 'error', $output );
			$this->_task_bid_obj->setUid ( $this->_uid );
			$this->_task_bid_obj->setUsername ( $this->_username );
			$this->_task_bid_obj->setArea ( $work_info ['area'] );
			$this->_task_bid_obj->setCycle ( $work_info ['task_over_time'] );
			$this->_task_bid_obj->setQuote ( $work_info ['txt_cash'] );
			$this->_task_bid_obj->setTask_id ( $this->_task_id );
			$this->_task_bid_obj->setBid_time ( time () );
			$this->_task_bid_obj->setHidden_status ( $work_info ['workhide'] );
			$this->_task_bid_obj->setMessage ( $work_info ['tar_content'] );
			$res = $this->_task_bid_obj->create_keke_witkey_task_bid ();
			
			if($res){
				keke_finance_class::cash_out ($this->_uid, $hand_credit, 'pub_word', 0, 'task', $this->_task_id, 1 ); 
			}
			
			$work_info ['workhide'] == 1 and keke_payitem_class::payitem_cost ( "workhide", '1', 'work', 'spend', $res, $this->_task_id );
			$this->plus_work_num (); 
			$this->plus_take_num ();
			kekezu::update_weibo_data($this->_task_id,'task',$this->_model_code,$this->_task_title,$this->_task_info['uid'],$this->_task_info['username'],$this->_task_info['leave_num'],$this->_task_info['focus_num'],$this->_task_info['work_num']+1,'work',$this->_task_info['task_cash_coverage'],$this->_uid,$this->_username);
			$url = '<a href ="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '">' . $this->_task_title . '</a>';
			$v_arr = array ($_lang['username'] => "$this->_gusername", $_lang['user'] => $this->_username, $_lang['call'] => $_lang['you'], $_lang['task_title'] => $url, $_lang['website_name'] => $kekezu->_sys_config ['website_name'] );
			keke_shop_class::notify_user ( $this->_guid, $this->_gusername, 'task_hand', $_lang['hand_work_notice'], $v_arr );
			kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."&view=work&ut=my",1, $_lang['tender_success'],"alert_right");
		}
	}
	public function work_choose($work_id, $to_status, $trust_response = false,  $hand_credit = 0, $get_credit=0) {
		global $_K;
		global $_lang;
		$bid_info = $this->select_bid_check ( $work_id, $url );
		$status_arr = $this->get_work_status ();
		if ($this->set_work_status ( $work_id, $to_status )) {
			if ($to_status == 4) {
				$this->set_task_status ( 4 );
				$this->plus_accepted_num ( $bid_info ['uid'] );
				$feed_arr = array ("feed_username" => array ("content" => $bid_info ['username'], "url" => "index.php?do=space&member_id=$bid_info[uid]" ), "action" => array ("content" => $_lang['success_bid_haved'], "url" => "" ), "event" => array ("content" => "$this->_task_title", "url" => "index.php?do=task&task_id={$this->_task_id}" ) );
				kekezu::save_feed ( $feed_arr, $bid_info ['uid'], $bid_info ['username'], 'work_accept', $this->_task_info ['task_id'] );
			}
			$to_status == 7 and $action = "task_unbid" or $action="task_bid";
			$url = '<a href ="index.php?do=task&task_id=' . $this->_task_id . '" target="_blank" >' . $this->_task_title . '</a>';
			$v = array ($_lang['work_status'] => $status_arr [$to_status],
					 $_lang['task_id'] => $this->_task_id, $_lang['task_title'] => $url,
                     $_lang['bid_cash']=>$bid_info['quote']
					);
			$this->notify_user ( $action, $_lang['work'] . $status_arr [$to_status], $v, '1', $bid_info ['uid'] ); 
			
			if($work_id){
				keke_finance_class::cash_in($bid_info['uid'], floatval(0),intval($hand_credit),'choose_back','','task', $task_info['task_id']);
				
				$tudi_info = db_factory::get_one ( " select * from ".TABLEPRE."witkey_space where uid=".$bid_info['uid']); 
				if($tudi_info['pid'])	keke_finance_class::cash_in($tudi_info['pid'], floatval(0),intval($hand_credit*$basic_config['shifu_get_credit']),'tudi_choose','','task', $task_info['task_id']);
				
				keke_finance_class::cash_in($this->_uid, floatval(0),intval($get_credit),'choose_sucess','','task', $task_info['task_id']);
			}
			
			kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."&view=work",1, $_lang['choose_tender_success'],"alert_right");
		} else {
			kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."view=work",1,$_lang['choose_tender_fail'],"alert_error");
		}
	}
	public function select_bid_check($work_id, $url) {
		global $_lang;
		$this->_uid != $this->_guid and kekezu::keke_show_msg ( $url, $_lang['sorry_you_not_rights_operate'] );
		$this->_task_bid_obj->setWhere ( " bid_id = " . $work_id );
		$bid_info = $this->_task_bid_obj->query_keke_witkey_task_bid ();
		$bid_info = $bid_info ['0'];
		$bid_info ['bid_status'] and kekezu::keke_show_msg ( $url, $_lang['please_not_repeat_bid'] );
		$this->_task_info ['task_status'] != 3 && $this->_task_info ['task_status'] != 2 and kekezu::keke_show_msg ( $url, $_lang['present_status_not_choose_work'] );
		return $bid_info;
	}
	public function get_work_info($w = array(), $order = null, $p = array()) {
		global $kekezu, $_K,$uid;
		$work_arr = array ();
		$sql = " select a.* from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		$count_sql = " select count(a.work_id) from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		$where = " where a.task_id = '$this->_task_id' ";
		if (! empty ( $w )) {
			$w ['user_type'] == 'my' and $where .= " and a.uid = '$this->_uid'";
			isset ( $w ['work_status'] ) and $where .= " and a.work_status = '" . intval ( $w ['work_status'] ) . "'";
		}
		$where .= " order by work_time desc ";
		if (! empty ( $p )) {
			$page_obj = $kekezu->_page_obj;
			$count = intval ( db_factory::get_count ( $count_sql . $where ) );
			$pages = $page_obj->getPages ( $count, $p ['page_size'], $p ['page'], $p ['url'], $p ['anchor'] );
			$where .= $pages ['where'];
		}
		$work_info = db_factory::query ( $sql . $where );
		$work_arr ['work_info'] = $work_info;
		$work_arr ['pages'] = $pages;
		$work_ids = implode ( ',',array_keys ( $work_info ));
		$work_ids&&$uid==$this->_task_info['uid'] and db_factory::execute('update '.TABLEPRE.'witkey_task_bid set is_view=1 where bid_id in ('.$work_ids.') and is_view=0');
		return $work_arr;
	}
	function set_work_status($work_id, $status) {
		$this->_task_bid_obj->setWhere ( "bid_id = $work_id and task_id = $this->_task_id" );
		$this->_task_bid_obj->setBid_status ( $status );
		$res = $this->_task_bid_obj->edit_keke_witkey_task_bid ();
		return $res;
	}
	public function process_can() {
		$wiki_priv = $this->_priv; 
		$process_arr = array ();
		$status = intval ( $this->_task_status );
		$task_info = $this->_task_info;
		$config = $this->_task_config;
		$g_uid = $this->_guid;
		$uid = $this->_uid;
		$user_info = $this->_userinfo;
		switch ($status) {
			case "2" : 
				switch ($g_uid == $uid) {
					case "1" :
						$process_arr['tools'] = true;
						$process_arr ['reqedit'] = true; 
							$process_arr ['work_choose'] = true; 
						$process_arr ['work_comment'] = true; 
						break;
					case "0" : 
						$process_arr ['work_hand'] = true; 
						$process_arr ['task_comment'] = true;
						$process_arr ['task_report'] = true; 
						break;
				}
				break;
			case "3" : 
				switch ($g_uid == $uid) { 
					case "1" :
						$process_arr ['work_choose'] = true;
						$process_arr ['work_comment'] = true; 
						break;
					case "0" : 
						$process_arr ['task_comment'] = true; 
						$process_arr ['task_report'] = true; 
						break;
				}
				break;
			case "4" :
				$bid_info = $this->get_bid_info ();
				switch ($g_uid == $uid) {
					case "1" :
						$process_arr ['work_comment'] = true; 
						break;
					case "0" :
						$process_arr ['task_comment'] = true; 
						$this->_uid == $bid_info ['uid'] && $bid_info ['ext_status'] != 1 and $process_arr ['pub_agreement'] = true;
						$process_arr ['task_report'] = true;
						break;
				}
				$process_arr ['work_report'] = true;
				break;
			case "5" : 
				$bid_info = $this->get_bid_info ();
				switch ($g_uid == $uid) {
					case "1" :
						$bid_info ['ext_status'] == 1 and $process_arr ['work_over'] = true;
						break;
					case "0" :
						$this->_uid == $bid_info ['uid'] && $bid_info ['ext_status'] != 1 and $process_arr ['pub_agreement'] = true;
						break;
				}
				break;
			case "8" : 
				switch ($g_uid == $uid) { 
					case "1" :
						$process_arr ['work_comment'] = true; 
						$process_arr ['work_mark'] = true; 
						break;
					case "0" :
						$process_arr ['task_comment'] = true; 
						$process_arr ['task_mark'] = true;
						break;
				}
				break;
		}
		$uid != $g_uid and $process_arr ['task_complaint'] = true;
		$process_arr ['work_complaint'] = true; 
		if($user_info['group_id']){
			switch ($status){
				case 1:
					$process_arr['task_audit'] = true;
					break;
				case 2:
					$process_arr['task_recommend']=true;
					$process_arr['task_freeze'] = true;
					break;
				default:
					if($status>1&&$status<8){
						$process_arr['task_freeze'] = true;
					}
			}
		}
		$this->_process_can = $process_arr;
		return $process_arr;
	}
	public static function get_task_status() {
		global $_lang;
		return array ("0" => $_lang['task_no_pay'], "1" => $_lang['task_wait_audit'], "2" => $_lang['tendering'], "3" => $_lang['choose_tendering'], "4" => $_lang['working'], "7" => $_lang['freeze'], "8" => $_lang['task_over'], "9" => $_lang['fail'], "10" => $_lang['task_audit_fail']);
	}
	public static function get_work_status() {
		global $_lang;
		return array ('4' => $_lang['task_bid'], '7' => $_lang['task_out'], '8' => $_lang['task_can_not_choose_bid'] );
	}
	public function get_task_timedesc() {
		global $_lang;
		$status_arr = $this->_task_status_arr;
		$task_status = $this->_task_status;
		$task_info = $this->_task_info;
		$time_desc = array ();
		switch ($task_status) {
			case "0" :
				$time_desc ['ext_desc'] = $_lang['task_nopay_can_not_look'];
				break;
			case "1" :
				$time_desc ['ext_desc'] = $_lang['wait_patient_to_audit'];
				break;
			case "2" : 
				$time_desc ['time_desc'] = $_lang['from_hand_bid_deadline']; 
				$time_desc ['time'] = $task_info ['sub_time']; 
				$time_desc ['ext_desc'] = $_lang['bidding_and_eagerly_tender'];
					$time_desc ['g_action'] = $_lang['present_state_employer_can_choose']; 
				break;
			case "3" : 
				$time_desc ['time_desc'] = $_lang['from_choose_deadline']; 
				$time_desc ['time'] = $task_info ['end_time']; 
				$time_desc ['ext_desc'] = $_lang['bidding_and_wait_employer_choose'];
				break;
			case "4" : 
				$time_desc ['ext_desc'] = $_lang['bidder_working'];
				break;
			case "5" : 
				$time_desc ['ext_desc'] = $_lang['task_over_for_jf'];
				break;
			case "7" : 
				$time_desc ['ext_desc'] = $_lang['task_frozen_can_not_operate'];
				break;
			case "8" : 
				$time_desc ['ext_desc'] = $_lang['task_over_congra_witkey'];
				break;
			case "9" : 
				$time_desc ['ext_desc'] = $_lang['pity_task_fail'];
				break;
			case "10":
				$time_desc ['ext_desc'] =  $_lang['fail_audit_please_repub']; 
				break;
			case "11" : 
				$time_desc ['ext_desc'] = $_lang['wait_for_task_arbitrate'];
				break;
		}
		return $time_desc;
	}
	function get_bid_info() {
		$this->_task_bid_obj->setWhere ( " task_id = $this->_task_id and bid_status = 4" );
		$bid_info = $this->_task_bid_obj->query_keke_witkey_task_bid ();
		$bid_info = $bid_info ['0'];
		if ($bid_info) {
			return $bid_info;
		} else {
			return false;
		}
	}
	function set_bid_status($bid_id, $bid_status) {
		$this->_task_bid_obj->setWhere ( " bid_id = $bid_id" );
		$this->_task_bid_obj->setBid_status ( $bid_status );
		$res = $this->_task_bid_obj->edit_keke_witkey_task_bid ();
		if ($res) {
			return $res;
		} else {
			return false;
		}
	}
	function set_agreement_status($bid_id, $status) {
		$this->_task_bid_obj->setWhere ( " bid_id = $bid_id" );
		$this->_task_bid_obj->setExt_status ( $status );
		$res = $this->_task_bid_obj->edit_keke_witkey_task_bid ();
		if ($res) {
			return $res;
		} else {
			return false;
		}
	}
	public function dispose_order($order_id) {
		global $kekezu, $_K;
		global $_lang;
		$task_config = $this->_task_config;
		$task_info = $this->_task_info; 
		$url = $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id;
		$task_status = $this->_task_status;
		$order_info = db_factory::get_one ( sprintf ( "select order_amount,order_status from %switkey_order where order_id='%d'", TABLEPRE, intval ( $order_id ) ) );
		$order_amount = $order_info ['order_amount'];
		if ($order_info ['order_status'] == 'ok') {
			$task_status == 1 && $notice = $_lang['task_pay_success_and_wait_admin_audit'];
			$task_status == 2 && $notice = $_lang['task_pay_success_and_task_pub_success'];
			return pay_return_fac_class::struct_response ( $_lang['operate_notice'], $notice, $url, 'success' );
		} else {
			$data = array(':model_name'=>$this->_model_name,':task_id'=>$this->_task_id,':task_title'=>$this->_task_title);
			keke_finance_class::init_mem('pub_task', $data);
			$order_amount>0 and $res = keke_finance_class::cash_out ( $task_info ['uid'], $order_amount, 'pub_task',$task_info['task_cash']);
			kekezu::save_weibo_data($task_info['task_id'],'task',$kekezu->_model_list[$task_info[model_id]]['model_code'],$task_info['task_title'],$task_info['uid'],$task_info['username'],0,0,0,'pub',$task_info['task_cash'],$uid,$username);
			switch ($res == true) {
				case "1" :
					$kekezu->init_prom ();
					if ($kekezu->_prom_obj->is_meet_requirement ( "pub_task", $this->_task_id )) {
						$kekezu->_prom_obj->create_prom_event ( "pub_task", $this->_guid, $task_info ['task_id'], $task_info ['task_cash'] );
					}
					keke_union_class::union_task_submit($this->_g_userinfo,$this->_task_id);
						$feed_arr = array ("feed_username" => array ("content" =>$task_info['username'], "url" => "index.php?do=space&member_id={$task_info['uid']}" ), "action" => array ("content" => $_lang['pub_task'], "url" => "" ), "event" => array ("content" => "{$task_info['task_title']}", "url" => "index.php?do=task&task_id={$task_info['task_id']}" ) );
						kekezu::save_feed ( $feed_arr,$task_info['uid'],$task_info['username'], 'pub_task',$task_info['task_id']);
					$consume = kekezu::get_cash_consume($task_info['task_cash']);
					db_factory::execute(sprintf(" update %switkey_task set cash_cost='%s',credit_cost='%s' where task_id='%d'",TABLEPRE,$consume['cash'],$consume['credit'],$this->_task_id));
					db_factory::updatetable ( TABLEPRE . "witkey_order", array ("order_status" => "ok" ), array ("order_id" => "$order_id" ) );
					if ($task_config ['zb_audit']==1) {
						$this->set_task_status ( 1 ); 
						return pay_return_fac_class::struct_response ( $_lang['operate_notice'], $_lang['task_pay_success_and_wait_admin_audit'], $url, 'success' );
					} else {
						$this->set_task_status ( 2 );
						return pay_return_fac_class::struct_response ( $_lang['operate_notice'], $_lang['task_pay_success_and_task_pub_success'], $url, 'success' );
					}
					break;
				case "0" :
					$pay_url = $_K ['siteurl'] . "/index.php?do=pay&order_id=$order_id"; 
					return pay_return_fac_class::struct_response ( $_lang['operate_notice'], $_lang['task_pay_error_and_please_repay'], $pay_url, 'warning' );
					break;
			}
		}
	}
	public static function master_opera($m_id,$t_id,$url,$t_cash) {
		global $uid,$_K,$do,$view,$_lang;
		$status = db_factory::get_count ( sprintf ( ' select task_status from %switkey_task where task_id=%d and uid=%d', TABLEPRE, $t_id, $uid ), 0, 'task_status', 600 );
		$order_info = db_factory::get_one(sprintf("select order_id from %switkey_order_detail where obj_id=%d",TABLEPRE,$t_id));				
		$site   = $_K['siteurl'].'/';
		$button = array();
		$button['view'] = array(
				'href'=>$site.'index.php?do=task&task_id='.$t_id,
				'desc'=>$_lang['view'], 
				'ico'=>'book');
		$button['onkey'] = array(
				'href'=>$site.'index.php?do=release&t_id='.$t_id.'&model_id='.$m_id.'&pub_mode=onekey',
				'desc'=>$_lang['one_key_pub'], 
				'ico'=>'book');
		$button['del'] = array(
				'href'=>$site.$url.'&ac=del&task_id='.$t_id,
				'desc'=>$_lang['delete'],
				'click'=>'return del(this);',
				'ico'=>'trash');
		switch ($status) {
			case 0 : 
				$button['pay'] = array(
				'href'=>$site.'index.php?do='.$do.'&view='.$view.'&task_id='.$t_id.'&model_id='.$m_id.'&ac=pay',
				'desc'=>$_lang['payment'], 
				'click'=>"return pay(this,$t_cash,$order_info[order_id]);",
				'ico'=>'loop');
				break;
			case 2 : 
				$button['tool'] = array(
				'href'=>$site.'index.php?do=task&task_id='.$t_id.'&view=tools',
				'desc'=>$_lang['toolbox'], 
				'ico'=>'trash');
				break;
			case 3 : 
				$button['view']['desc'] = '';  
				$button['view']['href'] = $site.'index.php?do=task&task_id='.$t_id.'&view=work';
				break;
			case 5 :
				$button['confirm_work'] = array(
						'click'=>"work_over('index.php?do=task&task_id=$t_id&op=work_over')",
						'desc'=>'验收工作', 
						'ico'=>'book',
						'href'=>'javascript:void(0);'
				);
				break;
		}
		if(!in_array($status,array(0,8,9,10))){
			unset($button['del']);
		}
		return $button;
	}
	public static function wiki_opera($m_id,$t_id, $w_id,$url) {
		global $uid,$_K,$do,$view,$_lang;
		$status = db_factory::get_count ( sprintf ( ' select task_status from %switkey_task where task_id=%d', TABLEPRE, $t_id, $uid ), 0, 'task_status', 600 );
		$site   = $_K['siteurl'].'/';
		$button = array();
		$button['view'] = array(
				'href'=>$site.'index.php?do=task&task_id='.$t_id.'&view=work&ut=my&work_id='.$w_id,
				'desc'=>$_lang['view_work'],
				'ico'=>'book');
		switch ($status) {
			case 2 : 
				$button['share'] = array(
				'href'=>'javascript:void(0);',
				'desc'=>$_lang['share'],
				'click'=>'share('.$t_id.');',
				'ico'=>'share');
				break;
			case 4 :
				$button['start_work'] = array(
						'click'=>"work_over('index.php?do=task&task_id=$t_id&op=pub_agreement')",
						'desc'=>$_lang['confirm_work'],
						'ico'=>'book',
						'href'=>'javascript:void(0);'
				);
			case 8:
			case 9:
				$button['del'] = array(
				'href'=>$site.$url.'&ac=del&work_id='.$w_id,
				'desc'=>$_lang['delete'],
				'click'=>'return del(this);',
				'ico'=>'trash');
				break;
		}
		return $button;
	}
	public function check_bid() {
		$count = db_factory::get_count ( sprintf ( "select count(*) from %switkey_task_bid where task_id='%d' and uid='%d'", TABLEPRE, $this->_task_id, $this->_uid ) );
		if (intval ( $count ) == 0) {
			return true;
		} else {
			return false;
		}
	}
}