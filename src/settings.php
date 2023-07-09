<?php

include_once plugin_dir_path( __FILE__ ) . 'settings/api-key-setting.php';
include_once plugin_dir_path( __FILE__ ) . 'settings/display-debug-setting.php';
include_once plugin_dir_path( __FILE__ ) . 'settings/affiliate-partner-setting.php';
include_once plugin_dir_path( __FILE__ ) . 'settings/download-data.php';

class SettingsPage {
    private $settings = [];
    private $plugin_slug = 'paff';
    private $plugin_name = 'Personalized Affiliates';

    function __construct() {
        $this->settings[] = new ApiKeySetting();
        $this->settings[] = new DisplayDebugSetting();
        $this->settings[] = new PartnersSetting();
        $this->settings[] = new DownloadDataSetting();


        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_settings_page']);
    }

    function register_settings() {
        foreach ($this->settings as $setting) {
            $setting->register('paff');
        }
    }

    function add_settings_page() {
        add_options_page(
            $this->plugin_name . ' Settings',
            $this->plugin_name,
            'manage_options',
            $this->plugin_slug,
            [$this, 'settings_page_html']
        );
    }

    function settings_page_html() {
        ?>
        <div class="wrap">
            <h1><?php echo $this->plugin_name; ?> Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->plugin_slug);
                do_settings_sections($this->plugin_slug);
                ?>
                <table class="form-table">
                    <?php
                    foreach ($this->settings as $setting) {
                        $setting->html();
                    }
                    ?>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new SettingsPage();
