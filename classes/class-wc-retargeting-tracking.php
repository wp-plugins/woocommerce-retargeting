<?php
class WC_Integration_Retargeting_Tracking extends WC_Integration {
	
	/*
	* Construct
	*/
	public function __construct(){
		$this->id = 'retargeting';
		$this->method_title = "Retargeting Tracking";
		$this->method_description = __('Implements the required...');

		$this->init_form_fields();
		$this->init_settings();

		add_action('woocommerce_update_options_integration_retargeting', array($this, 'process_admin_options'));
		add_filter('plugin_action_links', array($this, 'register_action_links'), 10, 2);
	}
	/*
	* Init admin form
	*/
	function init_form_fields(){
		$this->form_fields = array(
			'domain_api_key'=> array(
				'title'	=>	__('Domain API KEY'),
				'description' => __('Insert retargeting API Key'),
				'type'	=> 'text',
				'default'	=> '',
			),
			'discounts_api_key'=> array(
				'title'	=>	__('Discounts API KEY'),
				'description' => __('Insert retargeting API Key'),
				'type'	=> 'text',
				'default'	=> '',
			),
			);
	}

	/*
	* Add integration
	*/
	public static function add_integration($integrations = array()){
		$integrations[] = __CLASS__;
		return $integrations;
	}

	/*
	* Register action links
	*/
	public function register_action_links($links, $plugin_file){
		if($plugin_file === WC_Retargeting_Tracking::get_instance()->get_plugin_name()){
			$url = admin_url('admin.php?page=wc-settings&tab=integration&section=retargeting');
			$links[] = '<a href="'. esc_attr($url) . '">' . esc_html__('Settings') . '</a>';
		}
		return $links;
	}
}