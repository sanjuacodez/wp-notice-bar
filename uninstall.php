<?php
/**
 * Uninstall WP Notice Bar
 *
 * @package WPNoticeBar
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('wnb_settings');
delete_option('wnb_version');
