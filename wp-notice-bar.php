<?php
/**
 * WP Notice Bar
 *
 * @package     WPNoticeBar
 * @author      sanjushankar
 * @copyright   2025 acowebs
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WP Notice Bar
 * Plugin URI:  https://sanjayshankar.me
 * Description: A professional, customizable notice bar system for WordPress.
 * Version:     1.0.0
 * Author:      Sanjay Shankar
 * Author URI:  https://sanjayshankar.me
 * Text Domain: wp-notice-bar
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version.
define('WNB_VERSION', '1.0.0');

// Plugin directory path.
define('WNB_PATH', plugin_dir_path(__FILE__));

// Plugin directory URL.
define('WNB_URL', plugin_dir_url(__FILE__));

// Plugin basename.
define('WNB_BASENAME', plugin_basename(__FILE__));

// Autoloader
require_once WNB_PATH . 'includes/class-wnb-autoloader.php';

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 */
function wnb_init() {
    return WP_Notice_Bar::get_instance();
}

// Initialize the plugin
add_action('plugins_loaded', 'wnb_init');
