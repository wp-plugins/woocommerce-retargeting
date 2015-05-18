<?php
/*
* Send Product
*/
if(isset($product) && is_array($product)):
?>

<script>
function _ra_helper_addLoadEvent(func) {
var oldonload = window.onload;
if (typeof window.onload != 'function') {
window.onload = func;
} else {
window.onload = function() {
if (oldonload) {
oldonload();
}
func();
}
}
}

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

// clickImage

function _ra_triggerClickImage() {
	if(typeof _ra.clickImage !== "undefined") _ra.clickImage("<?php echo $product['pid']; ?>");
}
_ra_helper_addLoadEvent(function(){
	if(document.getElementsByClassName("attachment-shop_single").length > 0){
		document.getElementsByClassName("attachment-shop_single")[0].onmouseover = _ra_triggerClickImage;
	}
	
		if(document.getElementsByClassName("product-gallery-slider").length > 0){
		document.getElementsByClassName("product-gallery-slider")[0].onmouseover = _ra_triggerClickImage;
	}

});

// setVariation

</script>
<?php endif; ?>
