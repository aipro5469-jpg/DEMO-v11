<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait AuditLoggerTrait
{
    public static function bootAuditLoggerTrait()
    {
        static::created(function (Model $model) {
            self::logAudit('created', $model);
        });

        static::updated(function (Model $model) {
            self::logAudit('updated', $model, $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function (Model $model) {
            self::logAudit('deleted', $model, $model->toArray());
        });
    }

    protected static function logAudit(string $event, Model $model, $oldValues = null, $newValues = null)
    {
        if (!Auth::check()) {
            return; // Only log authenticated actions
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
