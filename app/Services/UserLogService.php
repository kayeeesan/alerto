<?php

namespace App\Services;

use App\Models\UserLog;
use illuminate\Support\Facades\Auth;

class UserLogService
{
    public function logAction(string $entityType, int $entityId, string $action, array $changes = [])
    {
        UserLog::create([
            'user_id' => Auth::id(),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'changes' => $changes,
        ]);
    }
}
