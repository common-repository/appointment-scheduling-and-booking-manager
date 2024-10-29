<?php
/**
 * @package   Appointment scheduling and Booking Manager
 * @author     Paras Agarwal
 * @license   GPL-2.0+
 * @copyright 2016 eav
 *
 * @wordpress-plugin
 * Plugin Name:    Appointment scheduling and Booking Manager
 * Description:    Appointment scheduling and Booking Manager is a BuddyPress plugin to schedule appointments with your buddies
 * Version:          1.0
 * Author:            Paras Agarwal
 * Text Domain:   Appointment scheduling and Booking Manager
 * License:           GPL-2.0+
 */
 
 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! class_exists( 'Eav Networks' ) ) :
/**
 * Main Eav Networks Class
 *
 * @since Eav Networks (1.0.0)
 */
class Eav_Networks {
	/**
	 * Instance of this class.
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Required BuddyPress version for the plugin.
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 *
	 * @var      string
	 */
	public static $required_bp_version = '2.6.2';

	/**
	 * BuddyPress config.
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 *
	 * @var      array
	 */
	public static $bp_config = array();

	/**
	 * Initialize the plugin
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 */
	private function __construct() {
		// First you will set your plugin's globals
		$this->eav_setup_globals();
		// Then include the needed files
		$this->eav_includes();
		// Then hook to BuddyPress actions & filters
		$this->eav_setup_hooks();
		
		$this->eav_add_css_js();
		$this->eav_admin_settings();
	}
	
	public function eav_add_css_js(){
	
	add_action('init', 'eav_register_script');
		
	}
	
	
	public function eav_admin_settings()
	{
	
		function eav_admin()
		{
       include('includes/eav_networks_admin.php' );
         }
 
       function eav_admin_actions() 
	      {
        add_options_page("Appointment scheduling and Booking Manager setting", "Appointment scheduling and Booking Manager  setting", 1, "appointment-scheduling-and-booking-manager", "eav_admin");
            }
 
         add_action('admin_menu', 'eav_admin_actions');
		
		
	}
	

	/**
	 * Return an instance of this class.
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function start() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Sets some globals for the plugin
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 */
	private function eav_setup_globals() {

		// Define a global that will hold the current version number
		$this->version       = '1.0';

		// Define a global to get the textdomain of your plugin.
		$this->domain        = 'appointment-scheduling-and-booking-manager';

		$this->file          = __FILE__;
		$this->basename      = plugin_basename( $this->file );

		// Define a global that we can use to construct file paths throughout the component
		$this->plugin_dir    = plugin_dir_path( $this->file );

		// Define a global that we can use to construct file paths starting from the includes directory
		$this->includes_dir  = trailingslashit( $this->plugin_dir . 'includes' );

		// Define a global that we can use to construct file paths starting from the includes directory
		$this->lang_dir      = trailingslashit( $this->plugin_dir . 'languages' );


		$this->plugin_url    = plugin_dir_url( $this->file );
		$this->includes_url  = trailingslashit( $this->plugin_url . 'includes' );

		// Define a global that we can use to construct url to the javascript scripts needed by the component
		$this->plugin_js     = trailingslashit( $this->includes_url . 'js' );

		// Define a global that we can use to construct url to the css needed by the component
		$this->plugin_css    = trailingslashit( $this->includes_url . 'css' );
	}

	/**
	 * Include the component's loader.
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 */
	private function eav_includes() {
		if ( self::bail() )
			return;

		require( $this->includes_dir . 'eav_networks-loader.php' );
	}

	/**
	 * Sets the key hooks to add an action or a filter to
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 */
	private function eav_setup_hooks() {

		if ( ! self::bail() ) {
			// Load the component
			add_action( 'bp_loaded', 'eav_networks_load_component' );


			// loads the languages..
			add_action( 'bp_loaded', array( $this, 'load_textdomain' ) );

		} else {
			// Display a warning message in network admin or admin
			add_action( self::$bp_config['network_active'] ? 'network_admin_notices' : 'admin_notices', array( $this, 'warning' ) );
		}

	}

	/**
	 * Display a warning message to admin
	 *
	 * @package Eav Networks
	 *
	 * @since Eav Networks (1.0.0)
	 */
	public function warning() {
		$warnings = array();

		if( ! self::version_check() ) {
			$warnings[] = sprintf( __( 'Eav Networks requires at least version %s of BuddyPress.', 'eav_networks' ), self::$required_bp_version );
		}

		if ( ! empty( self::$bp_config ) ) {
			$config = self::$bp_config;
		} else {
			$config = self::config_check();
		}

		if ( ! bp_core_do_network_admin() && ! $config['blog_status'] ) {
			$warnings[] = __( 'Eav Networks requires to be activated on the blog where BuddyPress is activated.', 'eav_networks' );
		}

		if ( bp_core_do_network_admin() && ! $config['network_status'] ) {
			$warnings[] = __( 'Eav Networks and BuddyPress need to share the same network configuration.', 'eav_networks' );
		}

		if ( ! empty( $warnings ) ) :
		?>
		<div id="message" class="error">
			<?php foreach ( $warnings as $warning ) : ?>
				<p><?php echo esc_html( $warning ) ; ?></p>
			<?php endforeach ; ?>
		</div>
		<?php
		endif;
	}


