# Installation Guide

## Quick Installation

1. **Upload the plugin folder** to your WordPress plugins directory:
   ```
   /wp-content/plugins/timetics-google-calendar-fix/
   ```

2. **Activate the plugin** in WordPress Admin:
   - Go to `Plugins` → `Installed Plugins`
   - Find "Timetics Google Calendar Fix"
   - Click `Activate`

3. **Verify the fix is working**:
   - Create a new booking in Timetics
   - Check your Google Calendar - the event should now appear at the correct consultation time

## Requirements

- ✅ WordPress 5.0+
- ✅ PHP 7.4+
- ✅ Timetics plugin (active)
- ✅ Google Calendar integration configured in Timetics

## Testing the Fix

If you have WordPress debugging enabled (`WP_DEBUG = true`), you can test the fix:

1. Go to `Tools` → `Test GC Fix` in WordPress Admin
2. Click the test button to verify the fix is working
3. Check the results to ensure the datetime structure is corrected

## Troubleshooting

### Plugin Not Working?

1. **Check if Timetics is active**:
   - The plugin requires Timetics to be installed and active
   - You'll see an error notice if Timetics is missing

2. **Check WordPress error log**:
   - Enable debugging in `wp-config.php`:
     ```php
     define('WP_DEBUG', true);
     define('WP_DEBUG_LOG', true);
     ```
   - Check `/wp-content/debug.log` for fix messages

3. **Verify Google Calendar integration**:
   - Ensure Google Calendar is connected in Timetics settings
   - Test with a new booking to see if events appear at correct times

### Still Having Issues?

1. **Check the error log** for detailed debugging information
2. **Verify the consultation times** are set correctly in your bookings
3. **Test with a simple booking** to isolate the issue

## Uninstallation

To remove the plugin:

1. **Deactivate** the plugin in WordPress Admin
2. **Delete** the plugin folder from `/wp-content/plugins/`
3. The uninstall script will clean up any plugin data automatically

## Support

For issues or questions:
1. Check the WordPress error log first
2. Verify all requirements are met
3. Test with a simple booking scenario
