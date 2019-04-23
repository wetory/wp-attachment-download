<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/includes
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
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/includes
 * @author     Wetory <tomas.rybnicky@wetory.eu>
 */
class Wp_Attachment_Download {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wp_Attachment_Download_Loader    $loader    Maintains and registers all hooks for the plugin.
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
        if (defined('WAD_VERSION')) {
            $this->version = WAD_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'wp-attachment-download';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wp_Attachment_Download_Loader. Orchestrates the hooks of the plugin.
     * - Wp_Attachment_Download_i18n. Defines internationalization functionality.
     * - Wp_Attachment_Download_Admin. Defines all hooks for the admin area.
     * - Wp_Attachment_Download_Public. Defines all hooks for the public side of the site.
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
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-attachment-download-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-attachment-download-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-attachment-download-admin.php';

        $this->loader = new Wp_Attachment_Download_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wp_Attachment_Download_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Wp_Attachment_Download_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Wp_Attachment_Download_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Add menu item
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

        // Add plugin page link to the plugin
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');

        // Form submissions
        $this->loader->add_action('admin_post_download_attachments', $plugin_admin, 'download_attachments');
        $this->loader->add_action('wp_ajax_download_attachments', $plugin_admin, 'download_attachments');
        
        // Ajax actions
        $this->loader->add_action('wp_ajax_regenerate_preview', $plugin_admin, 'regenerate_preview');
        
        // Register admin notices
        $this->loader->add_action('admin_notices', $plugin_admin, 'print_plugin_admin_notices');

        // Formatting filters
        $this->loader->add_filter('attachment_file_name', $plugin_admin, 'format_attachment_file_name', 10, 3);
        $this->loader->add_filter('zip_file_name', $plugin_admin, 'format_zip_file_name');
        $this->loader->add_filter('created_by', $plugin_admin, 'get_created_by');
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
     * @return    Wp_Attachment_Download_Loader    Orchestrates the hooks of the plugin.
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

}
