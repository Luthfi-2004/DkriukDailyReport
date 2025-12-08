<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'report_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    // Relasi: Laporan ini punya siapa?
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Laporan ini isinya apa aja?
    public function details(): HasMany
    {
        return $this->hasMany(DailyReportDetail::class);
    }
    
    // Helper Method untuk cek status
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}