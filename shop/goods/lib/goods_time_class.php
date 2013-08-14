<?php
final class goods_time_class extends time_base_class {
	function __construct() {
		parent::__construct ();
	}
	function validtaskstatus() {
		$this->ys_end_time ();
	}
	public function ys_end_time() {
		$order_list = db_factory::query ( sprintf ( " select * from %switkey_order where order_status='ok' and model_id=6 and ys_end_time<%d", TABLEPRE, time () ) );
		if (is_array ( $order_list )) {
			foreach ( $order_list as $k => $v ) {
					$obj = new goods_shop_class();
		            $obj->dispose_order ( $v['order_id'], 'confirm' );
			}
		}
	}
}