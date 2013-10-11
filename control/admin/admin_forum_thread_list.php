<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );


if(!isset($type)) $type = 'thread';
if(!isset($op)) $op = 'list';

$url = "index.php?do=forum&view=thread_list&op=$op&page_size=$page_size&page=$page&type=$type";

if($ac == 'del'){
	if($tid)	$res = db_factory::execute(" delete from ".TABLEPRE."forum_thread where tid=".$tid);
	if($pid)	$res = db_factory::execute(" delete from ".TABLEPRE."forum_post where pid=".$pid);
	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
}

if($ac == 'pass' || $ac == 'nopass'){
	if($ac == 'pass') $status = 2;
	else $status = 0;
	
	if($tid)	$res = db_factory::execute(" update ".TABLEPRE."forum_thread set status=".$status." where tid=".$tid);
	if($pid)	$res = db_factory::execute(" update ".TABLEPRE."forum_post set status=".$status." where pid=".$pid);
	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
}

if(strstr('chtr',$ac)){
	$res = db_factory::get_one(" select * from ".TABLEPRE."forum_thread where tid=".$tid);
	
	if(strstr($res['flag'],$ac)){
		$res['flag'] = str_replace($ac,'',$res['flag']);	
	}else{
		$res['flag'] .= $ac;
	}
	$res = db_factory::execute(" update ".TABLEPRE."forum_thread set flag='".$res['flag']."' where tid=".$tid);
	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
}


$res = db_factory::query ( sprintf ( "select id,type_name from %sforum_type ", TABLEPRE ) );
$type_arr = array();
foreach($res as $key => $val){
	$type_arr[$val['id']] = $val['type_name'];
}
if($type == 'thread'){
	
	$where = "where isShow=1";
	
	if($type_id) $where .= ' and type_id='.$type_id;
	$title and $where .= " and title like '%" . $title . "%'";
	if($op == 'check') $where .= ' and status=1';
	else	 $where .= ' and status=2';
	
	$count = db_factory::get_count (  "select count(tid) count from ".TABLEPRE."forum_thread ".$where);
	
	$page_size  and $page_size = intval ( $page_size ) or $page_size = 20;
	$page and $page = intval ( $page ) or $page = 1;
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
	
	$where .= " order by pub_time desc".$pages['where'];
	$thread_arr = db_factory::query ( "select * from ".TABLEPRE."forum_thread ".$where);
}else{

	$where = "where floor>1";
	
	if($type_id) $where .= ' and type_id='.$type_id;
	$title and $where .= " and title like '%" . $title . "%'";
	if($op == 'check') $where .= ' and status=1';
	else	 $where .= ' and status=2';
	
	$count = db_factory::get_count ( "select count(tid) count from ".TABLEPRE."forum_post ".$where );
	
	$page_size  and $page_size = intval ( $page_size ) or $page_size = 20;
	$page and $page = intval ( $page ) or $page = 1;
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
	
	$where .= " order by pub_time desc".$pages['where'];
	$thread_arr = db_factory::query ( "select * from ".TABLEPRE."forum_post ".$where );
}

function getTitle($tid){
	$res = db_factory::get_one ( sprintf ( "select * from %sforum_thread where tid=".$tid, TABLEPRE ) );
	return $res['title'];
}

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);
