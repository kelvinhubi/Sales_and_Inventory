# Activity Logs System Documentation

## Overview
The Activity Logs system tracks all user actions in the Sales & Inventory system for security, compliance, and audit purposes. Every critical action performed by users (especially managers) is automatically logged with detailed information.

## Features

### 🔐 Security Tracking
- **Login/Logout Tracking**: Records all authentication events
- **Failed Login Attempts**: Flags potential security threats
- **Password Changes**: Tracks password modifications
- **Password Resets**: Monitors password reset requests
- **Session Management**: Tracks user activity and session details

### 📊 Activity Monitoring
- **CRUD Operations**: Logs all Create, Read, Update, Delete actions
- **Module Tracking**: Categorizes activities by module (products, orders, brands, etc.)
- **User Actions**: Tracks specific user activities with timestamps
- **IP Address Logging**: Records IP addresses for security audits
- **User Agent Tracking**: Stores browser/device information

### 🎯 Severity Levels
- **Low**: Normal operations (view, list, read)
- **Medium**: Modifications (create, update, password changes)
- **High**: Critical actions (delete, bulk operations)
- **Critical**: Security events (failed logins, unauthorized access attempts)

## Database Schema

```sql
activity_logs table:
- id: Primary key
- user_id: Foreign key to users table
- user_name: Cached user name for historical records
- user_role: User's role (owner/manager/customer)
- action_type: Type of action (login, create, update, delete, etc.)
- module: Module name (authentication, products, orders, etc.)
- description: Human-readable description of the action
- details: JSON field for additional data
- ip_address: IP address of the user
- user_agent: Browser/device information
- severity: Severity level (low, medium, high, critical)
- created_at: Timestamp
- updated_at: Timestamp
```

## API Endpoints

### 1. Get All Activity Logs
```
GET /api/activity-logs
```
**Parameters:**
- `user_id` (optional): Filter by specific user
- `action_type` (optional): Filter by action type
- `module` (optional): Filter by module
- `severity` (optional): Filter by severity level
- `start_date` (optional): Start date for range filter
- `end_date` (optional): End date for range filter
- `search` (optional): Search in user name, description, IP
- `per_page` (optional): Results per page (default: 50)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [...],
    "total": 100
  }
}
```

### 2. Get Statistics
```
GET /api/activity-logs/statistics
```
**Response:**
```json
{
  "success": true,
  "data": {
    "total_activities": 500,
    "today_activities": 25,
    "this_week_activities": 150,
    "critical_activities": 5,
    "high_severity": 10,
    "failed_logins": 3,
    "by_action_type": [...],
    "by_module": [...],
    "recent_activities": [...]
  }
}
```

### 3. Get My Logs (Manager)
```
GET /api/activity-logs/my-logs
```
Returns only the authenticated user's activity logs.

### 4. Export Logs
```
GET /api/activity-logs/export
```
Exports logs to CSV format with same filtering options as index.

## Web Routes

### Owner Access
```
GET /owner/logs
```
- View all activity logs from all users
- Advanced filtering and search
- Export capabilities
- Statistics dashboard

### Manager Access
```
GET /manager/logs
```
- View only their own activity logs
- Basic filtering
- Personal activity history

## Usage Examples

### 1. Logging a Manual Activity

```php
use App\Traits\LogsActivity;

class ProductController extends Controller
{
    use LogsActivity;

    public function store(Request $request)
    {
        $product = Product::create($request->validated());
        
        // Log the activity
        self::logActivity(
            'create',                    // Action type
            'products',                  // Module
            "Created product: {$product->name}", // Description
            ['product_id' => $product->id],      // Additional details
            'medium'                     // Severity
        );
        
        return response()->json(['success' => true]);
    }
}
```

### 2. Logging Authentication Events

```php
// Already implemented in login controller
use App\Traits\LogsActivity;

// On successful login
self::logLogin($user->id, $user->name, $user->Role);

// On failed login
self::logFailedLogin($request->email);

// On logout
self::logLogout();

// On password change
self::logPasswordChange();

