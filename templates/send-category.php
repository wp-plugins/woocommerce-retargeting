<?php 
/*
* Send Category
*/

if ( isset( $category ) ): 

?>


<script>
var _ra = _ra || {};
	_ra.sendCategoryInfo = {
		"id": <?php echo $category['catid']; ?>,
		"name" : "<?php echo $category['cat']; ?>",
		"parent": <?php echo $category['catparent']; ?>,
		"category_breadcrumb": []
	}
	
	if (_ra.ready !== undefined) {
		_ra.sendCategory(_ra.sendCategoryInfo);
	}
</script>
<?php endif; ?>