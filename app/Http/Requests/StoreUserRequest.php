<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:admin,organisateur',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom est obligatoire.',
            'name.max'           => 'Le nom ne doit pas dépasser 100 caractères.',
            'email.required'     => 'L\'adresse email est obligatoire.',
            'email.email'        => 'L\'adresse email n\'est pas valide.',
            'email.unique'       => 'Cette adresse email est déjà utilisée.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required'      => 'Le rôle est obligatoire.',
            'role.in'            => 'Le rôle sélectionné n\'est pas valide.',
        ];
    }
}
