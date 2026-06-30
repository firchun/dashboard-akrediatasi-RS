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
        'target',
        'link',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'target' => 'date',
    ];

    public function pokja()
    {
        return $this->belongsTo(Pokja::class);
    }
}
