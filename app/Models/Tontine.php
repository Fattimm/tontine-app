<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tontine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organisateur_id',
        'nom',
        'nombre_membres_max',
        'description',
        'montant_cotisation',
        'montant_gain',
        'frequence',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'date_debut'         => 'date',
        'date_fin'           => 'date',
        'montant_cotisation' => 'decimal:2',
        'montant_gain'       => 'decimal:2',
    ];

    // ✅ Soft delete cascade
    protected static function booted(): void
    {
        static::deleting(function (Tontine $tontine) {
            $tontine->cotisations()->delete();
            $tontine->tours()->delete();
        });

        static::restoring(function (Tontine $tontine) {
            $tontine->cotisations()->withTrashed()->restore();
            $tontine->tours()->withTrashed()->restore();
        });
    }

    // Relations
    public function organisateur()
    {
        return $this->belongsTo(User::class, 'organisateur_id');
    }

    public function membres()
    {
        return $this->belongsToMany(Membre::class, 'membre_tontine')
                    ->withPivot('date_adhesion', 'statut')
                    ->withTimestamps()
                    ->whereNull('membres.deleted_at');
    }

    public function tousLesMembres()
    {
        return $this->belongsToMany(Membre::class, 'membre_tontine')
                    ->withPivot('date_adhesion', 'statut')
                    ->withTimestamps()
                    ->withTrashed();
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function tours()
    {
        return $this->hasMany(Tour::class)->orderBy('numero_tour');
    }

    public function prochainBeneficiaire(): ?Tour
    {
        return $this->tours()
                    ->where('statut', 'en_attente')
                    ->orderBy('numero_tour')
                    ->with('membre')
                    ->first();
    }

    public function peutAjouterMembre(): bool
    {
        return $this->membres()->count() < $this->nombre_membres_max;
    }

    public function placesRestantes(): int
    {
        return max(0, $this->nombre_membres_max - $this->membres()->count());
    }
}
