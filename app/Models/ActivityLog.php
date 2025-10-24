<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action_type',
        'module',
        'description',
        'details',
        'ip_address',
        'user_agent',
        'severity',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the activity log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by action type
     */
    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope to filter by module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get severity badge color
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'orange',
            'critical' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get action type badge color
     */
    public function getActionColorAttribute()
    {
        return match($this->action_type) {
            'login' => 'success',
            'logout' => 'secondary',
            'create' => 'primary',
            'update' => 'info',
            'delete' => 'danger',
            'password_change' => 'warning',
            'password_reset' => 'warning',
            'failed_login' => 'danger',
            default => 'secondary',
        };
    }
}
