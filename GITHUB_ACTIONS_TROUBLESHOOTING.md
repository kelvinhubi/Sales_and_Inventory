# 🔧 GitHub Actions Troubleshooting Guide

## 🚨 Common Errors After Composer Install

### **1. Package Discovery Issues**
```bash
Error: Class 'Package\ServiceProvider' not found
```

**✅ Fixed by:**
- Removed `--no-scripts` flag to allow Laravel package discovery
- Added `composer dump-autoload --optimize` step

### **2. Permission Errors**
```bash
file_put_contents(): failed to open stream: Permission denied
```

**✅ Fixed by:**
- Proper directory permissions (755 instead of 777)
- Specific SQLite database permissions (664)

### **3. Environment Configuration Issues**
```bash
RuntimeException: No application encryption key has been specified
```

**✅ Fixed by:**
- Proper .env setup with testing environment
- Explicit environment variable configuration
- `php artisan key:generate` step

### **4. Migration Failures**
```bash
SQLSTATE[HY000]: General error: 1 no such table
```

**✅ Fixed by:**
- SQLite database creation with proper permissions
- Migration status checking before running
- Clear cache before migrations

## 🎯 **Current Workflow Improvements**

### **Enhanced Composer Installation**
```yaml
- name: Install Composer Dependencies
  run: |
    if [ "${{ matrix.dependency-version }}" = "prefer-lowest" ]; then
      composer update --prefer-lowest --no-ansi --no-interaction --no-progress --optimize-autoloader
    else
      composer install --no-ansi --no-interaction --no-progress --optimize-autoloader
    fi

- name: Clear Composer Cache (if needed)
  run: composer clear-cache

- name: Verify Composer Autoload
  run: composer dump-autoload --optimize
```

### **Proper Environment Setup**
```yaml
- name: Set Environment Variables
  run: |
    echo "APP_ENV=testing" >> .env
    echo "APP_DEBUG=true" >> .env
    echo "DB_CONNECTION=sqlite" >> .env
    echo "DB_DATABASE=database/database.sqlite" >> .env
```

### **Diagnostic Steps**
```yaml
- name: Verify Laravel Installation
  run: |
    php artisan --version
    php artisan config:show app.name || echo "Config check completed"
```

## 🔍 **Debugging Steps**

If you still get errors, check these in the GitHub Actions logs:

### **1. Composer Issues**
```bash
# Check if vendor directory exists
ls -la vendor/

# Verify autoload files
ls -la vendor/autoload.php

# Check composer.lock
cat composer.lock | grep "name"
```

### **2. Laravel Issues**
```bash
# Check Laravel version
php artisan --version

# Verify configuration
php artisan config:cache
php artisan config:show

# Check database connection
php artisan migrate:status
```

### **3. Permission Issues**
```bash
# Check directory permissions
ls -la storage/
ls -la bootstrap/cache/
ls -la database/
```

## 📋 **Error Categories & Solutions**

| Error Type | Symptom | Solution |
|------------|---------|----------|
| **Autoload** | Class not found | `composer dump-autoload` |
| **Config** | Config not found | `php artisan config:clear` |
| **Database** | Migration failed | Check SQLite permissions |
| **Cache** | Cache errors | `php artisan cache:clear` |
| **Permissions** | Cannot write | Fix directory permissions |
| **Dependencies** | Package missing | Check `composer.json` |

## 🎯 **Most Common Solutions**

1. **Remove `--no-scripts`** - Allows Laravel package discovery
2. **Proper permissions** - Use 755 for directories, 664 for files
3. **Clear caches** - Start with clean state
4. **Environment setup** - Explicit testing configuration
5. **Diagnostic steps** - Add verification commands

## 🚀 **Current Status**

✅ **All common issues addressed:**
- Package discovery enabled
- Proper permissions set
- Environment correctly configured  
- Caches cleared before operations
- Diagnostic steps included
- Error handling improved

**The workflow should now handle most composer post-install issues gracefully!**

---

**Still having issues?** Share the specific error message and I'll help debug it! 🔍