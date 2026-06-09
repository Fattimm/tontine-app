<?php

namespace App\Http\Requests;

use App\Rules\TelephoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMembreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Pour l'update : ignorer l'unicité du membre en cours d'édition
        $id = $this->route('membre')?->id;

        return [
            'nom'       => ['required', 'string', 'max:100'],
            'prenom'    => ['required', 'string', 'max:100'],
            'telephone' => [
                'required',
                new TelephoneRule(),              // ✅ Format sénégalais
                "unique:membres,telephone,{$id}", // ✅ Unicité sauf soi-même
            ],
            'email'     => [
                'nullable',
                'email',
                "unique:membres,email,{$id}",
            ],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'statut'    => ['sometimes', 'in:actif,inactif'],
            'tontine_id' => $this->isMethod('POST')
                ? ['required', 'exists:tontines,id']
                : ['nullable', 'exists:tontines,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'       => 'Le nom est obligatoire.',
            'nom.max'            => 'Le nom ne doit pas dépasser 100 caractères.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'prenom.max'         => 'Le prénom ne doit pas dépasser 100 caractères.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique'   => 'Ce numéro de téléphone est déjà enregistré.',
            'email.email'        => 'Veuillez fournir une adresse email valide.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'statut.in'          => 'Le statut doit être actif ou inactif.',
            'tontine_id.required' => 'Veuillez sélectionner une tontine.',
            'tontine_id.exists'   => 'La tontine sélectionnée n\'existe pas.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        
        parent::failedValidation($validator);
    }
}
