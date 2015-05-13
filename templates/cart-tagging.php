<?php
/**
* Add to cart
*/
global $woocommerce;
if ( isset( $line_items ) && is_array( $line_items ) ):
?>
<script>
	var _ra = _ra || {};
	_ra.checkoutIdsInfo = <?php echo json_encode($line_items)?>;
	
	if (_ra.ready !== undefined) {
		_ra.checkoutIds(_ra.checkoutIdsInfo);
	}
</script>
<?php endif; ?>
