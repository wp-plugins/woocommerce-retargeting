<?php
/**
 * Plugin Name: WooCommerce Retargeting
 * Plugin URI: https://retargeting.biz/woocommerce-documentation
 * Description: Adds Retargeting Tracking code to WooCommerce.
 * Version: 0.0.8
 * Author: Retargeting Team
 * Author URI: http://retargeting.biz
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    exit; 
}

/**
* Check if WooCommerce is active
**/

if(in_array('woocommerce/woocommerce.php', apply_filters(
'active_plugins', get_option('active_plugins') ) ) ){

class WC_Retargeting_Tracking {
	const PV = '1.0.0';
	const MIN_WP_VERSION = '3.5';
	const MIN_WC_VERSION = '2.3.0';
	const IN_STOCK = 1;
	const OO_STOCL = 0;

	private static $instance = null;
	protected $plugin_dir = '';
	protected $plugin_url = '';
	protected $plugin_name = '';

	protected static $product_type = array(
		'simple',
		'variable',
		'grouped'
	);

	/*
	*get working instance of the plugin
	*/
	public static function get_instance(){
		if(null === self::$instance){
			self::$instance = new WC_Retargeting_Tracking();
		}

		return self::$instance;
	}

	/*
	* Constructor. Plugin uses singleton pattern
	*/
	private function __construct(){
		$this->plugin_dir = plugin_dir_path(__FILE__);
		$this->plugin_url = plugin_dir_url(__FILE__);
		$this->plugin_name = plugin_basename(__FILE__);
	}

	/*
	*init plugin
	*/
	public function init() {
		if(is_admin()){
			$this->init_admin();
		}else{
			$this->init_frontend();
		}
	}

	/*
	* Get Plugin Name
	*/
	public function get_plugin_name(){
		return $this->plugin_name;
	}

	/*
	* Tag product
	*/
	public function tag_product(){
		if(is_product()){
			global $product;
			if($product instanceof WC_Product && $product->is_type(self::$product_type)){
				$data = array();
				$product_id = (int)$product->id;
				$data['url'] = get_permalink();
				$data['pid'] = $product_id;
				$data['name'] = $product->get_title();
				if($product->is_type('variable') ){
				    $data['variation'] = $product->get_available_variations();
				} else {
				    $data['variation'] = '';
				}
				$image_url = wp_get_attachment_url(get_post_thumbnail_id());
				if(!empty($image_url)){
					$data['image_url'] = (string)$image_url;
				}
				$data['price'] = $product->get_regular_price();
				$data['special_price'] = $product->get_sale_price();
				$data['stock'] = $product->is_in_stock() ? self::IN_STOCK : self::OO_STOCK;

				$categories = get_the_terms($_product->id, 'product_cat');
				if($categories){
					foreach ($categories as $category){
						$data['catid'] = $category->term_id;	
						$data['cat'] = $category->name;
						$data['catparent'] = $category->parent;
					}
				}
				if(!empty($data)) {
					$this->render('send-product', array('product' => $data));
				}
			}
		}
	}

	/*
	* Tag Category
	*/
	public function tag_category() {
		if( is_product_category() ){
		$categories = get_the_terms($_product->id, 'product_cat');
		if($categories){
		    foreach ($categories as $category){
			$data['catid'] = $category->term_id;
			$data['cat'] = $category->name;
			$data['catparent'] = $category->parent;
		    }
		}
		
		if(!empty($data)){
		    $this->render('send-category', array('category' => $data));
		}
	}
	}

	/*
	* Set Email
	*/
	public function set_email() {
		if( ! is_admin() ){
			$email = array();
			$email['email'] = wp_get_current_user()->user_email;
			$email['firstname'] = wp_get_current_user()->user_firstname;
			$email['lastname'] = wp_get_current_user()->user_lastname;
			$email['city'] = $customerDetail->city;
			$email['phone'] = "false";
			if(!empty($email)) {
				$this->render('set-email', array('email' => $email));
			}
		}
	}

	/*
	* Tag cart
	*/
	public function tag_cart() {
		global $woocommerce;
		if($woocommerce->cart instanceof WC_Cart && 0 < count($woocommerce->cart->get_cart() )) {
			$cart_items = $woocommerce->cart->get_cart();
			$line_items = array();

			foreach ($cart_items as $cart_item) {
				if(isset($cart_item['data']) && $cart_item['data'] instanceof WC_Product) {
					$product = $cart_item['data'];
					$line_item = (int)$cart_item['product_id'];
					$line_items[] = $line_item;
				}
			}

			if(!empty($line_items)) {
				$this->render('cart-tagging', array('line_items' => $line_items));
			}
		}

	}

	/*
	* Tag order
	*/
	public function tag_order($order_id) {
		if (is_numeric($order_id) && 0 < $order_id) {
			$order = new WC_Order($order_id);
		//Get Used Coupons
    		$coupons_list = '';
    		if ($order->get_used_coupons()) {
        	    $coupons_count = count($order->get_used_coupons());
        	    $i = 1;
        	foreach ($order->get_used_coupons() as $coupon) {
            	    $coupons_list .= $coupon;
            	    if ($i < $coupons_count){
                	$coupons_list .= ', ';
            	    $i++;
            	    }
        	}
    		}

			$buyer = array(
				'first_name'		=>	$order->billing_first_name,
				'last_name'		=>	$order->billing_last_name,
				'email'			=>	$order->billing_email,
				'phone'			=>	$order->billing_phone,
				'state'			=>	$order->billing_state,
				'city'			=>	$order->billing_city,
				'address'		=>	$order->billing_address_1 . " " . $order->billing_address_2,
				'discount_code'		=>	$coupons_list,
				'order_total'		=>	$order->order_total,
				'shipping'		=>	$order->get_total_shipping,
				'discount'		=> 	$order->get_discount
			);

			$data = array(
				'order_number'		=>	$order->id,
				'buyer'			=>	$buyer,
				'line_items'		=>	array(),
			);

			foreach((array)$order->get_items() as $item_id => $item) {
			$_product  = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
			$item_meta = new WC_Order_Item_Meta( $item['item_meta'], $_product );
			if(apply_filters('woocommerce_order_item_visible', true, $item)){
			    $line_item = array(
				'id'	=> $item['product_id'],
				'name' => $item['name'],
				'price' => $item['line_subtotal'],
				'variation_code' => ($item['variation_id'] == 0) ? false : $item['variation_id']
			    );
			}
				$data['line_items'][] = $line_item;
			}
			$this->render('save-order', array('order' => $data));
		}
	}

	/*
	* Render
	*/
	public function render($template, $data = array()){
		if(is_array($data)) {
			extract($data);
		}
		$file = $template . '.php';
		require($this->plugin_dir . 'templates/' . $file);
	}
	/*
	* Load class
	*/
	public function load_class($class_name = '') {
		$file = 'class-' . strtolower(str_replace('_','-',$class_name)) . '.php';
		require_once($this->plugin_dir . 'classes/' . $file);
	}

	/*
	* Customer data
	*/

	protected function get_customer_data($user){
		$customer = array();

		if($user instanceof WP_User){
			$customer['first_name'] = ! empty($user->user_firstname) ? $user->user_firstname : '';

			if(!empty($user->user_lastname)) {
				$customer['last_name'] = $user->user_lastname;
			} elseif (!empty($user->user_login)) {
				$customer['last_name'] = $user->user_login;
			} else {
				$customer['last_name'] = '';
			}

			$customer['email'] = ! empty($user->user_email) ? $user->user_email : '';
		}

		return $customer;
	}

	/*
	* Build category Path
	*/

	protected function build_category_path($term){
		$category_path = '';

		if(is_object($term) && ! empty($term->term_id)){
			$terms = $this->get_parent_terms($term);
			$terms[] = $term;

			$term_names = array();

			foreach($terms as $term) {
				$term_names['name'] = $term->name;
			}

			if(!empty($term_names)){
				$category_path = implode(",", $term_names);
			}
		}

		return $category_path;
	}

	/*
	* Get parent Terms
	*/

	protected function get_parent_terms($term, $taxonomy = 'product_cat') {
		if(empty($term->parent)) {
			return array();
		}

		$parent = get_term($term->parent, $taxonomy);

		if(is_wp_error($parent)){
			return array();
		}

		$parents = array($parent);

		if($parent->parent && ($parent->parent !== $parent->term_id)) {
			$parents = array_merge($parents, $this->get_parent_terms($parent,$taxonomy));
		}

		return array_reverse($parents);
	}

	/*
	* Init Admin
	*/

	protected function init_admin() {
		$this->load_class('WC_Retargeting_Tracking');
		add_filter('woocommerce_integrations', array('WC_Integration_Retargeting_Tracking', 'add_integration'));

	}

	/*
	* Init Frontend
	*/
	protected function init_frontend() {

		add_action('woocommerce_before_single_product', array($this, 'tag_product'), 20, 0);
	    	add_action('woocommerce_before_main_content', array($this, 'tag_category'), 30,0);
        	add_action('woocommerce_thankyou', array($this, 'tag_order'));
        	add_action('woocommerce_after_cart',array($this, 'tag_cart'));
        	add_action('woocommerce_after_checkout_form',array($this, 'tag_cart'));
		add_action('wp_head', array($this, 'add_tracking_code'), 30,0);
		add_action('woocommerce_after_add_to_cart_button',array($this, 'add_to_cart'));
        	add_action('woocommerce_after_my_account', array($this, 'set_email'),10,0);
    		//Feed
    		add_action('do_feed_products', 'products_feed_rss2', 10,1);
	}

	/*
	* Add tracking code
	*/
	function add_tracking_code() {
		echo '<!-- Retargeting -->
		<script type="text/javascript">
                (function(){
                var ra = document.createElement("script"); ra.type ="text/javascript"; ra.async = true; ra.src = ("https:" ==
                document.location.protocol ? "https://" : "http://") + "retargeting-data.eu/" +
                document.location.hostname.replace("www.","") + "/ra.js"; var s =
                document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ra,s);})();
                </script>
        	<!-- Retargeting -->
		';
        }

	/*
	* Product Feed
	*/
	function products_feed_rss2() {
		$rss_template = get_template_directory() . '/product-feed.php';
		load_template($rss_template);
		add_action('init','my_add_product_feed');
	}

	/*
	* Rewrite Rules
	*/
	function my_rewrite_product_rules($wp_rewrite) {
		$new_rules = array(
			'feed/(.+)' => 'index.php?feed=' . $wp_rewrite->preg_index(1)
		);
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}

	/*
	* Add Rewrite Rule
	*/
	function my_add_product_feed() {
		global $wp_rewrite;
		add_action('generate_rewrite_rules', 'my_rewrite_product_rules');

		$wp_rewrite->flush_rules();
	}
/*
* Add to cart
*/
function add_to_cart(){
    if(!is_single()){
	return;
    }
    global $product, $woocommerce;
    
    echo "<script>
    jQuery('.single_add_to_cart_button').click(function(){
	_ra.addToCart(" . $product->id .",false,function(){console.log('cart')});	

    });

    </script>";
}
	/*
	* Check dependencies
	*/
    protected function check_dependencies(){
        global $wp_version;
        $title = sprintf(__('Retargeting module %s not compatible'), self::VERSION);
        $error = '';
        $args = array(
            'back_link' => true,
        );

        if(version_compare($wp_version, self::MIN_WP_VERSION, '<')){
            $error = sprintf(__('Looks like youre run an onlder version of wp %1$s to use Retargeting module you need %2$s.'),self::MIN_WP_VERSION,self::VERSION);
        }

        if(! defined ('WC_VERSION')) {
            $error = sprintf(__('Look like you r not running any version of woocommerce. you need to run woocommerce %1$s to use retargeting %2$s'), self::MIN_WC_VERSION,self::VERSION);
        } else if (version_compare(WC_VERSION, self::MIN_WC_VERSION, '<')){
            $error = sprintf(__('Look like youre using an older version of woocommerce. you need to use WooCommerce %1$s to use Retargeting tracker module %2$s', self::MIN_WC_VERSION,self::VERSION));
        }

        if(!empty($error)){
            deactivate_plugins($this->plugin_name);
            wp_die($error,$title,$args);
            return false;
        }
        return true;
    }
}

add_action('plugins_loaded', array(WC_Retargeting_Tracking::get_instance(),'init'));

} //End check
