<?php
/**
 * Autoloader for WP Notice Bar.
 *
 * @package WPNoticeBar
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Class WNB_Autoloader
 */
class WNB_Autoloader {

    /**
     * Stores all the classes that map to our loading methods.
     *
     * @var array The mappings for classes to file paths.
     */
    private static $class_map = array(
        'WP_Notice_Bar' => 'includes/class-wp-notice-bar.php',
        'WNB_Admin_Menu' => 'includes/admin/class-wnb-admin-menu.php',
        'WNB_Admin'     => 'includes/admin/class-wnb-admin.php',
        'WNB_Settings'   => 'includes/admin/class-wnb-settings.php',
        'WNB_Assets'     => 'includes/class-wnb-assets.php',
        'WNB_Display'    => 'includes/class-wnb-display.php',
    );

    /**
     * Register the autoloader.
     */
    public static function init() {
        spl_autoload_register(array(self::class, 'autoload'));
    }

    /**
     * Autoloader method.
     *
     * @param string $class_name The name of the class to load.
     */
    public static function autoload($class_name) {
        if (isset(self::$class_map[$class_name])) {
            $file = WNB_PATH . self::$class_map[$class_name];
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}

// Initialize the autoloader
WNB_Autoloader::init();
