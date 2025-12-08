<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'master_item_id',
        'quantity',
    ];

    // Relasi balik ke Header Laporan
    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    // Relasi ke Barang (Untuk ambil nama barang & satuannya)
    public function item(): BelongsTo
    {
        return $this->belongsTo(MasterItem::class, 'master_item_id');
    }
}