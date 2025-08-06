<?php
/**
 * Banner Display Class
 *
 * @package WPNoticeBar
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Class AAB_Display
 */
class WNB_Display {

    /**
     * Settings instance.
     *
     * @var WNB_Settings
     */
    private $settings;

    /**
     * Stores pseudo-element styles for background images.
     *
     * @var string
     */
    private $pseudo_element_styles = '';

    /**
     * Convert hex color to rgba.
     *
     * @param string $hex Hex color code.
     * @param float $opa                case 'bou                case 'pulse':
                    $css .= "
                        .wnb-banner {
                            animation: wnbPulse 2s ease infinite;
                        }
                        " . ($pause_on_hover === 'yes' ? "
                        .wnb-banner:hover {
                            animation-play-state: paused;
                        }" : "") . "
                        @keyframes wnbPulse {                $css .= "
                        .wnb-banner {
                            animation: wnbBounce 1s ease infinite;
                        }
                        " . ($pause_on_hover === 'yes' ? "
                        .wnb-banner:hover {
                            animation-play-state: paused;
                        }" : "") . "
                        @keyframes wnbBounce {ty value.
     * @return string RGBA color value.
     */
    private function hex_to_rgba($hex, $opacity) {
        if (empty($hex)) {
            return 'rgba(255, 255, 255, ' . $opacity . ')';
        }

        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "rgba($r, $g, $b, $opacity)";
    }

    /**
     * Constructor.
     *
     * @param WNB_Settings $settings Settings instance.
     */
    public function __construct($settings) {
        $this->settings = $settings;
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        // Add display hooks
        add_action('wp_footer', array($this, 'render_banner'), 10);
        add_action('wp_body_open', array($this, 'maybe_render_banner_top'), 5);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        
        // Handle admin bar
        add_action('admin_bar_init', array($this, 'handle_admin_bar'));
        
        // Add body class for admin bar
        add_filter('body_class', array($this, 'add_body_classes'));
    }
    
    /**
     * Add body classes.
     *
     * @param array $classes Array of body classes.
     * @return array
     */
    public function add_body_classes($classes) {
        $options = get_option('wnb_settings', array());
        $position = isset($options['position']) ? $options['position'] : 'top';
        $type = isset($options['type']) ? $options['type'] : 'fixed';
        
        if ($position === 'top' && $type === 'fixed') {
            $classes[] = 'has-wnb-banner';
            $classes[] = 'has-wnb-banner-top';
        } elseif ($position === 'bottom' && $type === 'fixed') {
            $classes[] = 'has-wnb-banner';
            $classes[] = 'has-wnb-banner-bottom';
        }
        
        return $classes;
    }
    
    /**
     * Handle rendering for top position in relative mode.
     */
    public function maybe_render_banner_top() {
        $options = get_option('wnb_settings', array());
        $position = isset($options['position']) ? $options['position'] : 'top';
        $type = isset($options['type']) ? $options['type'] : 'fixed';
        
        if ($type === 'relative' && $position === 'top') {
            $this->output_banner_html();
        }
    }
    
