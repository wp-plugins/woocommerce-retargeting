<?php
/*
* Send Product
*/
if(isset($product) && is_array($product)):
?>

<script>
var _ra = _ra || {};
	_ra.sendProductInfo = {
		"id": <?php echo $product['pid']; ?>,
		"name": <?php echo '"' . $product['name'] . '"'; ?>,
		"url": <?php echo '"' . $product['url'] . '"'; ?>,
		"img": <?php echo '"' . $product['image_url'] . '"'; ?>,
		"price": <?php echo (empty($product['variation'][0]['display_price'])) ? $product['price'] : $product['variation'][0]['display_price'];?>,
		"promo": <?php echo ($product['special_price']) ? $product['special_price'] : "0"; ?>,
		"stock": <?php echo $product['stock']; ?>,
		"brand": false,
		"category": {
			"id": <?php echo $product['catid']; ?>,
			"name": "<?php echo $product['cat']; ?>",
			"parent": <?php echo $product['catparent']; ?>
		},
		"category_breadcrumb": []
	};
	
	if (_ra.ready !== undefined) {
		_ra.sendProduct(_ra.sendProductInfo);
	}

	//click image

	_ra.clickImageInfo = {
		"product_id" : <?php echo $product['pid']; ?>
	};

	document.addEventListener('DOMContentLoaded', _ra_mouse_over, false);
		function _ra_mouse_over(){
			if ( document.getElementsByClassName("attachment-shop_single").length > 0 ) {
		        	document.getElementsByClassName("attachment-shop_single")[0].onmouseover  = function(){console.log("Click");};
		}
	}

	if (_ra.ready !== undefined) {
		_ra.clickImage(_ra.clickImageInfo.product_id);
	}

</script>
<?php endif; ?>