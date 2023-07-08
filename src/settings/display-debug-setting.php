<?php

class DisplayDebugSetting {
    private $setting_name = 'paff_display_debug';

    function register($plugin_slug) {
        register_setting($plugin_slug, $plugin_slug . '_display_debug');
    }

    function html() {
        ?>
        <tr valign="top">
            <th scope="row">Display the Debug Panel</th>
            <td><input type="checkbox" name="paff_display_debug" value="1" <?php checked(1, get_option($this->setting_name), true); ?> /></td>
        </tr>
        <?php
    }
}
