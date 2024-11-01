<?php

/**
 * The file defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.weedesign.de
 * @since      1.0.0
 *
 * @package    weebotLite
 * @subpackage weebotLite/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    weebotLite
 * @subpackage weebotLite/includes
 * @author     Wolfgang Ertl <wolfgang.ertl@weedesign.de>
 */
class weebotLite {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      weebotLite_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'weebotLite';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - weebotLite_Loader. Orchestrates the hooks of the plugin.
	 * - weebotLite_i18n. Defines internationalization functionality.
	 * - weebotLite_Admin. Defines all hooks for the admin area.
	 * - weebotLite_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class.weebotLite.loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class.weebotLite.i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class.weebotLite.admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class.weebotLite.public.php';

		$this->loader = new weebotLite_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the weebotLite_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new weebotLite_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new weebotLite_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'options_update');

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'custom_toolbar_link', 999);

		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
		
		$this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'register_admin_user_column' );
		$this->loader->add_filter( 'manage_users_custom_column', $plugin_admin, 'display_admin_user_column', 10, 3 );
		
		$this->loader->add_action('wp_ajax_nopriv_'.$this->plugin_name.'AJAXadmin', $plugin_admin, 'AJAX');
		$this->loader->add_action('wp_ajax_'.$this->plugin_name.'AJAXadmin', $plugin_admin, 'AJAX');
		
		$this->loader->add_action('init', $plugin_admin, 'online');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new weebotLite_Public( $this->plugin_name, $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_action('wp_ajax_nopriv_'.$this->plugin_name.'AJAX', $plugin_public, 'AJAX');
		$this->loader->add_action('wp_ajax_'.$this->plugin_name.'AJAX', $plugin_public, 'AJAX');
		
		$this->loader->add_action('init', $plugin_public, 'user');
		
		$this->loader->add_action('init', $plugin_public, 'add_shortcode');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    weebotLite_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	
	/**
	 * Default Option-Settings of the plugin
	 *
	 * @since     1.0.0
	 * @return    array    Array of default options
	 */
	public function default_options() {
		return array(
			"chat" => 1,
			"footer" => 1,
			"theme" => "avatar",
			"chat:width" => "",
			"chat:welcome" => "",
			"chat:seconds" => 2,
			"chat:supporter" => 1,
			"chat:messages:limit" => 10,
			"page:parameter" => 0,
			"page:hash" => 0,
			"chat:logged" => 0,
			"chat:hide" => 0,
			"chat:questions" => 1,
			"support" => 1,
			"spam" => 1,
			"online" => 0,
			"spam:chars" => 2,
			"spam:words" => 0,
			"spam:words:exclude" => "",
			"spam:words:include" => "",
			"spam:strings:exclude" => ""
		);
	}

}
