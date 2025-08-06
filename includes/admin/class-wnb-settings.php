<?php
/**
 * Settings Class
 *
 * @package WPNoticeBar
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Class WNB_Settings
 */
class WNB_Settings {

    /**
     * Settings sections.
     *
     * @var array
     */
    private $sections;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->init_sections();
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Initialize settings sections.
     */
    private function init_sections() {
        $this->sections = array(
            'general' => array(
                'title' => __('Notice Bar Settings', 'wp-notice-bar'),
                'fields' => array(
                    'position' => array(
                        'title' => __('Position', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'top' => __('Top', 'wp-notice-bar'),
                            'bottom' => __('Bottom', 'wp-notice-bar'),
                        ),
                        'default' => 'top',
                        'description' => __('Choose where to display the notice bar.', 'wp-notice-bar'),
                    ),
                    'type' => array(
                        'title' => __('Type', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'fixed' => __('Fixed', 'wp-notice-bar'),
                            'relative' => __('Relative', 'wp-notice-bar'),
                        ),
                        'default' => 'fixed',
                        'description' => __('Fixed will stick to the viewport, relative will scroll with the content.', 'wp-notice-bar'),
                    ),
                ),
            ),
            'design' => array(
                'title' => __('Design Settings', 'wp-notice-bar'),
                'fields' => array(
                    'width' => array(
                        'title' => __('Width', 'wp-notice-bar'),
                        'type' => 'text',
                        'default' => '100%',
                        'description' => __('Width of the notice bar (e.g., 100%, 1200px)', 'wp-notice-bar'),
                    ),
                    'alignment' => array(
                        'title' => __('Container Alignment', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'left' => __('Left', 'wp-notice-bar'),
                            'center' => __('Center', 'wp-notice-bar'),
                            'right' => __('Right', 'wp-notice-bar'),
                        ),
                        'default' => 'center',
                        'description' => __('Align the notice bar container when width is less than 100%', 'wp-notice-bar'),
                    ),
                    'background_type' => array(
                        'title' => __('Background Type', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'color' => __('Solid Color', 'wp-notice-bar'),
                            'gradient' => __('Gradient', 'wp-notice-bar'),
                            'image' => __('Image', 'wp-notice-bar'),
                        ),
                        'default' => 'color',
                        'description' => __('Choose the type of background for the notice bar.', 'wp-notice-bar'),
                        'class' => 'wnb-background-type',
                    ),
                    // Solid Color Options
                    'background_color' => array(
                        'title' => __('Background Color', 'wp-notice-bar'),
                        'type' => 'color',
                        'default' => '#ffffff',
                        'description' => __('Background color of the notice bar.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-background-color',
                        'depends' => array('background_type' => 'color'),
                    ),
                    // Gradient Options
                    'gradient_start_color' => array(
                        'title' => __('Gradient Start Color', 'wp-notice-bar'),
                        'type' => 'color',
                        'default' => '#4a90e2',
                        'description' => __('Starting color for gradient background.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-gradient',
                        'depends' => array('background_type' => 'gradient'),
                    ),
                    'gradient_end_color' => array(
                        'title' => __('Gradient End Color', 'wp-notice-bar'),
                        'type' => 'color',
                        'default' => '#7b64ff',
                        'description' => __('Ending color for gradient background.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-gradient',
                        'depends' => array('background_type' => 'gradient'),
                    ),
                    'gradient_direction' => array(
                        'title' => __('Gradient Direction', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'to right' => __('Left to Right', 'wp-notice-bar'),
                            'to left' => __('Right to Left', 'wp-notice-bar'),
                            'to bottom' => __('Top to Bottom', 'wp-notice-bar'),
                            'to top' => __('Bottom to Top', 'wp-notice-bar'),
                            '45deg' => __('45 Degree', 'wp-notice-bar'),
                            '-45deg' => __('Reverse 45 Degree', 'wp-notice-bar'),
                        ),
                        'default' => 'to right',
                        'description' => __('Direction of the gradient effect.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-gradient',
                        'depends' => array('background_type' => 'gradient'),
                    ),
                    // Image Options
                    'background_image' => array(
                        'title' => __('Background Image', 'wp-notice-bar'),
                        'type' => 'image',
                        'default' => '',
                        'description' => __('Upload or choose a background image.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-image',
                        'depends' => array('background_type' => 'image'),
                    ),
                    'background_image_color' => array(
                        'title' => __('Background Color (behind image)', 'wp-notice-bar'),
                        'type' => 'color',
                        'default' => '#ffffff',
                        'description' => __('Background color to show behind or if image fails to load.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-image',
                        'depends' => array('background_type' => 'image'),
                    ),
                    'background_position' => array(
                        'title' => __('Background Position', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'left top' => __('Left Top', 'wp-notice-bar'),
                            'left center' => __('Left Center', 'wp-notice-bar'),
                            'left bottom' => __('Left Bottom', 'wp-notice-bar'),
                            'center top' => __('Center Top', 'wp-notice-bar'),
                            'center center' => __('Center Center', 'wp-notice-bar'),
                            'center bottom' => __('Center Bottom', 'wp-notice-bar'),
                            'right top' => __('Right Top', 'wp-notice-bar'),
                            'right center' => __('Right Center', 'wp-notice-bar'),
                            'right bottom' => __('Right Bottom', 'wp-notice-bar'),
                        ),
                        'default' => 'center center',
                        'description' => __('Position of the background image.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-image',
                        'depends' => array('background_type' => 'image'),
                    ),
                    'background_repeat' => array(
                        'title' => __('Background Repeat', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'no-repeat' => __('No Repeat', 'wp-notice-bar'),
                            'repeat' => __('Repeat', 'wp-notice-bar'),
                            'repeat-x' => __('Repeat Horizontally', 'wp-notice-bar'),
                            'repeat-y' => __('Repeat Vertically', 'wp-notice-bar'),
                        ),
                        'default' => 'no-repeat',
                        'description' => __('How the background image should repeat.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-image',
                        'depends' => array('background_type' => 'image'),
                    ),
                    'background_opacity' => array(
                        'title' => __('Background Opacity', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            '1' => '100%',
                            '0.9' => '90%',
                            '0.8' => '80%',
                            '0.7' => '70%',
                            '0.6' => '60%',
                            '0.5' => '50%',
                            '0.4' => '40%',
                            '0.3' => '30%',
                            '0.2' => '20%',
                            '0.1' => '10%',
                        ),
                        'default' => '1',
                        'description' => __('Set the opacity of the background.', 'wp-notice-bar'),
                        'class' => 'wnb-background-field wnb-image',
                        'depends' => array('background_type' => 'image'),
                    ),
                    'content_width' => array(
                        'title' => __('Content Width', 'wp-notice-bar'),
                        'type' => 'text',
                        'default' => '1200px',
                        'description' => __('Maximum width of the content inside the notice bar.', 'wp-notice-bar'),
                    ),
                    'padding' => array(
                        'title' => __('Padding', 'wp-notice-bar'),
                        'type' => 'text',
                        'default' => '15px',
                        'description' => __('Padding inside the notice bar (e.g., 15px, 1rem)', 'wp-notice-bar'),
                    ),
                ),
            ),
            'content' => array(
                'title' => __('Content Settings', 'wp-notice-bar'),
                'fields' => array(
                    'content' => array(
                        'title' => __('Notice Content', 'wp-notice-bar'),
                        'type' => 'editor',
                        'default' => '',
                        'description' => __('The content to display in your notice bar. Supports HTML and shortcodes.', 'wp-notice-bar'),
                    ),
                    'enable_schedule' => array(
                        'title' => __('Enable Scheduling', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'no' => __('No', 'wp-notice-bar'),
                            'yes' => __('Yes', 'wp-notice-bar'),
                        ),
                        'default' => 'no',
                        'description' => sprintf(
                            /* translators: %s: current time */
                            __('Set a schedule for when the notice bar should be displayed. Current server time: %s', 'wp-notice-bar'),
                            current_time('Y-m-d H:i:s')
                        ),
                    ),
                    'schedule_start' => array(
                        'title' => __('Start Date/Time', 'wp-notice-bar'),
                        'type' => 'datetime',
                        'default' => '',
                        'description' => __('When the notice bar should start displaying. Leave empty for immediate start.', 'wp-notice-bar'),
                    ),
                    'schedule_end' => array(
                        'title' => __('End Date/Time', 'wp-notice-bar'),
                        'type' => 'datetime',
                        'default' => '',
                        'description' => __('When the notice bar should stop displaying. Leave empty to display indefinitely.', 'wp-notice-bar'),
                    ),
                    'enable_timer' => array(
                        'title' => __('Show Countdown Timer', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'no' => __('No', 'wp-notice-bar'),
                            'yes' => __('Yes', 'wp-notice-bar'),
                        ),
                        'default' => 'no',
                        'description' => __('Display a countdown timer showing time remaining until the end date.', 'wp-notice-bar'),
                        'depends' => array('enable_schedule' => 'yes'),
                    ),
                ),
            ),
            'animation' => array(
                'title' => __('Animation Settings', 'wp-notice-bar'),
                'fields' => array(
                    'animation' => array(
                        'title' => __('Animation Type', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'none' => __('None', 'wp-notice-bar'),
                            'scroll' => __('Scroll', 'wp-notice-bar'),
                            'blink' => __('Blink', 'wp-notice-bar'),
                            'fade' => __('Fade In', 'wp-notice-bar'),
                            'slide-down' => __('Slide Down', 'wp-notice-bar'),
                            'slide-up' => __('Slide Up', 'wp-notice-bar'),
                            'bounce' => __('Bounce', 'wp-notice-bar'),
                            'pulse' => __('Pulse', 'wp-notice-bar'),
                        ),
                        'default' => 'none',
                        'description' => __('Choose animation effect for the notice bar.', 'wp-notice-bar'),
                    ),
                    'animation_speed' => array(
                        'title' => __('Animation Speed', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'slow' => __('Slow', 'wp-notice-bar'),
                            'medium' => __('Medium', 'wp-notice-bar'),
                            'fast' => __('Fast', 'wp-notice-bar'),
                        ),
                        'default' => 'medium',
                        'description' => __('Speed of the animation effect.', 'wp-notice-bar'),
                    ),
                    'pause_on_hover' => array(
                        'title' => __('Pause on Hover', 'wp-notice-bar'),
                        'type' => 'select',
                        'options' => array(
                            'yes' => __('Yes', 'wp-notice-bar'),
                            'no' => __('No', 'wp-notice-bar'),
                        ),
                        'default' => 'yes',
                        'description' => __('Pause animation when mouse hovers over the notice bar.', 'wp-notice-bar'),
                    ),
                ),
            ),
        );
    }

    /**
     * Register settings.
     */
    public function register_settings() {
        register_setting(
            'wnb_settings',
            'wnb_settings',
            array(
                'sanitize_callback' => array($this, 'sanitize_settings'),
                'default' => $this->get_default_settings()
            )
        );

        // Debug output
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Notice Bar - Registering settings sections: ' . print_r($this->sections, true));
        }

        foreach ($this->sections as $section_id => $section) {
            add_settings_section(
                $section_id,
                $section['title'],
                null,
                'wnb_settings'
            );

            foreach ($section['fields'] as $field_id => $field) {
                add_settings_field(
                    $field_id,
                    $field['title'],
                    array($this, 'render_field'),
                    'wnb_settings',
                    $section_id,
                    array(
                        'id' => $field_id,
                        'type' => $field['type'],
                        'options' => isset($field['options']) ? $field['options'] : array(),
                        'default' => $field['default'],
                        'description' => $field['description'],
                    )
                );
            }
        }
    }

    /**
     * Get sections.
     *
     * @return array
     */
    public function get_sections() {
        return $this->sections;
    }

    /**
     * Get field information from sections.
     *
     * @param string $field_id The field ID to look for.
     * @return array|null Field information if found, null otherwise.
     */
    private function get_field_info($field_id) {
        foreach ($this->sections as $section) {
            if (isset($section['fields'][$field_id])) {
                return $section['fields'][$field_id];
            }
        }
        return null;
    }

    /**
     * Render a settings field.
     *
     * @param array $args Field arguments.
     */
    public function render_field($args) {
        $options = get_option('wnb_settings');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        
        // Get field info from sections
        $field_info = $this->get_field_info($args['id']);
        $field_class = isset($field_info['class']) ? $field_info['class'] : '';
        $depends = isset($field_info['depends']) ? ' data-depends="' . esc_attr(json_encode($field_info['depends'])) . '"' : '';
        
        echo '<div class="wnb-field-wrap ' . esc_attr($field_class) . '"' . $depends . '>';
        
        switch ($args['type']) {
            case 'text':
                printf(
                    '<input type="text" id="%1$s" name="wnb_settings[%1$s]" value="%2$s" class="regular-text">',
                    esc_attr($args['id']),
                    esc_attr($value)
                );
                break;

            case 'select':
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP Notice Bar - Rendering select field: ' . $args['id']);
                    error_log('WP Notice Bar - Current value: ' . $value);
                    error_log('WP Notice Bar - Available options: ' . print_r($args['options'], true));
                }
                
                echo '<select id="' . esc_attr($args['id']) . '" name="wnb_settings[' . esc_attr($args['id']) . ']">';
                foreach ($args['options'] as $key => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($key),
                        selected($value, $key, false),
                        esc_html($label)
                    );
                }
                echo '</select>';
                break;

            case 'color':
                printf(
                    '<input type="text" id="%1$s" name="wnb_settings[%1$s]" value="%2$s" class="wnb-color-picker">',
                    esc_attr($args['id']),
                    esc_attr($value)
                );
                break;
                
            case 'image':
                $image_url = !empty($value) ? wp_get_attachment_image_url($value, 'medium') : '';
                ?>
                <div class="wnb-image-upload-wrap">
                    <input type="hidden" 
                           id="<?php echo esc_attr($args['id']); ?>" 
                           name="wnb_settings[<?php echo esc_attr($args['id']); ?>]" 
                           value="<?php echo esc_attr($value); ?>"
                           data-field="image">
                    
                    <div class="wnb-image-preview" style="<?php echo !empty($image_url) ? '' : 'display:none;'; ?>">
                        <?php if ($image_url): ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width: 100%; height: auto;">
                        <?php endif; ?>
                    </div>
                    
                    <div class="wnb-image-controls">
                        <input type="button" 
                               class="button wnb-upload-image" 
                               value="<?php esc_attr_e('Choose Image', 'wp-notice-bar'); ?>">
                        
                        <input type="button" 
                               class="button wnb-remove-image" 
                               value="<?php esc_attr_e('Remove Image', 'wp-notice-bar'); ?>"
                               style="<?php echo !empty($value) ? '' : 'display:none;'; ?>">
                    </div>
                </div>
                <?php
                break;

            case 'datetime':
                ?>
                <input type="datetime-local" 
                       id="<?php echo esc_attr($args['id']); ?>" 
                       name="wnb_settings[<?php echo esc_attr($args['id']); ?>]" 
                       value="<?php echo esc_attr($value ? date('Y-m-d\TH:i', strtotime($value)) : ''); ?>" 
                       class="wnb-datetime">
                <?php
                break;

            case 'editor':
                wp_editor(
                    $value,
                    'wnb_' . $args['id'],
                    array(
                        'textarea_name' => 'wnb_settings[' . esc_attr($args['id']) . ']',
                        'textarea_rows' => 8,
                        'media_buttons' => true,
                        'teeny' => true,
                    )
                );
                break;
        }

        if (!empty($args['description'])) {
            printf(
                '<p class="description">%s</p>',
                wp_kses_post($args['description'])
            );
        }
        
        echo '</div>';
    }

    /**
     * Sanitize settings.
     *
     * @param array $input The input array to sanitize.
     * @return array
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
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Notice Bar - Default settings: ' . print_r($defaults, true));
        }
        
        return $defaults;
    }

    /**
     * Sanitize settings.
     *
     * @param array $input The input array to sanitize.
     * @return array
     */
    public function sanitize_settings($input) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Notice Bar - Sanitizing settings input: ' . print_r($input, true));
            error_log('WP Notice Bar - Current sections: ' . print_r($this->sections, true));
        }
        
        $sanitized = array();
        
        // Get existing settings to preserve values that aren't being updated
        $existing_settings = get_option('wnb_settings', array());

        foreach ($this->sections as $section) {
            foreach ($section['fields'] as $field_id => $field) {
                // Start with existing value or default
                $value = isset($existing_settings[$field_id]) ? $existing_settings[$field_id] : $field['default'];
                
                // If the field is in the input, process it
                if (isset($input[$field_id])) {
                    switch ($field['type']) {
                        case 'text':
                            $value = sanitize_text_field($input[$field_id]);
                            break;
                        case 'select':
                            if (array_key_exists($input[$field_id], $field['options'])) {
                                $value = sanitize_text_field($input[$field_id]);
                            }
                            break;
                        case 'color':
                            $color = sanitize_hex_color($input[$field_id]);
                            if ($color) {
                                $value = $color;
                            }
                            break;
                        case 'editor':
                            $value = wp_kses_post($input[$field_id]);
                            break;
                        case 'image':
                            $image_id = absint($input[$field_id]);
                            if ($image_id > 0 && wp_attachment_is_image($image_id)) {
                                $value = $image_id;
                            } elseif (empty($input[$field_id])) {
                                $value = '';
                            }
                            break;
                        case 'datetime':
                            if (!empty($input[$field_id])) {
                                $datetime = DateTime::createFromFormat('Y-m-d\TH:i', $input[$field_id]);
                                if ($datetime !== false) {
                                    $value = $datetime->format('Y-m-d H:i:s');
                                }
                            } elseif ($input[$field_id] === '') {
                                $value = '';
                            }
                            break;
                    }
                }
                
                $sanitized[$field_id] = $value;
            }
        }

        return $sanitized;
    }
}
