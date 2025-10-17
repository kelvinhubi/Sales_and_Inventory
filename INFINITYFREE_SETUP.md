# InfinityFree Hosting Setup Guide

## Issues Fixed

### 1. **Fetch Errors When Editing Manager Accounts**
   - **Problem**: InfinityFree doesn't always support PUT/DELETE HTTP methods properly
   - **Solution**: Updated `apiRequest()` function to use POST with `_method` parameter for PUT/DELETE requests
   - **Location**: `resources/views/owner/manager.blade.php`

### 2. **Heartbeat Errors**
   - **Problem**: Frequent AJAX requests (every 3 seconds) were being rate-limited by InfinityFree
   - **Solution**: 
     - Increased heartbeat interval from 3 seconds to 30 seconds
     - Added silent error handling for heartbeat failures
     - Made online-users endpoint failure non-critical
   - **Location**: `resources/views/owner/manager.blade.php`

### 3. **Session/Authentication Issues**
   - **Problem**: API routes weren't maintaining sessions properly
   - **Solution**: Added `web` middleware to manager API routes
   - **Location**: `routes/api.php`

### 4. **Request Timeouts**
   - **Problem**: Requests hanging indefinitely on slow hosting
   - **Solution**: Added 30-second timeout to all API requests
   - **Location**: `resources/views/owner/manager.blade.php`

## Additional InfinityFree Compatibility Settings

### Required .htaccess Rules
Add this to your `public/.htaccess` file if not already present:

```apache
# Enable method spoofing for PUT/DELETE
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    
    # Redirect all requests to index.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Environment Configuration (.env)
Ensure these settings in your `.env` file:

```env
# Session Configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache Configuration
CACHE_DRIVER=file

# Queue Configuration (don't use database on free hosting)
QUEUE_CONNECTION=sync

# Disable HTTPS enforcement if InfinityFree doesn't support it
# (Only use this on InfinityFree)
APP_FORCE_HTTPS=false
```

### CORS Headers
If you still face CORS issues, add this to `config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

## Testing on InfinityFree

### 1. Clear Cache
After uploading changes, clear Laravel cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 2. Check Permissions
Ensure these directories are writable (777):
- `storage/`
- `storage/framework/`
- `storage/framework/cache/`
- `storage/framework/sessions/`
- `storage/framework/views/`
- `storage/logs/`
- `bootstrap/cache/`

### 3. Debug Mode
Enable debug mode temporarily to see errors:
```env
APP_DEBUG=true
```
**Important**: Disable after testing!

### 4. Monitor Logs
Check `storage/logs/laravel.log` for errors

## Browser Console Debugging

Open browser console (F12) and check for:
1. CORS errors
2. 405 Method Not Allowed errors
3. 419 CSRF token errors
4. Network timeout errors

## Known InfinityFree Limitations

1. **Request Rate Limiting**: Max ~10 requests per minute
2. **No WebSocket Support**: Can't use real-time features
3. **Limited HTTP Methods**: PUT/DELETE may not work (now fixed with _method)
4. **Session Issues**: Sometimes sessions don't persist (now using web middleware)
5. **Slow Response Times**: Expect 2-5 second delays
6. **CPU Limits**: Long-running processes may fail

## Troubleshooting

### If Manager Edit Still Fails:
1. Check browser console for specific error
2. Verify CSRF token is present in page source
3. Check that `web` middleware is on the route
4. Clear browser cache and cookies
5. Try in incognito mode

### If Heartbeat Still Errors:
1. Increase interval to 60 seconds (edit line in manager.blade.php)
2. Disable heartbeat temporarily by commenting out `startHeartbeat()` call
3. Check if InfinityFree has rate-limited your IP

### If Nothing Works:
1. Check InfinityFree control panel for suspension/limits
2. Verify database connection works
3. Test basic routes first (like login)
4. Contact InfinityFree support about HTTP method restrictions

## Performance Tips

1. **Minimize AJAX Calls**: Reduce frequency of auto-refresh
2. **Use Pagination**: Don't load all data at once
3. **Cache Static Assets**: Use CDN for CSS/JS
4. **Optimize Images**: Compress before upload
5. **Lazy Load**: Load data only when needed

## Files Modified

1. `resources/views/owner/manager.blade.php`
   - Updated `apiRequest()` function
   - Increased heartbeat interval
   - Added timeout handling
   - Improved error handling

2. `routes/api.php`
   - Added web middleware to manager routes

## Success Indicators

✅ Manager creation works
✅ Manager editing works
✅ Manager deletion works
✅ Heartbeat runs without errors (check console)
✅ Online status updates (may be slower)
✅ No fetch/CORS errors in console

## Need Help?

If issues persist, check:
1. InfinityFree forum for similar issues
2. Laravel logs (`storage/logs/laravel.log`)
3. Browser network tab for failed requests
4. Server error logs in InfinityFree control panel
