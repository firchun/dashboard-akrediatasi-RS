<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
        'role',
        'pokja_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function pokja()
    {
        return $this->belongsTo(Pokja::class);
    }

    public function isIt()
    {
        return $this->role === 'it';
    }

    public function isKetuaTim()
    {
        return $this->role === 'ketua_tim';
    }

    public function isVerifikator()
    {
        return $this->role === 'verifikator';
    }

    public function isRegulasi()
    {
        return $this->role === 'regulasi';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['it', 'ketua_tim', 'regulasi']);
    }

    public function hasSettingsAccess()
    {
        return in_array($this->role, ['it', 'ketua_tim']);
    }
}