    /**
     * Handle admin bar adjustments.
     */
    public function handle_admin_bar() {
        if (!is_admin_bar_showing()) {
            return;
        }
        
        $options = wp_parse_args(get_option('wnb_settings', array()), array(
            'position' => 'top',
            'type' => 'fixed',
            'padding' => '15px'
        ));
        
        if ($options['type'] === 'fixed' && $options['position'] === 'top') {
            add_action('wp_head', function() use ($options) {
                // Calculate the total height of the banner including padding
                $total_height = 'calc(' . $options['padding'] . ' * 2)';
                
                $styles = '
                    .wnb-banner.wnb-top.wnb-fixed {
                        top: 32px;
                    }
                    
                    /* Adjust header positioning when admin bar is present */
                    body.admin-bar.has-wnb-banner-top #masthead,
                    body.admin-bar.has-wnb-banner-top header.site-header,
                    body.admin-bar.has-wnb-banner-top .header-area,
                    body.admin-bar.has-wnb-banner-top .main-header,
                    body.admin-bar.has-wnb-banner-top .site-header,
                    body.admin-bar.has-wnb-banner-top .header-wrapper,
                    body.admin-bar.has-wnb-banner-top #header {
                        top: calc(' . $total_height . ' + 32px);
                    }
                    
                    @media screen and (max-width: 782px) {
                        .wnb-banner.wnb-top.wnb-fixed {
                            top: 46px;
                        }
                        
                        body.admin-bar.has-wnb-banner-top #masthead,
                        body.admin-bar.has-wnb-banner-top header.site-header,
                        body.admin-bar.has-wnb-banner-top .header-area,
                        body.admin-bar.has-wnb-banner-top .main-header,
                        body.admin-bar.has-wnb-banner-top .site-header,
                        body.admin-bar.has-wnb-banner-top .header-wrapper,
                        body.admin-bar.has-wnb-banner-top #header {
                            top: calc(' . $total_height . ' + 46px);
                        }
                    }';
                
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP Notice Bar - Adding admin bar and header styles: ' . $styles);
                }
                
                echo '<style>' . $styles . '</style>';
            }, 1000); // High priority to ensure it loads after other styles
        }
    }

    /**
     * Enqueue frontend styles.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'wnb-banner-style',
            plugins_url('assets/css/banner.css', dirname(__FILE__)),
            array(),
            WNB_VERSION
        );

        // Get settings
        $options = get_option('wnb_settings', array());
        
        // Generate dynamic CSS
        $css = $this->generate_dynamic_css($options);
        
        // Add inline styles
        wp_add_inline_style('wnb-banner-style', $css);
    }

    /**
     * Generate dynamic CSS based on settings.
     *
     * @param array $options Plugin settings.
     * @return string
     */
    private function generate_dynamic_css($options) {
        $position = isset($options['position']) ? $options['position'] : 'top';
        $type = isset($options['type']) ? $options['type'] : 'fixed';
        $width = isset($options['width']) ? $options['width'] : '100%';
        $content_width = isset($options['content_width']) ? $options['content_width'] : '1200px';
        $padding = isset($options['padding']) ? $options['padding'] : '15px';

        // Get background settings
        $background_type = isset($options['background_type']) ? $options['background_type'] : 'color';
        $background_color = isset($options['background_color']) ? $options['background_color'] : '#ffffff';
        $gradient_start = isset($options['gradient_start_color']) ? $options['gradient_start_color'] : '#4a90e2';
        $gradient_end = isset($options['gradient_end_color']) ? $options['gradient_end_color'] : '#7b64ff';
        $gradient_direction = isset($options['gradient_direction']) ? $options['gradient_direction'] : 'to right';
        $background_image = isset($options['background_image']) ? wp_get_attachment_url($options['background_image']) : '';
        $opacity = isset($options['background_opacity']) ? $options['background_opacity'] : '1';

        // Get alignment
        $alignment = isset($options['alignment']) ? $options['alignment'] : 'center';
        
        // Calculate margin based on alignment
        $margin_left = 'auto';
        $margin_right = 'auto';
        
        if ($alignment === 'left') {
            $margin_right = 'initial';
        } elseif ($alignment === 'right') {
            $margin_left = 'initial';
        }
        
        // Generate background CSS based on type
        $background_props = array();
        
        switch ($background_type) {
            case 'color':
                $rgba_color = $this->hex_to_rgba($background_color, $opacity);
                $background_props[] = "background-color: {$rgba_color};";
                break;
            case 'gradient':
                $rgba_start = $this->hex_to_rgba($gradient_start, $opacity);
                $rgba_end = $this->hex_to_rgba($gradient_end, $opacity);
                $background_props[] = "background-image: linear-gradient({$gradient_direction}, {$rgba_start}, {$rgba_end});";
                break;
            case 'image':
                if ($background_image) {
                    $background_props[] = "position: relative;";
                    $background_props[] = "overflow: hidden;";
                    
                    // Add background color behind image if set
                    if (!empty($options['background_image_color'])) {
                        $background_props[] = "background-color: {$options['background_image_color']};";
                    }
                    
                    // Add pseudo-element styles after the main banner styles are generated
                    $this->pseudo_element_styles = "
                    .wnb-banner::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-image: url('{$background_image}');
                        background-size: cover;
                        background-position: center;
                        opacity: {$opacity};
                        z-index: 0;
                    }
                    .wnb-banner-content {
                        position: relative;
                        z-index: 1;
                    }";
                }
                break;
        }
        
        // Join background properties with semicolons
        $background_css = implode("\n                ", $background_props);

        // Start with basic banner styles
        $css = "
            /* Ensure consistent box model */
            .wnb-banner,
            .wnb-banner * {
                box-sizing: border-box;
            }
            
            .wnb-countdown {
                display: inline-block;
                margin-left: 15px;
                font-weight: bold;
            }
            
            .wnb-countdown-item {
                display: inline-block;
                margin: 0 5px;
                padding: 2px 5px;
                background: rgba(0, 0, 0, 0.1);
                border-radius: 3px;
            }
            
            .wnb-countdown-label {
                font-size: 0.8em;
                opacity: 0.8;
                margin-left: 2px;
            }
            
            .wnb-banner {
                width: {$width};
                margin-left: {$margin_left};
                margin-right: {$margin_right};
                {$background_css}
            }";
            
        // Add position-specific styles
        if ($type === 'fixed') {
            // Calculate the total height of the banner including padding
            $total_height = 'calc(' . $padding . ' * 2)'; // Two times padding (top and bottom)
            
            $css .= "
            .wnb-banner.wnb-fixed {
                position: fixed;
                left: 0;
                right: 0;
                z-index: 999999;
            }
            
            .wnb-banner.wnb-fixed.wnb-" . esc_attr($position) . " {
                " . esc_attr($position) . ": 0;
            }
            
            /* Don't add extra spacing when admin bar is present */
            body.admin-bar .wnb-banner.wnb-fixed.wnb-top {
                top: 0;
            }";

            // Add transition for smooth padding adjustments
            if ($position === 'top') {
                $css .= "
                body.has-wnb-banner-top {
                    transition: padding-top 0.3s ease-in-out;
                }
                
                /* Adjust common header selectors */
                body.has-wnb-banner-top #masthead,
                body.has-wnb-banner-top header.site-header,
                body.has-wnb-banner-top .header-area,
                body.has-wnb-banner-top .main-header,
                body.has-wnb-banner-top .site-header,
                body.has-wnb-banner-top .header-wrapper,
                body.has-wnb-banner-top #header {
                    transition: top 0.3s ease-in-out;
                }";
            } else {
                // For bottom position, add transition
                $css .= "
                body.has-wnb-banner-bottom {
                    transition: padding-bottom 0.3s ease-in-out;
                }";
            }
        } else {
            $css .= "
            .wnb-banner.wnb-relative {
                position: relative;
                width: 100%;
            }";
        }
            
        $css .= "
            .wnb-banner-content {
                max-width: {$content_width};
                padding: {$padding} 0;
                text-align: " . esc_attr($alignment) . ";
                box-sizing: border-box;
                line-height: 1.4;
            }";

        // Handle fixed position
        if ($type === 'fixed') {
            // Add body padding when fixed
            $adminBarHeight = is_admin_bar_showing() ? '32px' : '0';
            
            $css .= "
            body {
                " . ($position === 'top' ? 'padding-top' : 'padding-bottom') . ": " . $padding . ";
            }
            
            @media screen and (max-width: 782px) {
                .wnb-banner.wnb-admin-bar-top {
                    top: 46px;
                }
                body.admin-bar {
                    " . ($position === 'top' ? 'padding-top: calc(' . $padding . ' + 46px);' : '') . "
                }
            }
            
            @media screen and (min-width: 783px) {
                .wnb-banner.wnb-admin-bar-top {
                    top: 32px;
                }
                body.admin-bar {
                    " . ($position === 'top' ? 'padding-top: calc(' . $padding . ' + 32px);' : '') . "
                }
            }";
        }

            $css .= "
            .wnb-banner-content {
                max-width: {$content_width};
                margin: 0 auto;
                padding: {$padding} 0;
                box-sizing: border-box;
                line-height: 1.4;
            }";        // Get animation settings
        $animation = isset($options['animation']) ? $options['animation'] : 'none';
        $animation_speed = isset($options['animation_speed']) ? $options['animation_speed'] : 'medium';
        $pause_on_hover = isset($options['pause_on_hover']) ? $options['pause_on_hover'] : 'yes';
        
        // Append pseudo-element styles if we have them
        if (!empty($this->pseudo_element_styles)) {
            $css .= $this->pseudo_element_styles;
        }

        // Set animation duration based on speed
        $duration_map = array(
            'slow' => '30s',
            'medium' => '20s',
            'fast' => '10s'
        );
        $duration = $duration_map[$animation_speed];

        // Add animation styles if enabled
        if ($animation !== 'none') {
            switch ($animation) {
                case 'scroll':
                    $css .= "
                        .wnb-banner-content {
                            white-space: nowrap;
                            overflow: hidden;
                        }
                        .wnb-banner-content .wnb-content-inner {
                            display: inline-block;
                            padding-left: 100%;
                            animation: wnbScroll {$duration} linear infinite;
                        }
                        " . ($pause_on_hover === 'yes' ? "
                        .wnb-banner:hover .wnb-content-inner {
                            animation-play-state: paused;
                        }" : "") . "
                        @keyframes wnbScroll {
                            0% { transform: translateX(0); }
                            100% { transform: translateX(-200%); }
                        }
                    ";
                    break;

                case 'blink':
                    $blink_duration = str_replace('s', '', $duration) / 10 . 's';
                    $css .= "
                        .wnb-banner-content .wnb-content-inner {
                            display: inline-block;
                            width: 100%;
                            animation: wnbBlink {$blink_duration} ease-in-out infinite;
                        }
                        " . ($pause_on_hover === 'yes' ? "
                        .wnb-banner:hover .wnb-content-inner {
                            animation-play-state: paused;
                            opacity: 1;
                        }" : "") . "
                        @keyframes wnbBlink {
                            0%, 100% { opacity: 1; }
                            50% { opacity: 0.5; }
                        }
                    ";
                    break;

                case 'fade':
                    $css .= "
                        .wnb-banner-content .wnb-content-inner {
                            display: block;
                            animation: wnbFadeIn 1s ease-out;
                        }
                        @keyframes wnbFadeIn {
                            from { opacity: 0; }
                            to { opacity: 1; }
                        }
                    ";
                    break;

                case 'slide-down':
                    $css .= "
                        .wnb-banner-content .wnb-content-inner {
                            display: block;
                            animation: wnbSlideDown 1s ease-out;
                        }
                        @keyframes wnbSlideDown {
                            from { transform: translateY(-100%); opacity: 0; }
                            to { transform: translateY(0); opacity: 1; }
                        }
                    ";
                    break;

                case 'slide-up':
                    $css .= "
                        .wnb-banner-content .wnb-content-inner {
                            display: block;
                            animation: wnbSlideUp 1s ease-out;
                        }
                        @keyframes wnbSlideUp {
                            from { transform: translateY(100%); opacity: 0; }
                            to { transform: translateY(0); opacity: 1; }
                        }
                    ";
                    break;

                case 'bounce':
                    $css .= "
                        .wnb-banner-content .wnb-content-inner {
                            display: inline-block;
                            animation: wnbBounce 1s ease infinite;
                        }
                        " . ($pause_on_hover === 'yes' ? "
                        .wnb-banner:hover .wnb-content-inner {
                            animation-play-state: paused;
                        }" : "") . "
                        @keyframes wnbBounce {
                            0%, 100% { transform: translateY(0); }
                            50% { transform: translateY(-10px); }
                        }
                    ";
                    break;

                case 'pulse':
                    $css .= "
                        .wnb-banner-content .wnb-content-inner {
                            display: inline-block;
                            transform-origin: center;
                            animation: wnbPulse 2s ease infinite;
                        }
                        " . ($pause_on_hover === 'yes' ? "
                        .wnb-banner:hover .wnb-content-inner {
                            animation-play-state: paused;
                        }" : "") . "
                        @keyframes wnbPulse {
                            0% { transform: scale(1); }
                            50% { transform: scale(1.02); }
                            100% { transform: scale(1); }
                        }
                    ";
                    break;
            }
        }

        return $css;
    }

    /**
     * Render the banner.
     */
    public function render_banner() {
        $options = wp_parse_args(get_option('wnb_settings', array()), array(
            'position' => 'top',
            'type' => 'fixed',
            'content' => '',
            'padding' => '15px'
        ));
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Notice Bar - Render banner called with options: ' . print_r($options, true));
        }
        
        // Check if we have content
        if (empty($options['content'])) {
            return;
        }

        // Skip footer output for relative top banners
        if ($options['type'] === 'relative' && $options['position'] === 'top') {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP Notice Bar - Skipping footer output for relative top banner');
            }
            return;
        }

        // For fixed banners or relative bottom banners, output in footer
        $this->output_banner_html();
        
        // Add padding to body for fixed position
        if ($options['type'] === 'fixed') {
            $style = sprintf(
                '<style>
                    body.has-wnb-banner-%1$s { 
                        padding-%1$s: %2$s;
                    }
                </style>',
                esc_attr($options['position']),
                esc_attr($options['padding'])
            );
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP Notice Bar - Adding body padding style: ' . $style);
            }
            
            echo $style;
        }
    }

    /**
     * Output the banner HTML.
     */
    /**
     * Check if the banner should be displayed based on schedule.
     *
     * @param array $options Plugin settings.
     * @return bool
     */
    private function should_display_banner($options) {
        // If scheduling is not enabled or content is empty, use default behavior
        if (empty($options['enable_schedule']) || $options['enable_schedule'] === 'no') {
            return true;
        }

        // Get current time in WordPress timezone
        $current_time = current_time('timestamp');
        $wp_timezone = wp_timezone();
        $now = new DateTime("@$current_time");
        $now->setTimezone($wp_timezone);

        // If no dates are set, show the banner
        if (empty($options['schedule_start']) && empty($options['schedule_end'])) {
            return true;
        }

        // Check start time if set
        if (!empty($options['schedule_start'])) {
            $start_time = new DateTime($options['schedule_start'], $wp_timezone);
            if ($now < $start_time) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log(sprintf(
                        'Banner scheduled to start at %s, current time is %s',
                        $start_time->format('Y-m-d H:i:s'),
                        $now->format('Y-m-d H:i:s')
                    ));
                }
                return false;
            }
        }

        // Check end time if set
        if (!empty($options['schedule_end'])) {
            $end_time = new DateTime($options['schedule_end'], $wp_timezone);
            if ($now > $end_time) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log(sprintf(
                        'Banner scheduled to end at %s, current time is %s',
                        $end_time->format('Y-m-d H:i:s'),
                        $now->format('Y-m-d H:i:s')
                    ));
                }
                return false;
            }
        }

        // If we get here, the banner should be shown
        return true;
    }

    /**
     * Output the banner HTML.
     */
    /**
     * Add dynamic padding adjustment script
     */
    /**
     * Add countdown timer script
     */
    public function add_countdown_script() {
        ?>
        <script>
        (function() {
            function updateCountdown() {
                var countdownEl = document.querySelector('.wnb-countdown');
                if (!countdownEl) return;
                
                var endTime = new Date(countdownEl.getAttribute('data-end')).getTime();
                var now = new Date().getTime();
                var timeLeft = endTime - now;
                
                if (timeLeft <= 0) {
                    // Hide the notice bar when countdown ends
                    var banner = document.querySelector('.wnb-banner');
                    if (banner) {
                        banner.style.display = 'none';
                        // Reset body padding
                        document.body.style.paddingTop = '0';
                        document.body.style.paddingBottom = '0';
                    }
                    return;
                }
                
                // Calculate time units
                var days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                var hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                // Update DOM
                countdownEl.querySelector('.wnb-days .wnb-countdown-value').textContent = days;
                countdownEl.querySelector('.wnb-hours .wnb-countdown-value').textContent = hours;
                countdownEl.querySelector('.wnb-minutes .wnb-countdown-value').textContent = minutes;
                countdownEl.querySelector('.wnb-seconds .wnb-countdown-value').textContent = seconds;
            }
            
            // Update immediately and every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        })();
        </script>
        <?php
    }

    private function add_dynamic_padding_script() {
        ?>
        <script>
        (function() {
            function adjustBodyPadding() {
                var banner = document.querySelector('.wnb-banner');
                if (!banner) return;
                
                var bannerHeight = banner.offsetHeight;
                var position = banner.classList.contains('wnb-top') ? 'top' : 'bottom';
                var adminBarHeight = document.body.classList.contains('admin-bar') ? (window.innerWidth > 782 ? 32 : 46) : 0;
                
                // For top position
                if (position === 'top') {
                    // For body padding, we only need the banner height
                    document.body.style.paddingTop = bannerHeight + 'px';
                    
                    // Adjust the banner position for admin bar if it's fixed
                    if (banner.classList.contains('wnb-fixed')) {
                        banner.style.top = adminBarHeight + 'px';
                    }
                    console.log('Banner Height:', bannerHeight, 'Admin Bar Height:', adminBarHeight);
                    
                    // Also adjust fixed headers if they exist
                    var commonHeaders = document.querySelectorAll('#masthead, header.site-header, .header-area, .main-header, .site-header, .header-wrapper, #header');
                    commonHeaders.forEach(function(header) {
                        if (window.getComputedStyle(header).position === 'fixed') {
                            header.style.top = totalPadding + 'px';
                        }
                    });
                } else {
                    document.body.style.paddingBottom = bannerHeight + 'px';
                }
            }

            // Run on load and on resize
            window.addEventListener('load', adjustBodyPadding);
            window.addEventListener('resize', adjustBodyPadding);

            // Run immediately if document is already loaded
            if (document.readyState === 'complete') {
                adjustBodyPadding();
            }
        })();
        </script>
        <?php
    }

    public function output_banner_html() {
        // Get current settings with defaults
        $options = wp_parse_args(get_option('wnb_settings', array()), array(
            'position' => 'top',
            'type' => 'fixed',
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
            'content' => '',
            'enable_schedule' => 'no',
            'schedule_start' => '',
            'schedule_end' => '',
            'animation' => 'none',
            'animation_speed' => 'medium',
            'pause_on_hover' => 'yes'
        ));
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Notice Bar - Current Settings with defaults: ' . print_r($options, true));
        }
        
        // Check if banner should be displayed based on schedule
        if (!$this->should_display_banner($options)) {
            return;
        }
        
        // Get position and type (these should now always be set due to wp_parse_args above)
        $position = $options['position'];
        $type = $options['type'];
        $animation = $options['animation'];
        
        // Build classes
        $classes = array(
            'wnb-banner',
            'wnb-' . esc_attr($position),
            'wnb-' . esc_attr($type),
            'wnb-animation-' . esc_attr($animation)
        );
        
        // Add admin bar class if needed
        if ($position === 'top' && $type === 'fixed' && is_admin_bar_showing()) {
            $classes[] = 'wnb-admin-bar-top';
        }
        
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <div class="wnb-banner-content">
                <div class="wnb-content-inner">
                    <?php echo wp_kses_post($options['content']); ?>
                    <?php
                    // Add countdown timer if enabled and end date is set
                    if ($options['enable_schedule'] === 'yes' && 
                        $options['enable_timer'] === 'yes' && 
                        !empty($options['schedule_end'])) {
                        echo '<div class="wnb-countdown" data-end="' . esc_attr($options['schedule_end']) . '">
                            <span class="wnb-countdown-item wnb-days"><span class="wnb-countdown-value">0</span><span class="wnb-countdown-label">d</span></span>
                            <span class="wnb-countdown-item wnb-hours"><span class="wnb-countdown-value">0</span><span class="wnb-countdown-label">h</span></span>
                            <span class="wnb-countdown-item wnb-minutes"><span class="wnb-countdown-value">0</span><span class="wnb-countdown-label">m</span></span>
                            <span class="wnb-countdown-item wnb-seconds"><span class="wnb-countdown-value">0</span><span class="wnb-countdown-label">s</span></span>
                        </div>';
                        
                        // Add countdown JavaScript
                        add_action('wp_footer', array($this, 'add_countdown_script'), 100);
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        
        // Add dynamic padding script for fixed position banners
        if ($options['type'] === 'fixed') {
            $this->add_dynamic_padding_script();
        }
    }
}
