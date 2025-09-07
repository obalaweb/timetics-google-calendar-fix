<?php
/**
 * Uninstall script for Timetics Google Calendar Fix
 * 
 * This file is executed when the plugin is deleted through the WordPress admin.
 */

// Prevent direct access
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Clean up any plugin-specific data
delete_transient('timetics_gc_fix_activated');

// Remove any options if we added them
delete_option('timetics_gc_fix_version');
delete_option('timetics_gc_fix_settings');

// Log the uninstallation
error_log('Timetics Google Calendar Fix: Plugin uninstalled successfully');
