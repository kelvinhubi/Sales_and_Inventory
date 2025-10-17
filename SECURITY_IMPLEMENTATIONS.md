# Security Implementation Guide
**Ready-to-use code fixes for security vulnerabilities**

---

## 🔒 CRITICAL FIX #1: Role-Based Access Control Middleware

### Step 1: Create Role Middleware

**File:** `app/Http/Middleware/CheckRole.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('Login');
        }

        $userRole = strtolower(Auth::user()->Role ?? '');
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized access. You do not have permission to view this page.');
        }

        return $next($request);
    }
}
```

### Step 2: Register Middleware

**File:** `app/Http/Kernel.php`

Add to `$middlewareAliases` array:

```php
protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
    'signed' => \App\Http\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    'role' => \App\Http\Middleware\CheckRole::class,  // ADD THIS LINE
];
```

### Step 3: Update Routes

**File:** `routes/web.php`

Replace:
```php
Route::middleware('auth')->prefix('owner')->name('owner.')->group(function () {
```

With:
```php
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
```

Do the same for manager routes:
```php
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
```

---

## 🔒 CRITICAL FIX #2: Secure API Endpoints

### Update routes/api.php

Replace the current API routes with properly authenticated versions:

```php
<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UserHeartbeatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ============================================
// AUTHENTICATED API ROUTES
// ============================================

Route::middleware(['web', 'auth'])->group(function () {
    
    // Manager routes
    Route::apiResource('managers', App\Http\Controllers\ManagerController::class);
    
    // Brand routes
    Route::apiResource('brands', BrandController::class);
    Route::get('brands/{brand}/standard-items', [BrandController::class, 'getStandardItems']);
    Route::put('brands/{brand}/standard-items', [BrandController::class, 'updateStandardItems']);
    
    // Branch routes
    Route::apiResource('brands.branches', BranchController::class);
    Route::get('branches', function () {
        return response()->json([
            'data' => \App\Models\Branch::with('brand')->get()->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'brand_name' => $branch->brand->name,
                ];
            }),
        ]);
    });
    
    // Product routes
    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
    Route::post('products/delete-expired', [App\Http\Controllers\Api\ProductController::class, 'deleteExpiredProducts']);
    Route::get('productss', function () {
        return response()->json([
            'data' => \App\Models\Product::with('brand')->get(),
        ]);
    });
    
    // Rejected goods routes
    Route::post('rejected-goods', [App\Http\Controllers\RejectedGoodController::class, 'store']);
    Route::get('rejected-goods', [App\Http\Controllers\RejectedGoodController::class, 'index']);
    
    // Order routes
    Route::get('orders/final-summary', [OrderController::class, 'finalSummary']);
    Route::get('orders/statistics', [OrderController::class, 'statistics']);
    Route::apiResource('orders', OrderController::class);
    
    // Expense routes
    Route::apiResource('expenses', ExpenseController::class);
    
    // User activity routes
    Route::post('/heartbeat', [UserHeartbeatController::class, 'heartbeat']);
    Route::get('/online-users', [UserHeartbeatController::class, 'getOnlineUsers']);
    
    // Analytics routes
    Route::get('analytics/sales-data', [App\Http\Controllers\Api\AnalyticsController::class, 'getSalesData']);
    Route::get('analytics/product-sales', [App\Http\Controllers\Api\AnalyticsController::class, 'getProductSalesData']);
    Route::get('analytics/top-bottom-brands', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomBrands']);
    Route::get('analytics/top-bottom-branches', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomBranches']);
    Route::get('analytics/top-bottom-products', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomProducts']);
    
    // Dashboard routes
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics']);
    Route::get('/dashboard/brands', [DashboardController::class, 'brands']);
    Route::get('/dashboard/branches', [DashboardController::class, 'getBranches']);
    Route::get('/dashboard/products', [DashboardController::class, 'getProducts']);
    
    // User info route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
```

---

## 🔒 CRITICAL FIX #3: Secure CORS Configuration

**File:** `config/cors.php`

