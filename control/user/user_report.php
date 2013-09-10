<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


$views = array ("rights", "report",  "process" );
in_array ( $view, $views ) or $view = "rights";

if(!isset($show)) $show = 'send';
$action_arr    = keke_report_class::get_transrights_type();
$trans_status = keke_report_class::get_transrights_status(); 
$trans_object = keke_report_class::get_transrights_obj(); 
$page and $page=intval ( $page ) or $page = '1';
$page_size and $page_size=intval ( $page_size ) or $page_size = "10";
$url = "index.php?do=$do&view=$view&report_status=$report_status&obj=$obj&ord=$ord&page_size=$page_size&page=$page";


if ($ac) {
	switch ($ac) {
		case "del" :
			if ($report_id) {
				$res = db_factory::execute ( sprintf ( " delete from %switkey_report where report_id='%d'", TABLEPRE, $report_id ) );
				$res and kekezu::show_msg ( $_lang['record_delete_success'], $url, "3",'','success' ) or kekezu::admin_show_msg ($action_arr[$view]. $_lang['record_delete_fail'], $url, "3",'','warning');
			} else
				kekezu::show_msg ($_lang['choose_delete_operate'], $url, "3",'','warning' );
			break;
		case "download" :
			keke_file_class::file_down ( $filename, $filepath );
			break;
	}
} else {
	
	$report_obj = new Keke_witkey_report_class ();
	$page_obj = $kekezu->_page_obj;
	$where = " report_type = '" . $report_type . "'";
	
	if($show == 'send')	$where .= " and username='$username'";
	if($show == 'rec')	$where .= " and to_username='$username'";
	
	if($start_time || $end_time){
		if($start_time) $where .= " and on_time > ".strtotime($start_time);
		if($end_time) $where .= " and on_time < ".strtotime($end_time);
	}
	
	
	$report_id and $where .= " and report_id='$report_id'";
	$report_status and $where .= " and report_status='$report_status' ";
	$obj and $where .= " and obj='$obj' ";
	is_array($w['ord']) and $where .=' order by '.$ord['0'].' '.$ord['1']  or $where .= " order by report_id desc ";
	$report_obj->setWhere ( $where );
	$count = intval ( $report_obj->count_keke_witkey_report () );
	$page_obj->setAjax(1);
	$page_obj->setAjaxDom("ajax_dom");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
	$report_obj->setWhere ( $where . $pages ['where'] );;
	$report_list = $report_obj->query_keke_witkey_report ();
}
if($report_type == 1) $report_name = '投诉';
if($report_type == 2) $report_name = '举报';
require keke_tpl_class::template ( "user/" . $do . "_" . $op );
