<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpItem extends Model
{
    protected $fillable = [
        'pokja_id',
        'standar_id',
        'no_urut',
        'uraian',
        'bukti_r',
        'bukti_d',
        'bukti_o',
        'bukti_w',
        'bukti_s',
        'nilai',
        'fakta_analisis',
        'rekomendasi',
        'pengingat',
        'pic',
        'link',
        'history',
        'keterangan',
    ];

    protected $casts = [
        'bukti_r' => 'boolean',
        'bukti_d' => 'boolean',
        'bukti_o' => 'boolean',
        'bukti_w' => 'boolean',
        'bukti_s' => 'boolean',
        'history' => 'array',
    ];

    public function pokja()
    {
        return $this->belongsTo(Pokja::class);
    }

    public function standar()
    {
        return $this->belongsTo(Standar::class);
    }

    public function uploadFiles()
    {
        return $this->hasMany(UploadFile::class, 'related_id')->where('jenis_upload', 'ep')->orderBy('id', 'asc');
    }
}
