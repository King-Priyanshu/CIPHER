<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AdminAuditService
{
    /**
     * Log an admin action.
     *
     * @param User $admin The admin performing the action.
     * @param string $action The action name (e.g., 'refund_approved').
     * @param array $details Detailed data about the action (snapshot).
     * @param string $severity Severity level: 'info', 'warning', 'critical'.
     * @param Model|null $entity The related entity (optional).
     * @return ActivityLog
     */
    public function log(User $admin, string $action, array $details, string $severity = 'info', $entity = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $admin->id, // Admin is also a user
            'admin_id' => $admin->id,
            'action' => $action,
            'description' => $this->formatDescription($action, $details),
            'severity' => $severity,
            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity ? $entity->id : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            // We can store the full details in a 'properties' JSON column if we add one, 
            // but for now we'll put key info in description or assume schema holds basic logs.
            // If we want detailed snapshots, we should use a package like spatie/laravel-activitylog
            // or add a 'metadata' JSON column. Given constraints, let's keep it simple for now and rely on description.
        ]);
    }

    protected function formatDescription(string $action, array $details): string
    {
        $desc = ucfirst(str_replace('_', ' ', $action));
        if (!empty($details)) {
            $desc .= ': ' . json_encode($details);
        }
        return $desc;
    }
}
