<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuans';

    protected $fillable = [
        'judul',
        'deskripsi',
        'status_id',
        'maker_id',
        'meta',
        'rejection_reason',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id');
    }
}