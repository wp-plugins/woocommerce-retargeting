<?php
/*
* Save Order
*/

if(isset($order) && is_array($order)) :
?>
<script>
	var _ra = _ra || {};
	_ra.saveOrderInfo = {
		"order_no": <?php echo esc_html( $order['order_number'] ); ?>,
		"lastname": "<?php echo esc_html( $order['buyer']['last_name'] ); ?>",
		"firstname": "<?php echo esc_html( $order['buyer']['first_name'] ); ?>",
		"email": "<?php echo esc_html( $order['buyer']['email'] ); ?>",
		"phone": "<?php echo esc_html( $order['buyer']['phone'] ); ?>",
		"state": <?php echo (empty($order['buyer']['state'] )) ? "false" : '"' .$order['buyer']['state'] .'"' ; ?>,
		"city": "<?php echo esc_html( $order['buyer']['city'] ); ?>",
		"address": "<?php echo esc_html( $order['buyer']['address'] ); ?>",
		"discount_code": <?php echo (empty($order['buyer']['discount_code'])) ? "false" : '"' . $order['buyer']['discount_code'] . '"'; ?>,
		"discount": <?php echo (empty($order['buyer']['discount'])) ? "false" : '"' . $order['buyer']['discount'] . '"'; ?>,
		"shipping": <?php echo (empty($order['buyer']['shipping'])) ? "false" : '"' . $order['buyer']['shipping'] . '"'; ?>,
		"total": <?php echo esc_html( $order['buyer']['order_total'] ); ?>
	};
	_ra.saveOrderProducts = <?php echo json_encode($order['line_items']) ?>;
	
	if( _ra.ready !== undefined ){
		_ra.saveOrder(_ra.saveOrderInfo, _ra.saveOrderProducts);
	}

	</script>

<?php endif; ?>
