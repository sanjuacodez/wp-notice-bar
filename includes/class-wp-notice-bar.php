<?php
/**
 * Main plugin class.
 *
 * @package WPNoticeBar
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Class Advanced_Announcement_Banner
 */
class WP_Notice_Bar {

    /**
     * Instance of this class.
     *
     * @var WP_Notice_Bar
     */
    private static $instance;

    /**
     * Admin menu class instance.
     *
     * @var WNB_Admin_Menu
     */
    public $admin_menu;

    /**
     * Settings class instance.
     *
     * @var WNB_Settings
     */
    public $settings;

    /**
     * Assets class instance.
     *
     * @var WNB_Assets
     */
    public $assets;

    /**
     * Display class instance.
     *
     * @var WNB_Display
     */
    public $display;

    /**
     * Get the singleton instance.
     *
     * @return WP_Notice_Bar
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->init_hooks();
        $this->init_components();
    }

    /**
     * Initialize WordPress hooks.
     */
    private function init_hooks() {
        register_activation_hook(WNB_BASENAME, array($this, 'activate'));
        register_deactivation_hook(WNB_BASENAME, array($this, 'deactivate'));
        
        add_action('init', array($this, 'load_textdomain'));
    }

    /**
     * Initialize plugin components.
     */
    private function init_components() {
        // Initialize settings first since other components depend on it
        $this->settings = new WNB_Settings();

        // Initialize admin menu and admin features if in admin area
        if (is_admin()) {
            $this->admin_menu = new WNB_Admin_Menu($this->settings);
            new WNB_Admin(); // Initialize admin features
        }

        // Initialize assets
        $this->assets = new WNB_Assets();

        // Initialize display
        $this->display = new WNB_Display($this->settings);
    }

    /**
     * Plugin activation.
     */
    public function activate() {
        // Check for old settings to migrate
        $old_settings = get_option('aab_settings');
        if ($old_settings) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP Notice Bar - Found old settings to migrate: ' . print_r($old_settings, true));
            }
            update_option('wnb_settings', $old_settings);
            delete_option('aab_settings'); // Remove old settings
        }
        
        // Add default settings if none exist
        if (!get_option('wnb_settings')) {
            $defaults = array(
                // General Settings
                'position' => 'top',
                'type' => 'fixed',
                
                // Design Settings
                'width' => '100%',
                'alignment' => 'center',
                'background_type' => 'color',
                'background_color' => '#ffffff',
                'gradient_start_color' => '#4a90e2',
                'gradient_end_color' => '#7b64ff',
                'gradient_direction' => 'to right',
                'background_image' => '',
                'background_opacity' => '1',
                'content_width' => '1200px',
                'padding' => '15px',
                
                // Content Settings
                'content' => '',
                'enable_schedule' => 'no',
                'schedule_start' => '',
                'schedule_end' => '',
                
                // Animation Settings
                'animation' => 'none',
                'animation_speed' => 'medium',
                'pause_on_hover' => 'yes'
            );
            update_option('wnb_settings', $defaults);
        }

        // Set version
        update_option('wnb_version', WNB_VERSION);
    }

    /**
     * Plugin deactivation.
     */
    public function deactivate() {
        // Clean up if needed
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'wp-notice-bar',
            false,
            dirname(WNB_BASENAME) . '/languages/'
        );
    }

    /**
     * Prevent cloning.
     */
    /**
     * Get default settings by scanning sections.
     *
     * @return array
     */
    private function get_default_settings() {
        $defaults = array();
        
        foreach ($this->sections as $section) {
            foreach ($section['fields'] as $field_id => $field) {
                $defaults[$field_id] = $field['default'];
            }
        }
        
        return $defaults;
    }

    /**
     * Prevent cloning.
     */
    private function __clone() {}

    /**
     * Prevent unserializing.
     */
    public function __wakeup() {}
}
