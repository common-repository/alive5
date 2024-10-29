<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://alive5.com
 * @since      1.0.0
 *
 * @package    Alive5
 * @subpackage Alive5/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Alive5
 * @subpackage Alive5/public
 * @author     Alive5 <kashif.manzoor@unstoppable.io>
 */
class Alive5_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Alive5_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Alive5_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/alive5-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Alive5_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Alive5_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/alive5-public.js', array( 'jquery' ), $this->version, false );

	}

	public function render_a5_widget() {
		$html = '';
		if (true == $widgets = get_option('a5_widgets')) {
			if (true == $widget = get_option($widgets['selected_widget'])) {
				$options = get_option('a5_settings');
				$jwt = get_option('a5_jwt');
				$url = 'https://api.alive5.com/1.0/widget-code/get-by-id?org_name='.$options['a5_org_name'].'&widget_id='.$widget['widget_id'];
				$args = array( 'headers' => array('authorization' => $jwt));
				$request = wp_remote_get("$url", $args);
				
				if( is_wp_error( $request ) ) {
					return false;
				}
				$body = wp_remote_retrieve_body($request);
				$data = json_decode($body);
				$search_vars = array('#widget_code_id#','#org#','#widget_template_id#','#is_alive5_phone_number#','#sms_phone_number#');
				$replace_vars = array($data->data->id, $data->data->org_name, $widget['widget_id'], $data->data->is_alive5_phone_number, $data->data->sms_phone_number);
				$html .= str_replace($search_vars, $replace_vars, $data->data->code_template);
			}
		}
		echo $html, "\n";
	}

}
