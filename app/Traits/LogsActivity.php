<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity
     */
    public static function logActivity(
        string $actionType,
        string $module,
        string $description,
        array $details = null,
        string $severity = 'low'
    ) {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        return ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->Role ?? 'guest',
            'action_type' => $actionType,
            'module' => $module,
            'description' => $description,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'severity' => $severity,
        ]);
    }

    /**
     * Log login activity
     */
    public static function logLogin($userId, $userName, $userRole)
    {
        return ActivityLog::create([
            'user_id' => $userId,
            'user_name' => $userName,
            'user_role' => $userRole,
            'action_type' => 'login',
            'module' => 'authentication',
            'description' => 'User logged in successfully',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'severity' => 'low',
        ]);
    }

    /**
     * Log logout activity
     */
    public static function logLogout()
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        return ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->Role ?? 'guest',
            'action_type' => 'logout',
            'module' => 'authentication',
            'description' => 'User logged out',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'severity' => 'low',
        ]);
    }

    /**
     * Log failed login attempt
     */
    public static function logFailedLogin($email)
    {
        return ActivityLog::create([
            'user_id' => 0,
            'user_name' => $email,
            'user_role' => 'unknown',
            'action_type' => 'failed_login',
            'module' => 'authentication',
            'description' => 'Failed login attempt',
            'details' => json_encode(['email' => $email]),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'severity' => 'high',
        ]);
    }

    /**
     * Log password change
     */
    public static function logPasswordChange()
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        return ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->Role ?? 'guest',
            'action_type' => 'password_change',
            'module' => 'authentication',
            'description' => 'Password changed successfully',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'severity' => 'medium',
        ]);
    }

    /**
     * Log password reset
     */
    public static function logPasswordReset($email)
    {
        return ActivityLog::create([
            'user_id' => 0,
            'user_name' => $email,
            'user_role' => 'unknown',
            'action_type' => 'password_reset',
            'module' => 'authentication',
            'description' => 'Password reset requested',
            'details' => json_encode(['email' => $email]),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'severity' => 'medium',
        ]);
    }
}
