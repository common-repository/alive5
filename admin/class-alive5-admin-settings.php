<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://alive5.com
 * @since      1.0.0
 *
 * @package    Alive5
 * @subpackage Alive5/admin
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Alive5
 * @subpackage Alive5/admin
 * @author     Alive5 <kashif.manzoor@unstoppable.io>
 */
class Alive5_Admin_Settings {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    public function alive5_defaults() {
        $defaults = array(
            'a5_auth_url'   =>  'https://api.alive5.com/1.0/authuser',
            'a5_widgets_url'    =>  'https://api.alive5.com/1.0/widget-code/get-all'
        );

        return $defaults;
    }

    public function alive5_page() {
        add_menu_page(
            'Alive5',
            'Alive5',
            'manage_options',
            'alive5_page',
            array($this, 'render_alive5_page'),
            plugin_dir_url(__FILE__) . 'images/logo-small.png'
        );
    }

    public function render_alive5_page( $active_tab = '' ) {
        ?>
        <div class="wrap">
            <a href="http://alive5.com" target="_blank"><img id="a5logo" src="<?php echo plugin_dir_url(__FILE__); ?>images/logo.svg" /></a>
            <?php settings_errors(); ?>
            <?php if (isset($_GET['tab'])) {
                $active_tab = $_GET['tab'];
            } elseif ( $active_tab == 'a5_settings' ) {
                $active_tab = 'a5_settings';
            } else {
                $active_tab = 'a5_widgets';
            }
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=alive5_page&tab=a5_widgets" class="nav-tab <?php echo $active_tab == 'a5_widgets' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Widgets', 'alive5' ); ?></a> 
                <a href="?page=alive5_page&tab=a5_settings" class="nav-tab <?php echo $active_tab == 'a5_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'alive5' ); ?></a> 
            </h2>
            <form method="post" action="options.php">
                <?php
                if ($active_tab == 'a5_widgets') {
                    settings_fields('a5_widgets');
                    do_settings_sections('a5_widgets');
                    submit_button("Use Widget");
                } else {
                    settings_fields('a5_settings');
                    do_settings_sections('a5_settings');
                    submit_button("Next");
                }
                ?>
            </form>
        </div>
        <?php
    }
    ///////////////////// Alive 5 Settings Tab /////////////////////
    public function initialize_a5_settings() {

        add_settings_section(
            'a5_settings_section',
            __('Enter Your Alive5 Account Details:', 'a5_settings'),
            array($this, 'a5_settings_section_callback'),
            'a5_settings'
        );

        register_setting(
            'a5_settings',
            'a5_settings'
        );
    }
    // Call back functions for a5_settings
    public function a5_settings_section_callback() {
        if (false == $a5_settings = get_option('a5_settings')) {
            $a5_settings = array('a5_org_name' => '', 'a5_username' => '', 'a5_password' => '');
        }
        echo '<input id= "a5_org_name" name="a5_settings[a5_org_name]" type="text" placeholder="Organization" value="'.$a5_settings['a5_org_name'].'" />';
        echo '<input id= "a5_username" name="a5_settings[a5_username]" type="text" placeholder="Email" value="'.$a5_settings['a5_username'].'" />';
        echo '<input id= "a5_password" name="a5_settings[a5_password]" type="password" placeholder="Password" value="'.$a5_settings['a5_password'].'" />';
        // $this->a5_auth();
    }

    ///////////////////// End Alive 5 Settings Tab /////////////////////

    ///////////////////// Alive 5 Widgets Tab /////////////////////
    public function initialize_a5_widgets() {
        
        add_settings_section(
            'a5_widgets_section',
            __('Widgets', 'a5_widgets'),
            array($this, 'a5_widgets_section_callback'),
            'a5_widgets'
        );

        register_setting(
            'a5_widgets',
            'a5_widgets'
        );
    }
    // Callback function for a5_widgets
    public function a5_widgets_section_callback() {
        if (false == get_option('a5_settings')) {
            echo '<h2 id="a5_error">Please configure plugin using Settings tab.</h2>';
            wp_die();
        }

        if (true == $this->a5_auth()) {
            if (true == $a5_auth_creds = get_option('a5_settings')) {
                $defaults = $this->alive5_defaults();
                $a5_widgets_url = $defaults['a5_widgets_url'] . '?org_name=' . $a5_auth_creds['a5_org_name'];
            } else {
                return false;
            }
			$a5_jwt = get_option('a5_jwt');
            $request = wp_remote_get("$a5_widgets_url", array(
				'headers' => array(
					'authorization' => $a5_jwt
				)
			));
			if (is_wp_error($request)) {
				// var_dump($request);
				echo '<h2 style="color: red;">Error occured while fetching widgets, check settings</h2>';
				return false;
			}
			$body = wp_remote_retrieve_body($request);
			$data = json_decode($body);
			// var_dump ($data);
			if (!empty($data->data)) {
                $a5_widgets = get_option('a5_widgets');
				foreach ($data->data as $widget) {
                    $html = '<div class="a5_widget">';
                    $html .= '<div class="a5_radio">';
                    $html .= '<input type="radio" id="checkbox_example" name="a5_widgets[selected_widget]" value="'.$widget->id.'" ' . checked( $widget->id, $a5_widgets['selected_widget'], false ) . ' />';
                    $html .= '</div>';
                    $html .= '<div class="widget_img">';
                    $html .= '<img src="'.$widget->image_url.'" />';
                    $html .= '<p class="a5_desc"><b>Phone Number:</b> '.$widget->sms_phone_number.'</p>';
                    $html .= '<p class="a5_desc"><b>Channel:</b> #</p>';
                    $html .= '</div>';
                    $html .= '</div>';
                    echo $html;
                    $arr = get_object_vars($widget);
					if (false == get_option($wiget->id)) {
						add_option($widget->id, $arr);
					}
                } 
            } else {
                echo '<h2 id="a5_error">Widgets not configured on Alive5 account or Settings are incorrect.</h2>';
                wp_die();
            }
        } else {
            echo '<h2 id="a5_error">Authentication Error: Please check credentials on settings tab.</h2>';
            wp_die();
        }
    }
    ///////////////////// End Alive 5 Widgets Tab /////////////////////
    public function a5_auth($reauth = false) {
        $a5_jwt = get_option('a5_jwt');
        if (false == $a5_jwt || $reauth == true) {
            $defaults = $this->alive5_defaults();
            $a5_auth_creds = get_option('a5_settings');
            // $auth_url = $defaults['a5_auth_url'].'?org_name='.$a5_auth_creds['a5_org_name'].'&email='.$a5_auth_creds['a5_username'].'&password='.$a5_auth_creds['a5_password'];
                $request = wp_remote_post($defaults['a5_auth_url'], array(
						'body' => array(
							'org_name' => $a5_auth_creds['a5_org_name'],
							'email' => $a5_auth_creds['a5_username'],
							'password' => $a5_auth_creds['a5_password']
						)
				));
                
                if( is_wp_error( $request ) ) {
                    return false; // Bail early
                }
                $body = wp_remote_retrieve_body($request);
                $data = json_decode($body);
				// print_r($data);
                if (!empty($data->data->jwt)) {
                    add_option('a5_jwt', $data->data->jwt);
                    return true;
                } else {
                    return false;
                }
        } else {
            // print_r($a5_jwt);
            return true;
        }
    }
}
