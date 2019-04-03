<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/admin
 * @author     Wetory <tomas.rybnicky@wetory.eu>
 */
class Wp_Attachment_Download_Admin {

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
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Attachment_Download_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Attachment_Download_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if ('tools_page_' . $this->plugin_name == get_current_screen()->id) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-attachment-download-admin.css', array(), $this->version, 'all');
            wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Attachment_Download_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Attachment_Download_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if ('tools_page_' . $this->plugin_name == get_current_screen()->id) {
            $params = array('ajaxurl' => admin_url('admin-ajax.php'), 'plugin_name' => $this->plugin_name);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-attachment-download-admin.js', array('jquery', 'jquery-ui-datepicker'), $this->version, false);
            wp_localize_script($this->plugin_name, 'params', $params);
        }
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        add_management_page(__('Download Attachments', $this->plugin_name), __('Attachments', $this->plugin_name), 'edit_posts', $this->plugin_name, array($this, 'display_plugin_page')
        );
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links) {
        /*
         *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
         */
        $settings_link = array(
            '<a href="' . admin_url('tools.php?page=' . $this->plugin_name) . '">' . __('Download attachments', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_page() {
        include_once( 'partials/wp-attachment-download-admin-display.php' );
    }

    /**
     * Validate form inputs
     * @param array $input
     * 
     * @since    1.0.0
     */
    public function validate_download_form_input($input) {
        
    }

    /**
     * Action hook for wp_attachment_download_form. Processing this form will prepare ZIP file and make it available for download.
     */
    public function download_attachments() {

        if (isset($_POST['wp_attachment_download_nonce']) && wp_verify_nonce($_POST['wp_attachment_download_nonce'], 'wp_attachment_download_form_nonce')) {

            $result = $this->prepare_zip_file($_POST);

            // check if form submitted with AJAX
            if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {

                $reponse['title'] = __('The request was successful.', $this->plugin_name);
                $text = sprintf(__('Your ZIP file is prepared and should be downloaded automatically. If not you can download it <a href="%1$s" download target="_blank">here</a>. Have a nice day!', $this->plugin_name), DOWNLOAD_MEDIA_FOLDER_URL . $result['zip_file']);
                $reponse['text'] = $text;
                $reponse['zip_file'] = DOWNLOAD_MEDIA_FOLDER_URL . $result['zip_file'];

                wp_send_json($reponse);
                wp_die();
            }

            // add the admin notice
            $admin_notice = "success";
            // redirect the user to the appropriate page
            $this->custom_redirect($admin_notice, $text);
            exit;
        } else {
            wp_die(__('Invalid nonce specified', $this->plugin_name), __('Error', $this->plugin_name), array(
                'response' => 403,
                'back_link' => 'admin.php?page=' . $this->plugin_name,
            ));
        }
    }

    /**
     * Action hook for inputs value change in wp_attachment_download_form
     */
    public function regenerate_preview() {
        if (isset($_POST)) {

            // get file fields names
            $acf_field_group = sanitize_key($_POST['wp-attachment-download']['acf_field_group']);
            $file_fields = $this->get_fields_by_type($acf_field_group, 'file');

            // get posts based on $data
            $posts = $this->get_posts($this->prepare_arguments($_POST));

            $attachments_count = 0;
            $posts_count = 0;

            // iterate through posts and get attachments count
            while ($posts->have_posts()) : $posts->the_post();
                $post_attachments = $this->get_post_attachments(get_the_ID(), $file_fields);
                $attachments_count += sizeof($post_attachments);
                $posts_count++;
            endwhile;

            // return adequate message
            $message = sprintf(__('Found %1$d posts with %2$d attachments.', $this->plugin_name), $posts_count, $attachments_count);
            if ($posts_count > 0 && $attachments_count > 0) {
                $message .= ' ' . __('Continue by clicking Download button.', $this->plugin_name);
            } else {
                $message .= ' ' . __('There is nothing to download for specified parameters.', $this->plugin_name);
            }

            $response = array(
                'message' => $message,
                'posts_count' => $posts_count,
                'attachments_count' => $attachments_count,
            );
            wp_send_json($response);
        }
        wp_die();
    }

    /**
     * Prepare archive file for further processing. It parse input data, iterate all posts and related attachments and add them to ZIP file.
     * @param array $data Input data from form which are used for gathering attachments
     * @return array associative array with output message and path to created ZIP file for futher processing
     */
    public function prepare_zip_file($data, $get_messages = false) {
        // create ZIP file
        $zip_file = apply_filters('zip_file_name', $data['wp-attachment-download']['post_type']);
        $zip = $this->create_zip_file(DOWNLOAD_MEDIA_FOLDER . $zip_file);
        $message = sprintf(__('ZIP file created here: <b>%1$s</b><br>'), $zip->filename);

        // get file fields names
        $acf_field_group = sanitize_key($data['wp-attachment-download']['acf_field_group']);
        $file_fields = $this->get_fields_by_type($acf_field_group, 'file');

        // get posts based on $data
        $posts = $this->get_posts($this->prepare_arguments($data));

        // iterate through posts and get attachments
        while ($posts->have_posts()) : $posts->the_post();
            $message .= sprintf(__('Post <b>%1$s</b>:<br>'), get_the_title());
            $post_attachments = $this->get_post_attachments(get_the_ID(), $file_fields);
            foreach ($post_attachments as $attachment_key => $attachment) {
                $file_name = apply_filters('attachment_file_name', get_the_ID(), $attachment_key, $attachment);

                $zip->addFile(get_attached_file($attachment['ID']), $file_name);
                $message .= sprintf(__(' - file <b>%1$s</b> added to ZIP file<br>'), $attachment['name']);
            }
        endwhile;

        $zip->close();

        return array(
            'message' => $message,
            'zip_file' => $zip_file
        );
    }

    /**
     * Redirect
     * 
     * @since    1.0.0
     */
    public function custom_redirect($admin_notice, $response) {
        wp_redirect(esc_url_raw(add_query_arg(array(
            'wda_admin_add_notice' => $admin_notice,
            'wda_response' => $response,
                                ), admin_url('admin.php?page=' . $this->plugin_name)
        )));
    }

    /**
     * Print Admin Notices
     * 
     * @since    1.0.0
     */
    public function print_plugin_admin_notices() {
        if (isset($_REQUEST['wda_admin_add_notice'])) {
            if ($_REQUEST['wda_admin_add_notice'] === "success") {
                $html = '<div class="notice notice-success is-dismissible"><p><strong>' . __('The request was successful.', $this->plugin_name) . '</strong></p><br>';
                $html .= '<pre>' . htmlspecialchars(print_r($_REQUEST['wda_response'], true)) . '</pre></div>';
                echo $html;
            }
        } else {
            return;
        }
    }

    /**
     * Parse data given by array to WP_Query arguments needed for posts query
     * @param array $data associative array including data
     * @return array WP_Query ready arguments
     */
    public function prepare_arguments($data) {
        $args = array(
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'order_by' => 'date',
        );

        if (isset($data['wp-attachment-download']['published_from'])) {
            $args['date_query']['after'] = strval(sanitize_text_field($data['wp-attachment-download']['published_from']));
        }

        if (isset($data['wp-attachment-download']['published_to'])) {
            $args['date_query']['before'] = strval(sanitize_text_field($data['wp-attachment-download']['published_to']));
        }

        if (isset($data['wp-attachment-download']['post_type'])) {
            $args['post_type'] = strval(sanitize_key($data['wp-attachment-download']['post_type']));
        }

        return $args;
    }

    /**
     * Create ZIP archive file on given path
     * @param string $file_path path to the file on the server
     * @return \ZipArchive Object of given type
     */
    public function create_zip_file(string $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $zip = new ZipArchive();
        if ($zip->open($file_path, ZIPARCHIVE::CREATE) != TRUE) {
            die("Could not open archive");
        }
        return $zip;
    }

    /**
     * Retrieve WP posts based on given WP_Query arguments
     * @param array $args WP_Query ready arguments
     * @return WP_Post[]|int[] posts
     */
    public function get_posts($args) {
        if (isset($args) && sizeof($args) > 0) {
            return new WP_Query($args);
        }
        return;
    }

    /**
     * Get values for given post and field names array
     * @param int $post post identificator
     * @param array $fields array of field names to be gathered
     * @return array associative array of values
     */
    public function get_post_field_values($post, $fields) {
        $values = array();
        foreach ($fields as $field) {
            $value = get_field($field, $post);
            if ($value) {
                $values[$field] = $value;
            }
        }
        return $values;
    }

    /**
     * Get attachments for given post
     * @param int $post post identificator
     * @param array $attachment_fields array of field names with type = 'file'
     * @return array|boolean associated array of attachments for given post or false if not found
     */
    public function get_post_attachments($post, $attachment_fields = false) {
        if ($attachment_fields) {
            return $this->get_post_field_values($post, $attachment_fields);
        }
        return false;
    }

    /**
     * Get ACF fields by its type
     * @param string $parent parent group key
     * @return array[]|boolean returns array of file fields name or false when not found
     */
    public function get_fields_by_type($parent, $type) {
        $fields = acf_get_fields($parent);
        $file_fields = array();
        foreach ($fields as $field) {
            if ($field['type'] === $type) {
                array_push($file_fields, $field['name']);
            }
        }
        return sizeof($file_fields) > 0 ? $file_fields : false;
    }

    /**
     * Filter function to prepare attachment name
     * @param int $post_id post identification
     * @param string|int $attachment_key name of attachment
     * @param array $attachment attachment object array
     * @return string Formatted file name
     */
    public function format_attachment_file_name($post_id, $attachment_key, $attachment) {
        $post_title = sanitize_title(get_the_title($post_id));
        $file_name = sanitize_file_name(
                $post_title
                . '_' . $post_id
                . '_' . $attachment_key
                . '_' . $attachment['title']
                . '_' . get_the_time('Y-m-d', $post_id)
                . '.' . $attachment['subtype']
        );
        return $file_name;
    }

    /**
     * Filter function to prepare ZIP file name
     * @param string $post_type post type slug to be included in file name
     * @return string formatted file name
     */
    public function format_zip_file_name($post_type) {
        $file_name = sanitize_file_name(
                remove_accents(__('Attachments', $this->plugin_name))
                . '_' . $post_type
                . '_' . date('Ymd_His')
                . '.zip'
        );
        return $file_name;
    }

    /**
     * Filter hook to print created by information
     * @param string $plugin_file_path absolute server path to plugin main file where all information is defined
     */
    public function get_created_by($plugin_file_path) {
        $plugin_data = get_plugin_data($plugin_file_path, false, false);
        echo '<div class="created-by-info">';
        printf(__('<a href="%1$s" target="_blank">%2$s</a> by <a href="%3$s" target="_blank">%4$s</a>'), $plugin_data['PluginURI'], $plugin_data['Name'], $plugin_data['AuthorURI'], $plugin_data['Author']);
        echo '</div>';
    }

}
