<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if (current_user_can('edit_posts')) : ?>

        <?php if (!class_exists('ACF')): ?>
            <p>
                <?php _e('Required plugin <a href="https://www.advancedcustomfields.com/" target="blank">Advanced Custom Fields</a> not found! Please make sure you have it installed and activated.', $this->plugin_name); ?>
            </p>
        <?php else : ?>

            <?php
            // Populate the dropdown options for post types
            $post_type_options_html = '<option value="">' . __('Select post type', $this->plugin_name) . '</option>';
            $post_types = get_post_types(['public' => true], 'objects', 'and');
            foreach ($post_types as $post_type) {
                $post_type_options_html .= '<option value="' . $post_type->name . '">' . $post_type->label . '</option>' . "\n";
            }

            // Get ACF fields available
            $groups = acf_get_field_groups();
            $file_field_groups = array();
            foreach ($groups as $group) {
                $group_file_fields = array();
                $fields = acf_get_fields($group['key']);
                foreach ($fields as $field) {
                    if ($field['type'] === 'file') {
                        array_push($group_file_fields, $field['key']);
                    }
                }

                if (sizeof($group_file_fields) != 0) {
                    $tmp_group = array(
                        'key' => $group['key'],
                        'title' => $group['title'],
                        'file_fields' => $group_file_fields,
                    );

                    array_push($file_field_groups, $tmp_group);
                }
            }

            // Populate the dropdown options for ACF field groups
            // $acf_group_options_html = '<option value="">' . __('Select field group', $this->plugin_name) . '</option>';
            $acf_group_options_html = '';
            foreach ($file_field_groups as $file_field_group) {
                $acf_group_options_html .= '<option value="' . $file_field_group['key'] . '">' . $file_field_group['title'] . '</option>' . "\n";
            }

            // set dafault values for date inputs
            $d_date_from = date('01.01.Y');
            $d_date_to = date('d.m.Y');

            // Generate a custom nonce value.
            $custom_nonce = wp_create_nonce('wp_attachment_download_form_nonce');
            ?>

            <?php if (sizeof($file_field_groups) == 0) : ?>
                <p>
                    <?php _e('No field groups containing any <a href="https://www.advancedcustomfields.com/resources/file/" target="blank">file field</a> found! This plugin is useles until you have some file fields in you system.', $this->plugin_name); ?>
                </p>
            <?php else: ?>

                <p>
                    <?php _e('First select posts you want to download attachments from. Select post type and publish date range, then hit <b>Download</b> button. After successfull processing archive in ZIP format will be prepared to download to your PC.', $this->plugin_name); ?>
                </p>

                <p>
                    <?php
                    printf(
                            __('ZIP file will contain attachment files with folowing naming convention: %1$s_%2$s_%3$s_%4$s.%4$s', $this->plugin_name), '<code>[post_name]</code>', '<code>[post_id]</code>', '<code>[attachment_name]</code>', '<code>[publish_date]</code>', '<code>[file_extension]</code>');
                    ?>
                </p>

                <form method="post" id="wp_attachment_download_form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

                    <input type="hidden" name="wp_attachment_download_nonce" value="<?php echo $custom_nonce ?>" /> 

                    <h2><?php _e('Specify posts', $this->plugin_name); ?></h2>
                    <table class="form-table">        
                        <tr valign="top">
                            <td scope="row">
                                <span><?php esc_attr_e('Post type', $this->plugin_name); ?></span>
                            </td>
                            <td>
                                <select required id="<?php echo $this->plugin_name; ?>-post_type" name="<?php echo $this->plugin_name; ?>[post_type]" class="regular-text affect-preview" >
                                    <?php echo $post_type_options_html; ?>
                                </select>  
                            </td>
                        </tr>
                        <tr valign="top" class="alternate">
                            <td scope="row">
                                <span><?php esc_attr_e('Published from', $this->plugin_name); ?></span>
                            </td>
                            <td>
                                <input required value="<?php echo $d_date_from; ?>" type="text" placeholder="<?php esc_attr_e('Select date', $this->plugin_name); ?>" class="custom_date regular-text affect-preview" id="<?php echo $this->plugin_name; ?>-published_from" name="<?php echo $this->plugin_name; ?>[published_from]"/>
                                <span class="description"><?php _e('By default set to first day of current year.', $this->plugin_name); ?></span><br>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td scope="row">
                                <span><?php esc_attr_e('Published to', $this->plugin_name); ?></span>
                            </td>
                            <td>
                                <input required value="<?php echo $d_date_to; ?>" type="text" placeholder="<?php esc_attr_e('Select date', $this->plugin_name); ?>" class="custom_date regular-text affect-preview" id="<?php echo $this->plugin_name; ?>-published_to" name="<?php echo $this->plugin_name; ?>[published_to]"/>
                                <span class="description"><?php _e('By default set to current day.', $this->plugin_name); ?></span><br>
                            </td>
                        </tr>
                    </table>

                    <!-- Verify ACF field -->
                    <h2><?php _e('Field group verification', $this->plugin_name); ?></h2>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e('Select field group that contains ACF file fields (available only when more of them).', $this->plugin_name); ?></span></legend>
                        <p class="description"><?php _e('This field is editable only if there are more field groups containing <a href="https://www.advancedcustomfields.com/" target="blank" title="Advanced Custom Fields">ACF</a> file fields and you need to select one of them.', $this->plugin_name); ?></p><br>
                        <label for="<?php echo $this->plugin_name; ?>-acf_field_group">
                            <span><?php _e('Field group', $this->plugin_name); ?></span>
                            <select <?php echo sizeof($file_field_groups) > 1 ? '' : 'disabled'; ?> required id="<?php echo $this->plugin_name; ?>-acf_field_group" class="affect-preview" name="<?php echo $this->plugin_name; ?>[acf_field_group]">
                                <?php echo $acf_group_options_html; ?>
                            </select>  
                        </label>
                    </fieldset>

                    <!-- Preview info -->
                    <div id="preview-info-spinner" class="spinner">
                        <?php _e('Preview beeing generated...', $this->plugin_name); ?>
                    </div>

                    <div id="preview-info-block" class="hidden">
                        <h2><?php _e('Preview', $this->plugin_name); ?></h2>
                        <p id="preview-info-text">

                        </p>               
                    </div>

                    <p class="submit">
                        <input type="submit" disabled="true" name="submit" id="submit" class="button button-primary" value="<?php _e('Download', $this->plugin_name) ?> ">
                    </p>

                    <div id="execution-info-spinner" class="spinner" style="float:none;width:auto;height:auto;padding:10px 0 10px 25px;background-position: 0px 10px;">
                        <?php _e('Your attachments archive is being prepared. Execution time depends on posts count you specified.', $this->plugin_name); ?>
                    </div>

                    <!-- Execution outcome info -->
                    <div id="execution-info" class="hidden">
                        <h2 id="execution-info-title"></h2>
                        <p id="execution-info-text"></p>               
                    </div>  

                </form>
            <?php endif; // check if ACF field groups exists ?>
        <?php endif; // check if ACF exists ?>
    <?php else: ?>
        <p> <?php __("You are not authorized to perform this operation.", $this->plugin_name) ?> </p>
    <?php endif; // check user permissions to perform actions ?>

    <?php apply_filters('created_by', PLUGIN_FILE); ?>

</div>
