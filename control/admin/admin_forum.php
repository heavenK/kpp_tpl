<?php defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );


$views = array ('cat_list','cat_edit','thread_list','thread_edit','keywords');
(! empty ( $view ) && in_array ( $view, $views )) or $view = 'cat_list';



if (file_exists ( ADMIN_ROOT . 'admin_forum_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_forum_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'',3,'','warning' );
}
