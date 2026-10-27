<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class EviscerationEfficiencyReport extends Model
{
    protected $fillable = [
        'report_date',
        'shift',
        'location',
        'status',
        'created_by',
        'checker_name',
        'known_by',
        'known_name',
        'known_at',
        'approved_by',
        'approved_name',
        'approved_at',
    ];

    public const KNOWN_NAMES = ['Faisal', 'Faridah'];

    public const APPROVED_NAMES = ['Awang W.', 'Diana F', 'Nadya P'];

    public const CREATOR_ROLES = ['checker', 'checker_hanging', 'foreman', 'supervisor', 'manager', 'admin', 'superadmin'];

    public const KNOWER_ROLES = ['foreman', 'superadmin', 'admin'];

    public const APPROVER_ROLES = ['supervisor', 'manager', 'superadmin'];

    public const MANAGER_ROLES = ['foreman', 'supervisor', 'manager', 'admin', 'superadmin'];

    protected $casts = [
        'report_date' => 'date',
        'known_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(EviscerationEfficiencyItem::class)->orderBy('sort')->orderBy('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function knower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'known_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'known' => 'Diketahui',
            'approved' => 'Disetujui',
            default => 'Draft',
        };
    }

    private static function roleSlug(?User $user): ?string
    {
        $user ??= Auth::user();

        return $user?->role?->slug;
    }

    public function canBeEdited(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $this->status === 'draft'
            && ($this->created_by === $user?->id || in_array(self::roleSlug($user), self::MANAGER_ROLES, true));
    }

    public function canManageItems(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $this->status === 'draft' && in_array(self::roleSlug($user), self::CREATOR_ROLES, true);
    }

    public function canBeDeleted(?User $user = null): bool
    {
        return $this->canBeEdited($user);
    }

    public function canBeKnown(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $this->status === 'draft' && in_array(self::roleSlug($user), self::KNOWER_ROLES, true);
    }

    public function canBeApproved(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $this->status === 'known' && in_array(self::roleSlug($user), self::APPROVER_ROLES, true);
    }
}
