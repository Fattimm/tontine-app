<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tontine_id'  => 'required|exists:tontines,id',
            'membre_id'   => 'required|exists:membres,id',
            'numero_tour' => 'required|integer|min:1',
            'date_prevue' => 'required|date',
            'statut'      => 'nullable|in:en_attente,complete,reporte',
        ];
    }

    public function messages(): array
    {
        return [
            'tontine_id.required'  => 'La tontine est obligatoire.',
            'tontine_id.exists'    => 'La tontine sélectionnée n\'existe pas.',
            'membre_id.required'   => 'Le membre est obligatoire.',
            'membre_id.exists'     => 'Le membre sélectionné n\'existe pas.',
            'numero_tour.required' => 'Le numéro du tour est obligatoire.',
            'numero_tour.integer'  => 'Le numéro du tour doit être un entier.',
            'numero_tour.min'      => 'Le numéro du tour doit être au moins 1.',
            'date_prevue.required' => 'La date prévue est obligatoire.',
            'date_prevue.date'     => 'La date prévue n\'est pas une date valide.',
            'statut.in'            => 'Le statut sélectionné n\'est pas valide.',
        ];
    }
}
