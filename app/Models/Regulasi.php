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
        'history',
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
        $hasFiles = $this->uploadFiles()->exists();
        $isComplete = !empty($this->pic) && (!empty($this->link) || $hasFiles);
        
        if (!$isComplete) {
            return 'Belum';
        }
        
        if ($this->is_verified) {
            return 'Selesai';
        }
        
        return 'Proses';
    }

    public function uploadFiles()
    {
        return $this->hasMany(UploadFile::class, 'related_id')->where('jenis_upload', 'regulasi')->orderBy('id', 'asc');
    }
}
