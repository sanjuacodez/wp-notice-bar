<?php
/**
 * Assets Class
 *
 * @package WPNoticeBar
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Class WNB_Assets
 */
class WNB_Assets {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Enqueue frontend assets.
     */
    public function enqueue_frontend_assets() {
        // Debug: Check if our style file exists
        $style_path = WNB_PATH . 'assets/css/banner.css';
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Notice Bar - Style path: ' . $style_path);
            error_log('WP Notice Bar - Style file exists: ' . (file_exists($style_path) ? 'yes' : 'no'));
        }
        
        // Enqueue banner styles
        wp_enqueue_style(
            'wnb-banner',
            WNB_URL . 'assets/css/banner.css',
            array(),
            WNB_VERSION
        );
    }

    /**
     * Enqueue admin assets.
     *
     * @param string $hook The current admin page.
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_wp-notice-bar' !== $hook) {
            return;
        }

        // Enqueue WordPress color picker
        wp_enqueue_style('wp-color-picker');
        
        // Enqueue WordPress media uploader
        wp_enqueue_media();

        // Enqueue admin styles
        wp_enqueue_style(
            'wnb-admin',
            WNB_URL . 'assets/css/admin.css',
            array(),
            WNB_VERSION
        );

        // Enqueue admin scripts
        wp_enqueue_script(
            'wnb-admin',
            WNB_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            WNB_VERSION,
            true
        );

        // Pass data to admin script
        wp_localize_script('wnb-admin', 'wnbAdmin', array(
            'mediaTitle' => __('Choose or Upload Banner Image', 'wp-notice-bar'),
            'mediaButton' => __('Use this image', 'wp-notice-bar'),
        ));
    }
}
