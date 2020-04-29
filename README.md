# WP Attachment Download
If you are adding functionality to your posts using popular [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) plugin. This plugin is focused on fields of type [File](https://www.advancedcustomfields.com/resources/file/). Handful when publishing some posts with attachments whole year and once a year you need to download all attachments to send them to third parties.

You are prompted to select post type, publish date range and ACF filed group you want to extract attachments from.
Then if there is something to download you can hit the button and archive file with attachments is prepared to download.

Official WordPress plugin - [WP Attachment Download](https://wordpress.org/plugins/wp-attachment-download/#description)

## Installation
From your WordPress dashboard

1. Visit Plugins > Add New
2. Search for “WP Attachment Download”
3. Activate WP Attachment Download from your Plugins page
4. Go to Tools -> Attachments to start your work.
5. Enjoy!


## Usage
After installing and activating plugin, you will find new section in Tools called Attachments.
You can use it for downloading all attachments specified by ACF file fields you want.

### Prerequisites

* installed and activated [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) plugin

### Instructions
All you need to do to get you attachments is:

1. Select required post type
2. Select published date range by specifying from and to days
3. If there are more ACF field groups that contains file field you can select only one of them
4. Check preview information
5. Hit Download button if there is something to download
6. Archive ZIP file will be downloaded automatically or you can use link in review under Download button

![Plugin operation screen](https://github.com/wetory/wp-attachment-download/blob/master/assets/screenshot-1.png)

## FAQ
### Download button is disabled. Why?
You have to select input values and check preview block. If there is nothing to download this button is disabled.

### What to do when download did not start automatically?
You can find download link in successful request information section under Download button area.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[GPLv2](http://www.gnu.org/licenses/gpl-2.0.html)
