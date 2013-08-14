<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title='广场首页'.'- '.$_K['html_title'];
$url = "index.php?do=square&view=index&t=$t";
$ext_types   = kekezu::get_ext_type (); 
$ext = '*.jpg;*.jpeg;*.gif;*.png;*.bmp';
$op_desc = array('pub'=>'发布','leave'=>'留言','work'=>'投稿','focus'=>'收藏','buy'=>'购买');
if(!$t||!in_array($t,array('all','free_task','task','free_service','service'))){
	$t='all';
}
if (isset ( $formhash ) && kekezu::submitcheck ( $formhash )) {
	!$uid and kekezu::show_msg ( $_lang ['operate_notice'], $url, 1,'未登录，请您先登录！', "alert_error" );
	$title = kekezu::escape($txt_title);
	$content = kekezu::escape($tar_content);
	$res = kekezu::check_session($pub_type,3,3);
	$res==false and kekezu::show_msg ( $_lang ['operate_notice'], $url, 1,'操作过于频繁，发布失败,五分钟后再试！', "alert_error" );
	if($pub_type=='free_task'){
		$detail_type='免费任务';
		$free_task_obj = new Keke_witkey_free_task_class();
		$free_task_obj->setTask_title($title);
		$free_task_obj->setTask_desc($content);
		$free_task_obj->setUid($uid);
		$free_task_obj->setUsername($username);
		$free_task_obj->setOn_time(time());
		$free_task_obj->setTask_file($file_path_2);
		$free_task_obj->setTask_pic($file_ids);
		$res = $free_task_obj->create_keke_witkey_free_task();
		$jump_url = "index.php?do=square&view=task_detail&task_id=$res";
	}elseif($pub_type=='free_service'){
		$detail_type='免费服务/商品';
		$free_service_obj = new Keke_witkey_free_service_class();
		$free_service_obj->setService_title($title);
		$free_service_obj->setService_desc($content);
		$free_service_obj->setUid($uid);
		$free_service_obj->setUsername($username);
		$free_service_obj->setService_file($file_path_2);
		$free_service_obj->setService_pic($file_ids);
		$free_service_obj->setOn_time(time());
		$res = $free_service_obj->create_keke_witkey_free_service();
		$jump_url = "index.php?do=square&view=goods_detail&service_id=$res";
	}
	if($res){
		kekezu::save_weibo_data($res,$pub_type,$pub_type,$title,$uid,$username,0,0,0,'pub',0,$uid,$username);
	    save_file($res,$pub_type,$file_path_2,$file_ids);
		kekezu::show_msg ( $_lang ['operate_notice'], $url, 1, '发布成功！', "alert_right" );
	}else{
		kekezu::show_msg ( $_lang ['operate_notice'], $url, 1,'发布失败！', "alert_error" );
	}
}else{
	if(isset($t)&&$t!='all'){
		$w = " and obj_type = '$t' ";
	}
	if($op=='get_data'){
		$last_id = intval($last_id);
		$last_info = db_factory::get_one(sprintf("select on_time from %switkey_weibo where weibo_id = %d",TABLEPRE,$last_id));
		$update_data = kekezu::get_table_data('*','witkey_weibo',"on_time>".($last_info[on_time]).$w,'on_time desc','','','weibo_id','');
		if($update_data){
			$data_count = count($update_data);
		    $up_str = implode(',',array_keys($update_data));
 			kekezu::echojson($data_count,1,$up_str);
		}
	}
	$where = " where  1 = 1 ";
	$pagesize or $pagesize = 20;
	$page or $page = 1;
	$sql = sprintf ( "select * from %switkey_weibo where 1=1 ".$w." order by on_time desc ", TABLEPRE);
	$count_sql = sprintf ( "select count(weibo_id) from %switkey_weibo where 1=1 ".$w." order by on_time desc ", TABLEPRE);
	$count = db_factory::get_count ( $count_sql  );
	$pages = $kekezu->_page_obj->getPages ( $count, $pagesize, $page, $url );
	$weibo_list = db_factory::query ( $sql  . $pages ['where'] );
    $weibo_all_list = db_factory::query(sprintf("select * from %switkey_weibo where 1=1 ".$w." order by on_time desc limit 0,50",TABLEPRE));
    $weibo_last_id = $weibo_all_list[0]['weibo_id'];
}
function save_file($obj_id,$obj_type,$file,$pic) {
	global $uid,$username;
	if ($file||$pic) {
		$file_obj = new Keke_witkey_file_class ();
		$file_arr = array_filter ( explode ( ',', $file ) );
		$pic_arr = array_filter( explode ( ',', $pic ));
		$arr_str = array_merge($file_arr,$pic_arr);
		foreach ( $arr_str as $k=>$v ) {
			$file_obj->setFile_id ( $v );
			$file_obj->setObj_type($obj_type);
			$file_obj->setObj_id( $obj_id );
			$file_obj->edit_keke_witkey_file ();
		}
	}
}
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" .$do."_". $view );