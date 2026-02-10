<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     */
    public function log(
        string $action, 
        string $description, 
        ?string $entityType = null, 
        ?int $entityId = null,
        string $severity = 'info',
        ?int $userId = null
    ) {
        $userId = $userId ?? Auth::id();

        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'severity' => $severity,
        ]);
    }

    /**
     * Log critical financial changes.
     */
    public function logFinancial($action, $description, $entity)
    {
        return $this->log(
            $action, 
            $description, 
            get_class($entity), 
            $entity->id, 
            'critical'
        );
    }
}
