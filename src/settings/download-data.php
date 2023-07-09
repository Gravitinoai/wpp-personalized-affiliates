<?php

class DownloadDataSetting {
    private $setting_name = 'download_data';
    private $data_file_url;

    function register($plugin_slug) {
        register_setting($plugin_slug, $plugin_slug . '_download_data');
        $this->data_file_url = plugins_url('/../data.txt', __FILE__);
    }

    function html() {
        ?>
        <tr valign="top">
            <a href="<?php echo $this->data_file_url; ?>" download class="button">Download Data</a>
        </tr>
        <?php
    }
}
