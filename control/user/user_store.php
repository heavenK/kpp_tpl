<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$ops = array (
		'setting','case' );
in_array ( $op, $ops ) or $op = "setting";
$sub_nav = array (
		array (
				"setting" => array (
						'店铺管理',
						"browser" 
				)
		) 
)
;
require 'user_'.$view."_".$op.".php";
