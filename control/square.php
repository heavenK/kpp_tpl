<?php defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$views = array (
		'index',
		'goods',
		'goods_detail',
		'demand',
		'favor',
		'manuscript',
		'message',
		'task_detail',
		'focus' 
);
in_array ( $view, $views ) and $view or $view = 'index';
keke_lang_class::package_init($do);
keke_lang_class::loadlang($do."_".$view);
$nav_active_index = "square";
if (! $plug_arr [square] [status]) {
	kekezu::show_msg ( $_lang ['operate_notice'], "index.php?do=index.php", '3', '此插件未开启', 'alert_error' );
}
if (isset ( $op ) && $op == 'denounce') {
	$denounce_obj = new Keke_witkey_free_denounce_class ();
	if ($sbt_edit) {
		if (CHARSET == 'gbk') {
			$from_type = kekezu::utftogbk ( $from_type );
			$to_username = kekezu::utftogbk ( $to_username );
			$reason = kekezu::utftogbk ( $reason );
			$obj_content = kekezu::utftogbk ( $obj_content );
		}
		!$uid and kekezu::echojson('未登录，请您先登录！', 0, '');
		if (check_if_denounce ( $obj_id, $uid, $from_type )) {
			kekezu::echojson ( '请勿重复举报', '0', '' );
		} else {
			$denounce_obj->setObj_id ( $obj_id );
			$denounce_obj->setFrom_type ( kekezu::escape ( $from_type ) );
			$denounce_obj->setUid ( $uid );
			$denounce_obj->setUsername ( $username );
			$denounce_obj->setTo_uid ( $to_uid );
			$denounce_obj->setTo_username ( $to_username );
			$denounce_obj->setReason ( kekezu::escape ( $reason ) );
			$denounce_obj->setContent ( kekezu::escape ( $obj_content ) );
			$denounce_obj->setOn_time ( time () );
			$res = $denounce_obj->create_keke_witkey_free_denounce ();
			$res and kekezu::echojson ( '提交成功，感谢您的举报！', 1, '' ) or kekezu::echojson ( '提交举报失败！', 0, '' );
		}
	} else {
		if (CHARSET == 'gbk') {
			$to_username = kekezu::utftogbk ( $to_username );
			$obj_content = kekezu::utftogbk ( $obj_content );
		}
		$title = '举报';
		require keke_tpl_class::template ( "denounce" );
	}
	die ();
}
function check_if_denounce($obj_id, $uid, $from_type) {
	$res = db_factory::get_one ( sprintf ( "select denounce_id from %switkey_free_denounce where obj_id = '%d' and uid = '%d' and from_type = '%s'", TABLEPRE, $obj_id, $uid, $from_type ) );
	if ($res) {
		return TRUE;
	} else {
		return FALSE;
	}
}
if(isset($action)&&$action=="add_favor"){
	!$uid and kekezu::echojson('未登录，无法收藏',0,'');
	if(check_if_favor($obj_id, $obj_type)){
		kekezu::echojson('已经收藏过了，请勿重新收藏',0,'');
	}else{
		$favor_obj = keke_table_class::get_instance('witkey_free_favor');
		if($obj_type=='free_service'){
			$obj_info = db_factory::get_one(sprintf("select * from %switkey_free_service where service_id=%d",TABLEPRE,$obj_id));
			$fds['obj_title']=$obj_info['service_title'];
		}elseif($obj_type=='free_task'){
			$obj_info = db_factory::get_one(sprintf("select * from %switkey_free_task where task_id=%d",TABLEPRE,$obj_id));
			$fds['obj_title']=$obj_info['task_title'];
		}
		$fds['obj_id'] = $obj_id;
		$fds['obj_type'] = $obj_type;
		$fds['uid'] = $uid;
		$fds['username'] = $username;
		$fds['on_time'] = time();
		$res = $favor_obj->save($fds);
		if($obj_type=='free_task'){
			db_factory::execute(sprintf("update %switkey_free_task set focus_num = ifnull(focus_num,0)+1 where task_id=%d",TABLEPRE,$obj_id));
			kekezu::update_weibo_data($obj_info['task_id'],$fds['obj_type'],'free_task',$obj_info['task_title'],$obj_info['uid'],$obj_info['username'],intval($obj_info['leave_num']),intval($obj_info['focus_num'])+1,intval($obj_info['work_num']),'focus',0,$uid,$username);
		}elseif($obj_type=='free_service'){
			db_factory::execute(sprintf("update %switkey_free_service set focus_num = ifnull(focus_num,0)+1 where service_id=%d",TABLEPRE,$obj_id));
			kekezu::update_weibo_data($obj_info['service_id'],$fds['obj_type'],'free_service',$obj_info['service_title'],$obj_info['uid'],$obj_info['username'],intval($obj_info['leave_num']),intval($obj_info['focus_num'])+1,intval($obj_info['work_num']),'focus',0,$uid,$username);
		}
		$res and kekezu::echojson('收藏成功',1,'') or kekezu::echojson('收藏失败',0,'');
	}
}
function check_if_favor($id,$type){
	global $uid;
	$res = db_factory::get_one(sprintf("select favor_id  from %switkey_free_favor where obj_id = '%d' and obj_type ='%s' and uid= %d;",TABLEPRE,$id,$type,$uid));
	if($res){
		return TRUE;
	}else{
		return FALSE;
	}
}
$model_info = kekezu::get_table_data ( '*', 'witkey_model', 'model_status=1', '', '', '', 'model_code' );
$cash_cove = $kekezu->get_cash_cove ( '', true );
$bulletin_arr = db_factory::query ( sprintf ( "select art_id,art_title,listorder,is_recommend,pub_time from %switkey_article where cat_type='bulletin' order by is_recommend desc, listorder asc, pub_time desc limit 0,4", TABLEPRE ) );
$task_count = db_factory::get_count ( sprintf ( "select count(task_id) from %switkey_task where uid=%d ", TABLEPRE, $uid ) );
$task_free_count = db_factory::get_count ( sprintf ( "select count(task_id) from %switkey_free_task where uid=%d", TABLEPRE, $uid ) );
$t_count = $task_count + $task_free_count;
$service_count = db_factory::get_count ( sprintf ( "select count(service_id) from %switkey_service where uid=%d", TABLEPRE, $uid ) );
$servoce_free_count = db_factory::get_count ( sprintf ( "select count(service_id) from %switkey_free_service where uid = %d", TABLEPRE, $uid ) );
$s_count = $service_count + $servoce_free_count;
require S_ROOT . '/control/' . $do . '/' . $do . '_' . $view . '.php';
 