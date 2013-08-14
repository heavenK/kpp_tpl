<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title = '任务推广' . '- ' . $_K ['html_title'];
$model_list = $kekezu->_model_list;
$page_obj = $kekezu->_page_obj;
$page_obj->setAjax ( '1' );
$page_obj->setAjaxDom ( 'ajax_list' );
$page or $page = 1;
$page_size or $page_size = 10;
$url = "index.php?do=prom&view=task&prom_code=bid_task";
$effect_mode_bid_task = keke_prom_class::get_prom_rule ( 'bid_task' );
$prom_rule = keke_prom_class::get_prom_rule ( 'bid_task' );
$task_config = unserialize($model_list[1][config]);
$table_title = array (
		$_lang ['belong_model'],
		$_lang ['task_title'],
		$_lang ['task_cash'],
		$_lang ['pro_cash'] 
);
$task_obj = new Keke_witkey_task_class ();
$where = " task_status='2' ";
$prom_rule ['model'] and $where .= " and model_id in (" . $prom_rule [model] . ") ";
$prom_rule ['indus_string'] and $where .= " and indus_id in (" . $prom_rule [indus_string] . ") ";
$where .= " order by start_time desc ";
$task_obj->setWhere ( $where );
$count = intval ( $task_obj->count_keke_witkey_task () );
$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
$task_obj->setWhere ( $where . $pages ['where'] );
$prom_list = $task_obj->query_keke_witkey_task ();
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" . $do . "_" . $view );