# WP Notice Bar

A professional, customizable notice bar system for WordPress. Display important announcements, promotions, or notifications at the top of your website with countdown timers, smooth animations, and full customization options.

## Features

- **Easy Setup**: Simple configuration through WordPress admin
- **Countdown Timer**: Display dynamic countdown to create urgency
- **Scheduling System**: Set start and end dates for notices
- **Background Options**: Choose between solid colors, gradients, or images
- **Custom Opacity**: Control background transparency
- **Customizable Design**: Full control over appearance
- **Responsive Layout**: Mobile-friendly design
- **Smooth Animations**: Professional entrance and exit effects
- **Position Control**: Top or bottom placement
- **Display Rules**: Conditional display options
- **Multiple Notices**: Create and manage multiple bars
- **Developer Friendly**: Hooks and filters for customization

## Installation

1. Upload the `wp-notice-bar` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'Notice Bar' in your WordPress admin menu
4. Configure your notice bar settings

## Usage

### Basic Setup

1. Go to Notice Bar > Settings in your WordPress admin
2. Enable the notice bar
3. Enter your notice text
4. Customize the appearance:
   - Use the color picker to select background color
   - Choose gradient colors or upload a background image
   - Set background opacity for better text visibility
   - Adjust button colors and hover states visually
5. Configure display options
6. Save changes

### Setting Up a Countdown Timer

1. Go to the Content tab in Notice Bar settings
2. Enable scheduling by selecting "Yes" for "Enable Scheduling"
3. Set your end date and time
4. Enable the countdown timer
5. The timer will automatically display:
   - Days remaining
   - Hours remaining
   - Minutes remaining
   - Seconds remaining
6. Timer will automatically hide the notice when countdown ends

#### Color Settings

The plugin uses WordPress's native color picker for intuitive color selection:
- Background Color: Set the notice bar's background
- Text Color: Choose readable text colors
- Button Colors: Configure normal and hover states
- Border Colors: Define border colors if enabled

### Advanced Configuration

The plugin provides several ways to extend its functionality using WordPress actions and filters. Here are some examples:

```php
// Modify classes added to the notice bar
add_filter('body_class', function($classes) {
    if (in_array('has-wnb-banner-top', $classes)) {
        $classes[] = 'my-custom-class';
    }
    return $classes;
});

// Hook into notice bar display timing
add_action('wp_footer', function() {
    // Your custom code before or after notice bar display
}, 9); // Priority 9 runs before notice bar (priority 10)
```

## Development

### Requirements

- PHP 7.2 or higher
- WordPress 5.0 or higher

### Asset Structure

The plugin uses vanilla JavaScript and CSS, no build process required. Assets are organized as follows:

```
assets/
├── admin/
│   ├── css/
│   │   └── admin.css      # Admin interface styles
│   └── js/
│       └── admin.js       # Admin interface functionality
└── css/
    └── banner.css         # Frontend notice bar styles
```

### Directory Structure

```
wp-notice-bar/
├── assets/
│   ├── css/
│   └── js/
├── includes/
│   ├── admin/
│   ├── class-wnb-autoloader.php
│   ├── class-wnb-assets.php
│   ├── class-wnb-display.php
│   └── class-wp-notice-bar.php
├── languages/
├── readme.txt
├── readme.md
└── wp-notice-bar.php
```

### Available Hooks

#### WordPress Actions Used

- `wp_footer`: Used to display the notice bar (priority: 10)
- `wp_body_open`: Used for top position in relative mode (priority: 5)
- `admin_bar_init`: Handles admin bar adjustments
- `wp_enqueue_scripts`: Enqueues frontend styles

#### WordPress Filters Used

- `body_class`: Adds appropriate classes for banner positioning
  - `has-wnb-banner`: General banner class
  - `has-wnb-banner-top`: For top positioned banner
  - `has-wnb-banner-bottom`: For bottom positioned banner

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/my-new-feature`
5. Submit a pull request

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## Credits

Created by [Sanjay Shankar](https://sanjayshankar.me)
