<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standar extends Model
{
    protected $fillable = [
        'pokja_id',
        'kode',
        'uraian',
    ];

    public function pokja()
    {
        return $this->belongsTo(Pokja::class);
    }

    public function epItems()
    {
        return $this->hasMany(EpItem::class);
    }
}
