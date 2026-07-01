<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regulasi extends Model
{
    protected $fillable = [
        'pokja_id',
        'nama',
        'jenis',
        'pic',
        'link',
        'keterangan',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'history' => 'array',
    ];

    protected $appends = ['status'];

    public function pokja()
    {
        return $this->belongsTo(Pokja::class);
    }

    public function getStatusAttribute()
    {
        $isComplete = !empty($this->pic) && !empty($this->link);
        
        if (!$isComplete) {
            return 'Belum';
        }
        
        if ($this->is_verified) {
            return 'Selesai';
        }
        
        return 'Proses';
    }
}
