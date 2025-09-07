<?php
/**
 * Test file for Timetics Google Calendar Fix
 * 
 * This file can be used to test the fix functionality.
 * Remove this file in production.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Test the datetime fix functionality
 */
function test_timetics_gc_fix() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized access');
    }
    
    echo '<h2>Timetics Google Calendar Fix - Test</h2>';
    
    // Test data that simulates the broken structure from the original plugin
    $test_data = array(
        'start' => array(
            'dateTime' => '2025-01-15T14:30:00',
            'timeZone' => 'America/New_York',
        ),
        'end' => array(
            'dateTime' => '2025-01-15T15:30:00',
            'timeZone' => 'America/New_York',
        ),
        'summary' => 'Test Consultation',
        'description' => 'Test consultation description',
    );
    
    echo '<h3>Original Data Structure (Broken):</h3>';
    echo '<pre>' . print_r($test_data, true) . '</pre>';
    
    // Apply the fix
    $fixed_data = apply_filters('timetics/booking/create/google-event', $test_data);
    
    echo '<h3>Fixed Data Structure:</h3>';
    echo '<pre>' . print_r($fixed_data, true) . '</pre>';
    
    // Verify the fix
    $start_fixed = isset($fixed_data['start']['date']) && isset($fixed_data['start']['time']);
    $end_fixed = isset($fixed_data['end']['date']) && isset($fixed_data['end']['time']);
    
    if ($start_fixed && $end_fixed) {
        echo '<div style="color: green; font-weight: bold;">✅ Fix applied successfully!</div>';
        echo '<p>The datetime structure has been corrected for Google Calendar API.</p>';
    } else {
        echo '<div style="color: red; font-weight: bold;">❌ Fix failed!</div>';
        echo '<p>The datetime structure was not properly fixed.</p>';
    }
    
    echo '<h3>Expected Result:</h3>';
    echo '<p>The start and end times should now have separate "date" and "time" keys instead of a combined "dateTime" key.</p>';
}

// Add test page to admin menu (only for testing)
add_action('admin_menu', function() {
    add_management_page(
        'Test GC Fix',
        'Test GC Fix',
        'manage_options',
        'test-gc-fix',
        'test_timetics_gc_fix'
    );
});
