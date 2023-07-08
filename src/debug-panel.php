<?php

class DebugPanel {
    private $ifDisplay = 'paff_display_debug';
    private $scriptHandle = 'paff-debug';

    public function __construct() {
        $this->ifDisplay = get_option($this->ifDisplay);
    }

    public function gen_debug_panel($mainScriptHandle) {
        if ($this->ifDisplay) {
            error_log('constructing debug panel');
            $this->add_debug_panel();
            $this->enqueue_scripts($mainScriptHandle);
        } else {
            error_log('Debug panel not enabled.');
        }
    }

    public function add_debug_panel() {
        error_log('adding debug panel');
        $file = plugin_dir_path(__FILE__) . 'static/debug-panel.html';
        if (file_exists($file)) {
            readfile($file);
        } else {
            error_log('File not found: ' . $file);
        }
    }

    public function enqueue_scripts($mainScriptHandle) {
        error_log('enqueueing scripts debug panel');
        wp_enqueue_style($this->scriptHandle, plugin_dir_url(__FILE__) . 'static/paff.css');
        wp_enqueue_script($this->scriptHandle, plugin_dir_url(__FILE__) . 'js/debug-panel.js', ['jquery', $mainScriptHandle], '1.0', true);
        wp_localize_script($this->scriptHandle, 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }
}