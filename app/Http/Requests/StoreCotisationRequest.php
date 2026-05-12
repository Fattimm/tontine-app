<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreCotisationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'membre_id'     => ['required', 'integer', 'exists:membres,id'],
            'tontine_id'    => ['required', 'integer', 'exists:tontines,id'],
            'tour_id'       => ['nullable', 'integer', 'exists:tours,id'],
            'montant'       => ['required', 'numeric', 'min:100', 'max:10000000'],
            'date_paiement' => ['required', 'date', 'before_or_equal:today'],
            'mode_paiement' => ['required', 'in:espece,mobile_money,virement'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
       return [
            'membre_id.required'            => 'Veuillez sélectionner un membre.',
            'membre_id.exists'              => 'Ce membre n\'existe pas.',
            'tontine_id.required'           => 'Veuillez sélectionner une tontine.',
            'tontine_id.exists'             => 'Cette tontine n\'existe pas.',
            'date_paiement.required'        => 'La date de paiement est obligatoire.',
            'date_paiement.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'mode_paiement.required'        => 'Le mode de paiement est obligatoire.',
        ];
    }
}
