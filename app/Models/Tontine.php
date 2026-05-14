<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tontine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom', 'description', 'nombre_membres_max', 'montant_cotisation',
        'frequence', 'date_debut', 'date_fin', 'statut'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'montant_cotisation' => 'decimal:2',
    ];

    public function membres()
    {
        return $this->belongsToMany(Membre::class, 'membre_tontine')
                    ->withPivot('date_adhesion', 'statut')
                    ->withTimestamps()
                    ->wherePivot('statut', 'actif') 
                    ->whereNull('membres.deleted_at'); 
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function tours()
    {
        return $this->hasMany(Tour::class)->orderBy('numero_tour');
    }

    // ✅ Méthode métier : prochain bénéficiaire
    public function prochainBeneficiaire(): ?Tour
    {
        return $this->tours()
                    ->where('statut', 'en_attente')
                    ->orderBy('numero_tour')
                    ->with('membre')
                    ->first();
    }

    public function tousLesMembres()
    {
        return $this->belongsToMany(Membre::class, 'membre_tontine')
                    ->withPivot('date_adhesion', 'statut')
                    ->withTimestamps()
                    ->withTrashed(); 
    }

        /**
     * ✅ Vérifie si la tontine peut encore accepter des membres
     */
    public function peutAjouterMembre(): bool
    {
        return $this->membres()->count() < $this->nombre_membres_max;
    }

    /**
     * ✅ Nombre de places restantes
     */
    public function placesRestantes(): int
    {
        return max(0, $this->nombre_membres_max - $this->membres()->count());
    }
}
