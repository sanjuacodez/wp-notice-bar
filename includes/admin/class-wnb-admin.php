<?php
/**
 * Admin Class
 *
 * @package WPNotic            wp_enqueue_script(
            'wnb-admin',
            plugin_dir_url($this->plugin_file) . 'assets/admin/js/admin.js',r
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Class WNB_Admin
 */
class WNB_Admin {

    /**
     * Plugin file path
     *
     * @var string
     */
    private $plugin_file;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->plugin_file = WNB_PATH . 'wp-notice-bar.php';
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @param string $hook The current admin page.
     */
    public function enqueue_scripts($hook) {
        // Only load on our plugin's settings page
        if ('toplevel_page_wnb-settings' !== $hook) {
            return;
        }

        // Add WordPress color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Add our admin script
        wp_enqueue_script(
            'wnb-admin',
            plugin_dir_url(WNB_PLUGIN_FILE) . 'assets/admin/js/admin.js',
            array('jquery', 'wp-color-picker'),
            WNB_VERSION,
            true
        );
    }
}
