<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    protected $fillable = [
        'jenis_upload',
        'file',
        'id_user',
        'related_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
