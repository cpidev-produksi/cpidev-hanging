<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ReportEvis extends Model
{
    protected $table = 'report_evis';
    protected $fillable = [
        'report_date',
        'created_by',
        'approved_by',
        'approved_at',
        'approved_signature_path',
        'status',
        'total_bag',
        'total_kg'
    ];

    protected $casts = [
        'report_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReportEvisItem::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeApproved(?User $user = null): bool
    {
        $user ??= Auth::user();
        return in_array($user?->role?->slug, ['superadmin','admin','foreman','supervisor','manager',]);
    }

    public function canBeEdited(?User $user = null): bool
    {
        $user ??= Auth::user();

        return in_array($user?->role?->slug, ['superadmin','admin','foreman','supervisor','manager',])
        || $this->created_by === $user?->id;
    }
}