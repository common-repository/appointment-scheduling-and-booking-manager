<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Eav_Networks Loader.
 *
 * Loads the component
 *
 * @package Eav_Networks
 * @subpackage Component
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Eav_Networks_Component class
 *
 * @package Eav_Networks
 * @subpackage Component
 *
 * @since Eav Networks (1.0.0)
 */
class Eav_Networks_Component extends BP_Component {

	/**
	 * Constructor method
	 *
	 * @package Eav_Networks
	 * @subpackage Component
	 *
	 * @since Eav Networks (1.0.0)
	 */
	function __construct() {
		$bp = buddypress();

		parent::start(
			'eav_networks',
			eav_networks()->get_component_name(),
			eav_networks()->includes_dir
		);

		$this->includes();

		$bp->active_components[$this->id] = '1';

		/**
		 * Only register the post type on the blog where BuddyPress is activated.
		 */
		if ( get_current_blog_id() == bp_get_root_blog_id() ) {
			add_action( 'init', array( &$this, 'register_post_types' ) );
		}
	}

	/**
	 * Include files
	 *
	 * @package Eav_Networks
	 * @subpackage Component
	 *
	 * @since Eav Networks (1.0.0)
	 */
	function includes( $includes = array() ) {

		// Files to include
		$includes = array(
		'eav_networks_script.php' ,
		'eav_networks_menu.php' ,
		'eav_user_management.php',
		'calendar.php',
		'book_slots.php',
		'calender-sheduling.php',
		'ajax_handler.php',
		'schedule_save_poat.php'
	 
		);

		if ( bp_is_active( 'notifications' ) ) {
			$includes[] = '';
		}

		if ( bp_is_active( 'activity' ) ) {
			$includes[] = '';
		}

		if ( bp_is_active( 'groups' ) ) {
			$includes[] = '';
		}

		if ( is_admin() ) {
			$includes[] = '';
		}

		parent::includes( $includes );
	}

	/**
	 * Set up globals
	 *
	 * @package Eav_Networks
	 * @subpackage Component
	 *
	 * @since Eav Networks (1.0.0)
	 */
	function setup_globals( $args = array() ) {

		// Set up the $globals array to be passed along to parent::setup_globals()
		$args = array(
			'slug'                  => eav_networks()->get_component_slug(),
			'notification_callback' => 'eav_networks_format_notifications',
			'search_string'         => __( 'Search Eav_Networks...', 'eav_networks' ),
		);

		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $args );


	}

	/**
	 * Register the eav_networks post type
	 *
	 * @package Eav_Networks
	 * @subpackage Component
	 *
	 * @since Eav Networks (1.0.0)
	 */
	function register_post_types() {
		// Set up some labels for the post type
		$rdv_labels = array(
			'name'	             => __( 'Eav_Networks',                                                     'eav_networks' ),
			'singular'           => _x( 'Eav_Networks',                   'eav_networks singular',           'eav_networks' ),
			'menu_name'          => _x( 'Eav_Networks',                   'eav_networks menu name',          'eav_networks' ),
			'all_items'          => _x( 'All Eav_Networks',               'eav_networks all items',          'eav_networks' ),
			'singular_name'      => _x( 'Eav_Networks',                   'eav_networks singular name',      'eav_networks' ),
			'add_new'            => _x( 'Add New Eav_Networks',           'eav_networks add new',            'eav_networks' ),
			'edit_item'          => _x( 'Edit Eav_Networks',              'eav_networks edit item',          'eav_networks' ),
			'new_item'           => _x( 'New Eav_Networks',               'eav_networks new item',           'eav_networks' ),
			'view_item'          => _x( 'View Eav_Networks',              'eav_networks view item',          'eav_networks' ),
			'search_items'       => _x( 'Search Eav_Networks',            'eav_networks search items',       'eav_networks' ),
			'not_found'          => _x( 'No Eav_Networks Found',          'eav_networks not found',          'eav_networks' ),
			'not_found_in_trash' => _x( 'No Eav_Networks Found in Trash', 'eav_networks not found in trash', 'eav_networks' )
		);

		$rdv_args = array(
			'label'	            => _x( 'Eav_Networks',                    'eav_networks label',              'eav_networks' ),
			'labels'            => $rdv_labels,
			'public'            => false,
			'rewrite'           => false,
			'show_ui'           => false,
			'show_in_admin_bar' => false,
			'show_in_nav_menus' => false,
			'capabilities'      =>   array(),
			'capability_type'   => array( 'eav_networks', 'eav_networks' ),
			'delete_with_user'  => true,
			'supports'          => array( 'title', 'author' )
		);

		// Register the post type for attachements.
		register_post_type( 'eav_networks', $rdv_args );

		parent::register_post_types();
	}

	/**
	 * Register the eav_networks types taxonomy
	 *
	 * @package Eav_Networks
	 * @subpackage Component
	 *
	 * @since Eav Networks (1.2.0)
	 */
	public function register_taxonomies() {
		// Register the taxonomy
		register_taxonomy( 'eav_networks_type', 'eav_networks', array(
			'public' => false,
		) );
	}
}

/**
 * Loads rendez vous component into the $bp global
 *
 * @package Eav_Networks
 * @subpackage Component
 *
 * @since Eav Networks (1.0.0)
 */
function eav_networks_load_component() {
	$bp = buddypress();

	$bp->eav_networks = new Eav_Networks_Component;
}
