<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.wetory.eu/
 * @since             1.0.0
 * @package           Wp_Attachment_Download
 *
 * @wordpress-plugin
 * Plugin Name:       WP Attachment Download
 * Plugin URI:        https://www.wetory.eu/wordpress/plugins/wp-attachments-download/
 * GitHub URI:        https://github.com/wetory/wp-attachment-download  
 * Description:       Plugin adds functionality to download posts attachments build with ACF file fields from administration. 
 * Version:           1.0.1
 * Author:            Wetory
 * Author URI:        https://www.wetory.eu/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-attachment-download
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Constants definition
defined('DOWNLOAD_MEDIA_FOLDER') or define('DOWNLOAD_MEDIA_FOLDER', plugin_dir_path(__FILE__) . '/downloads/');
defined('DOWNLOAD_MEDIA_FOLDER_URL') or define('DOWNLOAD_MEDIA_FOLDER_URL', plugin_dir_url(__FILE__) . '/downloads/');
defined('PLUGIN_DIR') or define('PLUGIN_DIR', dirname(plugin_basename(__FILE__)));
defined('PLUGIN_BASE') or define('PLUGIN_BASE', plugin_basename(__FILE__));
defined('PLUGIN_URL') or define('PLUGIN_URL', plugin_dir_url(__FILE__));
defined('PLUGIN_PATH') or define('PLUGIN_PATH', plugin_dir_path(__FILE__));
defined('PLUGIN_FILE') or define('PLUGIN_FILE', __FILE__);

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WP_ATTACHMENT_DOWNLOAD_VERSION', '1.0.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-attachment-download-activator.php
 */
function activate_wp_attachment_download() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-attachment-download-activator.php';
    Wp_Attachment_Download_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-attachment-download-deactivator.php
 */
function deactivate_wp_attachment_download() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-attachment-download-deactivator.php';
    Wp_Attachment_Download_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_attachment_download');
register_deactivation_hook(__FILE__, 'deactivate_wp_attachment_download');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp-attachment-download.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_attachment_download() {

    $plugin = new Wp_Attachment_Download();
    $plugin->run();
}

run_wp_attachment_download();
