<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$shows = array("list","add");
in_array($show,$shows) or $show="list";
$type = 'art';
if($show == 'add'){

	$art_obj = keke_table_class::get_instance ( "witkey_article" );
	

	$art_cat_arr = kekezu::get_table_data ( '*', "witkey_article_category", "cat_type = 'article'", " art_cat_id desc", '', '', 'art_cat_id', null );

	(isset ( $art_id ) and intval ( $art_id ) > 0) and $art_info = $art_obj->get_table_info ( 'art_id', $art_id );
	empty ( $art_info ) or extract ( $art_info );
	if ($sbt_edit) { 
		$fields ['pub_time'] = time ();
		$fields ['uid'] = $uid;
		if($type=='art'){
			$fields ['cat_type'] = 'article';
		}else{
			$fields ['cat_type'] = $type;
		}
		isset($fields['is_recommend']) or $fields['is_recommend']=0;
		$url = "index.php?do=$do&view=$view&op=$op&show=list";
		if($_FILES['art_pic']['name']){
				$art_pic = keke_file_class::upload_file('art_pic');
				$art_pic&&$fields['art_pic']= $art_pic;
		}
		$fields=kekezu::escape($fields);
		$res = $art_obj->save ( $fields, $pk );
		$log_ac = array('edit'=>$_lang['edit_art'],'add'=>$_lang['add_art']);
		if($pk['art_id']){
			kekezu::admin_system_log($log_ac['edit'].":".$fields['art_title']) ;
		}else{
			kekezu::admin_system_log($log_ac['add'].":".$fields['art_title']) ;
		} 
		if($res){
			
			$rs = db_factory::get_one(sprintf ( "select * from %switkey_space_ext where uid = '%d' and k='first_news'", TABLEPRE, $uid ));
				if(!$rs['v']){
					keke_finance_class::cash_in($uid, floatval(0),intval($basic_config['first_news']),'first_news','','first_news');
					$msg .= "，第一次发布案例，豆8网赠送您".$basic_config['first_news']."豆币！";
					
					if($rs)	db_factory::execute ( sprintf ( "update %switkey_space_ext set v=%d where uid = %d and k='first_news'", TABLEPRE, 1, $uid ) );
					else db_factory::execute ( sprintf ( "insert into %switkey_space_ext value(%d, 'first_news', %d, %d)", TABLEPRE, $uid, 1, time() ) );
					
					//$conf['on_time'] = time();
					//$msg_credit = "恭喜您已成功开通工作室，完善详细资料，豆8网将赠送您".$basic_config['shop_open_credit']."豆币！";
				}
			
			
			kekezu::show_msg($_lang['operate_success'].$msg,$url,3,'','success');
		}else{
			kekezu::show_msg($_lang['operate_fail'],$url,3,'','warning');
		}
	}
	if(isset($ac)&&$ac=='del'){
		if($filepath){
			$pk and db_factory::execute(" update ".TABLEPRE."witkey_article set art_pic ='' where art_id = ".intval($pk));
			$file_info = db_factory::get_one(" select * from ".TABLEPRE."witkey_file where save_name = '.$filepath.'");
			keke_file_class::del_att_file($file_info['file_id'], $file_info['save_name']);
			kekezu::echojson ( '', '1' );
		}
	}
	$cat_arr = array ();
	kekezu::get_tree ( $art_cat_arr, $cat_arr, 'option', $art_id, 'art_cat_id', 'art_cat_pid', 'cat_name' );
	



}else{
	
	
	$url = "index.php?do=$do&view=$view&username=$username&art_title=$art_title&page_size=$page_size&page=$page&type=$type";
	$art_obj = new Keke_witkey_article_class ();
	$table_obj = new keke_table_class ( "witkey_article" );
	
	if ($ac == 'del') {
		$res = $table_obj->del ( 'art_id', $art_id, $url );
		$res and kekezu::show_msg ( $_lang['operate_success'], $url,3,'','success' ) or kekezu::show_msg ( $_lang['operate_fail'], $url,3,'','warning' );
	}else {
		$where = ' 1 = 1 ';

		$where .= " and cat_type = 'article' ";

		$page_size  and $page_size = intval ( $page_size ) or $page_size = 10;
		$page and $page = intval ( $page ) or $page = 1;
		
		strval ( $art_title ) and $where .= " and art_title like '%$art_title%'";
		if($start_time || $end_time){
			if($start_time) $where .= " and pub_time > ".strtotime($start_time);
			if($end_time) $where .= " and pub_time < ".strtotime($end_time);
		}
		
		
		$where .= " and art_cat_id = 368";
		
		$where .= " and uid = $uid ";
		$ord[0]&&$ord[1] and $where .=' order by '.$ord[0].' '.$ord[1] or $where.=" order by art_id desc ";
		$r = $table_obj->get_grid ( $where, $url, $page, $page_size,null,1,'ajax_dom');
		$art_arr = $r [data];
		$pages = $r [pages];
	}
	
}



function get_fid($path){
	if(!path){
		return false;
	}
	$querystring = substr(strstr($path, '?'), 1);
	parse_str($querystring, $query);
	return $query['fid'];
}
require keke_tpl_class::template ("user/" . $do ."_".$view. "_" . $op );