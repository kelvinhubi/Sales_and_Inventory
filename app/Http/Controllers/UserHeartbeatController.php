<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserHeartbeatController extends Controller
{
    protected $offlineThreshold;

    public function __construct()
    {
        // Chain middleware together instead of separate calls
        $this->middleware(['web', 'auth']);
        $this->offlineThreshold = Carbon::now()->subMinutes(5);
    }

    public function update(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                Log::warning('Heartbeat: No authenticated user found');
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            if ($user->Role !== 'Manager' && $user->Role !== 'Owner') {
                Log::warning('Heartbeat: Non-manager user attempted access', ['user_id' => $user->id]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get activity status from request
            $isActive = $request->input('is_active', false);
            $lastActivity = $request->input('last_activity') 
                ? Carbon::parse($request->input('last_activity'))
                : now();

            // More granular status determination
            if (!$isActive) {
                // User explicitly marked as inactive (browser closed, etc.)
                $isOnline = false;
            } elseif ($lastActivity->lt(now()->subMinutes(5))) {
                // User hasn't been active for more than 5 minutes
                $isOnline = false;
            } else {
                // User is active and within time threshold
                $isOnline = true;
            }

            Log::info('Heartbeat received:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_active' => $isActive,
                'is_online' => $isOnline,
                'last_activity' => $lastActivity,
                'timestamp' => now()
            ]);

            $user->update([
                'last_activity' => $lastActivity,
                'is_online' => $isOnline
            ]);

            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'is_online' => $isOnline,
                    'is_active' => $isActive,
                    'last_activity' => $user->last_activity,
                    'status_text' => $this->getStatusText($isActive, $isOnline, $lastActivity)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Heartbeat error:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getOnlineUsers()
    {
        try {
            // First, update all inactive users' status
            $this->updateInactiveUsers();

            // Get all managers with their current online status
            $users = User::select('id', 'name', 'email', 'is_online', 'last_activity')
                ->where('Role', 'Manager')
                ->get();

            Log::info('Online users fetched', [
                'total_managers' => $users->count(),
                'online_count' => $users->where('is_online', true)->count()
            ]);

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Error fetching online users:', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Update the online status of inactive users
     */
    private function updateInactiveUsers()
    {
        try {
            $affected = User::where(function ($query) {
                $query->where('last_activity', '<', $this->offlineThreshold)
                    ->orWhereNull('last_activity');
            })->update(['is_online' => false]);

            if ($affected > 0) {
                Log::info("Marked {$affected} users as offline due to inactivity");
            }
        } catch (\Exception $e) {
            Log::error('Error updating inactive users:', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get human-readable status text
     */
    private function getStatusText($isActive, $isOnline, $lastActivity)
    {
        if (!$isOnline) {
            return 'Offline';
        }
        
        if (!$isActive) {
            return 'Away';
        }
        
        $minutesSinceActivity = now()->diffInMinutes($lastActivity);
        
        if ($minutesSinceActivity < 1) {
            return 'Active';
        } elseif ($minutesSinceActivity < 5) {
            return 'Idle';
        } else {
            return 'Away';
        }
    }
}
