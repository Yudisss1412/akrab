<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AdminActivityLogService
{
    /**
     * Mencatat aktivitas admin
     *
     * @param string $activity
     * @param string|null $description
     * @param string $status
     * @return AdminActivityLog
     */
    public static function log(string $activity, string $description = null, string $status = 'success'): AdminActivityLog
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        return AdminActivityLog::create([
            'user_id' => $user->id,
            'activity' => $activity,
            'description' => $description,
            'status' => $status,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Mencatat aktivitas admin untuk user tertentu
     *
     * @param int $userId
     * @param string $activity
     * @param string|null $description
     * @param string $status
     * @return AdminActivityLog
     */
    public static function logForUser(int $userId, string $activity, string $description = null, string $status = 'success'): AdminActivityLog
    {
        return AdminActivityLog::create([
            'user_id' => $userId,
            'activity' => $activity,
            'description' => $description,
            'status' => $status,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}