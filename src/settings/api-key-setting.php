<?php

class ApiKeySetting {
    private $setting_name = 'paff_api_key';

    function register($plugin_slug) {
        register_setting($plugin_slug, $plugin_slug . '_api_key');
    }

    function html() {
        ?>
        <tr valign="top">
            <th scope="row">API Key</th>
            <td><input type="text" name="paff_api_key" value="<?php echo esc_attr(get_option($this->setting_name)); ?>" /></td>
        </tr>
        <?php
    }
}
