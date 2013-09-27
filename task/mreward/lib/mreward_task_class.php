<?php
keke_lang_class::load_lang_class ( 'mreward_task_class' );
class mreward_task_class extends keke_task_class {
	public $_task_status_arr; 
	public $_work_status_arr; 
	public $_delay_rule; 
	public static function get_instance($task_info) {
		static $obj = null;
		if ($obj == null) {
			$obj = new mreward_task_class ( $task_info );
		}
		return $obj;
	}
	public function __construct($task_info) {
		parent::__construct ( $task_info );
		$this->init ();
	}
	public function init() {
		$this->status_init ();
		$this->task_requirement_init ();
		$this->delay_rule_init ();
		$this->wiki_priv_init ();
		$this->mark_init ();
	}
	public function mark_init() {
		$m = $this->get_mark_count_ext ();
		$t = $this->_task_info;
		$t ['mark'] ['all'] = intval ( $m [1] ['c'] + $m [2] ['c'] );
		$t ['mark'] ['master'] = intval ( $m [2] ['c'] );
		$t ['mark'] ['wiki'] = intval ( $m [1] ['c'] );
		$this->_task_info = $t;
	}
	public function status_init() {
		$this->_task_status_arr = $this->get_task_status ();
		$this->_work_status_arr = $this->get_work_status ();
	}
	public function delay_rule_init() {
		$this->_delay_rule = keke_task_config::get_delay_rule ( $this->_model_id, '3600' );
	}
	public function wiki_priv_init() {
		$arr = mreward_priv_class::get_priv ( $this->_task_id, $this->_model_id, $this->_userinfo );
		$this->_priv = $this->user_priv_format ( $arr );
	}
	public function task_requirement_init() {
		global $_lang;
		$require_arr = array (); 
		$require_arr [$_lang ['haved_work']] = $this->_task_info ['work_num'];
		$require_arr [$_lang ['haved_bid_work']] = $bid_num = intval ( db_factory::get_count ( sprintf ( " select count(work_id) count from %switkey_task_work where
		 work_status = '4' and task_id = '%d'", TABLEPRE, $this->_task_id ) ) );
	}
	public function get_task_timedesc() {
		global $_lang;
		$status_arr = $this->_task_status_arr;
		$task_status = $this->_task_status;
		$task_info = $this->_task_info;
		$time_desc = array ();
		switch ($task_status) {
			case "0":
				$time_desc ['ext_desc'] = $_lang['task_nopay_can_not_look']; 
				break;
			case "1": 
				$time_desc ['ext_desc'] = $_lang['wait_patient_to_audit'];
				break;
			case "2" : 
				$time_desc ['time_desc'] = $_lang ['from_hand_work_deadline']; 
				$time_desc ['time'] = $task_info ['sub_time'];
				$time_desc ['ext_desc'] = $_lang['hand_work_and_reward_trust']; 
					$time_desc ['g_action'] = $_lang ['now_employer_can_choose_work']; 
				break;
			case "3" :
				$time_desc ['time_desc'] = $_lang ['from_choose_deadline']; 
				$time_desc ['time'] = $task_info ['end_time']; 
				$time_desc ['ext_desc'] = $_lang['work_choosing_and_wait_employer_choose']; 
				break;
			case "4" : 
				$time_desc ['time_desc'] = $_lang ['from_vote_deadline']; 
				$time_desc ['time'] = $task_info ['sp_end_time']; 
				$time_desc ['ext_desc'] = $_lang ['task_voting_can_vote'];
				break;
			case "5" : 
				$time_desc ['time_desc'] = $_lang ['from_gs_deadline']; 
				$time_desc ['time'] = $task_info ['sp_end_time'];
				$time_desc ['ext_desc'] = $_lang['task_gs_and_emplyer_have_choose']; 
				break;
			case "6" : 
				$time_desc ['ext_desc'] = $_lang ['task_in_jf_rate']; 
				break;
			case "7" : 
				$time_desc ['ext_desc'] =$_lang['task_frozen_can_not_operate'];
				break;
			case "8" : 
				$time_desc ['ext_desc'] =$_lang['task_over_congra_witkey']; 
				break;
			case "9" : 
				$time_desc ['ext_desc'] = $_lang['pity_task_fail']; 
				break;
			case "10":
				$time_desc ['ext_desc'] = $_lang['fail_audit_please_repub']; 
				break;
			case "11" : 
				$time_desc ['ext_desc'] = $_lang['wait_for_task_arbitrate'];
				break;
		}
		return $time_desc;
	}
	public function get_work_info($w = array(), $order = null, $p = array()) {
		global $kekezu, $_K, $uid;
		$work_arr = array ();
		$sql = " select a.*,b.seller_credit,b.seller_good_num,b.seller_total_num,b.seller_level from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		$count_sql = " select count(a.work_id) from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		$where = " where a.task_id = '$this->_task_id' ";
		if (! empty ( $w )) {
			$w ['work_id'] and $where .= " and a.work_id='" . $w ['work_id'] . "'";
			$w ['user_type'] == 'my' and $where .= " and a.uid = '$this->_uid'";
			isset ( $w ['work_status'] ) and $where .= " and a.work_status = '" . intval ( $w ['work_status'] ) . "'";
		}
		$where .= "  order by (CASE WHEN  a.work_status!=0 THEN 100 ELSE 0 END) desc,work_time desc ";
		if (! empty ( $p )) {
			$page_obj = $kekezu->_page_obj;
			$page_obj->setAjax ( 1 );
			$page_obj->setAjaxDom ( "gj_summery" );
			$count = intval ( db_factory::get_count ( $count_sql . $where ) );
			$pages = $page_obj->getPages ( $count, $p ['page_size'], $p ['page'], $p ['url'], $p ['anchor'] );
			$where .= $pages ['where'];
		}
		$work_info = db_factory::query ( $sql . $where );
		$work_info = kekezu::get_arr_by_key ( $work_info, 'work_id' );
		$work_arr ['work_info'] = $work_info;
		$work_arr ['pages'] = $pages;
		$work_ids = implode ( ',', array_keys ( $work_info ) );
		$work_arr ['mark'] = $this->has_mark ( $work_ids );
		$work_ids && $uid == $this->_task_info ['uid'] and db_factory::execute ( 'update ' . TABLEPRE . 'witkey_task_work set is_view=1 where work_id in (' . $work_ids . ') and is_view=0' );
		return $work_arr;
	}
	public function work_hand($work_desc, $file_ids, $hidework = '2', $qq = '', $mobile = '', $hand_credit = 0) {
		global $_K, $uid, $username;
		global $_lang;
		if ($this->check_if_can_hand ( $url, $output )) {
			$work_obj = new Keke_witkey_task_work_class ();
			$work_obj->_work_id = null;
			$work_obj->setTask_id ( $this->_task_id );
			$work_obj->setUid ( $this->_uid );
			$work_obj->setUsername ( $this->_username );
			$work_obj->setVote_num ( 0 );
			$work_obj->setWork_status ( 0 );
			$work_obj->setWork_title ( $this->_task_title );
			$work_obj->setHide_work ( intval ( $hidework ) );
			CHARSET == 'gbk' and $work_desc = kekezu::utftogbk ( $work_desc );
			$work_obj->setWork_desc ( kekezu::escape ( kekezu::str_filter ( $work_desc ) ) );
			$work_obj->setWork_time ( time () );
			if ($file_ids) { 
				$file_arr = array_unique ( array_filter ( explode ( ',', $file_ids ) ) );
				$f_ids = implode ( ',', $file_arr ); 
				$work_obj->setWork_file ( implode ( ',', $file_arr ) );
				$work_obj->setWork_pic($this->work_pic($f_ids));
			}
			$work_id = $work_obj->create_keke_witkey_task_work ();
			
			if($work_id){
				keke_finance_class::cash_out ($this->_uid, $hand_credit, 'pub_word', 0, 'task', $this->_task_id, 1 ); 
			}
			
			$hidework == '1' and keke_payitem_class::payitem_cost ( "workhide", '1', 'work', 'spend', $work_id, $this->_task_id );
			if ($work_id) {
				$f_ids and db_factory::execute ( sprintf ( " update %switkey_file set work_id='%d',task_title='%s',obj_id='%d' where file_id in (%s)", TABLEPRE, $work_id, $this->_task_title, $work_id, $f_ids ) );
				$this->plus_work_num (); 
				$this->plus_take_num (); 
				kekezu::update_weibo_data($this->_task_id,'task',$this->_model_code,$this->_task_title,$this->_task_info['uid'],$this->_task_info['username'],$this->_task_info['leave_num'],$this->_task_info['focus_num'],$this->_task_info['work_num']+1,'work',$this->_task_info['task_cash'],$this->_uid,$this->_username);
				$notice_url = "<a href=\"" . $_K ['siteurl'] . "/index.php?do=task&task_id=" . $this->_task_id . "\">" . $this->_task_title . "</a>";
				$g_notice = array ($_lang ['user'] => $username, $_lang ['call'] => $_lang ['you'], $_lang ['task_title'] => $notice_url );
				$this->notify_user ( "task_hand", $_lang ['task_hand'], $g_notice, 2, $this->_guid ); 
				kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."&view=work&ut=my",1,$_lang ['congratulate_you_hand_work_success'],"alert_right");
			} else
				kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."&view=work&ut=my",1,$_lang ['pity_hand_work_fail'],"alert_error");
		}
	}
	public function work_choose($work_id, $to_status, $trust_response = false, $hand_credit=0, $get_credit=0) {
		global $_K, $kekezu;
		global $_lang;
		$kekezu->init_prom ();
		kekezu::check_login ( $url, $output ); 
		$this->check_if_operated ( $work_id, $to_status, $url, $output ); 
		$status_arr = $this->get_work_status ();
		if ($this->set_work_status ( $work_id, $to_status )) {
			$status_desc_arr = array ("1" => $_lang ['work_get_prize1'], "2" => $_lang ['work_get_prize2'], "3" => $_lang ['work_get_prize3'] );
			$work_info = $this->get_task_work ( $work_id ); 
			$prize_date = $this->get_prize_date (); 
			$prize_cash = $prize_date ['cash'] ['prize_' . $to_status];
			$url = '<a href ="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank" >' . $this->_task_title . '</a>';
			$v = array ($_lang ['work_status'] => $status_desc_arr [$to_status],$_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $url, $_lang ['bid_cash'] => $prize_cash );
			$this->notify_user ( "task_bid", $status_desc_arr [$to_status], $v, '1', $work_info ['uid'] ); 
			$feed_arr = array ("feed_username" => array ("content" => $work_info ['username'], "url" => "index.php?do=space&member_id={$work_info['uid']}" ), "action" => array ("content" => $_lang ['success_bid_haved'], "url" => "" ), "event" => array ("content" => "$this->_task_title ", "url" => "index.php?do=task&task_id=$this->_task_id", 'cash' => $prize_cash ) );
			kekezu::save_feed ( $feed_arr, $work_info ['uid'], $work_info ['username'], 'work_accept', $this->_task_id );
			if ($kekezu->_prom_obj->is_meet_requirement ( "bid_task", $this->_task_id )) {
				$kekezu->_prom_obj->create_prom_event ( "bid_task", $work_info ['uid'], $this->_task_id, $prize_cash );
			}
			$this->plus_accepted_num ( $work_info ['uid'] );			
			$this->check_if_gs (); 
			
			if($work_id){
				keke_finance_class::cash_in($work_info['uid'], floatval(0),intval($hand_credit),'choose_back','','task', $task_info['task_id']);
				
				$tudi_info = db_factory::get_one ( " select * from ".TABLEPRE."witkey_space where uid=".$work_info['uid']); 
				if($tudi_info['pid'])	keke_finance_class::cash_in($tudi_info['pid'], floatval(0),intval($hand_credit*$basic_config['shifu_get_credit']),'tudi_choose','','task', $task_info['task_id']);
				
				keke_finance_class::cash_in($this->_uid, floatval(0),intval($get_credit),'choose_sucess','','task', $task_info['task_id']);
			}
			
			
			kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."&view=work",1, $_lang ['work'] . $status_arr [$to_status] . $_lang ['set_success'],"alert_right");
		} else {
			kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."view=work",1,$_lang ['work'] . $status_arr [$to_status] . $_lang ['set_fail'],"alert_error");
		}
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
						sizeof ( $this->_delay_rule ) > 0 and $process_arr ['delay'] = true; 
							$process_arr ['work_choose'] = true; 
						$process_arr ['work_comment'] = true; 
						break;
					case "0" : 
						$process_arr ['work_hand'] = true;
						$process_arr ['task_comment'] = true; 
						$process_arr ['task_report'] = true; 
						break;
				}
				$process_arr ['work_report'] = true; 
				break;
			case "3" : 
				switch ($g_uid == $uid) { 
					case "1" :
						$process_arr ['work_choose'] = true; 
						$process_arr ['work_comment'] = true; 
						sizeof ( $this->get_task_work ( '', '5' ) ) > 1 and $process_arr ['task_vote'] = true; 
						break;
					case "0" :
						$process_arr ['task_comment'] = true; 
						$process_arr ['task_report'] = true; 
						break;
				}
				$process_arr ['work_report'] = true;
				break;
			case "5" : 
				switch ($g_uid == $uid) { 
					case "1" :
						$process_arr ['work_comment'] = true; 
						break;
					case "0" :
						$process_arr ['task_comment'] = true; 
						$process_arr ['task_report'] = true;
						break;
				}
				$process_arr ['work_report'] = true; 
				break;
			case "7" :
					if ($uid == ADMIN_UID&&$this->_task_config['auto_choose_rule']=='master_choose') {
						$process_arr ['work_choose'] = true;
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
					$task_info['is_top'] or $process_arr['task_recommend']=true;
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
	public function set_work_status($work_id, $to_status) {
		return db_factory::execute ( sprintf ( " update %switkey_task_work set work_status='%d' where work_id='%d'", TABLEPRE, $to_status, $work_id ) );
	}
	public function set_task_sp_end_time($time_type = 'notice_period') {
		global $_lang;
		$sp_end_time = time () + $this->_task_config [$time_type] * 24 * 3600;
		return db_factory::execute ( sprintf ( " update %switkey_task set sp_end_time = '%d' where task_id='%d' ", TABLEPRE, $sp_end_time, $this->_task_id ) );
	}
	public static function task_delay($task_id, $task_cash, $delay_cash) {
		$prize_data = db_factory::query ( sprintf ( " select * from %switkey_task_prize where task_id='%d'", TABLEPRE, $task_id ) );
		foreach ( $prize_data as $v ) {
			$rate = $v ['prize_cash'] / $task_cash;
			$new_cash = $v ['prize_cash'] + $delay_cash * $rate;
			db_factory::execute ( sprintf ( " update %switkey_task_prize set prize_cash='%.2f' where prize_id='%d'", TABLEPRE, $new_cash, $v ['prize_id'] ) );
		}
	}
	public function dispose_task_return() {
		global $kekezu;
		$config = $this->_task_config;
		$task_info = $this->_task_info;
		$task_cash = $task_info ['task_cash']; 
		switch ($config ['defeated']) {
			case "1" : 
				$return_cash = $task_cash * (1 - $config ['task_fail_rate'] / 100);
				$return_credit = '0';
				$data = array (':model_name' => $this->_model_name, ':task_id' => $this->_task_id, ':task_title' => $this->_task_title );
				keke_finance_class::init_mem ( 'task_fail', $data );
				$res = keke_finance_class::cash_in ( $this->_guid, floatval ( $return_cash ), 0, 'task_fail', '', 'task', $this->_task_id, $task_cash - $return_cash );
				break;
			case "2" :
				$return_credit = $task_cash * (1 - $config ['task_fail_rate'] / 100);
				$return_cash = '0';
				$data = array (':model_name' => $this->_model_name, ':task_id' => $this->_task_id, ':task_title' => $this->_task_title );
				keke_finance_class::init_mem ( 'task_fail', $data );
				$res = keke_finance_class::cash_in ( $this->_guid, 0, floatval ( $return_credit ), 'task_fail', '', 'task', $this->_task_id, $task_cash - $return_credit );
				break;
		}
		if ($res) {
			$this->union_task_close(-1);
			$kekezu->init_prom ();
			$p_event = $kekezu->_prom_obj->get_prom_event ( $this->_task_id, $this->_guid, "pub_task" );
			$kekezu->_prom_obj->set_prom_event_status ( $p_event ['parent_uid'], $this->_gusername, intval($p_event ['event_id']), '3' );
			$this->set_task_status ( 9 ); 
		}
	}
	public function task_tg_timeout() {
		global $_lang;
		if (time () > $this->_task_info ['sub_time'] && $this->_task_info ['task_status'] == 2) {
			$work_num = $this->_task_info ['work_num'];
			if ($work_num == 0) {
				$this->dispose_task_return ();
				$v_arr = array ($_lang ['username'] => $this->_gusername, $_lang ['model_name'] => $this->_model_name, $_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $this->_task_title, $_lang ['tb'] => $_lang ['haved_fail_for_task_not_work'], 'explain' => '' );
				keke_msg_class::notify_user ( $this->_guid, $this->_gusername, 'task_fail', $_lang ['task_fail'], $v_arr );
			}
			if ($work_num > 0) {
				$this->set_task_status ( 3 ); 
				$v_arr = array ($_lang ['username'] => $this->_gusername, $_lang ['model_name'] => $this->_model_name, $_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $this->_task_title, $_lang ['tb'] => $_lang ['tg'], $_lang ['time'] => date ( 'Y-m-d H:i:s' ) );
				keke_msg_class::notify_user ( $this->_guid, $this->_gusername, 'timeout', $_lang ['choose_work_timeout'], $v_arr );
			}
		}
	}
	public function task_xg_timeout() {
		global $_lang;
		if (time () > $this->_task_info ['end_time'] && $this->_task_info ['task_status'] == 3) {
			$mxs_config = kekezu::get_task_config ( 2 );
			$prize_date = $this->get_prize_date (); 
			$total_prize_count = $prize_date ['count'] ['prize_1'] + $prize_date ['count'] ['prize_2'] + $prize_date ['count'] ['prize_3'];
			$work_num = $this->_task_info ['work_num'];
			$bid_count = db_factory::get_count ( sprintf ( "select count(work_id) as work_count from %switkey_task_work where task_id='%d' and work_status in (1,2,3) ", TABLEPRE, $this->_task_id) );
			if (!$bid_count) {
				$this->auto_choose ( $prize_date ['count'] ['prize_1'], $prize_date ['count'] ['prize_2'], $total_prize_count ); 
			} else {
				$this->set_task_status ( 5 ); 
				$this->set_task_sp_end_time ();
				$v_arr = array ($_lang ['username'] => $this->_gusername, $_lang ['model_name'] => $this->_model_name, $_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $this->_task_title, $_lang ['tb'] => $_lang ['tg'], $_lang ['time'] => date ( 'Y-m-d H:i:s' ), 'next' => $_lang ['gs'] );
				keke_msg_class::notify_user ( $this->_guid, $this->_gusername, 'timeout', $_lang ['task_gs'], $v_arr );
			}
		}
	}
	public function task_gs_timeout() {
		global $kekezu;
		global $_lang;
		$kekezu->init_prom ();
		$prom_obj = $kekezu->_prom_obj;
		if (time () > $this->_task_info ['sp_end_time'] && $this->_task_info ['task_status'] == 5) {
			$prize_work_arr = db_factory::query ( sprintf ( "select * from %switkey_task_work where task_id='%d' and work_status in(1,2,3) ", TABLEPRE, $this->_task_id ) );
			$prize_date = $this->get_prize_date (); 
			$bid_uid = array();
			foreach ( $prize_work_arr as $k => $v ) {
				$prize = "prize_" . $v ['work_status'];
				$prize_cash = $prize_date ['cash'] [$prize];
				$prize_real_cash = $prize_cash * (1 - $this->_task_config ['task_rate'] / 100);
				$data = array (':task_id' => $this->_task_id, ':task_title' => $this->_task_title );
				keke_finance_class::init_mem ( 'task_bid', $data );
				keke_finance_class::cash_in ( $v ['uid'], $prize_real_cash, 0, 'task_bid', '', 'task', $this->_task_id, $prize_cash - $prize_real_cash ); 
				$prize_total_cash += $prize_cash;
				$status_desc_arr = array ("1" => $_lang ['work_get_prize1'], "2" => $_lang ['work_get_prize2'], "3" => $_lang ['work_get_prize3'] );
				$v_arr = array ($_lang ['username'] => $v ['username'], $_lang ['model_name'] => $this->_model_name, $_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $this->_task_title, $_lang ['bid_cash'] => $prize_cash );
				keke_msg_class::notify_user ( $v ['uid'], $v ['username'], 'task_bid', $status_desc_arr [$v ['work_status']], $v_arr );
				$prom_obj->dispose_prom_event ( "bid_task", $v ['uid'], $v ['work_id'] );
				keke_user_mark_class::create_mark_log ( $this->_model_code, 1, $v ['uid'], $this->_guid, $v ['work_id'], $prize_cash, $this->_task_id, $v ['username'], $this->_gusername );
				keke_user_mark_class::create_mark_log ( $this->_model_code, 2, $this->_guid, $v ['uid'], $v ['work_id'], $prize_cash * (1 - $this->_task_config ['task_rate'] / 100), $this->_task_id, $this->_gusername, $v ['username'] );
				$this->plus_mark_num ();
				$bid_uid[] = $v['uid'];
			}
			$this->union_task_close(1,$bid_uid);
			$this->set_task_status ( 8 ); 
			$prom_obj->dispose_prom_event ( "pub_task", $this->_guid, $this->_task_id );
			$if_sy = $this->_task_info ['task_cash'] - $prize_total_cash;
			if (intval ( $if_sy ) > 0) {
				$return_g_cash = $if_sy * (1 - $this->_task_config ['task_fail_rate'] / 100);
				if ($this->_task_config ['defeated'] == 1) { 
					$data = array (':model_name' => $this->_model_name, ':task_id' => $this->_task_id, ':task_title' => $this->_task_title );
					keke_finance_class::init_mem ( 'task_remain_return', $data );
					keke_finance_class::cash_in ( $this->_guid, floatval ( $return_g_cash ), 0, 'task_remain_return', '', 'task', $this->_task_id, $if_sy - $return_g_cash ); 
				} else { 
					$data = array (':model_name' => $this->_model_name, ':task_id' => $this->_task_id, ':task_title' => $this->_task_title );
					keke_finance_class::init_mem ( 'task_remain_return', $data );
					keke_finance_class::cash_in ( $this->_guid, 0, floatval ( $return_g_cash ), 'task_remain_return', '', 'task', $this->_task_id, $if_sy - $return_g_cash ); 
				}
				$v_arr = array ($_lang ['username'] => $this->_gusername, $_lang ['model_name'] => $this->_model_name, $_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $this->_task_title, $_lang ['reason'] => $_lang ['gs_timeout_and_task_over_and_return_your_remain_cash'] );
				keke_msg_class::notify_user ( $this->_guid, $this->_gusername, 'task_complete', $_lang ['task_js'], $v_arr );
			} else {
				$v_arr = array ($_lang ['username'] => $this->_gusername, $_lang ['model_name'] => $this->_model_name, $_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $this->_task_title, $_lang ['reason'] => $_lang ['gs_timeout_and_task_complete'] );
				keke_msg_class::notify_user ( $this->_guid, $this->_gusername, 'task_complete', $_lang ['task_js'], $v_arr );
			}
		}
	}
	public function auto_choose($prize1_num, $prize2_num, $prize_all) {
		global $kekezu;
		global $_lang;
		$kekezu->init_prom ();
		$prom_obj = $kekezu->_prom_obj;
		$has_bided = db_factory::get_count ( sprintf ( " select count(work_id) from %switkey_task_work where work_status in (1,2,3) and task_id ='%d'", TABLEPRE, $this->_task_id ) );
		if (!$has_bided) {
		switch ($this->_task_config ['end_action']) {
			case "split" :
				$prize_date = $this->get_prize_date (); 
				$split_style = $this->_task_config['auto_choose_rule'];
				if(in_array($split_style,array('work_time','take_num','seller_credit'))){
					switch($split_style){
						case "work_time":
							$order_field = "a.work_time asc";
							break;
						case "take_num":
							$order_field = "c.take_num desc";
							break;
						case "seller_credit":
							$order_field = "c.seller_credit desc";
							break;
					}
					$sql = "select a.* from %switkey_task_work a
									left join %switkey_space c on a.uid=c.uid
								where a.task_id='%d' and a.work_status='0' order by %s,a.work_time asc limit 0,%d";
					$work_bid_arr = db_factory::query(sprintf($sql,TABLEPRE,TABLEPRE,$this->_task_id,$order_field,$prize_all));
					foreach ( $work_bid_arr as $k => $v ) {
						if ($k < $prize1_num) {
							$this->set_work_status ( $v ['work_id'], 1 );
							$to_status = 1;
							$prize_cash = $prize_date ['cash'] ['1'];
						} elseif ($k < $prize1_num + $prize2_num) {
							$this->set_work_status ( $v ['work_id'], 2 );
							$to_status = 2;
							$prize_cash = $prize_date ['cash'] ['2'];
						} elseif ($k < $prize_all) {
							$this->set_work_status ( $v ['work_id'], 3 );
							$to_status = 3;
							$prize_cash = $prize_date ['cash'] ['3'];
						}
						if ($prom_obj->is_meet_requirement ( "bid_task", $this->_task_id )) {
							$prom_obj->create_prom_event ( "bid_task", $v ['uid'], $this->_task_id, $prize_cash );
						}
						$status_desc_arr = array ("1" => $_lang ['work_get_prize1'], "2" => $_lang ['work_get_prize2'], "3" => $_lang ['work_get_prize3'] );
						$v_arr = array ($_lang ['username'] => $v ['username'], $_lang ['model_name'] => $this->_model_name, $_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $this->_task_title, $_lang ['work_title'] => $v ['work_title'], $_lang ['bid_cash'] => $prize_cash );
						keke_msg_class::notify_user ( $v ['uid'], $v ['username'], 'task_bid', $status_desc_arr [$to_status], $v_arr );
					}
					$this->set_task_status ( 5 );
					$this->set_task_sp_end_time ();
				}elseif($split_style == 'master_choose'){
					$this->set_task_status (7);
				}
				break;
			case "refund" : 
				$this->dispose_task_return ();
				$v_arr = array ($_lang ['username'] => $this->_gusername, $_lang ['model_name'] => $this->_model_name, $_lang ['task_id'] => $this->_task_id, $_lang ['task_title'] => $this->_task_title, $_lang ['reason'] => $_lang ['for_no_operate_and_task_fail'] );
				keke_msg_class::notify_user ( $this->_guid, $this->_gusername, 'task_fail', $_lang ['task_fail'], $v_arr );
				break;
		}
		}
	}
	public function check_if_operated($work_id, $to_status, $url = '', $output = 'normal') {
		global $_lang;
		$can_select = false; 
		if ($this->check_if_can_choose ( $url, $output )) { 
			$work_status = db_factory::get_count ( sprintf ( " select work_status from %switkey_task_work where work_id='%d'", TABLEPRE, $work_id) );
			if ($work_status == '8') {
				kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."&view=work",1, $_lang ['the_work_is_not_choose_and_not_choose_the_work'],"alert_error");
			} else {
				$prize_date = $this->get_prize_date ();
				$prize_work_date = $this->get_prize_work_count ();
				$work_count = $prize_work_date ["prize_" . $to_status];
				$prize_count = $prize_date ['count'] ["prize_" . $to_status];
				if ($work_count == $prize_count) {
					kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."&view=work",1, $_lang ['now_task'] . "$to_status" . $_lang ['prize_have_full'] . "$to_status" . $_lang ['prize_th'],"alert_error");
				} else {
					return true;
				}
			}
		} else { 
			kekezu::show_msg($_lang['operate_tips'],"index.php?do=task&task_id=".$this->_task_id."&view=work",1, $_lang ['now_status_can_not_choose'],"alert_error");
		}
	}
	public static function get_task_status() {
		global $_lang;
		return array ("0" => $_lang ['task_no_pay'], "1" => $_lang ['task_wait_audit'], "2" => $_lang ['task_vote_choose'], "3" => $_lang ['task_choose_work'], "5" => $_lang ['task_gs'], "7" => $_lang ['freeze'], "8" => $_lang ['task_over'], "9" => $_lang ['fail'], "10" => $_lang ['task_audit_fail']);
	}
	public static function get_work_status() {
		global $_lang;
		return array ('1' => $_lang ['prize_1'], '2' => $_lang ['prize_2'], "3" => $_lang ['prize_3'], '8' => $_lang ['task_can_not_choose_bid'] );
	}
	public function get_work_prize() {
		global $_lang;
		$prize_arr = $this->get_task_prize ();
		switch (count ( $prize_arr )) {
			case 1 :
				return array ('1' => $_lang ['prize_1'] );
				break;
			case 2 :
				return array ('1' => $_lang ['prize_1'], '2' => $_lang ['prize_2'] );
				break;
			case 3 :
				return array ('1' => $_lang ['prize_1'], '2' => $_lang ['prize_2'], "3" => $_lang ['prize_3'] );
				break;
		}
	}
	public function get_task_prize() {
		$task_prize_arr = kekezu::get_table_data ( "*", "witkey_task_prize", "task_id={$this->_task_id}", "", "", "", "prize", 0 );
		return $task_prize_arr;
	}
	public function check_if_gs() {
		$prize_date = $this->get_prize_date (); 
		$work_count = $this->get_prize_work_count ();
		if ($prize_date ['count'] ['prize_1'] == $work_count ['prize_1'] && $prize_date ['count'] ['prize_2'] == $work_count ['prize_2'] && $prize_date ['count'] ['prize_3'] == $work_count ['prize_3']) {
			$this->set_task_status ( '5' );
			$this->set_task_sp_end_time ();
		}
	}
	public function get_prize_date() {
		$all_prize_data = array ();
		$count = array (); 
		$cash = array (); 
		$prize_arr = db_factory::query ( sprintf ( "select * from %switkey_task_prize where task_id='%d' ", TABLEPRE, $this->_task_id ) );
		$i = 1;
		foreach ( $prize_arr as $k => $v ) {
			$count ["prize_" . $i] = $v ['prize_count'];
			$cash ["prize_" . $i] = $v ['prize_cash'] / $v ['prize_count'];
			$i ++;
		}
		$all_prize_data ['count'] = $count;
		$all_prize_data ['cash'] = $cash;
		return $all_prize_data; 
	}
	public function get_prize_work_count() {
		$prize_work_date = array ();
		$work_count_arr = db_factory::query ( sprintf ( "select work_status,count(work_id)  as work_count from %switkey_task_work where task_id='%d' and work_status in(1,2,3) GROUP BY work_status ", TABLEPRE, $this->_task_id ) );
		foreach ( $work_count_arr as $v ) {
			$prize = "prize_" . $v ['work_status'];
			$prize_work_count [$prize] = $v ['work_count'];
		}
		return $prize_work_count; 
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
			$task_status == 1 && $notice = $_lang ['task_pay_success_and_wait_admin_audit'];
			$task_status == 2 && $notice = $_lang ['task_pay_success_and_task_pub_success'];
			return pay_return_fac_class::struct_response ( $_lang ['operate_notice'], $notice, $url, 'success' );
		} else {
			$data = array (':model_name' => $this->_model_name, ':task_id' => $this->_task_id, ':task_title' => $this->_task_title );
			keke_finance_class::init_mem ( 'pub_task', $data );
			$order_amount>0 and $res = keke_finance_class::cash_out ( $this->_task_info ['uid'], $order_amount, 'pub_task' );
			kekezu::save_weibo_data($task_info['task_id'],'task',$kekezu->_model_list[$task_info[model_id]]['model_code'],$task_info['task_title'],$task_info['uid'],$task_info['username'],0,0,0,'pub',$task_info['task_cash'],$uid,$username);
			if ($res) { 
				$kekezu->init_prom ();
				if ($kekezu->_prom_obj->is_meet_requirement ( "pub_task", $this->_task_id )) {
					$kekezu->_prom_obj->create_prom_event ( "pub_task", $this->_guid, $this->_task_id, $this->_task_info ['task_cash'] );
				}
					keke_union_class::union_task_submit($this->_g_userinfo,$this->_task_id);
				$consume = kekezu::get_cash_consume ( $task_info ['task_cash'] );
				db_factory::execute ( sprintf ( " update %switkey_task set cash_cost='%s',credit_cost='%s' where task_id='%d'", TABLEPRE, $consume ['cash'], $consume ['credit'], $this->_task_id ) );
				$feed_arr = array ("feed_username" => array ("content" => $task_info ['username'], "url" => "index.php?do=space&member_id={$task_info['uid']}" ), "action" => array ("content" => $_lang ['pub_task'], "url" => "" ), "event" => array ("content" => "{$task_info['task_title']}", "url" => "index.php?do=task&task_id={$task_info['task_id']}" ) );
				kekezu::save_feed ( $feed_arr, $task_info ['uid'], $task_info ['username'], 'pub_task', $task_info ['task_id'] );
				db_factory::updatetable ( TABLEPRE . "witkey_order", array ("order_status" => "ok" ), array ("order_id" => "$order_id" ) );
				if ($order_amount < $task_config ['audit_cash']) { 
					$this->set_task_status ( 1 ); 
					return pay_return_fac_class::struct_response ( $_lang ['operate_notice'], $_lang ['task_pay_success_and_wait_admin_audit'], $url, 'success' );
				} else {
					$this->set_task_status ( 2 ); 
					return pay_return_fac_class::struct_response ( $_lang ['operate_notice'], $_lang ['task_pay_success_and_task_pub_success'], $url, 'success' );
				}
			} else { 
				$pay_url = $_K ['siteurl'] . "/index.php?do=pay&order_id=$order_id"; 
				return pay_return_fac_class::struct_response ( $_lang ['operate_notice'], $_lang ['task_pay_error_and_please_repay'], $pay_url, 'warning' );
			}
		}
	}
	public static function master_opera($m_id, $t_id, $url,$t_cash) {
		global $uid, $_K, $do, $view, $_lang;
		$status = db_factory::get_count ( sprintf ( ' select task_status from %switkey_task where task_id=%d and uid=%d', TABLEPRE, $t_id, $uid ), 0, 'task_status', 600 );
		$order_info = db_factory::get_one(sprintf("select order_id from %switkey_order_detail where obj_id=%d",TABLEPRE,$t_id));				
		$site = $_K ['siteurl'] . '/';
		$button = array ();
		$button ['view'] = array ('href' => $site . 'index.php?do=task&task_id=' . $t_id, 'desc' => $_lang ['view'],
'ico' => 'book' );
		$button ['onkey'] = array ('href' => $site . 'index.php?do=release&t_id=' . $t_id . '&model_id=' . $m_id . '&pub_mode=onekey', 'desc' => $_lang ['one_key_pub'],
'ico' => 'book' );
		$button ['del'] = array ('href' => $site . $url . '&ac=del&task_id=' . $t_id, 
'desc' => $_lang ['delete'], 
'click' => 'return del(this);',
'ico' => 'trash' ); 
		switch ($status) {
			case 0 : 
				$button ['pay'] = array ('href' => $site . 'index.php?do=' . $do . '&view=' . $view . '&task_id=' . $t_id . '&model_id=' . $m_id . '&ac=pay', 'desc' => $_lang ['payment'], 
'click' => "return pay(this,$t_cash,$order_info[order_id]);",
'ico' => 'loop' );
				break;
			case 2 :
				$button ['tool'] = array ('href' => $site . 'index.php?do=task&task_id=' . $t_id . '&view=tools', 'desc' => $_lang ['toolbox'], 
'ico' => 'trash' );
				$button ['addprice'] = array ('click' => "taskDelay('index.php?do=task&task_id=$t_id')", 'desc' => $_lang ['delay_makeup'], 
'ico' => 'book', 'href' => 'javascript:void(0)' );
				break;
			case 3 : 
				$button ['view'] ['desc'] = $_lang ['choose_work']; 
				$button ['view'] ['href'] = $site . 'index.php?do=task&task_id=' . $t_id . '&view=work';
				break;
			case 4 : 
				$button ['view'] ['desc'] = $_lang ['vote']; 
				$button ['view'] ['href'] = $site . 'index.php?do=task&task_id=' . $t_id . '&view=work';
				break;
		}
		if (! in_array ( $status, array (0, 8, 9, 10 ) )) { 
			unset ( $button ['del'] );
		}
		return $button;
	}
	public static function wiki_opera($m_id, $t_id, $w_id, $url) {
		global $uid, $_K, $do, $view, $_lang;
		$status = db_factory::get_count ( sprintf ( ' select task_status from %switkey_task where task_id=%d', TABLEPRE, $t_id, $uid ), 0, 'task_status', 600 );
		$site = $_K ['siteurl'] . '/';
		$button = array ();
		$button ['view'] = array ('href' => $site . 'index.php?do=task&task_id=' . $t_id . '&view=work&ut=my&work_id=' . $w_id, 'desc' => $_lang ['view_work'], 
'ico' => 'book' );
		switch ($status) {
			case 2 : 
				$button ['share'] = array ('href' => 'javascript:void(0);', 'desc' => $_lang ['share'],
'click' => 'share(' . $t_id . ');', 'ico' => 'share' );
				break;
			case 8 : 
			case 9 :
				$button ['del'] = array ('href' => $site . $url . '&ac=del&work_id=' . $w_id, 
'desc' => $_lang ['delete'], 
'click' => 'return del(this);', 
'ico' => 'trash' ); 
				break;
		}
		return $button;
	}
}