// On password reset request
self::logPasswordReset($email);
```

### 3. Logging in API Controllers

```php
public function destroy($id)
{
    $product = Product::findOrFail($id);
    $productName = $product->name;
    
    $product->delete();
    
    self::logActivity(
        'delete',
        'products',
        "Deleted product: {$productName}",
        [
            'product_id' => $id,
            'product_name' => $productName
        ],
        'high' // High severity for delete operations
    );
    
    return response()->json(['success' => true]);
}
```

## Action Types

| Action Type | Description | Default Severity |
|------------|-------------|------------------|
| `login` | User logged in | Low |
| `logout` | User logged out | Low |
| `failed_login` | Failed login attempt | High |
| `create` | Created a new record | Medium |
| `update` | Updated existing record | Medium |
| `delete` | Deleted a record | High |
| `password_change` | Changed password | Medium |
| `password_reset` | Requested password reset | Medium |
| `view` | Viewed records | Low |
| `export` | Exported data | Medium |
| `import` | Imported data | Medium |

## Module Names

- `authentication` - Login/logout/password activities
- `products` - Product CRUD operations
- `orders` - Order management
- `brands` - Brand management
- `branches` - Branch management
- `users` - User management
- `suppliers` - Supplier management
- `expenses` - Expense tracking
- `reports` - Report generation

## Best Practices

### 1. Log Critical Actions
Always log:
- Authentication events
- Data modifications (create, update, delete)
- Password changes
- Permission changes
- Bulk operations
- Data exports

### 2. Set Appropriate Severity
- **Low**: Read operations, list views
- **Medium**: Create, update, password changes
- **High**: Delete operations, bulk actions
- **Critical**: Security breaches, unauthorized access

### 3. Include Relevant Details
```php
self::logActivity(
    'update',
    'products',
    "Updated product quantity",
    [
        'product_id' => $product->id,
        'old_quantity' => $oldQuantity,
        'new_quantity' => $newQuantity,
        'difference' => $newQuantity - $oldQuantity
    ],
    'medium'
);
```

### 4. Keep Descriptions Clear
- Use present tense
- Be specific
- Include relevant identifiers
- Keep it concise but informative

## Security Features

### 1. Immutable Records
- Activity logs cannot be edited or deleted by users
- Only system administrators have database-level access
- Maintains audit trail integrity

### 2. IP and User Agent Tracking
- Records IP address for geolocation tracking
- Stores browser/device information
- Helps identify suspicious patterns

### 3. Failed Login Monitoring
- Tracks failed login attempts
- High severity flagging
- Enables security alert systems

### 4. Role-Based Access
- Owners see all logs
- Managers see only their own logs
- Customers have no access to logs

## Monitoring and Alerts

### Dashboard Statistics
- Total activities count
- Today's activities
- This week's activities
- Critical activities count
- Failed login attempts
- Activity breakdown by type and module

### Filter Capabilities
- By user
- By action type
- By module
- By severity
- By date range
- Full-text search

## Compliance and Audit

### Data Retention
- Logs are stored indefinitely by default
- Can be archived or purged based on company policy
- Export capability for external archiving

### Audit Reports
- CSV export with all log details
- Filterable exports for specific time periods
- Complete audit trail for compliance

### Legal Requirements
- Maintains complete history of all system changes
- Provides accountability trail
- Supports compliance with data protection regulations

## Troubleshooting

### Common Issues

**1. Logs not appearing:**
- Check if migration was run: `php artisan migrate`
- Verify user is authenticated
- Check if LogsActivity trait is used in controller

**2. Permission errors:**
- Ensure user has correct role (owner/manager)
- Verify route middleware is properly configured

**3. Performance issues:**
- Add indexes to frequently queried fields
- Implement log rotation/archiving
- Use pagination for large datasets

## Future Enhancements

- Real-time log streaming
- Advanced analytics and reporting
- Anomaly detection
- Email alerts for critical events
- Integration with external SIEM systems
- Automated log rotation and archiving

## Maintenance

### Database Cleanup
```php
// Clean logs older than 1 year
ActivityLog::where('created_at', '<', now()->subYear())->delete();
```

### Performance Optimization
- Regular index maintenance
- Archive old logs to separate table
- Implement log rotation strategy
- Use caching for statistics

## Support

For issues or questions about the Activity Logs system:
1. Check this documentation
2. Review the code in `app/Models/ActivityLog.php`
3. Check trait implementation in `app/Traits/LogsActivity.php`
4. Contact system administrator

---

**Last Updated:** October 24, 2025
**Version:** 1.0.0
