<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(string $formKey, string $action, ?Model $model, array $changes = [], array $meta = []): void
    {
        $user = Auth::user(); 

        AuditLog::create([
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id'   => $model?->getKey(),
            'form_key'       => $formKey,
            'action'         => $action,

            'user_id'   => $user?->id,
            'user_name' => $user?->name,
            'user_role' => $user?->role?->slug ?? $user?->role?->name,

            'changes' => $changes ?: null,
            'meta'    => $meta ?: null,
        ]);
    }

    public static function diff(array $before, array $after): array
    {
        $changes = [];
        foreach ($after as $key => $val) {
            $old = $before[$key] ?? null;
            if ($old != $val) {
                $changes[$key] = ['before' => $old, 'after' => $val];
            }
        }
        return $changes;
    }
}