<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokja extends Model
{
    protected $fillable = [
        'code',
        'name',
        'group',
        'ep_total',
    ];

    public function regulasis()
    {
        return $this->hasMany(Regulasi::class);
    }

    public function epItems()
    {
        return $this->hasMany(EpItem::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
