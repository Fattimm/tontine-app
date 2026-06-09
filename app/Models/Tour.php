<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tontine_id', 'membre_id', 'numero_tour',
        'date_prevue', 'date_effective', 'montant_recu', 'statut'
    ];

    protected $casts = [
        'date_prevue'    => 'date',
        'date_effective' => 'date',
        'montant_recu'   => 'decimal:2',
    ];

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }
}