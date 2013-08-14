<?php
class preward_report_class extends keke_report_class {
	public static function get_instance($report_id, $report_info = null, $obj_info = null) {
		static $obj = null;
		if ($obj == null) {
			$obj = new preward_report_class ( $report_id, $report_info, $obj_info );
		}
		return $obj;
	}
	public function __construct($report_id, $report_info, $obj_info) {
		parent::__construct ( $report_id, $report_info, $obj_info );
		$this->_task_info = $this->get_task_info($this->_report_info['origin_id']);
		$this->_task_obj = new preward_task_class($this->_task_info);
	}
	function process_report($op_result, $type) {
		keke_lang_class::load_lang_class('preward_report_class');
		global $_K, $kekezu;
		global $_lang;
		$op_result = $this->op_result_format ( $op_result );
		$trans_name = $this->get_transrights_name ( $this->_report_info ['report_type'] );
		if($op_result ['action']){
			switch ($op_result ['task']) {
				case 1:
					$this->_task_obj->set_task_status(9);
					$this->_task_obj->dispose_task_return('task_fail');
					$this->process_notify ( 'pass', $this->_report_info, $this->_user_info, $this->_to_userinfo, $op_result ['result'] ); 
					$res = $this->change_status ( $this->_report_id, '4', $op_result, $op_result ['result'] ); 
					break;
				case 2:
					intval ( $this->_task_info ['work_count'] ) and $auto_num = intval ( $this->_task_info ['work_count'] );
					$work_list = db_factory::query ( sprintf ( "select * from %switkey_task_work where task_id='%d' and work_status=0 order by work_time asc limit 0,%d", TABLEPRE, $this->_task_obj->_task_id, $auto_num ) );
					if ($work_list) {
						foreach ( $work_list as $v ) {
							$this->_task_obj->set_work_status ( $v ['work_id'], 6 );
							$task_id = $this->_task_obj->_task_id;
							$task_title = $this->_task_obj->_task_title;
							$title_url = "<a href=\"$_K[siteurl]/index.php?do=task&task_id=$task_id\" >$task_title</a>";
							$this->_task_obj->work_choosed ( $v, $title_url );
						}
					}
					$this->process_notify ( 'pass', $this->_report_info, $this->_user_info, $this->_to_userinfo, $op_result ['result'] ); 
					$res = $this->change_status ( $this->_report_id, '4', $op_result, $op_result ['result'] ); 
					break;
				case 3:
					$this->process_notify ( 'nopass', $this->_report_info, $this->_user_info, $this->_to_userinfo, $op_result ['result'] ); 
					$res = $this->change_status ( $this->_report_id, '3', $op_result, $op_result, $op_result ['result'] ); 
					break;
				case 4:
					$res = $this->pingbi_work($this->_obj_info ['obj_id'],$type);
					$this->process_notify ( 'pass', $this->_report_info, $this->_user_info, $this->_to_userinfo, $op_result ['result'] ); 
					$res = $this->change_status ( $this->_report_id, '4', $op_result, $op_result ['result'] ); 
					break;
				case 5:
					$this->cancel_work ( $this->_obj_info ['obj_id'],$type);
						intval ( $this->_task_info ['work_count'] ) and $auto_num = intval ( $this->_task_info ['work_count'] );
						$work_list = db_factory::query ( sprintf ( "select * from %switkey_task_work where task_id='%d' and work_status=0 order by work_time asc limit 0,%d", TABLEPRE, $this->_task_obj->_task_id, $auto_num ) );
						if ($work_list) {
							foreach ( $work_list as $v ) {
								$this->_task_obj->set_work_status ( $v ['work_id'], 6 );
								$task_id = $this->_task_obj->_task_id;
								$task_title = $this->_task_obj->_task_title;
								$title_url = "<a href=\"$_K[siteurl]/index.php?do=task&task_id=$task_id\" >$task_title</a>";
								$this->_task_obj->work_choosed ( $v, $title_url );
							}
						}
						if(count($work_list)==$auto_num){
							$this->_task_obj->set_task_status(8);
						}
					$this->process_notify ( 'pass', $this->_report_info, $this->_user_info, $this->_to_userinfo, $op_result ['result'] ); 
					$res = $this->change_status ( $this->_report_id, '4', $op_result, $op_result ['result'] ); 
					break;
			}
			if($res){
				kekezu::admin_show_msg ( $trans_name . $_lang['deal_success'], "index.php?do=trans&view=report&type=$type", "3","","success" );
			}else{
				kekezu::admin_show_msg ( $trans_name . $_lang['deal_fail'], "index.php?do=trans&view=process&type=$type&report_id=" . $this->_report_id, "3","","warning" );
			}
		}
	}
	function process_rights($op_result, $type) {
		return true; 
	}
	public function cancel_work($work_id,$type) {
		keke_lang_class::load_lang_class('preward_report_class');
		global $_K, $kekezu;
		global $_lang;
		global $admin_info;
		$this->_task_info['task_status'] == 8  and kekezu::admin_show_msg ( '操作提示', "index.php?do=trans&view=report&type=$type", "3","当前任务不能取消中标","warning" );
		$work_info = $this->_task_obj->get_task_work($work_id);
		$work_info['work_status'] != 6 and kekezu::admin_show_msg ( '操作提示', "index.php?do=trans&view=report&type=$type", "3","当前稿件未中标，不能取消中标状态","warning" );
		$admin_info['group_id'] != 7 and kekezu::admin_show_msg ( '操作提示', "index.php?do=trans&view=report&type=$type", "3","您没有权限对此搞件进行取消中标操作","warning" );
		if($this->_task_obj->set_work_status($work_id, 8)){
			$cash = floatval ( $this->_task_info['single_cash'] ) * (1-floatval ( $this->_task_info ['profit_rate']/100) );
			$data = array (':model_name' => $this->_task_obj->_model_name, ':task_id' => $this->_task_obj->_task_id, ':task_title' => $this->_task_obj->_task_title );
			keke_finance_class::init_mem('cancel_bid', $data);
			$cash>0 and keke_finance_class::cash_out ( $work_info ['uid'], $cash, 'cancel_bid');
			$task_id = $this->_task_obj->_task_id;
			$task_title = $this->_task_obj->_task_title;
			$task_url = "<a href=\"{$_K[siteurl]}/index.php?do=task&task_id=$task_id\">$task_title</a>";
			kekezu::notify_user ( $_lang['cancel_bid_notice'], $_lang['you_in_task'] . $task_url . $_lang['de_hand_work_jh'] . $work_id . $_lang['by_site_kf_cancel_bid'], $work_info['uid'] );
		}
		db_factory::execute ( sprintf ( " update %switkey_space set accepted_num = accepted_num-1 where uid = '%d'", TABLEPRE, $this->_obj_info ['obj_uid'] ) );
	}
	public function pingbi_work($work_id,$type) {
		keke_lang_class::load_lang_class('preward_report_class');
		global $_K, $kekezu;
		global $_lang;
		$this->_task_info['task_status'] == 8  and kekezu::admin_show_msg ( '操作提示', "index.php?do=trans&view=report&type=$type", "3","当前任务不能屏蔽稿件","warning" );
		$work_info = $this->_task_obj->get_task_work($work_id);
		$work_info['work_status'] != 0 and kekezu::admin_show_msg ( '操作提示', "index.php?do=trans&view=report&type=$type", "3","当前稿件雇主已选择，不能屏蔽此稿件","warning" );
		$this->_task_obj->_userinfo ['group_id'] != 7 and kekezu::admin_show_msg ( '操作提示', "index.php?do=trans&view=report&type=$type", "3","您没有权限对此搞件进行取消中标操作","warning" );
		$this->_task_obj->set_work_status($work_id, 7);
	}
}
?>