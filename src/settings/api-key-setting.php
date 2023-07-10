<?php

class ApiKeySetting {
    private $setting_name = 'paff_api_key';
    private $project_id = 'google_project_id';

    function register($plugin_slug) {
        register_setting($plugin_slug, $this->setting_name);
        register_setting($plugin_slug, $this->project_id);
    }

    function html() {
        ?>
        <tr valign="top">
            <th scope="row">API Key File content</th>
            <td>
                <textarea cols="75" rows="5" name="<?php echo $this->setting_name; ?>"><?php echo esc_attr(get_option($this->setting_name)); ?></textarea>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Google project id</th>
            <td>
                <textarea cols="75" name="<?php echo $this->project_id; ?>"><?php echo esc_attr(get_option($this->project_id)); ?></textarea>
            </td>
        </tr>
        <?php
    }
}
