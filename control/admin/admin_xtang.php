<?php defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );


$views = array ('list', 'edit','cat_list','cat_edit','zsd_list','zsd_edit','zsd_question','question_list','question_edit');
(! empty ( $view ) && in_array ( $view, $views )) or $view = 'cat_list';



if (file_exists ( ADMIN_ROOT . 'admin_xtang_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_xtang_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'',3,'','warning' );
}
