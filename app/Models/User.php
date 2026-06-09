<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'membre_id'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ✅ Relations
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    // ✅ Helpers rôles
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganisateur(): bool
    {
        return $this->role === 'organisateur';
    }

    public function isMembre(): bool
    {
        return $this->role === 'membre';
    }

    public function isAdminOrOrganisateur(): bool
    {
        return in_array($this->role, ['admin', 'organisateur']);
    }
}