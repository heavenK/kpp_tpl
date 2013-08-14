<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
switch ($ajax) {
	case "prom_11" :
		$title = $_lang['get_promote_links'];;
		$lin = $_K [siteurl] . "/index.php?do=prom&u=" . $user_info ['uid'] . "&p=" . $prom_code;
		$o and $lin .= "&o=" . $o;
		$l and $lin .= "&l=" . $l;
		$promtext or $promtext = $_K ['html_title'];
		break;
	case "prom_list" :
		$model_list = $kekezu->_model_list;
		$page_obj = $kekezu->_page_obj;
		$page_obj->setAjax ( '1' );
		$page_obj->setAjaxDom ( 'ajax_list' );
		$page or $page = 1;
		$page_size or $page_size = 10;
		$url = "index.php?do=ajax&view=prom&ajax=prom_list&prom_code=$prom_code";
		$prom_rule = keke_prom_class::get_prom_rule ( $prom_code );
		switch ($prom_code) {
			case "bid_task" :
				$table_title = array ($_lang['belong_model'], $_lang['task_title'], $_lang['task_cash'], $_lang['pro_cash'] );
				$task_obj = new Keke_witkey_task_class ();
				$where = " task_status='2' ";
				$prom_rule ['model'] and $where .= " and model_id in (" . $prom_rule [model] . ") ";
				$prom_rule ['indus_string'] and $where .= " and indus_id in (" . $prom_rule [indus_string] . ") ";
				$task_obj->setWhere ( $where );
				$count = intval ( $task_obj->count_keke_witkey_task () );
				$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
				$task_obj->setWhere ( $where . $pages ['where'] );
				$prom_list = $task_obj->query_keke_witkey_task ();
				break;
			case "service" :
				$table_title = array ($_lang['belong_model'], $_lang['goods_title'], $_lang['goods_cash'], $_lang['pro_cash'] );
				$ser_obj = new Keke_witkey_service_class ();
				$where = " service_status='2' ";
				$prom_rule ['model'] and $where .= " and model_id in (" . $prom_rule [model] . ") ";
				$prom_rule ['indus_string'] and $where .= " and indus_id in (" . $prom_rule [indus_string] . ") ";
				$ser_obj->setWhere ( $where );
				$count = intval ( $ser_obj->count_keke_witkey_service () );
				$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
				$ser_obj->setWhere ( $where . $pages ['where'] );
				$prom_list = $ser_obj->query_keke_witkey_service ();
				break;
		}
		break;
}
require keke_tpl_class::template ( "ajax/ajax_" . $view );
