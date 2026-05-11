<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Cotisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'membre_id', 'tontine_id', 'tour_id',
        'montant', 'date_paiement', 'mode_paiement',
        'statut', 'reference', 'notes'
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    // ✅ Génère automatiquement une référence unique
    protected static function booted(): void
    {
        static::creating(function (Cotisation $cotisation) {
            if (empty($cotisation->reference)) {
                $cotisation->reference = 'COT-' . strtoupper(Str::random(8));
            }
        });
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}