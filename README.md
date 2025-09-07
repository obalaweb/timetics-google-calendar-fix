# Timetics Google Calendar Fix

## Description

This addon plugin fixes a critical issue with the Timetics Google Calendar integration where consultation events are created at the time of booking creation instead of at the scheduled consultation time.

## Problem

The original Timetics plugin has a bug in the Google Calendar sync functionality:

- **Issue**: Google Calendar events are created with the booking creation time instead of the actual consultation time
- **Root Cause**: The plugin passes only the date (e.g., "2025-01-15") to Google Calendar's `dateTime` field, which expects a full RFC3339 datetime string (e.g., "2025-01-15T14:30:00+00:00")
- **Impact**: Consultations appear at the wrong time in Google Calendar, causing scheduling conflicts and confusion

## Solution

This addon plugin:

1. **Intercepts** the Google Calendar event creation process using the `timetics/booking/create/google-event` filter
2. **Fixes** the datetime structure by properly combining date and time information
3. **Maintains** compatibility with future Timetics plugin updates
4. **Logs** the fix process for debugging purposes

## Installation

1. Upload the `timetics-google-calendar-fix` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The fix will be applied automatically to all new Google Calendar events

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Timetics plugin (active)
- Google Calendar integration configured in Timetics

## How It Works

### Before Fix
```php
// Original (broken) data structure
'start' => array(
    'dateTime' => '2025-01-15',  // Only date, no time
    'timeZone' => 'America/New_York',
)
```

### After Fix
```php
// Fixed data structure
'start' => array(
    'date' => '2025-01-15',      // Date component
    'time' => '14:30:00',        // Time component
    'timeZone' => 'America/New_York',
)
```

## Technical Details

The plugin hooks into the `timetics/booking/create/google-event` filter and:

1. **Detects** the incorrect datetime structure
2. **Parses** the datetime string using PHP's DateTime class
3. **Reformats** the data into the structure expected by the Timetics Calendar class
4. **Returns** the corrected data for Google Calendar API

## Debugging

The plugin logs all fixes to the WordPress error log. To view logs:

1. Enable WordPress debugging in `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```

2. Check the log file at `/wp-content/debug.log`

## Compatibility

- ✅ Compatible with Timetics plugin updates
- ✅ No modification of core plugin files
- ✅ Uses WordPress hooks and filters
- ✅ Maintains all original functionality

## Support

If you encounter any issues:

1. Check the WordPress error log for debugging information
2. Ensure the Timetics plugin is active and Google Calendar integration is configured
3. Verify that the consultation times are correctly set in your bookings

## Changelog

### Version 1.0.0
- Initial release
- Fixes Google Calendar datetime structure issue
- Adds debugging and logging functionality
- Compatible with Timetics plugin architecture
