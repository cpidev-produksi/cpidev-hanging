<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class DefeatReport extends Model
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

    protected $casts = [
        'report_date' => 'date',
        'known_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /** Role yang boleh mengisi/membuat laporan. */
    public const CREATOR_ROLES  = ['checker', 'checker_hanging', 'foreman', 'supervisor', 'manager', 'admin', 'superadmin'];
    /** Role yang boleh menandatangani "Diketahui oleh" (Foreman/lady). */
    public const KNOWER_ROLES   = ['foreman', 'superadmin', 'admin'];
    /** Role yang boleh menandatangani "Disetujui oleh" (Supervisor). */
    public const APPROVER_ROLES = ['supervisor', 'manager', 'superadmin'];
    /** Role yang boleh mengubah/menghapus laporan milik orang lain. */
    public const MANAGER_ROLES  = ['foreman', 'supervisor', 'manager', 'admin', 'superadmin'];

    public function items(): HasMany
    {
        return $this->hasMany(DefeatReportItem::class)->orderBy('sort')->orderBy('id');
    }

    /** Total & average seluruh farm dalam tabel ini. */
    public function summary(): array
    {
        return DefeatReportItem::summarize($this->items);
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

    public function isDraft(): bool    { return $this->status === 'draft'; }
    public function isKnown(): bool    { return $this->status === 'known'; }
    public function isApproved(): bool { return $this->status === 'approved'; }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'known'    => 'Diketahui',
            'approved' => 'Disetujui',
            default    => 'Draft',
        };
    }

    private static function slug(?User $user): ?string
    {
        $user ??= Auth::user();

        return $user?->role?->slug;
    }

    public function canBeEdited(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $this->isDraft()
            && ($this->created_by === $user?->id || in_array(self::slug($user), self::MANAGER_ROLES, true));
    }

    /** Tambah / ubah / hapus baris farm: hanya saat Draft dan oleh role pengisi. */
    public function canManageItems(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $this->isDraft() && in_array(self::slug($user), self::CREATOR_ROLES, true);
    }

    public function canBeDeleted(?User $user = null): bool
    {
        return $this->canBeEdited($user);
    }

    public function canBeKnown(?User $user = null): bool
    {
        return $this->isDraft() && in_array(self::slug($user), self::KNOWER_ROLES, true);
    }

    public function canBeApproved(?User $user = null): bool
    {
        return $this->isKnown() && in_array(self::slug($user), self::APPROVER_ROLES, true);
    }
}