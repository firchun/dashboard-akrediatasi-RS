<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpItem extends Model
{
    protected $fillable = [
        'pokja_id',
        'kode',
        'uraian',
        'bukti_r',
        'bukti_d',
        'bukti_o',
        'bukti_w',
        'bukti_s',
        'nilai',
        'pic',
        'link',
        'keterangan',
    ];

    protected $casts = [
        'bukti_r' => 'boolean',
        'bukti_d' => 'boolean',
        'bukti_o' => 'boolean',
        'bukti_w' => 'boolean',
        'bukti_s' => 'boolean',
    ];

    public function pokja()
    {
        return $this->belongsTo(Pokja::class);
    }
}
