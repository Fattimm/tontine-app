<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTontineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'                => 'required|string|max:150',
            'nombre_membres_max' => 'required|integer|min:2|max:100',
            'description'        => 'nullable|string',
            'montant_cotisation' => 'required|numeric|min:100',
            'montant_gain'       => 'required|numeric|min:100',
            'frequence'          => 'required|in:quotidien,hebdomadaire,mensuel,trimestriel',
            'date_debut'         => 'required|date',
            'date_fin'           => 'nullable|date|after:date_debut',
            'statut'             => 'in:active,terminee,suspendue',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'                => 'Le nom de la tontine est obligatoire.',
            'nom.max'                     => 'Le nom ne doit pas dépasser 150 caractères.',
            'nombre_membres_max.required' => 'Le nombre maximum de membres est obligatoire.',
            'nombre_membres_max.min'      => 'La tontine doit avoir au moins 2 membres.',
            'nombre_membres_max.max'      => 'La tontine ne peut pas dépasser 100 membres.',
            'montant_cotisation.required' => 'Le montant de cotisation est obligatoire.',
            'montant_cotisation.numeric'  => 'Le montant doit être un nombre.',
            'montant_cotisation.min'      => 'Le montant minimum est de 100 FCFA.',
            'montant_gain.required'       => 'Le montant du gain est obligatoire.',
            'montant_gain.numeric'        => 'Le montant du gain doit être un nombre.',
            'montant_gain.min'            => 'Le montant du gain minimum est de 100 FCFA.',
            'frequence.required'          => 'La fréquence est obligatoire.',
            'frequence.in'                => 'La fréquence choisie n\'est pas valide.',
            'date_debut.required'         => 'La date de début est obligatoire.',
            'date_debut.date'             => 'La date de début n\'est pas une date valide.',
            'date_fin.date'               => 'La date de fin n\'est pas une date valide.',
            'date_fin.after'              => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}
