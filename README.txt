=== Plugin Name ===
Contributors: rybnitom
Donate link: https://www.wetory.eu/
Tags: attachments, downloads, zip, media
Requires at least: 3.0.1
Tested up to: 5.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin adds functionality to download posts attachments build with ACF file fields from administration. 

== Description ==

If you are adding functionality to your posts using popular Advanced |Custom Fields plugin. This plugin
is focused on fields of type "file". Handful when publishing some posts with attachments whole year and 
once a year you need to download all attachments to send them to third parties. 

You are prompted to select post type, publish date range and ACF filed group you want to extract attachments from.
Then if there is something to download you can hit the button and archive file with attachments is prepared to download.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `wp-attachment-download.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Tools -> Attachments' to start your work.
4. Enjoy!

== Frequently Asked Questions ==

= Download button is disabled. Why? =

You have to select input values and check preview block. If there is nothing to download this button is disabled.

= What to do when download did not start automatically? =

You can find download link in successful request information section under Download button area.

== Screenshots ==

1. Tools page of plugin in administration
2. Successful request to download attachments screen

== Changelog ==

= 1.0.1 =
* Added check if there are some ACF field groups containing file fields before displaying admin form

= 1.0.0 =
* First fully functional tested version of this plugin.

== Upgrade Notice ==

== A brief Markdown Example ==

After installing and activating plugin, you will find new section in Tools called Attachments.
You can use it for downloading all attachments specified by ACF file fields you want.

Prerequisites:
* installed and activated Advanced Custom Field plugin

All you need to do to get you attachments is:

1. Select required post type
2. Select published date range by specifying from and to days
3. If there are more ACF field groups that contains file field you can select only one of them
4. Check preview information
5. Hit Download button if there is something to download
6. Archive ZIP file will be downloaded automatically or you can use link in review under Download button

You can find this plugin on [GitHub](https://github.com/wetory/wp-attachment-download "Your favorite public repository"),
 where you can report issues, review code and commits. Please report all possible problems to make it better. 
