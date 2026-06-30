<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'hospital_name',
        'target_date',
        'is_pendidikan',
        'prognas_full',
        'calc_mode',
    ];

    protected $casts = [
        'is_pendidikan' => 'boolean',
        'prognas_full' => 'boolean',
        'target_date' => 'date',
    ];
}
