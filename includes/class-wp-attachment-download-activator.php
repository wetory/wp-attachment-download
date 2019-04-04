<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/includes
 * @author     Wetory <tomas.rybnicky@wetory.eu>
 */
class Wp_Attachment_Download_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        if(!is_dir(DOWNLOAD_MEDIA_FOLDER)){
            mkdir(DOWNLOAD_MEDIA_FOLDER);
        }
    }
}