Replace the entire file with:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    // IMPORTANT: Replace with your actual domain(s)
    'allowed_origins' => [
        env('APP_URL', 'http://localhost'),
        // Add your production domains here:
        // 'https://yourdomain.com',
        // 'https://www.yourdomain.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'X-CSRF-TOKEN'],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => true,  // Important for session-based auth

];
```

---

## 🔒 CRITICAL FIX #4: Rate Limiting for Authentication

**File:** `routes/web.php`

Update authentication routes:

```php
// Rate-limited authentication routes
Route::middleware('throttle:5,1')->group(function () {
    Route::post('login', [login::class, 'loginUser'])->name('loginUser');
    Route::post('signup', [signup::class, 'signupUser'])->name('signupUser');
    
    // Password reset routes
    Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Login form (no rate limit on viewing)
Route::get('login', [login::class, 'showForm'])->name('Login');
Route::get('signup', [signup::class, 'showForm'])->name('Signup');
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
```

This allows only 5 login attempts per minute per IP address.

---

## 🔒 HIGH PRIORITY FIX #5: Input Sanitization

### Update Controller Methods

**File:** `app/Http/Controllers/Api/OrderController.php`

Replace this:
```php
$validator = Validator::make($request->all(), [
    // validation rules
]);
```

With this:
```php
$validatedData = $request->validate([
    'brand_id' => 'required|exists:brands,id',
    'branch_id' => 'required|exists:branches,id',
    'items' => 'required|array|min:1',
    'items.*.name' => 'required|string|max:255',
    'items.*.quantity' => 'required|numeric|min:1',
    'items.*.price' => 'required|numeric|min:0',
    'items.*.product_id' => 'required|exists:products,id',
    'total_amount' => 'required|numeric|min:0',
    'notes' => 'nullable|string|max:1000',
]);

// Then use $validatedData instead of $request->all()
$order = Order::create([
    'user_id' => Auth::id(),
    'brand_id' => $validatedData['brand_id'],
    'branch_id' => $validatedData['branch_id'],
    'total_amount' => $validatedData['total_amount'],
    'notes' => $validatedData['notes'] ?? null,
]);
```

**File:** `app/Http/Controllers/Api/ExpenseController.php`

Replace:
```php
$data = $request->all();
Expense::create($data);
```

With:
```php
$validatedData = $request->validate([
    'category' => 'required|string|max:255',
    'amount' => 'required|numeric|min:0',
    'date' => 'required|date',
    'description' => 'nullable|string|max:1000',
    'branch_id' => 'nullable|exists:branches,id',
]);

Expense::create($validatedData);
```

---

## 🔒 HIGH PRIORITY FIX #6: Secure Session Configuration

**File:** `.env`

Add/update these settings:

```env
# Session Security
SESSION_LIFETIME=480
SESSION_EXPIRE_ON_CLOSE=false
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# HTTPS (enable in production)
# FORCE_HTTPS=true
```

**File:** `config/session.php`

Update:
```php
'lifetime' => env('SESSION_LIFETIME', 480),  // 8 hours
'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
'encrypt' => env('SESSION_ENCRYPT', true),
'secure' => env('SESSION_SECURE_COOKIE', true),  // HTTPS only
'http_only' => true,
'same_site' => env('SESSION_SAME_SITE', 'strict'),  // Changed from 'lax' to 'strict'
```

---

## 🔒 HIGH PRIORITY FIX #7: Security Headers Middleware

### Step 1: Create Middleware

**File:** `app/Http/Middleware/SecurityHeaders.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // XSS Protection (legacy but still useful)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy (formerly Feature-Policy)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy (adjust as needed)
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com",
            "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);
        
        // HTTPS Strict Transport Security (enable in production with HTTPS)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
```

### Step 2: Register Middleware

**File:** `app/Http/Kernel.php`

Add to `$middleware` array (global middleware):

```php
protected $middleware = [
    // \App\Http\Middleware\TrustHosts::class,
    \App\Http\Middleware\TrustProxies::class,
    \Illuminate\Http\Middleware\HandleCors::class,
    \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    \App\Http\Middleware\SecurityHeaders::class,  // ADD THIS LINE
];
```

---

## 🔒 MEDIUM PRIORITY FIX #8: Session Regeneration After Login

**File:** `app/Http/Controllers/login.php`

Update the `loginUser` method:

```php
public function loginUser(Request $request): RedirectResponse|View
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();
        
        $user = Auth::user();
        
        // Update user status
        $user->update([
            'is_online' => true,
            'last_activity' => now(),
        ]);
        
        // Role-based redirection
        $role = strtolower($user->Role ?? '');
        
        return match($role) {
            'owner' => redirect()->intended(route('owner')),
            'manager' => redirect()->intended(route('manager')),
            'customer' => redirect()->intended(route('customer')),
            default => redirect()->route('Login')->with('status', 'Invalid user role'),
        };
    }

    return back()->withInput()->withErrors([
        'email' => 'Invalid credentials. Please check your email and password.',
    ]);
}
```

---

## 🔒 MEDIUM PRIORITY FIX #9: Remove Sensitive Logging

**File:** `app/Http/Controllers/Owner/PastOrderController.php`

Remove or update line 109:

```php
// REMOVE THIS:
Log::info('Request data:', $request->all());

// OR REPLACE WITH:
Log::info('Past order processing', [
    'user_id' => Auth::id(),
    'order_id' => $request->input('order_id'),
    // Only log non-sensitive fields
]);
```

---

## 🔒 MEDIUM PRIORITY FIX #10: Role Enum

### Step 1: Create Enum

**File:** `app/Enums/UserRole.php`

```php
<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match($this) {
            self::OWNER => 'Owner',
            self::MANAGER => 'Manager',
            self::CUSTOMER => 'Customer',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
```

### Step 2: Use Enum in Model

**File:** `app/Models/User.php`

```php
use App\Enums\UserRole;

protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'Role' => UserRole::class,  // ADD THIS
];
```

---

## 🔒 PRODUCTION CHECKLIST

Before deploying to production, ensure:

### Environment Configuration (.env)
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=[your-generated-key]

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
SESSION_ENCRYPT=true

DB_CONNECTION=mysql
# Use strong database password!

MAIL_ENCRYPTION=tls
```

### Additional Security Steps
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Disable directory listing in web server
- [ ] Enable HTTPS and redirect HTTP to HTTPS
- [ ] Set up firewall rules
- [ ] Enable fail2ban or similar
- [ ] Regular database backups
- [ ] Update all dependencies: `composer update`
- [ ] Review and update `storage/logs` retention policy

---

## 🧪 TESTING SECURITY FIXES

After implementing these fixes, test:

1. **Authentication:**
   - Try accessing owner routes as manager (should fail)
   - Try accessing manager routes as owner (should fail)
   - Test rate limiting (make 6 login attempts)

2. **API Security:**
   - Try accessing API without authentication (should fail)
   - Check CORS headers in browser dev tools

3. **Session Security:**
   - Check cookies are marked as Secure and HttpOnly
   - Verify session regenerates after login

4. **Input Validation:**
   - Try sending unexpected fields in requests
   - Test XSS attempts in form inputs

---

## 📚 Additional Resources

- [Laravel Security Best Practices](https://laravel.com/docs/10.x/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Checklist](https://github.com/Qloppa/laravel-security-checklist)

---

**Need help implementing these fixes? Review each section carefully and implement one at a time!**
