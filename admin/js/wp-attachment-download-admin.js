(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(function () {
        $('.custom_date').datepicker({
            dateFormat: 'dd.mm.yy'
        });

        /**
         * Main admin form for dowloadinf attachments archive ZIP file using AJAX call
         */
        $('#wp_attachment_download_form').submit(function (event) {

            event.preventDefault(); // Prevent the default form submit.            

            // serialize the form data
            var ajax_form_data = $(this).serialize();

            //add our own ajax check as X-Requested-With is not always reliable
            ajax_form_data = ajax_form_data + '&ajaxrequest=true&submit=Submit+Form';

            $.ajax({
                url: params.ajaxurl, // domain/wp-admin/admin-ajax.php
                type: 'post',
                data: ajax_form_data + '&action=download_attachments',
                beforeSend: function () {
                    jQuery('#execution-info-spinner').addClass('is-active');
                    $("#execution-info").hide();
                },
                success: function (response) {
                    $("#execution-info").show();
                    $("#execution-info-title").html(response['title']);
                    $("#execution-info-text").html(response['text']);
                    window.location.assign(response['zip_file']);
                },
                error: function () {
                    $("#execution-info-text").html("<h2>Something went wrong.</h2><br>");
                },
                complete: function () {
                    // event.target.reset();
                    jQuery('#execution-info-spinner').removeClass('is-active');
                },
            });
        });

        $('.affect-preview').change(function (event) {
            // get form data
            var post_type = $('#' + params.plugin_name + '-post_type').val();
            var published_from = $('#' + params.plugin_name + '-published_from').val();
            var published_to = $('#' + params.plugin_name + '-published_to').val();
            var acf_field_group = $('#' + params.plugin_name + '-acf_field_group').val();

            $.ajax({
                url: params.ajaxurl, // domain/wp-admin/admin-ajax.php
                type: 'post',
                data: {
                    'action': 'regenerate_preview',
                    'wp-attachment-download[post_type]': post_type,
                    'wp-attachment-download[published_from]': published_from,
                    'wp-attachment-download[published_to]': published_to,
                    'wp-attachment-download[acf_field_group]': acf_field_group,
                },
                beforeSend: function () {
                    jQuery('#preview-info-spinner').addClass('is-active');
                    $("#preview-info-block").hide();
                },
                success: function (response) {
                    $("#preview-info-block").show();
                    $("#preview-info-text").html(response['message']);
                    if (response['posts_count'] > 0 && response['attachments_count'] > 0) {
                        $("#submit").attr('disabled', false);
                    } else {
                        $("#submit").attr('disabled', true);
                    }

                },
                complete: function () {
                    // event.target.reset();
                    jQuery('#preview-info-spinner').removeClass('is-active');
                },
            });
        });
    });

})(jQuery);
