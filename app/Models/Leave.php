<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'reason',
        'status',
        'catatan',
        'tanggal',
        'type_leave',
        'attachment',
        'approved_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
