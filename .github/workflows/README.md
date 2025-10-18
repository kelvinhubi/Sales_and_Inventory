# GitHub Actions CI/CD Documentation

## Overview
This repository uses GitHub Actions for continuous integration and deployment with multiple PHP versions and comprehensive testing.

## Workflow Features

### 🧪 Testing Matrix
- **PHP Versions**: 8.1, 8.2, 8.3
- **Dependency Versions**: prefer-lowest, prefer-stable
- **Database**: SQLite for testing
- **Services**: MySQL 8.0 for integration tests

### 🔧 Jobs

#### 1. Laravel Tests (`laravel-tests`)
- Runs on multiple PHP versions
- Tests with both lowest and stable dependencies
- Includes code coverage reporting
- Uploads coverage to Codecov

#### 2. Security Scan (`security-scan`)
- Runs `composer audit` for security vulnerabilities
- Uses latest PHP version (8.3)

#### 3. Code Quality (`code-quality`)
- **PHP CS Fixer**: Code style checking
- **PHPStan**: Static analysis (Level 5)
- Ensures code quality standards

## 🚀 Key Improvements from Original

### Updated Components:
- ✅ **PHP Version**: Updated from 8.0 to 8.1, 8.2, 8.3
- ✅ **Actions**: Latest versions of all GitHub Actions
- ✅ **Matrix Testing**: Multiple PHP versions and dependency combinations
- ✅ **Caching**: Composer dependency caching for faster builds
- ✅ **Extensions**: Complete PHP extension list including MySQL
- ✅ **Node.js**: Added for asset compilation
- ✅ **Security**: Automated security vulnerability scanning
- ✅ **Code Quality**: PHP CS Fixer and PHPStan integration
- ✅ **Coverage**: Code coverage reporting with Codecov
- ✅ **Services**: MySQL service for comprehensive testing

### New Features:
- **Multi-PHP Testing**: Ensures compatibility across PHP versions
- **Dependency Matrix**: Tests with both newest and oldest supported packages
- **Asset Building**: Automatic frontend asset compilation
- **Security Scanning**: Automated vulnerability detection
- **Code Quality Gates**: Automated code style and static analysis
- **Smart NPM Handling**: Only runs if package.json exists

## 📋 Configuration Files

### `.php-cs-fixer.php`
- PSR-2 and PSR-12 compliance
- PHP 8.0+ migration rules
- Custom formatting rules

### `phpstan.neon`
- Level 5 static analysis
- Laravel-specific configurations
- Reasonable error ignoring

## 🔧 Setup Requirements

### Required Secrets (Optional):
- `CODECOV_TOKEN`: For code coverage reporting

### Environment Variables:
- Automatically configured for testing
- SQLite database for fast testing
- MySQL service available for integration tests

## 🏃‍♂️ Running Locally

```bash
# Install dependencies
composer install

# Run code style fixer
vendor/bin/php-cs-fixer fix

# Run static analysis
vendor/bin/phpstan analyse

# Run tests with coverage
php artisan test --coverage

# Security audit
composer audit
```

## 📊 Workflow Triggers

- **Push**: main, develop branches
- **Pull Request**: main branch
- **Manual**: Can be triggered manually from GitHub Actions tab

## 🎯 Best Practices Implemented

1. **Matrix Strategy**: Tests multiple PHP versions
2. **Dependency Caching**: Faster build times
3. **Parallel Jobs**: Security and quality checks run separately
4. **Conditional Steps**: Smart asset building and NPM handling
5. **Comprehensive Testing**: Unit, Feature, and Integration tests
6. **Security First**: Automated vulnerability scanning
7. **Code Quality**: Automated style and static analysis