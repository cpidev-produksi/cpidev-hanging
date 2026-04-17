<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'form_key',
        'action',
        'user_id',
        'user_name',
        'user_role',
        'changes',
        'meta',
    ];

    protected $casts = [
        'changes' => 'array',
        'meta' => 'array',
    ];

    public function auditable()
    {
        return $this->morphTo();
    }
}
