<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membre extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom', 'prenom', 'telephone', 'email', 'adresse', 'statut'
    ];

    // ✅ Accesseur : nom complet
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    // Relations
    public function tontines()
    {
        return $this->belongsToMany(Tontine::class, 'membre_tontine')
                    ->withPivot('date_adhesion', 'statut')
                    ->withTimestamps();
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function tours()
    {
        return $this->hasMany(Tour::class);
    }

    // ✅ Scope : filtrer membres actifs
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }
}