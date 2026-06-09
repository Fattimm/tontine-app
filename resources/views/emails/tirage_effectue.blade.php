<x-mail::message>
# Félicitations, vous êtes le prochain bénéficiaire !

Bonjour **{{ $tour->membre->nom_complet }}**,

Suite au tirage de la tontine **{{ $tour->tontine->nom }}**, vous avez été désigné(e) comme prochain bénéficiaire.

<x-mail::panel>
**Tontine :** {{ $tour->tontine->nom }}
**Tour numéro :** {{ $tour->numero_tour }}
**Date prévue du versement :** {{ $tour->date_prevue->translatedFormat('d F Y') }}
**Montant estimé :** {{ number_format($tour->tontine->montant_gain ?? ($tour->tontine->montant_cotisation * $tour->tontine->membres()->count()), 0, ',', ' ') }} FCFA
</x-mail::panel>

L'organisateur vous contactera pour confirmer les modalités du versement.

{{ config('app.name') }}
</x-mail::message>
