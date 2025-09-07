<?php
/**
 * Plugin Name: Timetics Google Calendar Fix
 * Plugin URI: https://github.com/obalaweb/timetics-google-calendar-fix
 * Description: Fixes Google Calendar integration issue where events are created at booking time instead of consultation time.
 * Version: 1.0.0
 * Author: Obala Joseph Ivan
 * License: GPL v2 or later
 * Text Domain: timetics-google-calendar-fix
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TIMETICS_GC_FIX_VERSION', '1.0.0');
define('TIMETICS_GC_FIX_PLUGIN_FILE', __FILE__);
define('TIMETICS_GC_FIX_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TIMETICS_GC_FIX_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class Timetics_Google_Calendar_Fix {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Check if Timetics plugin is active
        if (!class_exists('Timetics\Core\Integrations\Google\Service\Google_Calendar_Sync')) {
            add_action('admin_notices', array($this, 'timetics_missing_notice'));
            return;
        }
        
        // Hook into the Google Calendar event creation
        add_filter('timetics/booking/create/google-event', array($this, 'fix_google_calendar_event_data'), 10, 1);
        
        // Add admin notice for successful activation
        add_action('admin_notices', array($this, 'activation_notice'));
        
        // Include test file in development mode
        if (defined('WP_DEBUG') && WP_DEBUG) {
            require_once TIMETICS_GC_FIX_PLUGIN_DIR . 'test-fix.php';
        }
    }
    
    /**
     * Fix Google Calendar event data
     * 
     * @param array $args Event data
     * @return array Fixed event data
     */
    public function fix_google_calendar_event_data($args) {
        // Log the original data for debugging
        error_log('Timetics GC Fix: Original event data: ' . print_r($args, true));
        
        // Check if we have the required data structure
        if (!isset($args['start']) || !isset($args['end'])) {
            error_log('Timetics GC Fix: Missing start/end data in event args');
            return $args;
        }
        
        // Fix the start datetime
        if (isset($args['start']['dateTime'])) {
            $args['start'] = $this->fix_datetime_structure($args['start'], 'start');
        }
        
        // Fix the end datetime
        if (isset($args['end']['dateTime'])) {
            $args['end'] = $this->fix_datetime_structure($args['end'], 'end');
        }
        
        // Log the fixed data for debugging
        error_log('Timetics GC Fix: Fixed event data: ' . print_r($args, true));
        
        return $args;
    }
    
    /**
     * Fix datetime structure for Google Calendar
     * 
     * @param array $datetime_data
     * @param string $type 'start' or 'end'
     * @return array Fixed datetime structure
     */
    private function fix_datetime_structure($datetime_data, $type = 'start') {
        // The issue is that the original code passes dateTime directly
        // but the Calendar class expects separate date and time keys
        
        $dateTime = $datetime_data['dateTime'];
        $timeZone = isset($datetime_data['timeZone']) ? $datetime_data['timeZone'] : wp_timezone_string();
        
        // Parse the datetime string
        try {
            $dt = new DateTime($dateTime, new DateTimeZone($timeZone));
            
            // Return the correct structure expected by the Calendar class
            return array(
                'date' => $dt->format('Y-m-d'),
                'time' => $dt->format('H:i:s'),
                'timeZone' => $timeZone
            );
        } catch (Exception $e) {
            error_log('Timetics GC Fix: Error parsing datetime: ' . $e->getMessage());
            return $datetime_data; // Return original if parsing fails
        }
    }
    
    /**
     * Show notice if Timetics plugin is not active
     */
    public function timetics_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php esc_html_e('Timetics Google Calendar Fix', 'timetics-google-calendar-fix'); ?></strong>: 
                <?php esc_html_e('This plugin requires the Timetics plugin to be installed and activated.', 'timetics-google-calendar-fix'); ?>
            </p>
        </div>
        <?php
    }
    
    /**
     * Show activation notice
     */
    public function activation_notice() {
        // Only show on plugin activation
        if (get_transient('timetics_gc_fix_activated')) {
            delete_transient('timetics_gc_fix_activated');
            ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <strong><?php esc_html_e('Timetics Google Calendar Fix', 'timetics-google-calendar-fix'); ?></strong>: 
                    <?php esc_html_e('Plugin activated successfully! Google Calendar events will now be created at the correct consultation time.', 'timetics-google-calendar-fix'); ?>
                </p>
            </div>
            <?php
        }
    }
}

/**
 * Initialize the plugin
 */
function timetics_google_calendar_fix_init() {
    return Timetics_Google_Calendar_Fix::get_instance();
}

// Start the plugin
timetics_google_calendar_fix_init();

/**
 * Activation hook
 */
register_activation_hook(__FILE__, function() {
    set_transient('timetics_gc_fix_activated', true, 60);
});

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, function() {
    // Clean up any transients or options if needed
    delete_transient('timetics_gc_fix_activated');
});
