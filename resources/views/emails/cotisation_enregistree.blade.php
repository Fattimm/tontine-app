<x-mail::message>
# Cotisation confirmée

Bonjour **{{ $cotisation->membre->nom_complet }}**,

Votre cotisation pour la tontine **{{ $cotisation->tontine->nom }}** a bien été enregistrée.

<x-mail::panel>
**Montant :** {{ number_format($cotisation->montant, 0, ',', ' ') }} FCFA
**Date :** {{ $cotisation->date_paiement->translatedFormat('d F Y') }}
**Mode de paiement :** {{ ucfirst(str_replace('_', ' ', $cotisation->mode_paiement)) }}
**Type :** {{ $cotisation->est_reserve ? 'Réserve (paiement anticipé)' : 'Cotisation normale' }}
</x-mail::panel>

@if($cotisation->notes)
**Note :** {{ $cotisation->notes }}
@endif

Merci pour votre ponctualité.

{{ config('app.name') }}
</x-mail::message>
