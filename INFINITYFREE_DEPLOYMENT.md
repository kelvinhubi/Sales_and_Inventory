# InfinityFree Laravel Deployment Guide

## Prerequisites

Before deploying to InfinityFree, you need to:

### 1. Create InfinityFree Account
- Sign up at [InfinityFree](https://infinityfree.net/)
- Create a new website/subdomain
- Note down your FTP credentials and database details

### 2. Set Up GitHub Secrets

Add these secrets to your GitHub repository (Settings → Secrets and variables → Actions):

#### FTP Credentials:
- `INFINITYFREE_FTP_HOST` - Your FTP hostname (e.g., `files.000webhost.com`)
- `INFINITYFREE_FTP_USERNAME` - Your FTP username
- `INFINITYFREE_FTP_PASSWORD` - Your FTP password

#### Database Credentials:
- `INFINITYFREE_DB_HOST` - Database host (e.g., `sql200.infinityfree.com`)
- `INFINITYFREE_DB_DATABASE` - Database name (e.g., `if0_12345678_database`)
- `INFINITYFREE_DB_USERNAME` - Database username (e.g., `if0_12345678`)
- `INFINITYFREE_DB_PASSWORD` - Your database password

#### Application Settings:
- `INFINITYFREE_APP_URL` - Your application URL (e.g., `https://yoursite.free.nf`)

### 3. Database Setup

1. Login to your InfinityFree control panel
2. Go to MySQL Databases
3. Create a new database
4. Note the database credentials

### 4. Manual Steps After Deployment

Since InfinityFree has limitations, you'll need to:

#### Option A: Create a Migration Route (Recommended)
Add this route to `routes/web.php` for one-time database setup:

```php
Route::get('/setup-database', function () {
    if (app()->environment('production')) {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
        return 'Database setup completed!';
    }
    return 'Not allowed in this environment';
})->middleware(['auth']); // Add authentication if needed
```

#### Option B: Use cPanel File Manager
1. Login to your InfinityFree control panel
2. Open File Manager
3. Navigate to your Laravel app directory
4. Use the terminal (if available) to run migrations

### 5. File Structure on InfinityFree

After deployment, your files will be organized as:
```
/htdocs/                    (Your public files - Laravel's public folder)
├── index.php              (Modified to point to Laravel app)
├── css/
├── js/
└── ...

/laravel-app/               (Your Laravel application - outside web root for security)
├── app/
├── bootstrap/
├── config/
├── vendor/
└── ...
```

### 6. Important Notes

- **PHP Version**: InfinityFree supports PHP 8.1+
- **Cron Jobs**: Limited or not available on free plan
- **File Permissions**: Automatically handled in deployment
- **Storage**: Use `storage/app` for file uploads
- **Logs**: Check `storage/logs` for any issues
- **Cache**: Uses file-based caching (no Redis/Memcached)

### 7. Testing Your Deployment

1. Visit your website URL
2. Check if the homepage loads correctly
3. Test database connections
4. Verify file uploads work
5. Check error logs if issues occur

### 8. Troubleshooting

#### Common Issues:

1. **500 Internal Server Error**
   - Check file permissions (755 for directories, 644 for files)
   - Verify `.env` file configuration
   - Check storage and cache directory permissions

2. **Database Connection Error**
   - Verify database credentials in secrets
   - Ensure database exists in InfinityFree panel
   - Check database host and port

3. **Missing Files**
   - Ensure all Laravel files are uploaded
   - Check if vendor directory is present
   - Verify public files are in htdocs

4. **Route Not Found**
   - Clear route cache: `php artisan route:clear`
   - Check if mod_rewrite is working
   - Verify .htaccess file in public directory

### 9. Updating Your Application

To update your deployed application:
1. Push changes to the main branch
2. GitHub Actions will automatically deploy
3. Visit your setup route if database changes were made

### 10. Security Considerations

- Never commit `.env` files to git
- Use strong database passwords
- Enable HTTPS if available
- Keep Laravel and dependencies updated
- Monitor logs regularly

## Support

If you encounter issues:
1. Check GitHub Actions logs
2. Review InfinityFree documentation
3. Check Laravel error logs
4. Contact InfinityFree support if needed

Good luck with your deployment! 🚀