	/** Utilities *****************************************************************************/

	/**
	 * Checks BuddyPress version
	 *
	 * @package Eav_Networks
	 *
	 * @since Eav_Networks (1.0.0)
	 */
	public static function version_check() {
		// taking no risk
		if ( ! defined( 'BP_VERSION' ) )
			return false;

		return version_compare( BP_VERSION, self::$required_bp_version, '>=' );
	}

	/**
	 * Checks if your plugin's config is similar to BuddyPress
	 *
	 * @package Eav_Networks
	 *
	 * @since Eav_Networks (1.0.0)
	 */
	public static function config_check() {
		/**
		 * blog_status    : true if your plugin is activated on the same blog
		 * network_active : true when your plugin is activated on the network
		 * network_status : BuddyPress & your plugin share the same network status
		 */
		self::$bp_config = array(
			'blog_status'    => false,
			'network_active' => false,
			'network_status' => true
		);

		if ( get_current_blog_id() == bp_get_root_blog_id() ) {
			self::$bp_config['blog_status'] = true;
		}

		$network_plugins = get_site_option( 'active_sitewide_plugins', array() );

		// No Network plugins
		if ( empty( $network_plugins ) )
			return self::$bp_config;

		$eav_networks_basename = plugin_basename( __FILE__ );

		// Looking for BuddyPress and your plugin
		$check = array( buddypress()->basename, $eav_networks_basename );

		// Are they active on the network ?
		$network_active = array_diff( $check, array_keys( $network_plugins ) );

		// If result is 1, your plugin is network activated
		// and not BuddyPress or vice & versa. Config is not ok
		if ( count( $network_active ) == 1 )
			self::$bp_config['network_status'] = false;

		// We need to know if the plugin is network activated to choose the right
		// notice ( admin or network_admin ) to display the warning message.
		self::$bp_config['network_active'] = isset( $network_plugins[ $eav_networks_basename ] );

		return self::$bp_config;
	}

	/**
	 * Bail if BuddyPress config is different than this plugin
	 *
	 * @package Eav_Networks
	 *
	 * @since Eav_Networks (1.0.0)
	 */
	public static function bail() {
		$retval = false;

		$config = self::config_check();

		if ( ! self::version_check() || ! $config['blog_status'] || ! $config['network_status'] )
			$retval = true;

		return $retval;
	}

	/**
	 * Loads the translation files
	 *
	 * @package Eav_Networks
	 *
	 * @since Eav_Networks (1.0.0)
	 *
	 * @uses get_locale() to get the language of WordPress config
	 * @uses load_texdomain() to load the translation if any is available for the language
	 * @uses load_plugin_textdomain() to load the translation if any is available for the language
	 */
	public function load_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		// Setup paths to a Eav_Networks subfolder in WP LANG DIR
		$mofile_global = WP_LANG_DIR . '/eav_networks/' . $mofile;

		// Look in global /wp-content/languages/eav_networks folder
		if ( ! load_textdomain( $this->domain, $mofile_global ) ) {

			// Look in local /wp-content/plugins/eav_networks/languages/ folder
			// or /wp-content/languages/plugins/
			load_plugin_textdomain( $this->domain, false, basename( $this->plugin_dir ) . '/languages' );
		}
	}

	/**
	 * Get the component name of the plugin
	 *
	 * @package Eav_Networks
	 *
	 * @since Eav_Networks (1.2.0)
	 *
	 * @uses apply_filters() call 'eav_networks_get_component_name' to override default component name
	 */
	public static function get_component_name() {
		return apply_filters( 'eav_networks_get_component_name', __( 'Eav_Networks', 'eav_networks' ) );
	}

	/**
	 * Get the component slug of the plugin
	 *
	 * @package Eav_Networks
	 *
	 * @since Eav_Networks (1.0.0)
	 *
	 * @uses apply_filters() call 'eav_networks_get_component_slug' to override default component slug
	 */
	public static function get_component_slug() {
		// Defining the slug in this way makes it possible for site admins to override it
		if ( ! defined( 'EAV_NETWORKS_SLUG' ) ) {
			define( 'EAV_NETWORKS_SLUG', 'eav_networks' );
		}

		return EAV_NETWORKS_SLUG;
	}

	/**
	 * Get the schedule slug of the component
	 *
	 * @package Eav_Networks
	 *
	 * @since Eav_Networks (1.0.0)
	 *
	 * @uses apply_filters() call 'eav_networks_get_schedule_slug' to override default schedule slug
	 */
	public static function get_schedule_slug() {
		return 'schedule';
	}

	/**
	 * Get the attend slug of the component
	 *
	 * @package Rendez Vous
	 *
	 * @since Rendez Vous (1.2.0)
	 *
	 * @uses apply_filters() call 'eav_networks_get_attend_slug' to override default attend slug
	 */
	public static function get_attend_slug() {
		return 'booking';
	}
}

endif;

// BuddyPress is loaded and initialized, let's start !
function eav_networks() {
	return Eav_Networks::start();
}
add_action( 'bp_include', 'eav_networks' );

