<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 1 );
$config_basic_obj = new Keke_witkey_basic_config_class ();
$config_basic_arr = $config_basic_obj->query_keke_witkey_basic_config ();

$op = 'credit';

$lang_arr = keke_lang_class::lang_type();
foreach ( $config_basic_arr as $k => $v ) {
	$config_arr [$v ['k']] = $v ['v'];
}



isset ( $op ) and $url = "index.php?do=payitem&view=credit&op=$op" or $url = "index.php?do=payitem&view=credit&op=credit";

if (isset ( $_POST ) && ! empty ( $_POST )) {
	foreach ( $_POST as $k => $v ) {
		$config_basic_obj->setWhere ( "k = '$k'" );
		$config_basic_obj->setV (kekezu::k_input($v));
		$res += $config_basic_obj->edit_keke_witkey_basic_config ();
	}
	kekezu::admin_system_log ( $_lang ['update'] . $log_nav_arr [$op] );
	$kekezu->_cache_obj->set ( "keke_witkey_basic_config", $config_basic_arr );
	kekezu::admin_show_msg ( "提交成功", $url, 3, '', 'success' );
}

require $template_obj->template ( 'control/admin/tpl/admin_payitem_' . $view );