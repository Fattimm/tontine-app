<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'membre_id'      => 'required|exists:membres,id',
            'numero_tour'    => 'required|integer|min:1',
            'date_prevue'    => 'required|date',
            'date_effective' => 'nullable|date',
            'montant_recu'   => 'nullable|numeric|min:0',
            'statut'         => 'nullable|in:en_attente,complete,reporte',
        ];
    }

    public function messages(): array
    {
        return [
            'membre_id.required'   => 'Le membre est obligatoire.',
            'membre_id.exists'     => 'Le membre sélectionné n\'existe pas.',
            'numero_tour.required' => 'Le numéro du tour est obligatoire.',
            'numero_tour.integer'  => 'Le numéro du tour doit être un entier.',
            'numero_tour.min'      => 'Le numéro du tour doit être au moins 1.',
            'date_prevue.required' => 'La date prévue est obligatoire.',
            'date_prevue.date'     => 'La date prévue n\'est pas une date valide.',
            'date_effective.date'  => 'La date effective n\'est pas une date valide.',
            'montant_recu.numeric' => 'Le montant reçu doit être un nombre.',
            'montant_recu.min'     => 'Le montant reçu ne peut pas être négatif.',
            'statut.in'            => 'Le statut sélectionné n\'est pas valide.',
        ];
    }
}
