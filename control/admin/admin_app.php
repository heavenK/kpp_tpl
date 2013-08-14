<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$views = array("app_center","prom_union","task_list","task_edit","service_list","service_edit","message_list","weibo_list","denounce_list");
$view = (! empty ( $view ) && in_array ( $view, $views )) ? $view : 'app_center';
require "admin_app_$view.php";
