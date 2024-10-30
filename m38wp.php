<?php
/**
 *
 * @link              https://merch38.com
 * @since             1.0.0
 * @package           Merch38_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Merch38 for WP
 * Description:       This plugin allows you to insert a Merch38 widget into your website.
 * Version:           1.0.0
 * Author:            Merch38
 * Author URI:        https://merch38.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       merch38-plugin
 * Domain Path:       /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'M38_VERSION', '1.0.0' );
define( 'M38_PATH', dirname( __FILE__ ) );
define( 'M38_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'M38_FOLDER', basename( M38_PATH ) );
define( 'M38_URL', plugins_url() . '/' . M38_FOLDER );
define( 'M38_URL_INCLUDES', M38_URL . '/inc' );

class M38_Plugin_Base {
	public function __construct() {
		// add script and style calls the WP way
		add_action( 'wp_enqueue_scripts', array( $this, 'm38_add_JS' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'm38_add_CSS' ) );

		// add scripts and styles only available in admin
		add_action( 'admin_enqueue_scripts', array( $this, 'm38_add_admin_JS' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'm38_add_admin_CSS' ) );

		// register admin pages for the plugin
		add_action( 'admin_menu', array( $this, 'm38_admin_pages_callback' ) );

		// Register activation and deactivation hooks
		register_activation_hook( __FILE__, 'm38_on_activate_callback' );
		register_deactivation_hook( __FILE__, 'm38_on_deactivate_callback' );

		// Translation-ready
		add_action( 'plugins_loaded', array( $this, 'm38_add_textdomain' ) );

		// Add a sample shortcode
		add_action( 'init', array( $this, 'm38_shortcodes' ) );

		// Add actions for storing value and fetching URL
		// use the wp_ajax_nopriv_ hook for non-logged users (handle guest actions)
 		add_action( 'wp_ajax_store_ajax_value', array( $this, 'store_ajax_value' ) );
 		add_action( 'wp_ajax_fetch_ajax_url_http', array( $this, 'fetch_ajax_url_http' ) );
	}

	/**
	 *
	 * Adding JavaScript scripts
	 *
	 * Loading existing scripts from wp-includes or adding custom ones
	 *
	 */
	public function m38_add_JS() {
		wp_enqueue_script( 'jquery' );

		wp_register_script( 'm38_app_script', 'https://app.merch38.com/js/widget.js', array(), '1.0', true );
		wp_enqueue_script( 'm38_app_script' );

		wp_register_script( 'm38_script', plugins_url( '/js/script.js' , __FILE__ ), array('jquery', 'm38_app_script'), '1.1', true );
		wp_enqueue_script( 'm38_script' );
	}


	/**
	 *
	 * Adding JavaScript scripts for the admin pages only
	 *
	 * Loading existing scripts from wp-includes or adding custom ones
	 *
	 */
	public function m38_add_admin_JS( $hook ) {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'js-render-admin', plugins_url( '/js/jsrender.min.js', __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'js-render-admin' );
		wp_register_script( 'script-admin', plugins_url( '/js/script-admin.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'script-admin' );
	}

	/**
	 *
	 * Add CSS styles
	 *
	 */
	public function m38_add_CSS() {
		wp_register_style( 'm38_style', plugins_url( '/css/style.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'm38_style' );
	}

	/**
	 *
	 * Add admin CSS styles - available only on admin
	 *
	 */
	public function m38_add_admin_CSS( $hook ) {
		wp_register_style( 'm38_style_admin', plugins_url( '/css/style-admin.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'm38_style_admin' );
	}

	/**
	 *
	 * Callback for registering pages
	 *
	 * This demo registers a custom page for the plugin page
	 *
	 */
	public function m38_admin_pages_callback() {
		add_menu_page(
			__( "Merch38", 'm38base' ),
			__( "Merch38", 'm38base' ),
			'edit_themes',
			'm38-plugin-base',
			array( $this, 'm38_plugin_base' ),
			plugins_url( '/img/merch38-logo.svg', __FILE__ )
		);
	}

	/**
	 *
	 * The content of the base page
	 *
	 */
	public function m38_plugin_base() {
		include_once( M38_PATH_INCLUDES . '/admin-settings-page.php' );
	}

	/**
	 * Register a sample shortcode to be used
	 *
	 * First parameter is the shortcode name, would be used like: [m38sampcode]
	 *
	 */
	public function m38_shortcodes() {
		add_shortcode( 'merch38', array( $this, 'merch38_shortcode_body' ) );
	}

	/**
	 * Returns the content of the sample shortcode, like [merch38]
	 * @param array $attr arguments passed to array, like [merch38 attr1="one" attr2="two"]
	 * @param string $content optional, could be used for a content to be wrapped, such as [m38samcode]somecontnet[/m38samcode]
	 */
	public function merch38_shortcode_body( $attr, $content = null ) {
		$jsonAttr = json_encode($attr);
		$attrSerialized = base64_encode($jsonAttr);
		ob_start();
		?>
		<div class="m38-wp-widget" id="m38wp_<?php echo esc_attr( $attr['id'] ); ?>" data-args="<?php echo esc_attr($attrSerialized); ?>"></div>
		<?php
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	/**
	 * Add textdomain for plugin
	 */
	public function m38_add_textdomain() {
		load_plugin_textdomain( 'm38base', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Callback for saving a simple AJAX option with no page reload
	 */
	public function store_ajax_value() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['m38_option_api_key'] ) ) {
			$m38_option_api_key = sanitize_text_field( $_POST['data']['m38_option_api_key'] );
			if ( strlen( $m38_option_api_key ) < 50 ) {
				update_option( 'm38_option_api_key' , $m38_option_api_key );
			} else {
				wp_send_json_error( 'Too long value', 400 );
			}
		}
		die();
	}

	/**
	 * Callback for getting a URL and fetching it's content in the admin page
	 */
	public function fetch_ajax_url_http() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['m38_url_for_ajax'] ) ) {
			$ajax_url = esc_url($_POST['data']['m38_url_for_ajax']);

			$response = wp_remote_get( $ajax_url );

			if( is_wp_error( $response ) ) {
				echo json_encode( __( 'Invalid HTTP resource', 'm38base' ) );
				die();
			}

			if( isset( $response['body'] ) ) {
				if( preg_match( '/<title>(.*)<\/title>/', $response['body'], $matches ) ) {
					echo esc_js( json_encode( $matches[1] ) );
					die();
				}
			}
		}
		echo json_encode( __( 'No title found or site was not fetched properly', 'm38base' ) );
		die();
	}

}


/**
 * Register activation hook
 *
 */
function m38_on_activate_callback() {
	// do something on activation
}

/**
 * Register deactivation hook
 *
 */
function m38_on_deactivate_callback() {
	// do something when deactivated
}

// Initialize everything
$m38_plugin_base = new M38_Plugin_Base();
