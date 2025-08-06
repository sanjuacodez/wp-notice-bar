<?php
/**
 * Admin Menu Class
 *
 * @package WPNoticeBar
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Class WNB_Admin_Menu
 */
class WNB_Admin_Menu {

    /**
     * Settings instance.
     *
     * @var WNB_Settings
     */
    private $settings;

    /**
     * Constructor.
     *
     * @param WNB_Settings $settings Settings instance.
     */
    public function __construct($settings) {
        $this->settings = $settings;
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Add menu page.
     */
    public function add_menu_page() {
        add_menu_page(
            __('WP Notice Bar', 'wp-notice-bar'),
            __('Notice Bar', 'wp-notice-bar'),
            'manage_options',
            'wp-notice-bar',
            array($this, 'render_settings_page'),
            'dashicons-megaphone',
            30
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @param string $hook_suffix The current admin page.
     */
    public function enqueue_scripts($hook_suffix) {
        if ('toplevel_page_wp-notice-bar' !== $hook_suffix) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        wp_enqueue_style(
            'wnb-admin-style',
            plugins_url('assets/admin/css/admin.css', dirname(dirname(__FILE__))),
            array(),
            WNB_VERSION
        );

        wp_enqueue_script(
            'wnb-admin-script',
            plugins_url('assets/admin/js/admin.js', dirname(dirname(__FILE__))),
            array('jquery', 'wp-color-picker', 'media-upload'),
            WNB_VERSION,
            true
        );

        // Enqueue media scripts
        wp_enqueue_media();

        // Add localization for admin scripts
        wp_localize_script('wnb-admin-script', 'wnbAdmin', array(
            'mediaTitle' => __('Choose or Upload Banner Image', 'wp-notice-bar'),
            'mediaButton' => __('Use this image', 'wp-notice-bar'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wnb-admin-nonce')
        ));
    }

    /**
     * Render settings page.
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Debug output
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Notice Bar - Current Settings: ' . print_r(get_option('wnb_settings'), true));
        }

        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        $tabs = array(
            'general' => __('General', 'wp-notice-bar'),
            'design' => __('Design', 'wp-notice-bar'),
            'content' => __('Content', 'wp-notice-bar'),
            'animation' => __('Animation', 'wp-notice-bar')
        );

        ob_start();
        ?>
        <div class="wrap wnb-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <nav class="nav-tab-wrapper wnb-tab-nav">
                <?php foreach ($tabs as $tab_id => $tab_name) : ?>
                    <a href="?page=wp-notice-bar&tab=<?php echo esc_attr($tab_id); ?>" 
                       class="nav-tab <?php echo $current_tab === $tab_id ? 'nav-tab-active' : ''; ?>" 
                       data-tab="<?php echo esc_attr($tab_id); ?>">
                        <?php echo esc_html($tab_name); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <form method="post" action="options.php" class="wnb-settings-form">
                <?php
                settings_fields('wnb_settings');
                
                // Get all sections
                $sections = $this->settings->get_sections();
                
                // Create tab content
                foreach ($tabs as $tab_id => $tab_name) {
                    printf(
                        '<div class="wnb-tab-content" id="tab-%s" style="%s">',
                        esc_attr($tab_id),
                        $tab_id === $current_tab ? 'display: block;' : 'display: none;'
                    );

                    // Only display the section that belongs to this tab
                    if (isset($sections[$tab_id])) {
                        echo '<h2>' . esc_html($sections[$tab_id]['title']) . '</h2>';
                        echo '<table class="form-table" role="presentation">';
                        foreach ($sections[$tab_id]['fields'] as $field_id => $field) {
                            echo '<tr>';
                            echo '<th scope="row">' . esc_html($field['title']) . '</th>';
                            echo '<td>';
                            $this->settings->render_field(array(
                                'id' => $field_id,
                                'type' => $field['type'],
                                'options' => isset($field['options']) ? $field['options'] : array(),
                                'default' => $field['default'],
                                'description' => $field['description'],
                            ));
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    
                    echo '</div>';
                }
                
                submit_button();
                ?>
            </form>
        </div>
        <?php
        echo ob_get_clean();
    }
}
