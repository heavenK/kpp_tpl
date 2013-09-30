<?php
defined ( 'IN_KEKE' ) or exit('Access Denied');


if($type == 1){
	
	$baoming = db_factory::get_one ( sprintf ( "select * from %switkey_task_baoming where task_id=%d and uid=%d", TABLEPRE, $task_id, $v_uid) );
	
	if($baoming){
		echo "fail";
		exit;
	}else{
	
		$sql = sprintf ( "insert into %switkey_task_baoming value(%d,%d,%d)", TABLEPRE, $task_id, $v_uid, time());
		$res = db_factory::execute ( $sql );
		echo $res;
	
		if($res)	$r = keke_finance_class::cash_out ($v_uid, $basic_config['baoming_credit'], 'baoming_vote','','','',1); 
		exit;
	}
}
if($type ==2){
	
	$vote_flag = db_factory::get_one ( sprintf ( "select * from %switkey_vote where task_id=%d and uid=%d", TABLEPRE, $task_id, $uid) );
	
	if($vote_flag){
		echo "fail";
		exit;
	}else{
		$vote_obj = new Keke_witkey_vote_class ();
		$vote_obj->setTask_id ( $task_id );
		$vote_obj->setWork_id ( $work_id );
		$vote_obj->setUid ( $uid );
		$vote_obj->setUsername ( $username );
		$vote_obj->setVote_ip ( kekezu::get_ip () );
		$vote_obj->setVote_time ( time () );
		$vote_id = $vote_obj->create_keke_witkey_vote ();
		if ($vote_id) {
			db_factory::execute ( sprintf ( " update %switkey_task_work set vote_num=vote_num+1 where work_id ='%d'", TABLEPRE, $work_id ) );
			echo 1;
			exit;
		} else {
			exit;
		}
	}
	
}
if($type ==3){
	
	$task_info = db_factory::get_one ( sprintf ( "select * from %switkey_task where task_id=%d", TABLEPRE, $task_id) );
	
	if($task_info['uid'] != $uid){
		echo "fail";
		exit;
	}else{
		db_factory::execute ( sprintf ( " update %switkey_task set is_vote=1 where task_id ='%d'", TABLEPRE, $task_id ) );

		$work_list = db_factory::query ( sprintf ( "select * from %switkey_task_work where task_id=%d and vote_num > 0 order by vote_num desc limit 0,3", TABLEPRE, $task_id) );
		foreach($work_list as $key => $val){
			keke_finance_class::cash_in($val['uid'], floatval(0),intval($basic_config['baoming_credit']),'vote_win','','vote_win');
		}
		
		echo 1;
		exit;

	}
	
}

if($type == 4){
	
	if($user_info['send_flower'] > 0){
		echo "sended";
		exit;
	}else if($user_info['credit'] < $basic_config['flower_credit']){
		echo "fail";
		exit;
	}else if(!$user_info['pid']){
		echo "no";
		exit;
	}else{
		$res = db_factory::execute ( sprintf ( " update %switkey_space set flower=flower+1 where uid ='%d'", TABLEPRE, $user_info['pid'] ) );
		db_factory::execute ( sprintf ( " update %switkey_space set send_flower=1 where uid ='%d'", TABLEPRE, $uid ) );
		
		if($res) {
			
			keke_finance_class::cash_out ($uid, $basic_config['flower_credit'], 'send_flower','','','',1); 
			
			echo 1;
			exit;
		}else{
			echo 0;
			exit;
		}
	}
	
}

if($type == 'index'){
	
	
		$res = db_factory::execute ( " update ".TABLEPRE."witkey_space set index_page='".$mtype."' where uid =".$uid);
		
		if($res) {
			echo 1;
			exit;
		}else{
			echo 0;
			exit;
		}
	
}

if($type == 'good'){
	
	
		$res = db_factory::execute ( " update ".TABLEPRE."forum_post set good=good+1 where pid =".$pid);
		
		if($res) {
			echo 1;
			exit;
		}else{
			echo 0;
			exit;
		}
	
}

if($type == 'zan' || $type == 'ding'){
	
	
		$res = db_factory::execute ( " update ".TABLEPRE."forum_thread set ".$type."=".$type."+1 where tid =".$tid);
		
		if($res) {
			echo 1;
			exit;
		}else{
			echo 0;
			exit;
		}
	
}









