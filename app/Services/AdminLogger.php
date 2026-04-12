<?php

namespace App\Services;

use App\Models\AdminLog;

class AdminLogger
{
    public static function log(string $action, string $description, ?string $targetType = null, ?int $targetId = null): void
    {
        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => $action,
            'description' => $description,
            'target_type' => $targetType,
            'target_id'   => $targetId,
        ]);
    }
}
