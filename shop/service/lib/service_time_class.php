<?php
final class service_time_class extends time_base_class {
	function __construct() {
		parent::__construct ();
	}
	function validtaskstatus() {
		$this->ys_end_time ();
		$this->service_end_time ();
	}
	public function ys_end_time() {
		$order_list = db_factory::query ( sprintf ( " select * from %switkey_order where order_status='confirm_complete' and model_id=7 and ys_end_time<%d", TABLEPRE, time () ) );
		
		if (is_array ( $order_list )) {
			foreach ( $order_list as $k => $v ) {
					$obj = new service_shop_class ();
		            $obj->dispose_order ( $v['order_id'], 'complete' );
			}
		}
	}
	public function service_end_time() {
		$service_list = db_factory::query ( sprintf ( " select * from %switkey_service where service_status=2 and  exist_time < %d and model_id = 7 ", TABLEPRE, time () ) );
		if (is_array ( $service_list )) {
			foreach ( $service_list as $k => $v ) {
				db_factory::execute(sprintf("update %switkey_service set service_status=1 where service_id=%d",TABLEPRE,$v['service_id']));
			}
		}
	}
}