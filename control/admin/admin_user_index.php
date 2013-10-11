<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 11 );
if($op == 'vip'){
	if($is_submit){
		db_factory::execute ( " delete from  ".TABLEPRE."witkey_member_index where type in ('vip1','vip2','vip3','vip4')");
		
		if($user1){
			foreach($user1 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=vip", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('vip1',".$user_flag['uid'].",'".$val."')");
			}
		}
		if($user2){
			foreach($user2 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=vip", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('vip2',".$user_flag['uid'].",'".$val."')");
			}
		}
		if($user3){
			foreach($user3 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=vip", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('vip3',".$user_flag['uid'].",'".$val."')");
			}
		}
		if($user4){
			foreach($user4 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=vip", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('vip4',".$user_flag['uid'].",'".$val."')");
			}
		}
	}else{
		$user_list = db_factory::query(" select * from ".TABLEPRE."witkey_member_index where type in ('vip1','vip2','vip3','vip4') order by id asc");
		$user1 = array();
		$user2 = array();
		$user3 = array();
		$user4 = array();
		$i = 0;
		$j = 0;
		$k = 0;
		$m = 0;
		foreach($user_list as $key => $val){
			if($val['type'] == 'vip1') {
				$user1[$i] = $val['username'];
				$i++;
			}
			if($val['type'] == 'vip2') {
				$user2[$j] = $val['username'];
				$j++;
			}
			if($val['type'] == 'vip3') {
				$user3[$k] = $val['username'];
				$k++;
			}
			if($val['type'] == 'vip4') {
				$user4[$m] = $val['username'];
				$m++;
			}
		}
	}
	
}
if($op == 'find'){
	if($is_submit){
		db_factory::execute ( " delete from  ".TABLEPRE."witkey_member_index where type in ('s1','s2','s3','s4','s5')");
		
		if($user1){
			foreach($user1 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=find", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('s1',".$user_flag['uid'].",'".$val."')");
			}
		}
		if($user2){
			foreach($user2 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=find", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('s2',".$user_flag['uid'].",'".$val."')");
			}
		}
		if($user3){
			foreach($user3 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=find", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('s3',".$user_flag['uid'].",'".$val."')");
			}
		}
		if($user4){
			foreach($user4 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=find", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('s4',".$user_flag['uid'].",'".$val."')");
			}
		}
		if($user5){
			foreach($user5 as $key => $val){
				if(!$val) continue;
				$user_flag = db_factory::get_one(" select * from ".TABLEPRE."witkey_member where username = '".$val."'");
				if(!$user_flag)	kekezu::admin_show_msg ( "用户".$val."不存在，请重新添加！", "index.php?do=user&view=index&op=find", 3, "用户".$val."不存在，请重新添加！", 'warning' );
				db_factory::execute ( " insert into  ".TABLEPRE."witkey_member_index(type,uid,username) value ('s5',".$user_flag['uid'].",'".$val."')");
			}
		}
	}else{
		$user_list = db_factory::query(" select * from ".TABLEPRE."witkey_member_index where type in ('s1','s2','s3','s4','s5') order by id asc");
		$user1 = array();
		$user2 = array();
		$user3 = array();
		$user4 = array();
		$user5 = array();
		$i = 0;
		$j = 0;
		$k = 0;
		$m = 0;
		$n = 0;
		foreach($user_list as $key => $val){
			if($val['type'] == 's1') {
				$user1[$i] = $val['username'];
				$i++;
			}
			if($val['type'] == 's2') {
				$user2[$j] = $val['username'];
				$j++;
			}
			if($val['type'] == 's3') {
				$user3[$k] = $val['username'];
				$k++;
			}
			if($val['type'] == 's4') {
				$user4[$m] = $val['username'];
				$m++;
			}
			if($val['type'] == 's5') {
				$user5[$n] = $val['username'];
				$n++;
			}
		}
	}
	
}


require keke_tpl_class::template ( 'control/admin/tpl/admin_user_index_'.$op );